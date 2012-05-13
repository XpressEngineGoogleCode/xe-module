<?php
	/**
	* @class  adAdminController
	* @author SMaker (dowon2308@paran.com)
	* @brief  admin controller class of ad module
	 **/

	class adAdminController extends ad {

		/**
		 * @brief init
		 **/
		function init() {
		}

		/**
		 * @brief 기본 설정 > 설정 저장
		 **/
		function procAdAdminInsertConfig() {
			// get request config
			$config = Context::getRequestVars();

			// update module config
			$oModuleController = &getController('module');
			$oModuleController->insertModuleConfig('ad', $config);

			// set message
			$this->setMessage('success_saved');
		}

		/**
		 * @brief 광고 모듈 생성 및 수정
		 **/
		function procAdAdminInsertModule($args = null) {
			// 광고 모듈의 model / controller 객체 생성
			$oModuleController = &getController('module');
			$oModuleModel = &getModel('module');

			// 광고 모듈의 controller 객체 생성
			$oAdController = &getController('ad');

			// ad module info
			$args = Context::getRequestVars();
			$args->module = 'ad';
			$args->mid = $args->mid;

			// 기본 값외의 것들을 정리
			if($args->ad_point<1) $args->ad_point = 0;
			if($args->ad_limit<1) $args->ad_list = 0;
			if($args->daily_limit<1) $args->daily_limit = 0;
			if($args->use_notify != 'Y') $args->use_notify = 'N';
			if($args->ad_type != 'image') $args->ad_type = 'text';

			// module_srl이 넘어오면 원 모듈이 있는지 확인
			if($args->module_srl) {
				$module_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
				if($module_info->module_srl != $args->module_srl) unset($args->module_srl);
			}

			// module_srl의 값에 따라 insert/update
			if(!$args->module_srl) {
				$output = $oModuleController->insertModule($args);
				$msg_code = 'success_registed';
			} else {
				$output = $oModuleController->updateModule($args);
				$msg_code = 'success_updated';
			}

			if(!$output->toBool()) return $output;

			// update notify module cache
			if($module_info->use_notify && $module_info->use_notify != $args->use_notify) $oAdController->updateNotifyModuleCache();

			// return
			$this->add('page',Context::get('page'));
			$this->add('module_srl',$output->get('module_srl'));

			// set message
			$this->setMessage($msg_code);
		}

		/**
		 * @brief delete ad module
		 **/
		function procAdAdminDeleteModule() {
			$module_srl = Context::get('module_srl');

			// delete module
			$oModuleController = &getController('module');
			$output = $oModuleController->deleteModule($module_srl);
			if(!$output->toBool()) return $output;

			$this->add('module','ad');
			$this->add('page',Context::get('page'));
			// set message
			$this->setMessage('success_deleted');
		}

		/**
		 * @brief 관리자 페이지에서 선택된 광고들을 삭제
		 **/
		function procAdAdminDeleteChecked() {

			// if not selected ad, return error
			$cart = Context::get('cart');
			if(!$cart) return $this->stop('msg_cart_is_null2');
			$document_srl_list= explode('|@|', $cart);
			$ad_count = count($document_srl_list);
			if(!$ad_count) return $this->stop('msg_cart_is_null2');

			// create controller class of ad module
			$oAdController = &getController('ad');

			$deleted_count = 0;

			// delete selected ad
			for($i=0;$i<$ad_count;$i++) {
				$document_srl = trim($document_srl_list[$i]);
				if(!$document_srl) continue;

				$output = $oAdController->deleteAd($document_srl, true, false);
				if(!$output->toBool()) continue;

				$deleted_count ++;
			}
	
			// set message
			$this->setMessage(sprintf(Context::getLang('msg_checked_ad_is_deleted'), $deleted_count));
		}

		/**
		 * @brief 공지 등록
		 **/
		function procAdAdminNoticeWrite() {
			// 권한 체크
			if(!$this->grant->manager) return new Object(-1, 'msg_not_permitted');

			// 공지 등록 시 필요한 변수를 세팅
			$obj = Context::getRequestVars();
			$obj->is_notice = 'Y';

			// 광고 내용이 없으면 에러
			settype($obj->ad_content, 'string');
			if($obj->ad_content == '') return new Object(-1, 'msg_invalid_request');

			// 기본값 지정
			$obj->title = $obj->ad_content; // 제목
			$obj->content = $obj->ad_content; // 내용
			$obj->allow_comment = 'N'; // 댓글 허용
			$obj->lock_comment = 'Y'; // 댓글 잠금
			if($obj->url && !preg_match('/^([a-z]+):\/\//i',$obj->url)) $obj->url = 'http://'.$obj->url; // URL (형식에 맞지 않으면 앞에 http://를 붙임)
			if(!in_array($obj->url_target,array('_self','_blank'))) $obj->url_target = '_blank'; // URL 열기 대상 (_self : 현재 창, _blank : 새 창)

			// 사용한 광고 옵션을 배열로 변환
			if($obj->used_style) $used_style = explode('|@|',$obj->used_style);

			// 글자색과 배경색이 같을 경우 오류
			if($obj->ad_color && $obj->ad_bgcolor && $obj->ad_color == $obj->ad_bgcolor) return new Object(-1, 'msg_invalid_color');

			// 사용한 글자색을 배열에 저장
			if($obj->ad_color) {
				$used_style[] = 'text_'.$obj->ad_color;
				unset($obj->ad_color);
			}

			// 사용한 배경색을 배열에 저장
			if($obj->ad_bgcolor) {
				$used_style[] = 'bg_'.$obj->ad_bgcolor;
				unset($obj->ad_bgcolor);
			}

			// 사용한 광고 옵션 & 스타일 배열을 합치기
			if(is_array($used_style)) $obj->used_style = join('|@|',$used_style);
			else $obj->used_style = array();

			// check ad time (Firebug나 개발자 도구 등을 통해서 임의로 조작하는 것을 막기)
			if($this->module_info->use_time == 'Y') {
				$obj->ad_time = (int)$obj->ad_time;
				$AdTimeRange = explode(',',$this->module_info->ad_time_range);
				$AdTimeRange[] = -1;
				if(!$obj->ad_time || !in_array($obj->ad_time, $AdTimeRange)) return new Object(-1, 'msg_invalid_ad_time');
			}
			
			// create model class of ad module
			$oAdModel = &getModel('ad');

			// create controller class of document module
			$oDocumentController = &getController('document');

			// 이미 존재하는 광고인지 체크
			$oAd = $oAdModel->getAd($obj->document_srl, $this->grant->manager);

			// 이미 존재하는 경우 에러 출력
			if($oAd->isExists() && $oAd->document_srl == $obj->document_srl) {
				return new Object(-1, 'msg_invalid_request');
			// 그렇지 않으면 신규 등록
			} else {
				// insert document
				$output = $oDocumentController->insertDocument($obj);
				$msg_code = 'success_registed';
				$obj->document_srl = $output->get('document_srl');
				if(!$output->toBool()) return $output;

				// insert notice
				$ad->document_srl = $obj->document_srl;
				$ad->ad_time = $obj->ad_time;
				$ad->url = $obj->url;
				$ad->url_target = $obj->url_target;
				$ad->style = $obj->used_style;
				$ad->start_date = $obj->start_date;
				$ad->start_hour = $obj->start_hour;
				$ad->start_minute = $obj->start_minute;
				$ad->start_second = $args->start_second;
				$oAdController= &getController('ad');
				$oAdController->insertNotice($ad);
			}

			// set message
			$this->setMessage($msg_code);
		}

		/**
		 * @brief
		 */
		function procAdAdminMigration(){
			// if exists old linead module info, execute update
			if(!file_exists(_XE_PATH_.'files/cache/ad/updated')) {
				$old_config = $oAdModel->getOldConfig();
				if($old_config) {
					// first, save the old module config
					$module_srl = $OldConfig->module_srl;

					// change old linead module to ad module
					$oAdController->updateModule();

					// save need config.
					$config_args->ad_point = $OldConfig->ad_point;
					$config_args->daily_limit = $OldConfig->daily_limit;
					$oModuleController->insertModulePartConfig('ad',$module_srl,$config_args);

					// delete old config
					$oAdController = &getController('ad');
					$oAdController->deleteOldConfig();
					$args->module = 'linead';
					$output = executeQuery('module.deleteModuleConfig', $args);
					if(!$output->toBool()) return $output;

					if($oAdModel->isExistsOldModule()) $oAdController->updateModule();

					// write cache file
					$cache_path ='./files/cache/ad/updated';
					FileHandler::writeFile($cache_path, 'Y');
			} else {
					// write cache file
					$cache_path ='./files/cache/ad/updated';
					FileHandler::writeFile($cache_path, 'Y');
			}

			if(!file_exists(_XE_PATH_.'files/cache/ad/updated') && $oAdModel->isExistsOldModule()) $oAdController->updateModule();
			}
		}

		/**
		 * @brief 기존 설정 (전광판 모듈 설정) 삭제
		 */
		function procAdAdminDeleteOldConfig(){
			// 기존 설정 삭제
			$oAdController = &getController('ad');
			$oAdController->deleteOldConfig();

			// 메시지 지정
			$this->setMessage('success_deleted');
		}

		/**
		 * @brief 광고 시간 범위를 캐시 파일로 저장
		 **/
		function makeAdTimeRangeFile($module_srl) {
			// 캐시 파일 생성시 필요한 정보가 없으면 그냥 return
			if(!$module_srl) return false;

			// 모듈 정보를 가져옴 (mid를 구하기 위해)
			$oModuleModel = &getModel('module');
			$module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			$mid = $module_info->mid;

			if(!is_dir('./files/cache/ad/ad_time_range')) FileHandler::makeDir('./files/cache/ad/ad_time_range');

			// 캐시 파일의 이름을 지정
			$xml_file = sprintf('./files/cache/ad/ad_time_range/%s.xml.php', $module_srl);
			$php_file = sprintf('./files/cache/ad/ad_time_range/%s.php', $module_srl);

			// 광고 시간 범위 목록을 구함
			$args->module_srl = $module_srl;
			$args->sort_index = 'list_order';
			$output = executeQuery('ad.getAdTimeRangeList', $args);

			$ad_time_range_list = $output->data;

			if(!$ad_time_range_list) {
				FileHandler::removeFile($xml_file);
				FileHandler::removeFile($php_file);
				return false;
			}
			if(!is_array($ad_time_range_list)) $ad_time_range_list = array($ad_time_range_list);

			$count = count($ad_time_range_list);
			for($i=0;$i<$count;$i++) {
				$time_srl = $ad_time_range_list[$i]->time_srl;
				$list[$time_srl] = $ad_time_range_list[$i];
			}

			// 구해온 데이터가 없다면 노드데이터가 없는 xml 파일만 생성
			if(!$list) {
				$xml_buff = "<root />";
				FileHandler::writeFile($xml_file, $xml_buff);
				FileHandler::writeFile($php_file, '<?php if(!defined("__ZBXE__")) exit(); ?>');
				return $xml_file;
			}

			// 구해온 데이터가 하나라면 array로 바꾸어줌
			if(!is_array($list)) $list = array($list);

			// 루프를 돌면서 tree 구성
			foreach($list as $time_srl => $node) {
				$node->mid = $mid;
				$tree[$time_srl] = $node;
			}

			// xml 캐시 파일 생성 (xml캐시는 따로 동작하기에 session 지정을 해주어야 함)
			$xml_header_buff = '';
			$xml_body_buff = $this->getXmlTree($tree[0], $tree, $module_info->site_srl, $xml_header_buff);
			$xml_buff = sprintf(
				'<?php '.
				'define(\'__ZBXE__\', true); '.
				'require_once(\'../../../config/config.inc.php\'); '.
				'$oContext = &Context::getInstance(); '.
				'$oContext->init(); '.
				'header("Content-Type: text/xml; charset=UTF-8"); '.
				'header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); '.
				'header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); '.
				'header("Cache-Control: no-store, no-cache, must-revalidate"); '.
				'header("Cache-Control: post-check=0, pre-check=0", false); '.
				'header("Pragma: no-cache"); '.
				'%s '.
				'$oContext->close();'.
				'?>'.
				'<root>%s</root>', 
				$xml_header_buff,
				$xml_body_buff
			);

			// php 캐시 파일 생성
			$php_output = $this->getPhpCacheCode($tree[0], $tree, $module_info->site_srl, $php_header_buff);
			$php_buff = sprintf(
				'<?php '.
				'if(!defined("__ZBXE__")) exit(); '.
				'%s; '.
				'$menu->list = array(%s); '.
				'?>', 
				$php_header_buff,
				$php_output['buff']
			);

			// 파일 저장
			FileHandler::writeFile($xml_file, $xml_buff);
			FileHandler::writeFile($php_file, $php_buff);
			return $xml_file;
		}

		/**
		 * @brief array로 정렬된 노드들을 parent_srl을 참조하면서 recursive하게 돌면서 xml 데이터 생성
		 * 메뉴 xml파일은 node라는 tag가 중첩으로 사용되며 이 xml doc으로 관리자 페이지에서 메뉴를 구성해줌\n
		 * (tree_menu.js 에서 xml파일을 바로 읽고 tree menu를 구현)
		 **/
		function getXmlTree($source_node, $tree, &$xml_header_buff) {
			if(!$source_node) return;

			foreach($source_node as $time_srl => $node) {
				$child_buff = "";

				// 자식 노드의 데이터 가져옴
				if($time_srl && $tree[$time_srl]) $child_buff = $this->getXmlTree($tree[$time_srl], $tree, $xml_header_buff);

				// 변수 정리
				$mid = $node->mid;
				$module_srl = $node->module_srl;
				$time = $node->time;
				$attribute = sprintf(
						'mid="%s" module_srl="%d" node_srl="%d" time_srl="%d" text="<?php echo $_titles[%d]:"")?>" url="%s"',
						$mid,
						$module_srl,
						$time_srl,
						$time_srl,
						$time_srl,
						getUrl('','mid',$node->mid,'time',$time_srl)
				);

				if($child_buff) $buff .= sprintf('<node %s>%s</node>', $attribute, $child_buff);
				else $buff .=  sprintf('<node %s />', $attribute);
			}
			return $buff;
		}

		/**
		 * @brief array로 정렬된 노드들을 php code로 변경하여 return
		 * 메뉴에서 메뉴를 tpl에 사용시 xml데이터를 사용할 수도 있지만 별도의 javascript 사용이 필요하기에
		 * php로 된 캐시파일을 만들어서 db이용없이 바로 메뉴 정보를 구할 수 있도록 한다
		 * 이 캐시는 ModuleHandler::displayContent() 에서 include하여 Context::set() 한다
		 **/
		function getPhpCacheCode($source_node, $tree, &$php_header_buff) {
			$output = array("buff"=>"", "time_srl_list"=>array());
			if(!$source_node) return $output;

			// 루프를 돌면서 1차 배열로 정리하고 include할 수 있는 php script 코드를 생성
			foreach($source_node as $time_srl => $node) {

				// 자식 노드가 있으면 자식 노드의 데이터를 먼저 얻어옴 
				if($time_srl&&$tree[$time_srl]) $child_output = $this->getPhpCacheCode($tree[$time_srl], $tree, $php_header_buff);
				else $child_output = array("buff"=>"", "time_srl_list"=>array());

				// 현재 노드의 url값이 공란이 아니라면 category_srl_list 배열값에 입력
				$child_output['time_srl_list'][] = $node->time_srl;
				$output['time_srl_list'] = array_merge($output['time_srl_list'], $child_output['time_srl_list']);

				// 변수 정리
				$selected = '"'.implode('","',$child_output['time_srl_list']).'"';
				$child_buff = $child_output['buff'];

				$time = $node->time;

				// 속성을 생성한다 ( time_srl_list를 이용해서 선택된 메뉴의 노드에 속하는지를 검사한다. 꽁수지만 빠르고 강력하다고 생각;;)
				$attribute = sprintf(
					'"mid" => "%s", "module_srl" => "%d","node_srl"=>"%s","time_srl"=>"%s","text"=>$_titles[%d], 
					"list"=>array(%s)',
					$node->mid,
					$node->module_srl,
					$node->time_srl,
					$node->time_srl,
					$node->time_srl,
					$child_buff
				);
				
				// buff 데이터를 생성한다
				$output['buff'] .=  sprintf('%s=>array(%s),', $node->time_srl, $attribute);
			}
			return $output;
		}
	}
?>
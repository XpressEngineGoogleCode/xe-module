<?php
/**
 * @class  adController
 * @author 퍼니엑스이 (admin@funnyxe.com)
 * @brief  ad 모듈의 controller class
 **/

class adController extends ad
{

	/**
	 * @brief 초기화
	 **/
	function init()
	{
		$oAdModel = &getModel('ad');

		if(!$this->module_info->ad_type)
		{
			$this->module_info->ad_type = 'text';
		}
		if(!$this->module_info->ad_time_range)
		{
			$this->module_info->ad_time_range = $oAdModel->getDefaultAdTimeRange();
		}

		$this->ad_type = $this->module_info->ad_type;
		$this->ad_time_range = $this->module_info->ad_time_range;
		$this->ad_mime = $oAdModel->getAdMimeByAdType($this->ad_type);
		$this->use_time = ($this->module_info->use_time == 'Y') ? true : false;
		$this->each_highlight_point = ($this->module_info->each_highlight_point == 'Y') ? true : false;
		$this->point->register = (int)$this->module_info->ad_point;
		$this->point->highlight = (int)$this->module_info->highlight_point;
	}

	/**
	 * @brief 광고 등록
	 **/
	function procAdRegister()
	{
		// 권한 확인
		if(!$this->grant->register_ad) return new Object(-1, 'msg_not_permitted');

		// 로그인 정보 구함
		$logged_info = Context::get('logged_info');

		// 입력 받은 항목 검사
		$obj = Context::getRequestVars();
		switch($this->ad_type)
		{
			case 'text':
				$obj->ad_type = 'linead';
				break;
			case 'image':
			case 'movie':
			case 'flash':
				$obj->ad_type = 'banner';
				$banner_url = removeHackTag(trim(Context::get('banner_' . $this->ad_type)));
				break;
		}

		$obj->module_srl = $this->module_srl;
		if($obj->is_notice !='Y' || !$this->grant->manager) $obj->is_notice = 'N';

		// 광고 모듈의 model 객체 생성
		$oAdModel = &getModel('ad');

			// 관리자가 아니라면
			if(!$this->grant->manager && $obj->is_notice != 'Y')
			{
				// 등록 제한 확인 (공지 제외)
				$ad_limit = (int)$tihs->module_info->ad_limit;
				$daily_limit = (int)$this->module_info->daily_limit;

				// 전체 등록 제한
				if($ad_limit)
				{
					$args->member_srl = $logged_info->member_srl;
					$args->is_notice = 'N';
					$count = $oAdModel->getAdCount($this->module_srl, $args);

					$m = $oAdModel->getMessageCode('ad_limit_over');
					if($count>$ad_limit) return $this->oAdModel->returnMessage(-1, sprintf($m, $ad_limit));
				}

				// 하루 등록 제한
				if($daily_limit) {
					$args->start_date = date('YmdHis', mktime(0, 0, 0));
					$args->member_srl = $logged_info->member_srl;
					$args->is_notice = 'N';
					$count = $oAdModel->getAdCount($this->module_srl, $args);

					$m = $oAdModel->getMessageCode('daily_limit_over');
					if($count>$daily_limit) return $this->oAdModel->returnMessage(-1, sprintf($m, $daily_limit));
				}
			}

			// 배너 용도로 사용 시의 처리
			if(!$obj->ad_content) {
				$obj->ad_content = trim($obj->site_name);
				$obj->used_style = $obj->used_style?'|@|site_'.$banner_url:'site_'.$banner_url;

				// 배너 URL이 없으면 에러
				if(!$banner_url) return new Object(-1, 'msg_invalid_request');

				// 배너 URL의 header를 요청
				$request = $oAdModel->getRemoteResourceHeader($banner_url);

				// 접속이 불가능하거나 유효하지 않은 주소라면 에러
				if(!$request) return $oAdModel->returnMessage(-1, 'invalid_banner_'.$this->ad_type);

				// Content-Type이 맞지 않으면 에러
				if(!in_array($request['header']['content-type'],$this->ad_mime)) return $oAdModel->returnMessage(-1, 'isnot_banner_'.$this->ad_type);
			}

			// URL에 입력된 주소를 걸러냄 (최고 관리자가 아니라면 문제가 될만한 문자를 걸러냄) 
			if($logged_info->is_admin != 'Y') $obj->url = removeHackTag($obj->url);

			// 광고 내용이 없으면 에러
			settype($obj->ad_content, 'string');
			if($obj->ad_content == '') return new Object(-1, 'msg_invalid_request');

			// 기본값 지정
			$obj->title = $obj->ad_content;
			$obj->content = $obj->ad_content;
			$obj->allow_comment = 'N';
			$obj->lock_comment = 'Y';
			if($obj->url && !preg_match('/^([a-z]+):\/\//i',$obj->url)) $obj->url = 'http://'.$obj->url;
			if(!in_array($obj->url_target,array('_self','_blank'))) $obj->url_target = '_blank';

			// 광고 강조 권한이 없으면 각종 옵션을 해제
			if(!$this->grant->highlight_ad) {
				unset($obj->ad_color);
				unset($obj->ad_bgcolor);
				unset($obj->used_style);
			}

			// 사용된 옵션을 배열로 변환
			$used_style = $obj->used_style?explode('|@|',$obj->used_style):array();

			// 글자색과 배경색이 같으면 에러
			if($obj->ad_color && $obj->ad_bgcolor && $obj->ad_color == $obj->ad_bgcolor) return $oAdModel->returnMessage(-1, 'invalid_color');

			// 강조 포인트 계산
			if($obj->is_notice != 'Y' && ($obj->used_style || $obj->ad_color || $obj->ad_bgcolor)) {
				if($this->each_highlight_point) {
					if(count($used_style)) $highlight_point += count($used_style) * $this->point->highlight;
					if($obj->ad_color) $highlight_point += $this->point->highlight;
					if($obj->ad_bgcolor) $highlight_point += $this->point->highlight_point;
				} else {
					$highlight_point = $this->point->highlight;
				}
			}

			// 지정된 글자색이 있으면 배열에 저장
			if($obj->ad_color) {
				$used_style[] = 'text_'.$obj->ad_color;
				unset($obj->ad_color);
			}

			// 지정된 배경색이 있으면 배열에 저장
			if($obj->ad_bgcolor) {
				$used_style[] = 'bg_'.$obj->ad_bgcolor;
				unset($obj->ad_bgcolor);
			}

			if(is_array($used_style)) $obj->used_style = join('|@|',$used_style);
			else $obj->used_style = '';

			// 포인트 모듈의 model / controller 객체 생성
			$oPointModel = &getModel('point');
			$oPointController = &getController('point');

			// 포인트 확인 (공지 제외)
			if($obj->is_notice !='Y') {
				$ad_point = $this->point->register;
				// 광고 시간을 사용할 경우의 처리
				if($this->module_info->ad_point_rate && $this->use_time) {
					$ad_point *= $obj->ad_time / $this->module_info->ad_point_rate;
					$ad_point += $highlight_point;
				}

				// 광고 소모 포인트가 0보다 크면 포인트 확인
				if($ad_point>0) {
					$prev_point = $oPointModel->getPoint($logged_info->member_srl);
					if($ad_point > $prev_point) return $oAdModel->returnMessage(-1, 'not_enough_point');
				}
			}

			// 광고 시간의 유효성을 확인 (Console 플러그인에 의한 조작 방지)
			if($this->use_time) {
				$obj->ad_time = (int)$obj->ad_time;
				$ad_time_range = explode(',',$this->ad_time_range);
				$ad_time_range[] = -1;

				// 광고 시간이 무제한인데 관리자 권한이나 무제한 광고 권한이 없으면 에러
				if($obj->ad_time == -1 && (!$this->grant->unlimited_ad && !$this->grant->manager)) return $oAdModel->returnMessage(-1, 'invalid_ad_time');
				if(!$obj->ad_time || !in_array($obj->ad_time, $ad_time_range)) return $oAdModel->returnMessage(-1, 'invalid_ad_time');
			}

			// 문서 모듈의 controller 객체 생성
			$oDocumentController = &getController('document');

			// 광고 존재 여부 확인
			$oAd = $oAdModel->getAd($obj->document_srl, $this->grant->manager);

			// 이미 존재하면 에러
			if($oAd->isExists() && $oAd->document_srl == $obj->document_srl) {
				return new Object(-1, 'msg_invalid_request');
			} else {
				// 문서 등록
				$output = $oDocumentController->insertDocument($obj);
				$msg_code = 'success_registed';
				$obj->document_srl = $output->get('document_srl');

				// 오류 발생시 멈춤
				if(!$output->toBool()) return $output;

				// 광고 등록
				$ad->module_srl = $obj->module_srl;
				$ad->document_srl = $obj->document_srl;
				$ad->ad_time = $obj->ad_time;
				$ad->ad_type = $obj->ad_type;
				$ad->url = $obj->url;
				$ad->url_target = $obj->url_target;
				$ad->style = $obj->used_style;
				$ad->start_date = date('YmdHis');
				$output = $this->insertAd($ad);
				if(!$output->toBool()) return $output;

				// 포인트 차감 (공지 제외)
				if($ad_point>0 && $obj->is_notice !='Y') $oPointController->setPoint($logged_info->member_srl, $ad_point, 'minus');
			}

			// 결과 반환
			$this->add('mid', Context::get('mid'));

			// 성공 메시지
			$this->setMessage($msg_code);
		}

		/**
		 * @brief 광고 수정
		 */
		function procAdModify() {
			$obj = Context::getRequestVars();

			// 광고 모듈의 model 객체 생성
			$oAdModel = &getModel('ad');

			// 문서 모듈의 controller 객체 생성
			$oDocumentController = &getController('document');

			// 광고 존재 여부 확인
			$oAd = $oAdModel->getAd($obj->document_srl, $this->grant->manager);
			if(!$oAd->isExists() || $oAd->document_srl != $obj->document_srl) return $oAdModel->returnMessage(-1, 'not_exists_ad');

			// 문서 업데이트
			$oDocumentController->updateDocument($oAd, $obj);

			// 광고 업데이트
			$this->updateAd($obj);

			$this->add('mid', Context::get('mid'));

			$this->setMessage('success_updated');
		}


		/**
		 * @brief 광고 삭제
		 */
		function procAdDelete() {
			$document_srl = Context::get('document_srl');

			// 광고 삭제
			$this->deleteAd($document_srl);

			$this->add('mid', Context::get('mid'));

			$this->setMessage('success_deleted');
		}
		/**
		 * @brief insert Ad
		 **/
		function insertAd($args) {
			// check $args
			if(!$args || !$args->document_srl) return;
			$args->ad_time = (int)$args->ad_time;
			if($this->module_info->use_time == 'Y' && !$args->ad_time) return $oAdModel->returnMessage(-1, 'input_ad_time');
			if($this->module_info->use_time != 'Y') $args->ad_time = -1;

			// module srl
			$module_srl = $args->module_srl;
			unset($args->module_srl);

			// create model object of ad module
			$oAdModel = &getModel('ad');

			// set end date
			if($this->module_info->use_time == 'Y' && $args->ad_time != -1) $args->end_date = date('YmdHis',$oAdModel->dateAdd('h',$args->ad_time,strtotime($args->start_date)));
			else $args->end_date = -1;

			// set default value
			if($args->url && !preg_match('/^([a-z]+):\/\//i',$args->url)) $args->url = 'http://'.$args->url;
			if(!in_array($args->url_target,array('_self','_target'))) $args->url_target = '_blank';
			if(!$args->start_date) $args->start_date = date('YmdHis');

			// execute query
			$obj->document_srl = $args->document_srl;
			$obj->module_srl = $module_srl;
			$obj->start_date = $args->start_date;
			$obj->end_date = $args->end_date;
			$obj->ad_time = $args->end_date==-1?-1:$args->end_date - $args->start_date;
			$obj->ad_type = $args->ad_type;
			$obj->url = $args->url;
			$obj->url_target = $args->url_target;
			$obj->style = $args->style;
			$output = executeQuery('ad.insertAd', $obj);

			return $output;
		}

		/**
		 * @brief 광고 수정
		 **/
		function updateAd($args) {
			// check $args
			if(!$args || !$args->document_srl) return;

			// 광고 모듈의 model 객체 생성
			$oAdModel = &getModel('ad');

			// get ad info
			$oAd = $oAdModel->getAd($args->document_srl, $this->grant->manager);
			if(!$oAd->isExists()) return new Object(-1, 'msg_invalid_request');

			// set default value
			if($args->url && !preg_match('/^([a-z]+):\/\//i',$args->url)) $args->url = 'http://'.$args->url;
			if(!in_array($args->url_target,array('_self','_target'))) $args->url_target = '_blank';

			// execute query
			$obj->document_srl = $args->document_srl;
			$obj->url = $args->url;
			$obj->url_target = $args->url_target;
			$output = executeQuery('ad.updateAd', $obj);

			return $output;
		}

		/**
		 * @brief 공지 등록
		 **/
		function insertNotice($args) {
			if(!$args || !$args->document_srl) return;

			// 광고 모듈의 model 객체 생성
			$oAdModel = &getModel('ad');

			$oAd = $oAdModel->getAd($args->document_srl, $this->grant->manager);

			// check $args
			$args->ad_time = (int)$args->ad_time;
			$args->start_date = trim($args->start_date);
			$args->start_hour = (int)trim($args->start_hour);
			$args->start_minute = (int)trim($args->start_minute);
			$args->start_second = (int)trim($args->start_second);
			if(!$args->ad_time) $args->ad_time = -1;
			if(!$args->start_hour) $args->start_hour = '00';
			if(!$args->start_minute) $args->start_minute = '00';
			if(!$args->start_second) $args->start_second = '00';
			if(strlen($args->start_hour)<2) $args->start_hour .= '0';
			if(strlen($args->start_minute)<2) $args->start_minute .= '0';
			if(strlen($args->start_second)<2) $args->start_second .= '0';

			// set end date
			if(!$args->start_date) {
				$args->start_date = date('YmdHis');
				if($args->ad_time != -1) $args->end_date = $oAdModel->dateAdd('h',$args->ad_time,date('YmdHis'));
				else $args->end_date = -1;
			} else {
				$date = $args->start_date.$args->start_hour.$args->start_minute.$args->start_second;
				$args->end_date = $oAdModel->dateAdd('h',$args->ad_time,$date);
			}

			// set default value
			if($args->url && !preg_match('/^([a-z]+):\/\//i',$args->url)) $args->url = 'http://'.$args->url;
			if(!in_array($args->url_target,array('_self','_target'))) $args->url_target = '_blank';

			// executeQuery
			$obj->document_srl = $args->document_srl;
			$obj->start_date = $date;
			$obj->end_date = $args->end_date;
			$obj->url = $args->url;
			$obj->url_target = $args->url_target;
			$obj->style = $args->style;
			$output = executeQuery('ad.insertAd', $obj);

			return $output;
		}

		/**
		 * @brief 광고 삭제
		 **/
		function deleteAd($document_srl, $delete_document = true) {
			if(!$document_srl) return;

			$obj->document_srl = $document_srl;

			// create model class of ad module
			$oAdModel = &getModel('ad');

			// create model class of document model
			$oDocumentModel = &getModel('document');

			// 광고 객체 구함
			$oAd = $oAdModel->getAd($document_srl, $this->grant->manager);

			// 광고가 존재하지 않으면 에러
			if(!$oAd->isExists()) return $oAdModel->returnMessage(-1, 'not_exists_ad');

			// 문서 삭제
			if($delete_document) {
				$oDocumentController = &getController('document');
				$oDocumentController->deleteDocument($document_srl);
			}

			// 광고 삭제
			$output = executeQuery('ad.deleteAd', $obj);

			return $output;
		}

		/**
		 * @brief 모듈이 삭제될때 등록된 모든 광고를 삭제하는 trigger
		 * @return new Object()
		 **/
		function triggerDeleteModuleAds(&$obj) {
			$module_srl = $obj->module_srl;
			if(!$module_srl) return new Object();

			// 광고 모듈이 아니면 무시
			$oModuleModel = &getModel('module');
			$oModuleInfo = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			if(!$oModuleInfo->module != 'ad') return new Object();

			// 광고 모듈의 model 객체 생성
			$oAdModel = &getModel('ad');

			// 등록된 광고 목록 구하기 (다중 테이블 접근을 지원하지 않아 두 번 query를 날려야 함 -_-;;)
			$args->module_srl = $module_srl;
			$args->select_document_srl = 'Y';
			$output = $oAdModel->getAdList($args);
			if(!$output->toBool()) return $output;

			unset($args);

			if(count($output->data)) {
				foreach($output->data as $key => $val) $documents[] = $val->document_srl;

				// 광고 삭제
				$args->document_srls = join(',',$documents);
				$output = executeQuery('ad.deleteModuleAds',$args);
				if(!$output->toBool()) return $output;
			}

			return new Object();
		}
	}
?>

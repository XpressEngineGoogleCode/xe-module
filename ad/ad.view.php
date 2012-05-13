<?php
	/**
	 * @class  adView
	 * @author SMaker (dowon2308@paran.com)
	 * @brief  광고 모듈의 view class
	 **/

	class adView extends ad {

		/**
		 * @brief 초기화
		 **/
		function init() {
			if(!$this->module_srl && $this->act != 'rss') $this->stop('msg_invalid_request');

			if(!$this->module_info->ad_type)
				$this->module_info->ad_type = 'text';

			$this->ad_type = $this->module_info->ad_type;

			// 로그인 정보 구하기
			$logged_info = Context::get('logged_info');

			// 광고 유형이 배너이고 최고 관리자가 아닌 경우 광고 등록 권한 뺏기
			if($this->ad_type != 'text' && $logged_info->is_admin != 'Y') {
				$this->grant->register_ad = false;
				Context::set('grant', $this->grant);
			}

			// 광고 모듈의 model 객체 생성
			$this->oAdModel = $oAdModel = &getModel('ad');
			Context::set('oAdModel',$oAdModel);

			// 스킨 경로 지정h
			$template_path = sprintf('%sskins/%s/',$this->module_path, $this->module_info->skin?$this->module_info->skin:'xe_default');
			if(!is_dir($template_path)) {
				$this->module_info->skin = 'xe_default';
				$template_path = sprintf('%sskins/%s/',$this->module_path, $this->module_info->skin);
			}
			$this->setTemplatePath($template_path);

			// 공용 JS 파일 로드
			Context::addJsFile($this->module_path.'tpl/js/ad.js');

			// 광고 시간 범위를 구해서 set
			if($this->module_info->use_time == 'Y') {
				$ad_time_range = $oAdModel->getAdTimeRange($this->module_info);
				Context::set('ad_time_range', $ad_time_range);
			}
		}

		/**
		 * @brief 광고 컨텐츠 목록
		 **/
		function dispAdContent() {
			// 광고 모듈의 model 객체 생성
			$oAdModel = &getModel('ad');

			// 목록을 구하기 위한 대상 모듈에 대한 옵션 설정
			$args->module_srl = $this->module_srl;

			// 지정된 정렬값이 없다면 정렬 값을 지정함
			$args->order_type = 'asc';
			$args->end_date = date('YmdHis');
			$args->with_page = true;

			// 일반 글을 구해서 context set
			$output = $oAdModel->getAdList($args);
			Context::set('ad_list', $output->data);
			Context::set('total_count', $output->total_count);
			Context::set('total_page', $output->total_page);
			Context::set('page', $output->page);
			Context::set('page_navigation', $output->page_navigation);

			$this->setTemplateFile('content');
		}

		/**
		 * @brief 광고 컨텐츠 보기
		 */
		function dispAdContentView() {
			$document_srl = Context::get('document_srl');
			if(!$document_srl) return $this->AdContentViewFailed();

			$oAdModel = &getModel('ad');
			$args->query_id = 'ad.getAd';
			$oAd = $oAdModel->getAd($document_srl, $args);
			if(!$oAd->isExists() || !$oAd->getUrl()) return $this->AdContentViewFailed();

			if($this->ad_type == 'banner') {
				$logged_info = Context::get('logged_info');

				$oDB = &DB::getInstance();
				$oDB->begin();

				// 확장 변수 구함
				$extra_vars = $oAd->getExtraVars();

				// 게시자에게 포인트 지급
				if($extra_vars->point) {
					$member_srl = $oAd->get('member_srl');

					// point 모듈의 controller 객체 생성
					$oPointController = &getController('point');
					$oPointController->setPoint($member_srl, $extra_vars->point, 'add');
				}

				// session에 정보로 조회수를 증가하였다고 생각하면 패스
				if($_SESSION['readed_ad'][$document_srl]) return false;

				 // 광고 게시자 ip와 현재 접속자의 ip가 동일하면 패스
				if($oAd->get('ipaddress') == $_SERVER['REMOTE_ADDR']) {
					$_SESSION['readed_ad'][$document_srl] = true;
					return false;
				}

				// 광고 게시자와 로그인 한 회원이 일치하면 세션 등록하고 패스
				if($logged_info->member_srl == $member_srl) {
					$_SESSION['readed_ad'][$document_srl] = true;
					return false;
				}

				// 클릭수 증가
				$args->document_srl = $document_srl;
				$output = executeQuery('ad.updateClickCount', $args);

				// 문제가 있을 경우 되돌림 (rollback)
				if(!$output->toBool()) {
					$oDB->rollback();
					return $output;
				}

				// 커밋
				$oDB->commit();

				// 세션 등록
				$_SESSION['readed_ad'][$document_srl] = true;

				// 해당 URL로 이동
				Context::close();
				header('location:'.$oAd->getUrl());
				exit;
			} else {
				return $this->AdContentViewFailed();
			}
		}

		function AdContentViewFailed() {
			Context::set('act','dispAdContent');
			return $this->dispAdContent();
		}

		/**
		 * @brief 광고 등록
		 **/
		function dispAdRegister() {
			// 광고 모듈의 model 객체 생성
			$oAdModel = &getModel('ad');

			// Js Filter 적용
			Context::AddJsFilter($this->module_path.'tpl/filter', 'register_ad.xml');

			$this->setTemplateFile('register');
		}

		/**
		 * @brief 내가 등록한 광고
		 **/
		function dispAdList() {
			// 광고 모듈의 model 객체 생성
			$oAdModel = &getModel('ad');

			// 로그인 정보 구함
			$logged_info = Context::get('logged_info');

			// 로그인 상태가 아니라면 에러
			if(!$logged_info) return $this->oAdModel->returnMessage(-1, 'please_login');

			// 목록을 구하기 위한 각종 옵션 설정
			$args->module_srl = $this->module_srl; //<< 모듈 번호
			$args->member_srl = $logged_info->member_srl; //<< 회원 번호
			$args->sort_index = 'documents.list_order'; //<< 정렬 대상
			$args->order_type = 'asc'; //<< 정렬 방법
			$args->with_page = true;
			$args->select_all_ad = true;

			// 내가 등록한 광고를 구해서 context set
			$output = $oAdModel->getAdList($args);
			Context::set('ad_list', $output->data);
			Context::set('total_count', $output->total_count);
			Context::set('total_page', $output->total_page);
			Context::set('page', $output->page);
			Context::set('page_navigation', $output->page_navigation);

			$this->setTemplateFile('list');
		}

		/**
		 * @brief 광고 삭제
		 **/
		function dispAdDelete() {
			// 로그인 정보 구함
			$logged_info = Context::get('logged_info');

			// 로그인 상태가 아니라면 에러
			if(!$logged_info) return $this->oAdModel->returnMessage(-1, 'please_login');

			// 요청 받은 광고 번호를 구함
			$document_srl = Context::get('document_srl');

			// 등록된 광고 내용을 구함
			$oAd = $this->oAdModel->getAd($document_srl);
			if(!$oAd->isExists()) return $this->oAdModel->returnMessage(-1, 'not_exists_ad');
			if(!$oAd->isGranted()) return new Object(-1, 'msg_not_permitted');

			Context::set('oAd', $oAd);

			$this->setTemplateFile('delete');
		}
		
		/**
		 * @brief 광고 수정 (작업중)
		 **/
		function dispAdModify() {
			// 로그인 정보 구함
			$logged_info = Context::get('logged_info');

			// 로그인 상태가 아니라면 에러
			if(!$logged_info) return $this->oAdModel->returnMessage(-1, 'please_login');

			// 요청 받은 광고 번호를 구함
			$document_srl = Context::get('document_srl');

			// 등록된 광고 내용을 구함
			$oAd = $this->oAdModel->getAd($document_srl);
			if(!$oAd->isExists()) return $this->oAdModel->returnMessage(-1, 'not_exists_ad');
			if(!$oAd->isGranted()) return new Object(-1, 'msg_not_permitted');

			Context::set('oAd', $oAd);

			$this->setTemplateFile('modify');
		}

		function rss() {
			$oRssView = &getView('rss');

			// module srl이 없다면 잠금 상태로 표시
			if(!$this->module_srl) {
				return $oRssView->dispError();
			}

			// 광고 목록 구함
			$args->module_srl = $this->module_srl;
			$args->list_count = 10;
			$output = $this->oAdModel->getAdList($args);
			$document_list = $output->data;

			// rss 출력
			$oRssView->rss($document_list, 'RSS Feed', '');
			$this->setTemplatePath($oRssView->getTemplatePath());
			$this->setTemplateFile($oRssView->getTemplateFile());
		}
	}
?>

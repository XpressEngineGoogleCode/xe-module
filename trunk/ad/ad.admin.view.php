<?php
	/**
	 * @class  AdAdminView
	 * @author 퍼니엑스이 (admin@funnyxe.com)
	 * @brief  광고 모듈의 admin view 객체
	 **/
	 
	 class adAdminView extends ad {

		/**
		 * @brief 초기화
		 **/
		function init() {
			$this->oAdModel = $oAdModel = &getModel('ad');
			$this->oAdAdminModel = $oAdAdminModel = &getAdminModel('ad');
			$this->oModuleModel = $oModuleModel = &getModel('module');

			// 로그인한 회원의 권한 확인
			$oMemberModel = &getModel('member');
			$logged_info = $oMemberModel->getLoggedInfo();

			// 최고 관리자가 아니라면
			if($logged_info->is_admin!='Y') return $this->stop($oAdModel->getMessageCode('is_not_administrator'));

			// 요청된 module_srl이 모듈의 module_srl과 다르다면
			$module_srl = Context::get('module_srl');
			if(!$module_srl && $this->module_srl) {
				$module_srl = $this->module_srl;
				Context::set('module_srl', $module_srl);
			}

			// 요청받은 모듈의 정보를 구함
			if($module_srl) {
				$module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
				if(!$module_info) {
					Context::set('module_srl','');
				} else {
					ModuleModel::syncModuleToSite($module_info);
					$this->module_info = $module_info;
					Context::set('module_info',$module_info);
				}
			}

			// 관리자 페이지에서 접속 시의 처리
			switch($this->module_info->module) {
				case 'admin':
				   // URL 구함
					$url = getNotEncodedUrl('module','ad');

					// 새 주소로 이동
					Context::close();
					header('location:'.$url);
					exit;
			}

			// 기본 설정 구함
			$config = $this->config = $oAdModel->getConfig();
			Context::set('config', $config);

			// 모듈 버전 구함
			Context::set('module_version',$oAdModel->getVersion());

			// 템플릿 경로 지정
			$template_path = sprintf('%stpl/',$this->module_path);
			$this->setTemplatePath($template_path);

			// 공용 css / js 파일 로드
			Context::addJsFile($this->module_path.'tpl/js/ad_admin.js');

			// 레이아웃 지정
			if(Context::get('act') == 'dispAdAdminFindUserId') {
				$this->setLayoutFile('popup_layout');
			} else {
				// 모듈 분류 구함
				$module_category = $oModuleModel->getModuleCategories();
				Context::set('module_category', $module_category);

				// 레이아웃 변경
				$this->setLayoutPath($this->getTemplatePath());
				$this->setLayoutFile('DashboardLayout');

				// 대시보드 메뉴 구함
				$this->dashboard_menus = $this->getDashboardMenus();
				$this->module_menus = $this->getModuleMenus();
				Context::set('dashboard_menus', $this->dashboard_menus);
				Context::set('module_menus', $this->module_menus);
			}

			// 브라우저 제목 지정
			Context::setBrowserTitle(Context::getLang('ad_module'));
		}

		/**
		 * @brief 대시보드 메인
		 */
		function dispAdAdminDashBoard() {
			// if exists old linead module info, execute update
			/*if(!file_exists(_XE_PATH_.'files/cache/ad/updated')) {
				if($oAdModel->getOldConfig()) $need_update = true;
			}*/

			if($this->config->display_today_ad == 'Y') {
				// 목록을 구하기 위한 목록 수/ 페이지 목록 수에 대한 옵션 설정
				$args->list_count = 10;
				$args->page_count = 1;

				// 오늘 등록된 광고만 뽑아오기
				$args->start_regdate = date('Ymd').'000000';

				// 검색과 정렬을 위한 변수 설정
				$args->sort_index = 'list_order';
				$args->order_type = 'asc';

				// 공지를 제외한
				$args->is_notice = 'N';

				// 모든 광고 출력
				$args->select_all_ad = 'Y';

				// 오늘 등록된 광고 목록 구함
				$output = $this->oAdModel->getAdList($args);
				Context::set('today_ad', $output->data);
				Context::set('total_count', $output->total_count);
				Context::set('total_page', $output->total_page);
				Context::set('page', $output->page);
				Context::set('page_navigation', $output->page_navigation);
			}

			// 브라우저 제목 지정
			$this->setPageTitle(Context::getLang('_dashboard'));

			// 템플릿 파일 지정
			$this->setTemplateFile('_dashboard');
		}

		/**
		 * @brief 대시보드 > 기본 설정
		 */
		function dispAdAdminConfig() {
			// Javascript Filter 적용
			Context::addJsFilter($this->module_path.'tpl/filter/','insert_config.xml');

			// 브라우저 제목 지정
			$this->setPageTitle($this->dashboard_menus[Context::get('act')]);

			// 템플릿 파일 지정
			$this->setTemplateFile('Config');
		}

		function dispAdAdminUpdate() {
			$command = Context::get('command');
			switch($command) {
				case 'migration':
					$title = Context::getLang('cmd_update');
					$template_file = 'Update';
					break;
				case 'delete':
					$title = Context::getLang('cmd_delete_old_config');
					$template_file = 'DeleteOldConfig';
					break;
				default :
					return new Object(-1, 'msg_invalid_request');
			}
			$title = $command!='update'?'기존 설정 삭제':Context::getLang('cmd_update');

			// 브라우저 제목 지정
			Context::setBrowserTitle(Context::getBrowserTitle().' > '.$title);

			// 템플릿 파일 지정
			$this->setTemplateFile($template_file);
		}

		/**
		 * @brief 대시보드 > 광고 관리
		 **/
		function dispAdAdminList() {
			if(Context::get('is_total') != 'Y') $args->module_srl = $this->module_srl;

			// 목록을 구하기 위한 대상 모듈/ 페이지 수/ 목록 수/ 페이지 목록 수에 대한 옵션 설정
			$args->page = Context::get('page');
			$args->list_count = $module_info->list_count;
			$args->page_count = $module_info->page_count;

			// 검색과 정렬을 위한 변수 설정
			$args->search_target = Context::get('search_target');
			$args->search_keyword = Context::get('search_keyword');

			// 지정된 정렬값이 없다면 정렬 값을 지정함
			$args->sort_index = 'list_order';
			$args->order_type = 'asc';

			// 공지를 제외한
			$args->is_notice = 'N';

			// 모든 광고
			$args->select_all_ad = 'Y';

			// 일반 글을 구해서 context set
			$output = $this->oAdModel->getAdList($args);
			Context::set('ad_list', $output->data);
			Context::set('total_count', $output->total_count);
			Context::set('total_page', $output->total_page);
			Context::set('page', $output->page);
			Context::set('page_navigation', $output->page_navigation);

			// 템플릿에서 사용할 변수를 Context::set()
			if($this->module_srl) Context::set('module_srl',$this->module_srl);
			Context::set('module_info',$this->module_info);

			// 브라우저 제목 지정
			$this->setPageTitle($this->dashboard_menus[Context::get('act')]);

			// 템플릿 파일 지정
			$this->setTemplateFile('AdList');
		}

		/**
		 * @brief 광고 모듈 목록
		 **/
		function dispAdAdminModuleList() {
			// 등록된 ad 모듈을 불러와 세팅
			$args->sort_index = 'module_srl';
			$args->page = Context::get('page');
			$args->list_count = 20;
			$args->page_count = 10;
			$args->s_module_category_srl = Context::get('module_category_srl');
			$output = executeQueryArray('ad.getAdModuleList', $args);
			ModuleModel::syncModuleToSite($output->data);

			// 템플릿에 쓰기 위해서 context::set()
			Context::set('module_list', $output->data);
			Context::set('total_count', $output->total_count);
			Context::set('total_page', $output->total_page);
			Context::set('page', $output->page);
			Context::set('page_navigation', $output->page_navigation);

			// 브라우저 제목 지정
			$this->setPageTitle($this->dashboard_menus[Context::get('act')]);

			// 템플릿 파일 지정
			$this->setTemplateFile('ModuleList');
		}

	   /**
		 * @brief 선택된 광고 모듈의 정보 출력 (바로 정보 입력으로 변경)
		**/
		function dispAdAdminModuleInfo() {
			$this->dispAdAdminInsertModule();
		}

		/**
		 * @brief 광고 모듈 추가 폼 출력
		 **/
		function dispAdAdminInsertModule() {
			if(!in_array($this->module_info->module, array('admin', 'ad'))) return new Object(-1, 'msg_invalid_request');

			// 스킨 목록을 구해옴
			$skin_list = $this->oModuleModel->getSkins($this->module_path);
			Context::set('skin_list',$skin_list);

			// 레이아웃 목록을 구해옴
			$oLayoutModel = &getModel('layout');
			$layout_list = $oLayoutModel->getLayoutList();
			Context::set('layout_list', $layout_list);

			// Javascript Filter 적용
			Context::addJsFilter($this->module_path.'tpl/filter/','insert_module.xml');

			$module = $this->oAdModel->getLangCode('module', 'dashboard');

			// module_srl의 유무에 따른 브라우저 제목 지정
			if(Context::get('module_srl')) {
				$this->setPageTitle($module->setup);
			} else {
				$this->setPageTitle($module->make);
			}

			$ads = Context::getLang('ads');

			$script = '<script type="text/javascript">
			var msg_choose_color = \'%s\';
			var msg_choose_correct_color = \'%s\';
			var msg_already_inserted = \'%s\';
			var msg_cannot_delete_default_color = \'%s\';
			';
			if($this->module_info->use_time == 'Y' || $this->module_info->use_color == 'Y' || $this->module_info->use_bgcolor == 'Y') {
				$script .= 'jQuery(document).ready(function($){';
				if($this->module_info->use_time == 'Y') $script .= 'initAdTimeRange();';
				if($this->module_info->use_color == 'Y') $script .= 'initAdColorRange(document.getElementById(\'selColorRange\'));';
				if($this->module_info->use_bgcolor == 'Y') $script .= 'initAdBgColorRange();';
				$script .= '});';
			}
			$script .= '</script>';
			Context::addHtmlFooter(sprintf($script, $ads->msg->choose_color, $ads->msg->choose_correct_color, $ads->msg->already_inserted, $ads->msg->cannot_delete_default_color));

			// 템플릿 파일 지정
			$this->setTemplateFile('ModuleInsert');
		}

		/**
		 * @brief 광고 모듈 삭제 화면 출력
		 **/
		function dispAdAdminDeleteModule() {
			if(!Context::get('module_srl')) return $this->dispAdAdminModuleList();
			if(!in_array($this->module_info->module, array('admin', 'ad'))) return new Object(-1, 'msg_invalid_request');

			$module_info = Context::get('module_info');

			// 광고 갯수 구함
			$oAdModel = &getModel('ad');
			$ad_count = $oAdModel->getAdCount($module_info->module_srl);
			$module_info->ad_count = $ad_count;

			Context::set('module_info',$module_info);

			// Javascript Filter 적용
			Context::addJsFilter($this->module_path.'tpl/filter/','delete_module.xml');

			$module = $this->oAdModel->getLangCode('module', 'dashboard');

			// 브라우저 제목 지정
			$this->setPageTitle($module->delete);

			// 템플릿 파일 지정
			$this->setTemplateFile('ModuleDelete');
		}

		/**
		 * @brief 광고 시간 범위 관리
		 */
		function dispAdAdminModuleAdTimeRange() {
			if(!$this->module_srl) return new Object(-1, 'msg_invalid_request');

			$grant_content = $this->oAdAdminModel->getAdTimeRangeHtml($this->module_srl);

			// 브라우저 제목 지정
			$this->setPageTitle($this->module_menus[Context::get('act')]);

			$this->setTemplateFile('ModuleAdTimeRange');
		}

		/**
		 * @brief 권한 관리 출력
		 **/
		function dispAdAdminModuleGrantInfo() {
			// 공통 모듈 권한 설정 페이지 호출
			$oModuleAdminModel = &getAdminModel('module');
			$grant_content = $oModuleAdminModel->getModuleGrantHTML($this->module_info->module_srl, $this->xml_info->grant);
			Context::set('grant_content', $grant_content);

			// 브라우저 제목 지정
			$this->setPageTitle($this->module_menus[Context::get('act')]);

			// 템플릿 파일 지정
			$this->setTemplateFile('ModuleGrant');
		}

		/**
		 * @brief 스킨 정보 보여줌
		 **/
		function dispAdAdminModuleSkinInfo() {
			// 공통 모듈 권한 설정 페이지 호출
			$oModuleAdminModel = &getAdminModel('module');
			$skin_content = $oModuleAdminModel->getModuleSkinHTML($this->module_info->module_srl);
			Context::set('skin_content', $skin_content);

			// 브라우저 제목 지정
			$this->setPageTitle($this->module_menus[Context::get('act')]);

			// 템플릿 파일 지정
			$this->setTemplateFile('SkinInfo');
		}

		/**
		 * @brief 공지 관리
		 **/
		function dispAdAdminNoticeList() {
			$oModuleModel = &getModel('module');
			$oAdModel = &getModel('ad');

			// 목록을 구하기 위한 페이지 수/ 목록 수/ 페이지 목록 수에 대한 옵션 설정
			$args->page = Context::get('page');
			$args->list_count = $module_info->list_count;
			$args->page_count = $module_info->page_count;

			// 검색과 정렬을 위한 변수 설정
			$args->search_target = Context::get('search_target');
			$args->search_keyword = Context::get('search_keyword');

			// 지정된 정렬값이 없다면 정렬 값을 지정함
			$args->sort_index = 'list_order';
			$args->order_type = 'asc';

			// 만약 검색어가 있으면 list_count를 search_list_count 로 이용
			if($args->search_keyword) $args->list_count = $this->search_list_count;

			$args->is_notice = 'Y';
			$args->with_page = true;
			$args->select_all_ad = 'Y';

			// 공지를 구해서 context set
			$output = $oAdModel->getAdList($args);
			Context::set('notice_list', $output->data);
			Context::set('total_count', $output->total_count);
			Context::set('total_page', $output->total_page);
			Context::set('page', $output->page);
			Context::set('page_navigation', $output->page_navigation);

			// 브라우저 제목 지정
			$this->setPageTitle($this->dashboard_menus[Context::get('act')]);

			// 템플릿 파일 지정
			$this->setTemplateFile('NoticeIndex');
		}

		/**
		 * @brief 공지 작성
		 **/
		function dispAdAdminNoticeWrite() {
			$module = $this->oAdModel->getLangCode('module', 'dashboard');
			$notice = $this->oAdModel->getLangCode('notice', 'dashboard');

			if(!Context::get('module_srl')) {
				// 등록된 광고 모듈을 구해옴
				$args->sort_index = 'module_srl';
				$args->page = Context::get('page');
				$args->list_count = 20;
				$args->page_count = 10;
				$args->s_module_category_srl = Context::get('module_category_srl');
				$output = executeQueryArray('ad.getAdModuleList', $args);
				ModuleModel::syncModuleToSite($output->data);

				// 템플릿에 쓰기 위해서 context::set
				Context::set('module_list', $output->data);
				Context::set('total_count', $output->total_count);
				Context::set('total_page', $output->total_page);
				Context::set('page', $output->page);
				Context::set('page_navigation', $output->page_navigation);

				// 브라우저 제목 지정
				$this->setPageTitle($notice->write.' > '.$module->select);
			} else {
				// module_srl로 mid 찾기
				$oModuleModel = &getModel('module');
				$module_info = $oModuleModel->getModuleInfoByModuleSrl((int)Context::get('module_srl'));

				// mid를 찾았다면 mid와 module_srl을 set, 찾지 못했다면 에러 출력
				if($module_info->mid) {
					Context::set('mid', $module_info->mid);
					Context::set('module_srl', $module_info->module_srl);
				} else {
					return $this->oAdModel->returnMessage(-1, 'module_is_not_exists');
				}

				// Js Filter 적용
				Context::addJsFilter($this->module_path.'tpl/filter/','insert_notice.xml');

				// 브라우저 제목 지정
				$this->setPageTitle($notice->write);
			}

			// 템플릿 파일 지정
			$this->setTemplateFile('NoticeWrite');
		}

		/**
		 * @brief 부가 기능 설정
		 **/
		function dispAdAdminPluginSetup() {
			// 부가 기능 목록을 구해옴
			$oAdModel = &getModel('ad');
			$plugin_list = $oAdModel->getPluginList(false, $site_srl);

			Context::set('plugin_list', $plugin_list);

			// 브라우저 제목 지정
			Context::setBrowserTitle(Context::getBrowserTitle().' > '.$this->dashboard_menus['PluginSetup']['title']);

			// 템플릿 파일 지정
			$this->setTemplateFile('PluginIndex');
		}

		/**
		 * @brief ID 찾기 팝업
		 **/
		function dispAdAdminFindUserId() {
			// 템플릿 파일 지정
			$this->setTemplateFile('FindUserID');
		}

		/**
		 * @brief 대시보드에서 사용되는 페이지 제목 제어 함수
		 */
		function setPageTitle($title) {
			$this->page_title = $title;

			// '광고 모듈 > 타이틀 이름' 형식으로 변경
			$browser_title = sprintf('%s > %s', Context::getLang('ad_module'), $title);
			Context::setBrowserTitle($browser_title);
		}

		/**
		 * @biref 현재 페이지 제목을 구하는 함수
		 */
		function getPageTitle($title) {
			return $this->page_title;
		}

		/**
		 * @biref 대시보드의 메뉴를 구하는 함수
		 */
		function getDashboardMenus() {
			return $this->oAdModel->getLangCode('dashboard_menus');
		}

		/**
		 * @biref 모듈의 탭 메뉴를 구하는 함수
		 */
		function getModuleMenus() {
			$module = $this->oAdModel->getLangCode('module', 'dashboard');
			return $module->tab_menus;
		}
	}
?>

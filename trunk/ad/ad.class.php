<?php
	/**
	 * @class  ad
	 * @author 퍼니엑스이 (admin@funnyxe.com)
	 * @brief  ad 모듈의 high class
	 **/

	class ad extends ModuleObject {
		// URL 열기 대상
		var $url_target = array('_blank','_self');

		// 파일 종류에 따른 Content-Type 정의
		var $image_mime = array('image/bmp','image/gif','image/jpeg','image/png');
		var $video_mime = array('video/mp4','video/mpeg','video/quicktime','video/x-flv','video/x-ms-wmv','video/x-msvideo');
		var $flash_mime = array('application/x-shockwave-flash');

		// 관리자 페이지에서 사용되는 검색 옵션
		var $search_option = array('content','user_id','member_srl','user_name','nick_name','is_notice','tags', 'regdate','ipaddress');

		/**
		 * @brief 광고 모듈 설치
		 * @return new Object
		 **/
		function moduleInstall() {
			$oModuleController = &getController('module');
			$oModuleController->insertTrigger('module.deleteModule', 'ad', 'controller', 'triggerDeleteModuleAds', 'before');

			return new Object();
		}

		/**
		 * @brief 광고 모듈 삭제
		 * @return new Object
		 */
		function moduleUninstall() {
			$oModuleModel = &getModel('module');
			$oModuleController = &getController('module');

			// 모듈 삭제
			$output = executeQueryArray('ad.getAllModule');
			if($output->data) {
				@set_time_limit(0);
				foreach($output->data as $module) {
					$oModuleController->deleteModule($module->module_srl);
				}
			}

			if($oModuleModel->getTrigger('module.deleteModule', 'ad', 'controller', 'triggerDeleteModuleAds', 'before'))
				$oModuleController->deleteTrigger('module.deleteModule', 'ad', 'controller' , 'triggerDeleteModuleAds', 'before');
			/* 트리거 삭제 끝 */

			return new Object();
		}

		/**
		 * @brief 업데이트가 필요한지 확인
		 **/
		function checkUpdate() {
			$oDB = &DB::getInstance();
			$oAdModel =  &getModel('ad');

			// 필드 존재 여부 확인
			if(!$oDB->isColumnExists('ad', 'module_srl')) return true;
			if(!$oDB->isColumnExists('ad', 'start_date')) return true;
			if(!$oDB->isColumnExists('ad', 'style')) return true;
			if(!$oDB->isColumnExists('ad', 'ad_time')) return true;
			if(!$oDB->isColumnExists('ad', 'ad_type')) return true;
			if(!$oDB->isColumnExists('ad', 'extra_vars')) return true;

			// 트리거 존재 여부 확인
			$oModuleModel = &getModel('module');
			if(!$oModuleModel->getTrigger('module.deleteModule', 'ad', 'controller', 'triggerDeleteModuleAds', 'before')) return true;

			return false;
		}

		/**
		* @brief 업데이트
		 * @return new Object
		**/
		function moduleUpdate() {
			$oDB = &DB::getInstance();
			$oAdModel = &getModel('ad');
			$oAdController = &getController('ad');
			$oModuleModel = &getModel('module');
			$oModuleController = &getController('module');

			// 필드 추가
			if(!$oDB->isColumnExists('ad', 'start_date')) {
				$oDB->addColumn('ad', 'start_date', 'date', '', '');
				$oDB->addIndex('ad', 'idx_start_date', 'start_date');
			}

			if(!$oDB->isColumnExists('ad', 'style')) $oDB->addColumn('ad', 'style', 'text', '', '');

			if(!$oDB->isColumnExists('ad', 'ad_time')) {
				$oDB->addColumn('ad', 'ad_time', 'number', 11, '');
				$oDB->addIndex('ad', 'idx_ad_time', 'ad_time');
			}

			if(!$oDB->isColumnExists('ad', 'ad_type')) {
				$oDB->addColumn('ad', 'ad_type', 'varchar', 20, '');
				$oDB->addIndex('ad', 'idx_ad_type', 'ad_type');
			}

			if(!$oDB->isColumnExists('ad', 'extra_vars')) {
				$oDB->addColumn('ad', 'extra_vars', 'text');
			}

			// 2010/04/26 module_srl 추가 및 업데이트
			if(!$oDB->isColumnExists('ad', 'module_srl')) {
				$oDB->addColumn('ad', 'module_srl', 'number', 11, '');
				$oDB->addIndex('ad', 'idx_module_srl', 'module_srl');

				$obj->select_all_ad = true;
				$oAdList = $oAdModel->getAdList($obj);

				foreach($oAdList->data as $no => $oAd) {
					$args->module_srl = $oAd->get('module_srl');
					$args->document_srl = $oAd->get('document_srl');
					$args->start_date = $oAd->get('start_date');
					$args->end_date = $oAd->get('end_date');

					if($oAd->get('end_date') == -1) {
						$args->ad_time = -1;
					} else {
						$args->ad_time = $oAd->get('end_date') - $oAd->get('start_date');
					}

					$args->ad_type = $oAd->getBannerPath()?'banner':'linead';
					$args->url = $oAd->getUrl();
					$args->url_target = $oAd->getUrlTarget();
					$args->style = serialize($oAd->getStyleList());
					$args->extra_vars = '';

					$output = executeQuery('ad.updateAd', $args);
					if(!$output->toBool()) return $output;
				}
			}

			// 광고 삭제용 트리거 추가
			if(!$oModuleModel->getTrigger('module.deleteModule', 'ad', 'controller', 'triggerDeleteModuleAds', 'before'))
				$oModuleController->insertTrigger('module.deleteModule', 'ad', 'controller', 'triggerDeleteModuleAds', 'before');

			return new Object(0,'success_updated');
		}

		/**
		 * @brief 캐시 파일 재생성
		 * @return none
		 **/
		function recompileCache() {
		}
	}

	if(!function_exists('date_parse')) {
		function date_parse($date) {
			$date = getdate(strtotime($date));
			return array(
			'second' => $date['seconds'],
			'minute' => $date['minutes'],
			'hour' => $date['hours'],
			'month' => $date['mon'],
			'year' => $date['year']
			);
		}
	}

	if(!function_exists('cal_days_in_month')) {
		function cal_days_in_month($calendar = '', $month , $year) {
			if(checkdate($month, 31, $year)) return 31;
			if(checkdate($month, 30, $year)) return 30;
			if(checkdate($month, 29, $year)) return 29;
			if(checkdate($month, 28, $year)) return 28;
		}
	}

	function days_in_month($month, $year) {
		return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31); 
	} 

	if(!function_exists('htmlspecialchars_decode')) {
		function htmlspecialchars_decode ($str, $quote_style = ENT_COMPAT) {
			return strtr($str, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
		}
	}
?>

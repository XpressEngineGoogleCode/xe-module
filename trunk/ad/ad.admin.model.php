<?php
	/**
	 * @class  AdAdminModel
	 * @author 퍼니엑스이 (admin@funnyxe.com)
	 * @brief  광고 모듈의 admin model 객체
	 **/
	 
	 class adAdminModel extends ad {

		/**
		 * @brief 초기화
		 **/
		function init() {
		}

		/**
		 * @brief 광고 시간 범위 정보의 xml 캐시 파일을 return
		 **/
		function getAdTimeRangeXmlFile($module_srl) {
			$xml_file = sprintf('files/cache/ad/ad_time_range/%s.xml.php',$module_srl);
			if(!file_exists($xml_file)) {
				$oAdAdminController = &getController('ad');
				$oAdAdminController->makeAdTimeRangeFile($module_srl);
			}
			return $xml_file;
		}

		/**
		 * @brief 모듈의 광고 시간 범위 관리
		 */
		function getAdTimeRangeHtml($module_srl) {
			$ad_time_range_xml_file = $this->getAdTimeRangeXmlFile($module_srl);

			Context::set('ad_time_range_xml_file', $ad_time_range_xml_file);

			// tree plugin load
			Context::loadJavascriptPlugin('ui.tree');

			// 템플릿 파일 컴파일
			$oTemplate = &TemplateHandler::getInstance();
			return $oTemplate->compile($this->module_path.'tpl', 'ModuleAdTimeRange_Template');
		}
	}
?>

<?php
	/**
	 * @class pointsend
	 * @author SMaker (dowon2308@paran.com)
	 * @brief  pointsend 모듈의 high class
	 **/

	class pointsend extends ModuleObject {

		/**
		 * @brief 설치시 추가 작업이 필요할시 구현
		 **/
		function moduleInstall() {
			$oModuleController = &getController('module');

			// 2010.03.04 회원 탈퇴 시 모든 정보를 삭제하는 트리거 추가
			$oModuleController->insertTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after');

			// 2010.12.10 포인트 선물 추가 (포인트 선물 애드온 폐기)
			$oModuleController->insertTrigger('moduleHandler.init', 'pointsend', 'controller', 'triggerAfterModuleInit', 'after');

			return new Object();
		}

		/**
		 * @brief 설치가 이상이 없는지 체크하는 method
		 **/
		function checkUpdate() {
			$oDB = &DB::getInstance();
			$oModuleModel = &getModel('module');

			if(!$oDB->isColumnExists('pointsend_log', 'comment')) return true;
			if(!$oModuleModel->getTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after')) return true;

			// 2010.12.10 포인트 선물 추가 (포인트 선물 애드온 폐기)
			if(!$oModuleModel->getTrigger('moduleHandler.init', 'pointsend', 'controller', 'triggerModuleHandlerInit', 'after')) return true;

			return false;
		}

		/**
		 * @brief 업데이트 실행
		 **/
		function moduleUpdate() {
			$oDB = &DB::getInstance();
			$oModuleModel = &getModel('module');
			$oModuleController = &getController('module');

			if(!$oDB->isColumnExists('pointsend_log', 'comment'))
				$oDB->addColumn('pointsend_log','comment','text','',0);

			if(!$oModuleModel->getTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after')) 
				$oModuleController->insertTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after');

			// 2010.12.10 포인트 선물 추가 (포인트 선물 애드온 폐기)
			if(!$oModuleModel->getTrigger('moduleHandler.init', 'pointsend', 'controller', 'triggerModuleHandlerInit', 'after'))
				$oModuleController->insertTrigger('moduleHandler.init', 'pointsend', 'controller', 'triggerModuleHandlerInit', 'after');

			return new Object(0,'success_updated');
		}

		/**
		 * @brief 캐시 파일 재생성
		 **/
		function recompileCache() {
		}
	}
?>

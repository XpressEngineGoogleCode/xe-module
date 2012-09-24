<?php
/**
 * @class loginlog
 * @author 퍼니엑스이 (admin@funnyxe.com)
 * @brief loginlog 모듈의 high class
 **/

class loginlog extends ModuleObject
{
	/**
	 * @brief 모듈 설치
	 */
	function moduleInstall()
	{
		$oModuleController = &getController('module');
		$oModuleController->insertTrigger('member.doLogin', 'loginlog', 'controller', 'triggerAfterLogin', 'after');

		// 회원 삭제 시 로그인 기록을 삭제하는 트리거 추가 (2010.06.09)
		$oModuleController->insertTrigger('member.deleteMember', 'loginlog', 'controller', 'triggerDeleteMember', 'after');

		// 회원 로그인 실패 시 로그인 기록을 남기는 트리거 추가 (2010.09.13)
		$oModuleController->insertTrigger('member.doLogin', 'loginlog', 'controller', 'triggerBeforeLogin', 'before');

		return new Object();
	}

	/**
	 * @brief 모듈 삭제
	 */
	function moduleUninstall()
	{
		$oModuleModel = &getModel('module');
		$oModuleController = &getController('module');

		// 트리거 삭제
		if($oModuleModel->getTrigger('member.doLogin', 'loginlog', 'controller', 'triggerAfterLogin', 'after'))
		{
			$oModuleController->deleteTrigger('member.doLogin', 'loginlog', 'controller', 'triggerAfterLogin', 'after');
		}

		// 회원 삭제 시 로그인 기록을 삭제하는 트리거 추가 (2010.06.09)
		if($oModuleModel->getTrigger('member.deleteMember', 'loginlog', 'controller', 'triggerDeleteMember', 'after'))
		{
			$oModuleController->deleteTrigger('member.deleteMember', 'loginlog', 'controller', 'triggerDeleteMember', 'after');
		}

		// 회원 로그인 실패 시 로그인 기록을 남기는 트리거 추가 (2010.09.13)
		if($oModuleModel->getTrigger('member.doLogin', 'loginlog', 'controller', 'triggerBeforeLogin', 'before'))
		{
			$oModuleController->deleteTrigger('member.doLogin', 'loginlog', 'controller', 'triggerBeforeLogin', 'before');
		}

		return new Object();
	}

	/**
	 * @brief 업데이트가 필요한지 확인
	 **/
	function checkUpdate()
	{
		$oModuleModel = &getModel('module');

		//회원 삭제 시 로그인 기록을 삭제하는 트리거 추가 (2010.06.09)
		if(!$oModuleModel->getTrigger('member.deleteMember', 'loginlog', 'controller', 'triggerDeleteMember', 'after'))
		{
			return true;
		}

		// 회원 로그인 실패 시 로그인 기록을 남기는 트리거 추가 (2010.09.13)
		if(!$oModuleModel->getTrigger('member.doLogin', 'loginlog', 'controller', 'triggerBeforeLogin', 'before'))
		{
			return true;
		}

		// 로그인 성공 여부를 기록하는 is_succeed 칼럼 추가 (2010.09.13)
		$oDB = &DB::getInstance();
		if(!$oDB->isColumnExists('member_loginlog', 'is_succeed'))
		{
			return true;
		}

		return false;
	}

	/**
	 * @brief 모듈 업데이트
	 **/
	function moduleUpdate()
	{
		$oModuleModel = &getModel('module');
		$oModuleController = &getController('module');

		if(!$oModuleModel->getTrigger('member.deleteMember', 'loginlog', 'controller', 'triggerDeleteMember', 'after'))
		{
			$oModuleController->insertTrigger('member.deleteMember', 'loginlog', 'controller', 'triggerDeleteMember', 'after');
		}

		// 회원 로그인 시 로그인 기록을 남기는 트리거 추가 (2010.09.13)
		if(!$oModuleModel->getTrigger('member.doLogin', 'loginlog', 'controller', 'triggerBeforeLogin', 'before'))
		{
			$oModuleController->insertTrigger('member.doLogin', 'loginlog', 'controller', 'triggerBeforeLogin', 'before');
		}

		// 로그인 성공 여부를 기록하는 is_succeed 칼럼 추가 (2010.09.13)
		$oDB = &DB::getInstance();
		if(!$oDB->isColumnExists('member_loginlog', 'is_succeed'))
		{
			$oDB->addColumn('member_loginlog', 'is_succeed', 'char', 1, 'Y', true);
			$oDB->addIndex('member_loginlog', 'idx_is_succeed', 'is_succeed', false);
		}

		return new Object(0, 'success_updated');
	}

	/**
	 * @brief 캐시 파일 재생성
	 **/
	function recompileCache()
	{
	}
}

/* End of file : loginlog.class.php */
/* Location : ./modules/loginlog/loginlog.class.php */
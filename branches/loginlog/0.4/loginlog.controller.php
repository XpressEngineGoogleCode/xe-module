<?php
/**
 * @class loginlogController
 * @author 퍼니엑스이 (admin@funnyxe.com)
 * @brief loginlog 모듈의 controller class
 **/

class loginlogController extends loginlog
{
	/**
	 * @brief 초기화
	 */
	function init()
	{
	}

	/**
	 * @brief 로그인 실패 시 로그인 기록 남김
	 */
	function triggerBeforeLogin(&$obj)
	{
		// 넘어온 아이디가 없다면 실행 중단
		$user_id = $obj->user_id;
		if(!$user_id)
		{
			return new Object();
		}

		// 대상 회원의 비밀번호와 회원 번호를 구함
		$output = executeQuery('loginlog.getMemberPassword', $obj);

		// 존재하지 않는 회원이라면 기록하지 않음
		if(!$output->data)
		{
			return new Object();
		}

		// 대상 회원의 비밀번호
		$password = $output->data->password;

		// memberModel 객체 생성
		$oMemberModel = &getModel('member');

		// 비밀번호가 맞다면 기록하지 않음
		if($oMemberModel->isValidPassword($password, $obj->password))
		{
			return new Object();
		}

		// 비밀번호가 틀렸다면 기록
		$args->member_srl = $output->data->member_srl;
		$args->ipaddress = $_SERVER['REMOTE_ADDR'];
		$args->is_succeed = 'N';
		$args->regdate = date('YmdHis');
		executeQuery('loginlog.insertLoginlog', $args);

		return new Object();
	}


	/**
	 * @brief 로그인 성공 시 로그인 기록 남김
	 */
	function triggerAfterLogin(&$member_info)
	{
		$member_srl = $member_info->member_srl;
		if(!$member_srl)
		{
			return new Object();
		}

		$args->member_srl = $member_srl;
		$args->ipaddress = $_SERVER['REMOTE_ADDR'];
		$args->is_succeed = 'Y';
		$args->regdate = date('YmdHis');
		executeQuery('loginlog.insertLoginlog', $args);

		return new Object();
	}

	/**
	 * @brief 회원 탈퇴 시 로그인 기록 삭제
	 */
	function triggerDeleteMember(&$obj)
	{
		$oModel = &getModel('loginlog');
		$config = $oModel->getModuleConfig();

		if($config->delete_logs != 'Y')
		{
			return new Object();
		}

		$member_srl = $obj->member_srl;
		if(!$member_srl)
		{
			return new Object();
		}

		executeQuery('loginlog.deleteMemberLoginlogs', $obj);

		return new Object();
	}
}

/* End of file : loginlog.controller.php */
/* Location : ./modules/loginlog/loginlog.controller.php */
<?php
	/**
	 * @file ko.lang.php
	 * @author SMaker (dowon2308@paran.com)
	 * @brief  pointsend 모듈의 기본 언어팩
	 **/
	$lang->point = '포인트';
	$lang->pointsend = $lang->pointgift = '포인트 선물';
	$lang->fee = '수수료';
	$lang->fee_persent = '수수료 비율';
	$lang->fee_apply_point = '수수료 적용 포인트';
	$lang->fee_but_group = '수수료 제외 그룹';
	$lang->pointgift_limit = '포인트 선물 제한';
	$lang->pointgift_denied_group = '선물 제한 그룹';
	$lang->pointgift_daily_limit = '일일 포인트 선물 제한';
	$lang->pointgift_sameip_deny = '동일 IP 선물 차단';

	$lang->cannot_use = '사용 불가';
	$lang->about_cannot_use = '회원 관리 확장 모듈이 설치되어 있지 않아 사용할 수 없는 기능입니다.';

	$lang->send_point = '보낼 포인트';
	$lang->current_point = '현재 포인트';
	$lang->after_point = '선물 후 포인트';

	$lang->cmd_pointsend = $lang->cmd_pointgift = '포인트 선물';
	$lang->cmd_find_member = '회원 찾기';
	$lang->cmd_pointsend_log_list = '포인트 선물 내역';
	$lang->cmd_batch_pointsend = '일괄 포인트 선물';
	$lang->cmd_group_pointsend = '그룹별 포인트 선물';
	$lang->cmd_member_pointsend = '회원별 포인트 선물';
	$lang->cmd_all_pointsend = '전체 회원 포인트 선물';
	$lang->cmd_continue = '계속';
	$lang->cmd_delete_pointgift_log = '포인트 선물 내역 삭제';
	$lang->cmd_view_pointsend_log = '포인트 선물 내역';
	$lang->cmd_view_log = array(
		'R' => '받은 포인트',
		'S' => '보낸 포인트'
	);
	$lang->cmd_pointsend_cancle = '포인트 선물 취소';

	$lang->pointsend_title = '[알림] %s님으로 부터 포인트를 선물 받았습니다.';
	$lang->pointsend_content = '%s 님으로 부터 %s 포인트를 선물 받았습니다.<br /><br /><strong>※ 이 쪽지는 자동으로 발송된 쪽지입니다.</strong><br /><br />----------------------------<br />남김말 : <br />%s';
	$lang->pointsendc_title = '[알림] 포인트 선물이 관리자에 의해 취소되었습니다.';
	$lang->pointsendc_content = '%s 님으로 부터 받은 포인트 선물이 관리자에 의해 취소되어 %s 포인트가 차감되었습니다';
	$lang->pointsendc_content2 = '%s 님에게 보낸 포인트 선물이 관리자에 의해 취소되어 %s 포인트가 적립되었습니다';

	$lang->about_send_message = '포인트 선물 성공 시 쪽지를 보냅니다.';
	$lang->about_send_group = '<h4 class="xeAdmin">치환자</h4>
	<div class="infoText">치환자를 사용하여 다수의 회원에게 다른 내용의 쪽지를 보내실 수 있습니다.</div>
	<ul>
		<li><strong>%_SENDER_%</strong> : 보내는이 (회원 확장 정보 사용)</li>
		<li><strong>%_RECEIVER_%</strong> : 받는이 (회원 확장 정보 사용)</li>
		<li><strong>%SENDER%</strong> : 보내는이 닉네임</li>
		<li><strong>%SENDER_ID%</strong> : 보내는이 아이디</li>
		<li><strong>%SENDER_SRL%</strong> : 보내는이 회원 번호</li>
		<li><strong>%RECEIVER%</strong> : 받는이 닉네임</li>
		<li><strong>%RECEIVER_ID%</strong> : 받는이 아이디</li>
		<li><strong>%RECEIVER_SRL%</strong> : 받는이 회원 번호</li>
		<li><strong>%POINT%</strong> : 선물 포인트</li>';
	$lang->about_deny_group = '특정 그룹에게 포인트 선물을 하지 못하도록 제한합니다.';
	$lang->about_delete_log = '정말 포인트 선물 내역을 삭제하시겠습니까?<br /><br />';
	$lang->about_pointsend_cancle = '정말 포인트 선물을 취소하시겠습니까?<br />
	선물을 취소하시면 보낸이와 받는이에게 선물 취소 알림 쪽지가 발송됩니다.<br /><br />';

	$lang->confirm_pointsend = '정말 포인트를 선물하시겠습니까?';

	$lang->msg_need_login = '로그인이 필요합니다.';
	$lang->msg_not_exists_member = '존재 하지 않는 회원입니다.';
	$lang->msg_not_exists_sender= '보내는이가 존재 하지 않습니다.';
	$lang->msg_not_exists_receiver = '받는이가 존재 하지 않습니다.';
	$lang->msg_not_enough_send_point = '보낼 포인트가 현재 보유하고 있는 포인트보다 많습니다.';
	$lang->msg_invalid_send_point = '보낼 포인트를 올바르게 입력해 주세요.'."\n\n".'보낼 포인트는 0 보다 커야 합니다.';
	$lang->msg_not_permitted_pointsend = '포인트 선물 권한이 없습니다.';
	$lang->msg_pointgift_daily_limit_over = '오늘은 더 이상 선물을 하실 수 없습니다.'."\n\n".'(하루에 보낼 수 있는 최대 포인트는 %s 입니다.)';
	$lang->msg_pointgift_denied_group = '포인트 선물이 제한된 그룹 (%s)에게 포인트 선물을 하실 수 없습니다.';
	$lang->msg_not_exists_pointgift_log = '포인트 선물 내역이 없습니다.';

	$lang->msg_pointgift_sameip_warning = '경고! 동일 IP간의 포인트 선물은 제한되어 있습니다.';

	$lang->success_pointsend = '정상적으로 포인트가 선물 되었습니다.';
	$lang->success_group_pointgift = '총 %s개의 그룹에게 포인트 선물을 완료하였습니다.\n\n(성공 : %s, 실패 : %s, 무시 : %s)';

	$lang->subtraction = '차감';

	$lang->cmd_setup_notification_message = '알림 쪽지 설정';
	$lang->gift_message = '전할 말';
	$lang->about_gift_message = '받으시는 분께 덧붙여 보낼 내용을 입력해주세요.';
?>

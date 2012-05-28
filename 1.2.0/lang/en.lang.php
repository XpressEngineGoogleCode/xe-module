<?php
	/**
	 * @file en.lang.php
	 * @author SMaker (dowon2308@paran.com)
	 * @brief  Default Language Pack of PointGift Module
	 **/
	$lang->point = 'Point';
	$lang->pointsend = $lang->pointgift = 'Point Gift';
	$lang->fee = 'Fee';
	$lang->fee_persent = 'Fee Persent';
	$lang->fee_apply_point = 'Fee Apply Point';
	$lang->fee_but_group = '수수료 제외 그룹';

	$lang->send_point = '보낼 포인트';
	$lang->current_point = 'Current Point';
	$lang->after_point = 'After Point';

	$lang->cmd_pointsend = 'Point Gift';
	$lang->cmd_find_member = 'Find Member';
	$lang->cmd_pointsend_log_list = 'Point Gift Log';
	$lang->cmd_group_pointsend = '그룹별 포인트 선물';
	$lang->cmd_member_pointsend = '회원별 포인트 선물';
	$lang->cmd_all_pointsend = '전체 회원 포인트 선물';

	$lang->success_pointsend = 'Point Gift Successful';

	$lang->pointsend_title = '[Notice] You received a Point Gift from %s.';
	$lang->pointsend_content = 'You received a Point Gift from %s (%s).<br /><br /><strong>※ This message was sent automatically.</strong><br /><br />----------------------------<br />Message : <br />%s';

	$lang->about_send_message = 'Send Message';
	$lang->about_send_group = '<h4 class="xeAdmin">Replacement</h4>
	<div class="infoText">치환자를 사용하여 다수의 회원에게 다른 내용의 쪽지를 보내실 수 있습니다.</div>
	<ul>
		<li><strong>%_SENDER_%</strong> : Sender (Use Member Extra Info)</li>
		<li><strong>%_RECEIVER_%</strong> : Receiver (Use Member Extra Info)</li>
		<li><strong>%SENDER%</strong> : Sender Nick Name</li>
		<li><strong>%SENDER_ID%</strong> : Sender ID</li>
		<li><strong>%SENDER_SRL%</strong> : Sender Member No.</li>
		<li><strong>%RECEIVER%</strong> : Receiver Nick Name</li>
		<li><strong>%RECEIVER_ID%</strong> : Receiver ID</li>
		<li><strong>%RECEIVER_SRL%</strong> : Receiver Member No.</li>
		<li><strong>%POINT%</strong> : Point</li>';
	$lang->about_deny_group = '특정 그룹이 포인트 선물을 받지 못하게 합니다.';

	$lang->confirm_pointsend = 'Are you sure to Point Gift?';

	$lang->cmd_view_log = array(
		'R' => 'Received Point',
		'S' => 'Sent Point'
	);

	$lang->pointgift_limit = 'Point Gift Limit';
	$lang->pointgift_denied_group = 'Point Gift Denied Group';
	$lang->pointgift_daily_limit = 'Daily Point Gift Limit';

	$lang->msg_need_login = 'You need login.';
	$lang->msg_not_exists_member = 'Member does not exist';
	$lang->msg_not_exists_sender = 'Sender does not exist';
	$lang->msg_not_exists_receiver = 'Receiver does not exist';
	$lang->msg_not_enough_send_point = '보낼 포인트가 현재 보유하고 있는 포인트 보다 많습니다.';
	$lang->msg_invalid_send_point = '보낼 포인트를 올바르게 입력해 주세요.\n\n보낼 포인트는 0 보다 커야 합니다.';
	$lang->msg_not_permitted_pointsend = 'You do not have permission to point gift';
	$lang->msg_pointgift_daily_limit_over = 'You exceed the Daily Point Gift Limit (%s Point).';
	$lang->msg_pointgift_denied_group = 'You can\'t point gift to Denied Group (%s).';
	$lang->msg_not_exists_pointgift_log = 'Point Gift log isn\'t exists';

	$lang->cmd_pointsend_cancle = 'Point Gift Cancel';
	$lang->about_pointsend_cancle = 'Are you sure to cancel the point gift?<br />
	If you cancel the gift, send the message to sender and receiver.<br /><br />';
?>

<?php
	/**
	 * @class  pointsendAdminController
	 * @author SMaker (dowon2308@paran.com)
	 * @brief  pointsend 모듈의 Admin Controller class
	 **/

	class pointsendAdminController extends pointsend {

		/**
		 * @brief 초기화
		 **/
		function init() {
		}

		/**
		 * @brief 설정 저장
		 */ 
		function procPointsendAdminInsertConfig() {
			// 입력받은 설정 항목을 구함
			$config = Context::getRequestVars();

			// 불필요한 항목 제외
			unset($config->module);
			unset($config->act);
			unset($config->body);
			unset($config->_filter);

			// 기본값 지정
			if(!$config->skin) $config->skin = 'default';
			if(!$config->use_fee) $config->use_fee = 'N';
			if(!$config->sameip_deny) $config->sameip_deny = 'N';

			$oModuleController = &getController('module');
			$oModuleController->insertModuleConfig('pointsend', $config);

			$this->setMessage('success_saved');
		}

		/**
		 * @brief 포인트 선물 취소 (관리자용)
		 */
		function procPointsendAdminRollback() {
			$log_srl = (int)Context::get('log_srl');
			if(!$log_srl) return new Object(-1, 'msg_invalid_request');

			$oPointsendModel = &getModel('pointsend');
			$log_info = $oPointsendModel->getLogInfoByLogSrl($log_srl);

			$sender_srl = (int)$log_info->sender_srl;
			$receiver_srl = (int)$log_info->receiver_srl;

			if(!$sender_srl || !$receiver_srl) return new Object(-1, 'msg_invalid_request');

			$oMemberModel = &getModel('member');
			$config = $oPointsendModel->getConfig();

			$point = $log_info->point;
			$sender_point = $point;
			$receiver_point = $point;

			$sender_info = $oMemberModel->getMemberInfoByMemberSrl($sender_srl);
			$receiver_info = $oMemberModel->getMemberInfoByMemberSrl($receiver_srl);

			// 수수료에 따른 포인트 계산
			$fee_per = (int)$config->fee;
			$fee_apply_point = (int)$config->fee_apply_point;
			$fee = 0;
			if($config->use_fee == 'Y' && $fee_per && $point>=$fee_apply_point) $fee = $point * ($config->fee/100);

			$receiver_point -= $fee;

			$oPointController = &getController('point');
			$oPointController->setPoint($sender_srl, $sender_point, 'add');
			$oPointController->setPoint($receiver_srl, $receiver_point, 'minus');

			// 쪽지 보내기
			$title = Context::getLang('pointsendc_title');
			$content = sprintf(Context::getLang('pointsendc_content2'), $sender_info->nick_name, $sender_info->user_id, $receiver_point);
			$content2 = sprintf(Context::getLang('pointsendc_content'), $receiver_info->nick_name, $receiver_info->user_id, $sender_point);

			$oCommunicationController = &getController('communication');
			$oCommunicationController->sendMessage($sender_srl, $receiver_srl, $title, $content, false);
			$oCommunicationController->sendMessage($receiver_srl, $sender_srl, $title, $content2, false);

			$args->log_srl = $log_srl;
			executeQuery('pointsend.deletePointsendLog',$args);
		}

		/**
		 * @brief 일괄 포인트 선물 - 그룹별
		 */
		function procPointsendAdminSendGroup() {
			$cart = Context::get('cart');
			if(!$cart) return new Object(-1, 'msg_invalid_request');

			$group_srls = str_replace('|@|', ',', Context::get('cart'));
			$send_point = Context::get('send_point');
			$title = Context::get('title');
			$content = Context::get('content');

			$oController = &getController('pointsend');
			$oController->pointsendToGroup($group_srls, $send_point, $title, $content);

			$total = $this->get('group_count');
			$success = $this->get('success_group');
			$failed = $this->get('failed_group');
			$ignore = $this->get('ignore_group');

			$msg = sprintf(Context::getLang('success_group_pointgift'), $total, $success, $failed, $ignore);
			$this->setMessage($msg);
		}

		/**
		 * @brief 포인트 선물 내역 삭제
		 */
		function procPointsendAdminDeleteLog() {
			$log_srl = Context::get('log_srl');
			if(!$log_srl) return new Object(-1, 'msg_invalid_request');

			// 삭제
			$oController = &getController('pointsend');
			$output = $oController->deleteLog($log_srl);

			// 에러가 발생하면
			if(!$output->toBool()) return $output;

			// 메시지 지정
			$this->setMessage('success_deleted');
		}
	}
?>

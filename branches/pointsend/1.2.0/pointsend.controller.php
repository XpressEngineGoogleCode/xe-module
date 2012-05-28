<?php
	/**
	 * @class  pointsendController
	 * @author SMaker (dowon2308@paran.com)
	 * @brief  pointsend 모듈의 Controller class
	 **/

	class pointsendController extends pointsend {

		/**
		 * @brief 초기화
		 **/
		function init() {
		}

		/**
		 * @brief 포인트 선물
		 **/
		function procPointsend() {
			// 로그인 상태인지 확인
			$logged_info = Context::get('logged_info');
			if(!$logged_info) return new Object(-1, 'msg_need_login');

			// 입력 받은 데이터 검사
			$obj = Context::getRequestVars();
			$obj->send_point = (int)$obj->send_point;
			$obj->content = $logged_info->is_admin!='Y'?removeHackTag(trim($obj->content)):trim($obj->content); //< XSS 공격 방지

			if($logged_info->is_admin != 'Y') unset($obj->is_subtraction);

			$sender_srl = (int)$obj->sender_srl;
			$receiver_srl = (int)$obj->receiver_srl;
			$point = (int)$obj->send_point;
			$isSubtraction = $obj->is_subtraction;

			// 설정을 구함
			$oModel = &getModel('pointsend');
			$config = $oModel->getConfig();

			$triggerObj = new stdClass;
			$triggerObj->sender_srl = $sender_srl;
			$triggerObj->receiver_srl = $receiver_srl;
			$triggerObj->point = $point;
			$triggerObj->isSubtraction = $isSubtraction;
			$triggerObj->config = $config;

			// 트리거 실행 (before)
			$triggerOutput = ModuleHandler::triggerCall('pointsend.procPointsend', 'before', $triggerObj);
			if(!$triggerOutput->toBool()) return $triggerOutput;

			// 트리거로 변수 조작
			$sender_srl = $triggerObj->sender_srl;
			$receiver_srl = $triggerObj->receiver_srl;
			$point = $triggerObj->point;
			$point = $triggerObj->isSubtraction;
			$config = $triggerObj->config;

			// 포인트 차감이면 음수(-)로 전환
			if($isSubtraction == 'Y') $point *= -1;

			// 포인트 선물
			$this->pointsend($sender_srl, $receiver_srl, $point, $obj->content, $config);

			// 트리거 실행 (after)
			$triggerOutput = ModuleHandler::triggerCall('pointsend.procPointsend', 'after', $triggerObj);
			if(!$triggerOutput->toBool()) return $triggerOutput;

			// 성공 메시지 지정
			$this->setMessage('success_pointsend');
		}

		/**
		 * @brief 포인트 선물
		 */
		function pointsend($sender_srl, $receiver_srl, $point, $comment, $config = null, $sender_exists = false, $receiver_exists = false, $send_message = true) {
			// 보내는 이와 받는 이가 없으면 에러
			if(!$sender_srl || !$receiver_srl) return new Object(-1, 'msg_invalid_request');

			// 로그인 정보 구함
			$logged_info = Context::get('logged_info');

			// 보낼 포인트가 없거나(잘못되었거나) 0보다 작을 경우 에러
			if(!$point || ($logged_info->is_admin != 'Y' && $point<0)) return new Object(-1, 'msg_invalid_send_point');

			// 로그인 한 회원과 받는이가 같으면 에러
			if($logged_info->member_srl == $receiver_srl) return new Object(-1, 'msg_invalid_request');

			// 로그인 한 회원과 보내는이가 다르면 에러
			if($logged_info->is_admin != 'Y' && $logged_info->member_srl != $sender_srl) return new Object(-1, 'msg_invalid_request');

			$oModel = &getModel('pointsend');
			$oMemberModel = &getModel('member');
			$oPointModel = &getModel('point');
			$oPointController = &getController('point');

			// 로그인 한 회원과 보내는이가 다르면 보내는이가 존재하는 지 확인
			if(!$sender_exists && $logged_info->member_srl != $sender_srl) {
				$oSender = $oMemberModel->getMemberInfoByMemberSrl($sender_srl);
				if(!$oSender) return new Object(-1, 'msg_not_exists_sender');
			}

			// 받는이가 존재 하지 않으면 에러
			if(!$receiver_exists) {
				$oReceiver = $oMemberModel->getMemberInfoByMemberSrl($receiver_srl);
				if(!$oReceiver) return new Object(-1, 'msg_not_exists_receiver');
			}

			// 보내는이의 포인트를 구함
			$oSender->point = $oPointModel->getPoint($sender_srl);

			// 보내는이의 포인트가 보낼 포인트보다 작으면 에러
			if($oSender->point<$point) return new Object(-1, 'msg_not_enough_send_point');

			$real_point = (int)$point;

			// 설정을 구함
			if(!$config) $config = $oModel->getConfig();

			if($logged_info->is_admin != 'Y') {
				// 일일 포인트 선물 제한을 설정한 경우 체크
				$daily_limit = (int)$config->daily_limit;
				if($daily_limit>0) {
					$args->member_srl = $sender_srl;
					$args->type = 'S';
					$log = $oModel->getTodayLog($args);
					if($daily_limit<$log->total) {
						return new Object(-1, sprintf(Context::getLang('msg_pointgift_daily_limit_over'),$daily_limit));
					}
				}

				// 선물 제한 그룹을 설정한 경우 체크
				$deny_group = $config->deny_group;
				if(count($deny_group)>0) {
					$groups = $oMemberModel->getMemberGroups($receiver_srl);
					if(count($groups)) {
						foreach($groups as $group_srl => $group_title) {
							if(in_array($group_srl, $deny_group)) return new Object(-1, sprintf(Context::getLang('msg_pointgift_denied_group'),$group_title));
						}
					}
				}

				// 동일 IP 선물 제한
				if($config->sameip_deny == 'Y') {
					// 현재 로그인 한 IP를 구함
					$args->member_srl = $logged_info->member_srl;
					$output = executeQuery('pointsend.getLastloggedIpaddress', $args);
					if(!$output->toBool()) return $output;

					$current_ip = $output->data->ipaddress;

					// 선물 대상 회원의 IP를 구함
					$args->member_srl = $receiver_srl;
					$output = executeQuery('pointsend.getLastloggedIpaddress', $args);
					if(!$output->toBool()) return $output;

					$receiver_ip = $output->data->ipaddress;

					if(($current_ip && $receiver_ip) && $current_ip == $receiver_ip) return new Object('msg_pointgift_sameip_warning');
				}
			}

			// 수수료에 따른 포인트 계산
			$fee_per = (int)$config->fee;
			$fee_apply_point = (int)$config->fee_apply_point;
			$fee = 0;
			if($config->use_fee == 'Y' && $fee_per && $point>=$fee_apply_point) $fee = $point * ($config->fee/100);

			// 포인트에 수수료 빼기
			$point -= $fee;

			// 보내는 이는 포인트 (-), 받는 이는 포인트 (+)
			$oSender->point -= $real_point;
			$oReceiver->point = $oPointModel->getPoint($receiver_srl) + $point;

			// 수수료 적용 시 포인트가 소수점이 될 수 있으므로 정수로 만듬
			$oSender->point = (int)$oSender->point;
			$oReceiver->point = (int)$oReceiver->point;

			// 포인트 선물
			$oPointController->setPoint($sender_srl, $oSender->point);
			$oPointController->setPoint($receiver_srl, $oReceiver->point);

			// 쪽지 보내기
			if($send_message) {
				$mtitle = sprintf(Context::getLang('pointsend_title'), $logged_info->nick_name);
				$mcontent = sprintf(Context::getLang('pointsend_content'), $logged_info->nick_name, $logged_info->user_id, $point, $comment);
				$oCommunicationController = &getController('communication');
				$oCommunicationController->sendMessage($sender_srl, $receiver_srl, $mtitle, $mcontent, false);
			}

			// 포인트 선물 내역 기록
			$this->insertLog($sender_srl, $receiver_srl, $real_point, $comment);

			return array('send_point' => $real_point, 'received_point' => $point);
		}

		/**
		 * @brief 다수의 회원에게 포인트 선물
		 */
		function pointsendToMember($member_srls, $point, $title, $comment) {
			// 로그인 정보 구함
			$logged_info = Context::get('logged_info');

			// 로그인 상태가 아니거나 최고 관리자가 아니면 에러
			if(!$logged_info || $logged_info->is_admin != 'Y') return new Object(-1, 'msg_invalid_request');

			// 넘어온 회원이 없으면 에러
			if(!$member_srls) return new Object(-1, 'msg_invalid_request');

			// 넘어온 회원을 정리
			$target_members = array_unique(explode(',', $member_srls));
			$member_count = count($target_members);

			// 보낼 회원이 없으면 에러
			if($member_count < 0) return new Object(-1, 'msg_invalid_request');

			// member 모듈의 model 객체
			$oMemberModel = &getModel('member');

			// communication 모듈의 controller 객체
			$oCommunicationController = &getController('communication');

			foreach($target_members as $key => $member_srl) {
				$mtitle = $title;
				$mcontent = $comment;

				// 받는이의 정보를 구함
				$receiver_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);

				// 포인트 선물
				$oPointController->setPoint($member_srl, $point, 'add');

				// 쪽지 제목과 내용을 정리
				$this->arrangeMessageContent($logged_info, $receiver_info, $point, $mtitle);
				$this->arrangeMessageContent($logged_info, $receiver_info, $point, $mcontent);

				// 쪽지 보내기
				$oCommunicationController->sendMessage($logged_info->member_srl, $member_srl, $mtitle, $mcontent);
			}

			// 포인트 선물 내역 기록
			$this->insertBatchLog('member', $logged_info->member_srl, $member_srls, $point, $title, $comment);
		}

		/**
		 * @brief 그룹별 포인트 선물
		 */
		function pointsendToGroup($group_srls, $point, $title, $content) {
			// 로그인 정보 구함
			$logged_info = Context::get('logged_info');

			// 로그인 상태가 아니거나 최고 관리자가 아니면 에러
			if(!$logged_info || $logged_info->is_admin != 'Y') return new Object(-1, 'msg_invalid_request');

			// 넘어온 그룹이 없으면 에러
			if(!$group_srls) return new Object(-1, 'msg_invalid_request');

			// 넘어온 그룹을 정리
			$target_groups = array_unique(explode(',', $group_srls));
			$group_count = count($target_groups);
			$success = $group_count;
			$failed = 0;
			$ignore = 0;

			// 보낼 그룹이 없으면 에러
			if($group_count < 0) return new Object(-1, 'msg_invalid_request');

			// member 모듈의 model 객체
			$oMemberModel = &getModel('member');

			// communication 모듈의 controller 객체
			$oCommunicationController = &getController('communication');

			// point 모듈의 controller 객체
			$oPointController = &getController('point');

			// 해당 그룹에 소속된 회원을 구함
			foreach($target_groups as $key => $group_srl) {
				$args->group_srl = $group_srl;
				$output = executeQueryArray('pointsend.getGroupMembers', $args);

				if(!$output->toBool()) {
					$success--;
					$failed++;
					continue;
				}

				if(!count($output->data)) {
					$success--;
					$ignore++;
					continue;
				}

				foreach($output->data as $k => $member_srl) {
					$target_members[] = $member_srl;
				}

				$member_count += $output->data->count;
			}

			// 회원이 있을 경우 보내기
			if(count($target_members)) {
				$tmp_count = count($target_members);

				for($i=0;$i<$tmp_count;$i++) {
					$_target_members[$i] = $target_members[$i]->member_srl;
				}

				$target_members = array_unique($_target_members);

				foreach($target_members as $k => $member_srl) {
					$mtitle = $title;
					$mcontent = nl2br($content);

					// 받는이의 정보를 구함
					$receiver_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);

					// 포인트 선물
					$oPointController->setPoint($member_srl, $point, 'add');

					// 쪽지 제목과 내용을 정리
					$this->arrangeMessageContent($logged_info, $receiver_info, $point, $mtitle);
					$this->arrangeMessageContent($logged_info, $receiver_info, $point, $mcontent);

					// 쪽지 보내기
					$oCommunicationController->sendMessage($logged_info->member_srl, $member_srl, $mtitle, $mcontent, false);
				}
			}

			// 포인트 선물 내역 기록
			$this->insertBatchLog('group', $logged_info->member_srl, $group_srls, $point, $title, $content);

			$this->add('group_count', $group_count);
			$this->add('member_count', $member_count);
			$this->add('success_group', $success);
			$this->add('failed_group', $failed);
			$this->add('ignore_group', $ignore);
		}

		/**
		 * @brief 모든 회원에게 포인트 선물 (한 번에 보내면 부하가 발생할 수 있으니 끊어서 보내야 함)
		 */
		function pointsendToAll($unit_number, $point, $title, $content) {
			// working...
			return;
		}

		/**
		 * @brief 포인트 선물 내역 추가
		 */
		function insertLog($sender_srl, $receiver_srl, $point, $comment) {
			if(!$sender_srl || !$receiver_srl || !$point) return;

			$args->log_srl = getNextSequence();
			$args->sender_srl = $sender_srl;
			$args->receiver_srl = $receiver_srl;
			$args->ipaddress = $ipaddress ? $ipaddress : $_SERVER['REMOTE_ADDR'];
			$args->point = $point;
			$args->comment = $comment;
			return executeQuery('pointsend.insertPointsendLog',$args);
		}

		/**
		 * @brief 포인트 선물 내역 삭제
		 */
		function deleteLog($log_srl) {
			if(!$log_srl) return;

			$args->log_srl = $log_srl;
			return executeQuery('pointsend.deletePointsendLog', $args);
		}

		/**
		 * @brief 특정 회원의 내역을 삭제
		 */
		function deleteMemberLogs($member_srl) {
			if(!$member_srl) return;

			$args->member_srl = $member_srl;
			return executeQuery('pointsend.deletePointsendLogByMemberSrl', $args);
		}

		/**
		 * @brief 회원 탈퇴 시 기록된 포인트 선물 내역 삭제
		 * @return new Object
		 */
		function triggerDeleteMember(&$obj) {
			$member_srl = (int)$obj->member_srl;
			if(!$member_srl) return new Object();

			$output = $this->deleteMemberLogs($member_srl);
			if(!$output->toBool()) return $output;

			return new Object();
		}

		function triggerModuleHandlerInit(&$module_info) {
			$logged_info = Context::get('logged_info');
			if(!$logged_info) return new Object();

			$oPointsendModel = &getModel('pointsend');

			// 회원 로그인 정보에 메뉴를 추가
			if($oPointsendModel->isGranted()) {
				$oMemberController = &getController('member');
				$oMemberController->addMemberMenu('dispPointsendLog', 'cmd_view_pointsend_log');
				if(Context::get('act') == 'getMemberMenu') {
					$member_srl = Context::get('target_srl');
					$mid = Context::get('cur_mid');
					// 자신이 아니라면 포인트 선물 기능 추가
					if($logged_info->member_srl != $member_srl) {
						// 대상 회원의 정보를 가져옴
						$oMemberModel = &getModel('member'); 
						$target_member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
						if(!$target_member_info->member_srl) return;

						// 포인트 선물
						$oMemberController->addMemberPopupMenu(getUrl('','module','pointsend','act','dispPointsend','receiver_srl', $member_srl), 'pointsend', '', 'popup');
					}
				}
			}

			return new Object();
		}

		/**
		 * @brief 쪽지 내용에 포함된 치환자를 정리
		 */
		function arrangeMessageContent($sender_info, $receiver_info, $point, &$content) {
			$content = str_replace('%_SENDER_%', sprintf('<span class="member_%s">%s</span>', $sender_info->member_srl ,$sender_info->nick_name), $content);
			$content = str_replace('%_RECEIVER_%', sprintf('<span class="member_%s">%s</span>', $receiver_info->member_srl ,$receiver_info->nick_name), $content);
			$content = str_replace('%SENDER%', $sender_info->nick_name, $content);
			$content = str_replace('%SENDER_ID%', $sender_info->user_id, $content);
			$content = str_replace('%SENDER_SRL%', $sender_info->member_srl, $content);
			$content = str_replace('%RECEIVER%', $receiver_info->nick_name, $content);
			$content = str_replace('%RECEIVER_ID', $receiver_info->user_id, $content);
			$content = str_replace('%RECEIVER_SRL', $receiver_info->member_srl, $content);
			$content = str_replace('%POINT%', $point, $content);
		}

		/**
		 * #brief 일괄 포인트 선물 내역 기록
		 */
		function insertBatchLog($target = 'group', $sender_srl, $target_srls, $point, $title, $comment) {
			if(!in_array($target, array('member', 'group'))) return;

			$args->log_srl = getNextSequence();
			$args->target = $target;
			$args->sender_srl = $sender_srl;
			$args->target_srls = $target_srls;
			$args->point = $point;
			$args->title = $title;
			$args->comment = $comment;

			return executeQuery('pointsend.insertBatchLog', $args);
		}
	}
?>

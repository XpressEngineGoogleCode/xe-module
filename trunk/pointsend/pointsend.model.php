<?php
    /**
     * @class  pointsendModel
     * @author SMaker (dowon2308@paran.com)
     * @brief pointsend 모듈의 Model class
     **/

    class pointsendModel extends pointsend {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 포인트 선물 모듈 설정 구함
         **/
        function getConfig($is_admin = false) {
            static $config = null;
            if(is_null($config)) {
                // 모듈 설정을 구함
                $oModuleModel = &getModel('module');
                $config = $oModuleModel->getModuleConfig('pointsend');
                $config->skin = !$config->skin ? 'default' : $config->skin;
                $config->use_fee = $config->use_fee == 'Y' ? 'Y' : 'N';
                $config->fee = $config->fee <1 ? 0 : $config->fee;
                $config->fee = $config->fee >99 ? 99 : $config->fee;
                $config->fee_but_group = $config->fee_but_group ? explode('|@|',$config->fee_but_group) : array();
                $config->deny_group = $config->deny_group ? explode('|@|',$config->deny_group) : array();
                $config->grants = $config->grants ? explode('|@|',$config->grants) : array();

                // 최고 관리자이면 동일 IP 선물 제한 비활성화
                $logged_info = Context::get('logged_info');
                if(!$is_admin && $logged_info->is_admin == 'Y') $config->sameip_deny = 'N';

                // 최고 관리자이거나 수수료 제외 그룹에 해당하면 수수료 기능 비활성화
                if(!$is_admin) {
                    if($logged_info->is_admin == 'Y') $config->use_fee = 'N';
                    if(count($config->fee_but_group)) {
                        foreach($logged_info->group_list as $key => $val) {
                            if(in_array($key, $config->fee_but_group)) {
                                $config->use_fee = 'N';
                                break;
                            }
                        }
                    }
                }
            }
            return $config;
        }

        /**
         * @brief 포인트 선물 권한이 있는지 확인
         */
        function isGranted() {
            // 로그인 하지 않은 경우 권한이 없음
            $logged_info = Context::get('logged_info');
            if(!$logged_info) return false;

            // 설정된 권한이 없을 경우 무조건 권한이 있음
            $granted = true;

            // 최고 관리자가 아닌 경우 권한이 있는지 확인
            if($logged_info->is_admin != 'Y') {
                $granted = true;
                // 설정을 구함
                $config = $this->getConfig();

                // 설정된 권한이 있다면
                if(count($config->grants)) {
                    $granted = false;
                    // 그룹이 하나라도 설정된 권한에 포함되어 있다면 권한이 있음
                    foreach($logged_info->group_list as $key => $val) {
                        if(in_array($key, $config->grants)) {
                            $granted = true;
                            break;
                        }
                    }
                }
            }

            return $granted;
        }

        /**
         * @brief log_srl로 포인트 선물 내역 정보 구함
         */
        function getLogInfoByLogSrl($log_srl) {
            $args->log_srl = $log_srl;
            $output = executeQuery('pointsend.getPointsendLogInfo',$args);
            return $output->data;
        }

        /**
         * @brief 포인트 선물 내역 목록을 구함
         **/
        function getLogList($obj = null) {
            $args->sender_srl = $obj->sender_srl;
            $args->receiver_srl = $obj->receiver_srl;
            $args->sort_index = $obj->sort_index?$obj->sort_index:'regdate';
            $args->order_type = $obj->order_type?$obj->order_type:'desc';
            $args->list_count = $obj->list_count?$obj->list_count:20;
            $args->page_count = $obj->page_count?$obj->page_count:10;
            $args->page = $obj->page?$obj->page:1;
            return executeQuery('pointsend.getPointsendLogList',$args);
        }

        /**
         * @brief 일괄 포인트 선물 내역 목록을 구함
         **/
        function getBatchLogList($obj = null) {
            $args->sender_srl = $obj->sender_srl;
            $args->sort_index = $obj->sort_index?$obj->sort_index:'regdate';
            $args->order_type = $obj->order_type?$obj->order_type:'desc';
            $args->list_count = $obj->list_count?$obj->list_count:20;
            $args->page_count = $obj->page_count?$obj->page_count:10;
            $args->page = $obj->page?$obj->page:1;
            return executeQuery('pointsend.getBatchLogList',$args);
        }

        /**
         * @brief 오늘 보낸/받은 포인트 총 합계를 구함
         */
        function getTodayLog($obj) {
            if(!$obj || !$obj->member_srl) return;
            if(!in_array($obj->type,array('S','R'))) $obj->type = 'S';

            $member_srl = $obj->member_srl;
            $type = $obj->type;

            switch($type) {
                case 'S':
                    $args->sender_srl = $member_srl;
                    break;
                case 'R':
                    $args->receiver_srl = $member_srl;
                    break;
            }

            $args->start_date = date('YmdHis',mktime(0,0,0));
            $args->end_date = date('YmdHis',mktime(24,59,59));

            $output = executeQuery('pointsend.getTodayPointsend',$args);

            return $output->data;
        }

        /**
         * @brief 금일의 포인트 선물 내역 목록을 구함
         * @remarks getTodayLog() 함수와 동작이 같으나 페이징 기능 유무 차이만 있음
         */
        function getTodayLogList($obj) {
            if(!$obj || $obj->member_srl) return;
            if(!in_array($obj->type,array('S','R'))) $obj->type = 'S';

            $member_srl = $obj->member_srl;
            $type = $obj->type;

            switch($type) {
                case 'S':
                    $args->sender_srl = $member_srl;
                    break;
                case 'R':
                    $args->receiver_srl = $member_srl;
                    break;
            }

            $args->start_date = date('YmdHis',mktime(0,0,0));
            $args->end_date = date('YmdHis',mktime(24,59,59));

            $output = executeQuery('pointsend.getTodayPointsendLogList',$args);

            return $output->data;
        }

        /**
         * @brief 특정 IP의 포인트 선물 내역을 구함
         */
        function getLogsByIpaddress($ipaddress) {
            if(!$ipaddress) return;

            $args->ipaddress = $ipaddress;
            $args->order_type = 'desc';
            return executeQuery('pointsend.getPointsendLogs', $args);
        }
    }
?>
<?php
	/*
	 * @class  adModel
	 * @author SMaker (dowon2308@paran.com)
	 * @brief 광고 모듈의 model 객체
	 **/

	require_once(_XE_PATH_.'modules/ad/ad.item.php');

	class adModel extends ad {
		
		/**
		 * @brief 초기화
		 **/
		function init() {
		}

		/**
		 * @brief 대시보드 설정 구함
		 **/
		function getConfig() {
			static $config = null;
			if(is_null($config)) {
				// 대시보드 설정 구함
				$oModuleModel = &getModel('module');
				$config = $oModuleModel->getModuleConfig('ad');
				if(!isset($config->display_today_ad)) $config->display_today_ad  = 'Y';
				if(!isset($config->use_help)) $config->use_help  = 'Y';
				if(!isset($config->join_network)) $config->join_network = 'N';
			}
			return $config;
		}

		/**
		 * @brief 전광판 모듈 설정 구함 (이전 버전 업데이트용)
		 **/
		function getOldConfig() {
			static $_module_info = null;
			if(is_null($_module_info)) {
				// module module_info의 값을 구함
				$oModuleModel = &getModel('module');
				$_module_info = $oModuleModel->getModuleConfig('linead');

				unset($_module_info->grants);
			}
			return $_module_info;
		}

		/**
		 * @brief 전광판 모듈이 존재하는 지 확인 (이전 버전 업데이트용)
		 **/
		function isExistsOldModule() {
			$output = executeQuery('ad.getOldModule');
			if(count($output->data)) return true;
			return false;
		}

		/**
		 * @brief 광고 객체 구함
		 * @return new AdItem($document_srl)
		 **/
		 function getAd($document_srl = 0, $option = null) {
			if(!$document_srl) return new AdItem();

			if(!isset($GLOBALS['__AdItem__'][$document_srl])) {
				$oAd = new AdItem($document_srl, $option->query_id);
				$GLOBALS['__AdItem__'][$document_srl] = $oAd;
			}

			return $GLOBALS['__AdItem__'][$document_srl];
		}

		/**
		 * @brief 여러 개의 광고의 객체를 구함
		 * @return new AdItem($document_srls)
		 **/
		function getAds($document_srls, $is_admin = false) {
			if(is_array($document_srls)) {
				$list_count = count($document_srls);
				$document_srls = implode(',',$document_srls);
			} else {
				$list_count = 1;
			}
			$args->document_srls = $document_srls;
			$args->list_count = $list_count;
			$args->order_type = 'asc';

			$output = executeQuery('ad.getAds', $args);
			$document_list = $output->data;
			if(!$document_list) return;
			if(!is_array($document_list)) $document_list = array($document_list);

			$document_count = count($document_list);
			foreach($document_list as $key => $attribute) {
				$document_srl = $attribute->document_srl;
				if(!$document_srl) continue;

				if(!$GLOBALS['XE_AD_LIST'][$document_srl]) {
					$oAd = null;
					$oAd = new AdItem();
					$oAd->setAttribute($attribute, false);
					$GLOBALS['XE_AD_LIST'][$document_srl] = $oAd;
				}

				$result[$attribute->document_srl] = $GLOBALS['XE_AD_LIST'][$document_srl];
			}

			$output = null;
			if(count($result)) {
				foreach($result as $document_srl => $val) {
					$output[$document_srl] = $GLOBALS['XE_AD_LIST'][$document_srl];
				}
			}

			return $output;
		}

		/**
		 * @brief 광고 목록 구함
		 **/
		function getAdList($obj) {
			// 기본 query id 지정
			$query_id = 'ad.getAdList';

			// module_srl 대신 mid가 넘어왔을 경우는 직접 module_srl을 구해줌
			if($obj->mid) {
				$oModuleModel = &getModel('module');
				$obj->module_srl = $oModuleModel->getModuleSrlByMid($obj->mid);
				unset($obj->mid);
			}

			// 넘어온 module_srl은 array일 수도 있기에 array인지를 체크
			if(is_array($obj->module_srl)) $args->module_srl = implode(',', $obj->module_srl);
			else $args->module_srl = $obj->module_srl;

			if($obj->with_page) {
				// 페이징 기능을 사용 한다면 query id 변경
				$query_id = 'ad.getAdListWithPage';
				$args->page = $obj->page?$obj->page:1;
				$args->page_count = $obj->page_count?$obj->page_count:10;
				$args->list_count = $obj->list_count?$obj->list_count:20;
			}

			$args->list_count = $obj->list_count;
			$args->sort_index = $obj->sort_index?$obj->sort_index:'documents.list_order';
			$args->order_type = $obj->order_type?$obj->order_type:'desc';
			$args->member_srl = $obj->member_srl;
			$args->start_date = date('YmdHis');
			$args->start_regdate = $obj->start_regdate;
			$args->end_regdate = $obj->end_regdate;
			$args->end_date = date('YmdHis');
			$args->is_notice = $obj->is_notice;

			// search option 검색 옵션 정리
			$search_target = $obj->search_target;
			$search_keyword = $obj->search_keyword;
			if($search_target && $search_keyword) {
				switch($search_target) {
					case 'content' :
						if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
						$args->s_content = $search_keyword;
						break;
				}
			}

			// document srl만 뽑아올 경우 query id 변경
			if($obj->select_document_srl) {
				switch($query_id) {
					case 'ad.getAdList':
						$query_id = 'ad.getAdDocumentSrl';
						break;
					case 'ad.getAdListWithPage':
						$query_id = 'ad.getAdDocumentSrlWithPage';
						break;
				}
			}

			if($obj->select_all_ad) {
				switch($query_id) {
					case 'ad.getAdList':
						$query_id = 'ad.getAllAdList';
						break;
					case 'ad.getAdListWithPage':
						$query_id = 'ad.getAllAdListWithPage';
						break;
					case 'ad.getAdListSelectDocumentSrl':
						$query_id = 'ad.getAllAdListSelectDocumentSrl';
						break;
					case 'ad.getAdListNoPageSelectDocumentSrl':
						$query_id = 'ad.getAllAdSelectDocumentSrl';
						break;
				}
			}

			$output = executeQueryArray($query_id, $args);

			// 결과가 없거나 오류 발생시 그냥 return
			if(!$output->toBool()||!count($output->data)) return $output;

			if($obj->simple) return $output;

			$idx = 0;
			$data = $output->data;
			unset($output->data);

			$keys = array_keys($data);
			$virtual_number = $keys[0];

			foreach($data as $key => $attribute) {
				$document_srl = $attribute->document_srl;
				$oAd = null;
				$oAd = new AdItem();
				$oAd->setAttribute($attribute);

				$output->data[$virtual_number] = $oAd;
				$virtual_number --;
			}

			return $output;
		}

		/**
		 * @brief get ad notify List
		 **/
		function getAdNotifyList($obj) {
			// 기본으로 사용할 query id 지정 (몇 가지 검색 옵션에 따라 query id 변경됨)
			$query_id = 'ad.getAdNotifyList';

			// module_srl 대신 mid가 넘어왔을 경우는 직접 module_srl을 구해줌
			if($obj->mid) {
				$oModuleModel = &getModel('module');
				$obj->module_srl = $oModuleModel->getModuleSrlByMid($args->mid);
				unset($args->mid);
			}

			// 넘어온 module_srl은 array일 수도 있기에 array인지를 체크
			if(is_array($obj->module_srl)) $args->module_srl = implode(',', $obj->module_srl);
			else $args->module_srl = $obj->module_srl;

			$args->member_srl = $obj->member_srl;
			$args->start_date = $obj->start_date;
			$args->end_date = $obj->end_date;
			$args->is_notice = $obj->is_notice;

			// 검색 옵션 정리
			if($obj->search_target) {
				switch($obj->search_target) {
					case 'content' :
						if($args->search_keyword) $args->search_keyword = str_replace(' ','%',$args->search_keyword);
						$args->s_content = $args->search_keyword;
						break;
				}
			}

			$output = executeQueryArray($query_id, $args);

			// 결과가 없거나 오류 발생시 그냥 return
			if(!$output->toBool()||!count($output->data)) return $output;

			$idx = 0;
			$data = $output->data;
			unset($output->data);

			$keys = array_keys($data);
			$virtual_number = $keys[0];

			foreach($data as $key => $attribute) {
				$document_srl = $attribute->document_srl;
				$oAd = null;
				$oAd = new AdItem();
				$oAd->setAttribute($attribute);

				$output->data[$virtual_number] = $oAd;
				$virtual_number --;
			}

			return $output;
		}

		/**
		 * @brief module_srl에 해당 하는 광고의 전체 갯수를 가져옴
		 **/
		function getAdCount($module_srl, $search_obj = NULL) {
			if(!$module_srl || !$search_obj) return;

			// Query ID 지정
			$query_id = 'ad.getAdCount';

			// 검색 옵션
			$args->module_srl = $module_srl;
			$args->s_content = $search_obj->s_content;
			$args->s_member_srl = $search_obj->s_member_srl;
			$args->s_user_id = $search_obj->s_user_id;;
			$args->is_notice = $search_obj->is_notice;
			$args->start_date = $search_obj->start_date;

			// 필요에 따라 Query ID 변경
			if($args->s_content || $args->s_member_srl || $args->s_user_id || $args->is_notice) {
				$query_id = 'getAdCountWithDocument';
			}

			$output = executeQuery($query_id, $args);

			// 전체 갯수 return
			return (int)$output->data->count;
		}

		/**
		 * @brief 날짜 덧셈
		 * @return timestamp
		 **/
		function dateAdd($interval, $number, $date) {
			$date_time_array = getdate($date);
			$hours = $date_time_array['hours'];
			$minutes = $date_time_array['minutes'];
			$seconds = $date_time_array['seconds'];
			$month = $date_time_array['mon'];
			$day = $date_time_array['mday'];
			$year = $date_time_array['year'];

			switch ($interval) {
				case 'yyyy':
					$year+=$number;
					break;
				case 'q':
					$year+=($number*3);
					break;
				case 'm':
					$month+=$number;
					break;
				case 'y':
				case 'd':
				case 'w':
					$day+=$number;
					break;
				case 'ww':
					$day+=($number*7);
					break;
				case 'h':
					$hours+=$number;
					break;
				case 'n':
					$minutes+=$number;
					break;
				case 's':
					$seconds+=$number; 
					break;
			}

			return mktime($hours,$minutes,$seconds,$month,$day,$year);
		}

		/**
		 * @brief 날짜 뺄셈
		 * @return timestamp
		 **/
		function dateSubtract($interval, $number, $date) {
			$date_time_array = getdate($date);
			$hours = $date_time_array['hours'];
			$minutes = $date_time_array['minutes'];
			$seconds = $date_time_array['seconds'];
			$month = $date_time_array['mon'];
			$day = $date_time_array['mday'];
			$year = $date_time_array['year'];

			switch ($interval) {
				case 'yyyy':
					$year-=$number;
					break;
				case 'q':
					$year-=($number*3);
					break;
				case 'm':
					$month-=$number;
					break;
				case 'y':
				case 'd':
				case 'w':
					$day-=$number;
					break;
				case 'ww':
					$day-=($number*7);
					break;
				case 'h':
					$hours-=$number;
					break;
				case 'n':
					$minutes-=$number;
					break;
				case 's':
					$seconds-=$number; 
					break;
			}

			return mktime($hours,$minutes,$seconds,$month,$day,$year);
		}

		/**
		 * @brief 두 날짜를 비교
		 * @return 배열 또는 숫자
		 **/
		function dateDiff($startDate, $endDate) {
			if($startDate > $endDate) return;

			$diffTime = $endDate - $startDate;

			$month = floor($diffTime/60/60/24/30);
			$day = floor($diffTime/60/60/24);
			$hour = sprintf('%d', ($diffTime/60/60)%24);
			$minute = sprintf('%d',($diffTime/60)%60);
			$second = sprintf('%d',($diffTime)%60);

			return array('month' => $month, 'day' => $day, 'hour' => $hour, 'minute' => $minute, 'second' => $second);
		}


		function getDefaultAdTimeRange() {
			return '5, 10, 15, 20';
		}

		function getDefaultAdTimeRangeArray() {
			return array(5, 10, 15, 20);
		}

		/**
		 * @brief 광고 시간 범위에 포인트 정보까지 추가하여 return
		 **/
		function getAdTimeRange($module_info = null) {
			// 모듈 정보가 넘어오지 않았다면 그냥 return
			if(!$module_info) return;

			// 광고 시간을 사용하지 않으면 그냥 return
			if($module_info->use_time != 'Y') return;

			$ad_time_range = explode(',', $module_info->ad_time_range?$module_info->ad_time_range:$this->getDefaultAdTimeRange());
			$ad_point = $module_info->ad_point;
			$ad_point_rate = $module_info->ad_point_rate;
			$lng = Context::getLang('ads');

			$range = array();

			$oModuleModel = &getModel('module');
			$point_config = $oModuleModel->getModuleConfig('point');
			$point_name = $point_config->point_name;
			foreach($ad_time_range as $key => $val) {
				$range[$key]['time'] = (int)$val;
				$point = $ad_point_rate?$range[$key]['time'] / $ad_point_rate * $ad_point:$ad_point;
				$range[$key]['title'] = sprintf('%s%s (%s %s)', $range[$key]['time'], $lng->unit_time->hour, $point, $point_name); /// < X시간 (X 포인트) 형식
			}

			return $range;
		}

		/**
		 * @brief 최고 관리자(의 회원 번호를 구함
		 */
		function getAdminMemberSrl() {
			$output = executeQuery('ad.getAdminMemberSrl');
			return $output->data->member_srl;
		}

		/**
		 * @brief 알림 대상 모듈 번호 구하기
		 */
		function getNotifyModuleSrls() {
			$output = executeQueryArray('ad.getNotifyModuleSrls');
			$data = $output->data;
			if(!count($data)) return;

			foreach($data as $key => $val) {
				$module[] = $val->module_srl;
			}

			return join(',', $module);
		}

		/**
		 * @brief 모듈 버전 구함
		 */
		function getVersion() {
			$oModuleModel = &getModel('module');
			$xml_info = $oModuleModel->getModuleInfoXml('ad');
			return $xml_info->version;
		}

		/**
		 * @brief 모듈 버전을 구해서 이미지로 반환 (미완성)
		 */
		function getVersionImg() {
			return $this->getVersion();
		}

        function getRemoteResourceHeader($url, $body = null, $timeout = 3, $method = 'GET', $content_type = null, $headers = array(), $cookies = array(), $post_data = array()) {
            set_include_path(_XE_PATH_.'libs/PEAR');
            require_once('PEAR.php');
            require_once('HTTP/Request.php');

            if(__PROXY_SERVER__!==null) {
                $oRequest = new HTTP_Request(__PROXY_SERVER__);
                $oRequest->setMethod('POST');
                $oRequest->_timeout = $timeout;
                $oRequest->addPostData('arg', serialize(array('Destination'=>$url, 'method'=>$method, 'body'=>$body, 'content_type'=>$content_type, 'headers'=>$headers, 'post_data'=>$post_data)));
            } else {
                $oRequest = new HTTP_Request($url);
                if(count($headers)) {
                    foreach($headers as $key => $val) {
                        $oRequest->addHeader($key, $val);
                    }
                }
                if($cookies[$host]) {
                    foreach($cookies[$host] as $key => $val) {
                        $oRequest->addCookie($key, $val);
                    }
                }
                if(count($post_data)) {
                    foreach($post_data as $key => $val) {
                        $oRequest->addPostData($key, $val);
                    }
                }
                if(!$content_type) $oRequest->addHeader('Content-Type', 'text/html');
                else $oRequest->addHeader('Content-Type', $content_type);
                $oRequest->setMethod($method);
                if($body) $oRequest->setBody($body);

                $oRequest->_timeout = $timeout;
            }

            $oResponse = $oRequest->sendRequest();

            $code = $oRequest->getResponseCode();
            $header = $oRequest->getResponseHeader();
            $body = $oRequest->getResponseBody();
            if($c = $oRequest->getResponseCookies()) {
                foreach($c as $k => $v) {
                    $cookies[$host][$v['name']] = $v['value'];
                }
            }

            if($code > 300 && $code < 399 && $header['location']) {
                return $this->getRemoteResourceHeader($header['location'], $body, $timeout, $method, $content_type, $headers, $cookies, $post_data);
            } 

            if($code != 200) return;

            return array('header' => $header, 'body' => $body);
        }

		/**
		 * @brief 광고 유형으로 MIME(Content-Type) 구함
		 */
		function getAdMimeByAdType($ad_type) {
			if(!$ad_type) $ad_type = $this->ad_type;

			switch($ad_type) {
				case 'image':
					return $this->image_mime;
				case 'video':
					return $this->video_mime;
				case 'flash':
					return $this->flash_mime;
				default:
					return;
			}
		}

		/**
		 * @brief 메시지 코드 구함
		 */
		function getMessageCode($message, $type = 'ads') {
			$msg = $this->getLangCode('msg', 'ads');
			return $msg->$message;
		}

		/**
		 * @brief 언어 코드 구함
		 */
		function getLangCode($code, $type = 'ads') {
			if(!$code || !$type) return; 

			$lng = Context::getLang($type);
			$msg = isset($lng->$code)?$lng->$code:$code;
			return $msg;
		}

		/**
		 * @brief 메시지 출력
		 * @return new Object
		 */
		function returnMessage($error, $message, $message_type = 'ads') {
			if(!$message || !$message_type) return;

			$msg = $this->getMessageCode($message, $message_type);

			return new Object($error, $msg);
		}
		
		function getRss() {
		}
	}
?>

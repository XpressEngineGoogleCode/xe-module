<?php
	/**
	 * @class  adItem
	 * @author SMaker (dowon2308@paran.com)
	 * @brief  광고 모듈의 item class
	 **/

	require_once(_XE_PATH_.'modules/document/document.item.php');

	class AdItem extends documentItem {
		function AdItem($document_srl = 0, $query_id = 'ad.getAdWithDocument') {
			$this->document_srl = $document_srl;
			$this->query_id = $query_id;
			$this->_loadFromDB();
		}

		function setAd($document_srl) {
			$this->document_srl = $document_srl;
			$this->_loadFromDB();
		}

		function _loadFromDB() {
			if(!$this->document_srl) return;
			if(!$this->query_id) $this->query_id = 'ad.getAdWithDocument';

			$obj->document_srl = $this->document_srl;
			$output = executeQuery($this->query_id, $obj);
			if(!$output->toBool()) return;

			$this->setAttribute($output->data);
		}

		function setAttribute($attribute) {
			$this->document_srl = $attribute->document_srl;
			$this->adds($attribute);

			$GLOBALS['__AdItem__'][$this->document_srl] = $this;
		}

		/**
		 * @brief 텍스트로만 이루어진 광고 내용 출력
		 */
		function getAdContentText($cut_size = 0) {
			return parent::getTitleText($cut_size);
		}

		/**
		 * @brief 광고 출력
		 * @param[in] $cut_size
		 * @param[in] $hyperlink
		 * @return 텍스트, 이미지, 동영상, 플래쉬,…
		 */
		function getAdContent($cut_size = 0, $hyperlink = false, $type = 'text') {
			// 지정된 type이 없으면 텍스트로 지정
			if(!$type) $type = 'text';

			// 광고 내용을 구함
			$content = $this->getAdContentText($type=='text'?$cut_size:0);
			$style = $this->getStyleList();

			// 사용된 스타일이 있으면 적용
			if(count($style) || $this->getUrl()) $this->arrangeWithStyle($content, $style, $type, $hyperlink);

			return $content;
		}

		/**
		 * @brief URL
		 */
		function getUrl() {
			return addslashes(htmlspecialchars($this->get('url')));
		}

		/**
		 * @brief URL 열기 대상
		 */
		function getUrlTarget() {
			return $this->get('url_target');
		}

		/**
		 * @brief 남은 시간을 계산해서 출력
		 * @return 배열 또는 문자열
		 **/
		function getRemainingTime($only_diff = false) {
			if($this->get('end_date') == -1) return;
			if($this->get('end_date')<$this->get('start_date')) return;

			$start_date = $this->get('start_date');
			$end_date = $this->get('end_date');

			// ad 모듈의 model 객체 생성
			$oAdModel = &getModel('ad');

			// 시작일과 종료일을 비교
			if($start_date>date('YmdHis')) {
				$is_started = false;
				$diff = $oAdModel->dateDiff(time(),strtotime($start_date));
			} else {
				$is_started = true;
				$diff = $oAdModel->dateDiff(time(),strtotime($end_date));
			}

			$diff['is_started'] = $is_started;

			if($only_diff) return $diff;

			// 시간 언어 구함
			$lng = Context::getLang('ads');

			// 남은 시간에 시간 단위를 붙이기
			if($diff['month']) $msg[] = $diff['month'].$lng->unit_time->month;
			if($diff['day']) $msg[] = $diff['day'].$lng->unit_time->day;
			if($diff['hour']) $msg[] = $diff['hour'].$lng->unit_time->hour;
			if($diff['hour'] || $diff['second']) $msg[] = $diff['minute'].$lng->unit_time->minute;
			if($diff['second']) $msg[] = $diff['second'].$lng->unit_time->second;

			// 배열 합치기
			if(is_array($msg)) $msg = join(' ', $msg);

			return $msg;
		}


		/**
		 * @brief 적용된 글자색 구함
		 */
		function getTextColor() {
			// get applied style list
			$style = $this->getStyleList();
			if(!count($style)) return;

			// extract the text color from style list
			$textColor = str_replace('text_','',$style[array_search('text_', $style)]);

			return $textColor?$textColor:false;
		}

		/**
		 * @brief 적용된 배경색 구함
		 */
		function getBgColor() {
			// 적용된 스타일 구함
			$style = $this->getStyleList();
			if(!count($style)) return false;

			// extract the background color from style list
			$bgColor = str_replace('bg_','',$style[array_search('bg_', $style)]);

			return $bgColor?$bgColor:false;
		}

		/**
		 * @brief 적용된 스타일 구함
		 * @return string
		 */
		function getStyle(){
			return str_replace('|@|',',',$this->get('style'));
		}

		/**
		 * @brief 적용된 스타일을 구해서 배열로 반환
		 * @return array
		 */
		function getStyleList(){
			return $this->get('style')?explode('|@|',$this->get('style')):array();
		}

		function isBold(){
			if(isset($this->is_bold)) return $this->is_bold;

			// 사용된 스타일이 없을 경우 return false
			$style = $this->getStyleList();

			$this->is_bold = false;
			if(!count($style)) return false;

			foreach($style as $key => $val) {
				if($val == 'bold') {
					$this->is_bold = true;
					return true;
				}
			}

			return false;
		}

		function isUnderline(){
			if(isset($this->is_underline)) return $this->is_underline;

			// 사용된 스타일이 없을 경우 return false
			$style = $this->getStyleList();
			if(!count($style)) {
				$this->is_underline = false;
				return false;
			}

			if(array_search('underline', $style)) {
				$this->is_underline = true;
				return true;
			}

			return false;
		}

		function isItalic(){
			if(isset($this->is_italic)) return $this->is_italic;

			// 사용된 스타일이 없을 경우 return false
			$style = $this->getStyleList();
			if(!count($style)) {
				$this->is_italic = false;
				return false;
			}

			if(array_search('italic', $style)) {
				$this->is_italic = true;
				return true;
			}

			return false;
		}

		function getBannerPath(){
			if(isset($this->banner_path)) return $this->banner_path;

			// 사용된 스타일이 없을 경우 return false
			$style = $this->getStyleList();
			if(!count($style)) {
				$this->banner_path = '';
				return false;
			}

			// 스타일 목록에서 배너 주소 추출
			$path = str_replace('site_','',$style[array_search('site_', $style)]);

			$this->banner_path = $path;

			return $path;
		}

		function arrangeWithStyle(&$content, $styles, $type = 'text', $hyperlink = false) {
			if(count($styles) && $type == 'text') {
				foreach($styles as $key => $val) {
					if(strpos($val, 'text_') !== false) {
						$textcolor = str_replace('text_','',$val);
						continue;
					}

					if(strpos($val, 'bg_') !== false) {
						$bgcolor = str_replace('bg_','',$val);
						continue;
					
					}
					if($val == 'bold') {
						$bold = true;
						continue;
					}

					if($val == 'underline') {
						$underline = true;
						continue;
					}

					if($val == 'italic') {
						$italic = true;
						continue;
					}
				}

				if($textcolor || $bgcolor) {
					if($textcolor) $style = sprintf('color:%s;',$textcolor);
					if($bgcolor) $style .= sprintf('background-color:%s;',$bgcolor);
					if($bold) $style .= 'font-weight:bold;';
					if($underline) $style .= 'text-decoration:underline;';
					if($italic) $style .= 'font-style:italic;';
					$content = sprintf('<span style="%s">%s</span>',$style,$content);
				}
			}

			switch($type) {
				case 'image':
					$content = sprintf('<img src="%s" alt="%s" title="%s" />', $this->getBannerPath(), $content, $content);
					break;
				case 'movie':
				case 'flash':
					$content = sprintf('<img src="%s" alt="%s" title="%s" />', $this->getBannerPath(), $content, $content);
					break;
			}

			// URL이 있을 경우 적용
			if($hyperlink && $this->getUrl()) $content = sprintf('<a href="%s" target="%s">%s</a>',$this->getUrl(),$this->getUrlTarget(),$content);
		}

		/**
		 * @brief 확장 변수 구함
		 */
		function getExtraVars() {
			// 확장 변수가 없을 경우 그냥 return
			if(!$this->get('extra_vars')) return;

			return unserialize($this->get('extra_vars'));
		}
	}
?>

<?php
	/**
	 * @file ko.lang.php
	 * @author 퍼니엑스이 (admin@funnyxe.com)
	 * @brief 광고 모듈의 기본 언어팩
	 **/

	// module name
	$lang->ad = '광고';
	$lang->ad_module = '광고 모듈';

	/* dashboard */
		/* config */
		$lang->dashboard->config = '대시보드 설정';

		/* version */
		$lang->dashboard->version = '현재 버전';

		/* description */
		$lang->dashboard->module_description = '광고 모듈은 사이트 내/외의 광고를 위한 프로그램입니다.';

		/* module */
		$lang->dashboard->module->make = '모듈 생성';
		$lang->dashboard->module->setup = '모듈 설정';
		$lang->dashboard->module->select = '모듈 선택';
		$lang->dashboard->module->delete = '모듈 삭제';
		$lang->dashboard->module->search = '모듈 검색';
		$lang->dashboard->module->tab_menus = array(
			'dispAdAdminModuleList' => '모듈 목록',
			'dispAdAdminModuleInfo' => '모듈 정보',
			//'dispAdAdminModuleAdTimeRange' => '광고 시간 범위',
			'dispAdAdminModuleGrantInfo' => '권한 관리',
			'dispAdAdminModuleSkinInfo' => '스킨 관리'
		);

		/* notice */
		$lang->dashboard->notice->write = '공지 작성';
		$lang->dashboard->notice->modify = '공지 수정';
		$lang->dashboard->notice->delete = '공지 삭제';

		/* common */
		$lang->dashboard->common->find_id = 'ID 찾기';
		$lang->dashboard->common->view_help = '도움말을 보시려면 클릭하세요!';
		$lang->dashboard->common->step = '단계';

		/* descriptoin */
		$lang->dashboard->desc->notice->select_module = '공지를 등록할 대상 모듈을 선택해 주세요.';

	$lang->textcolor = '글자색';
	$lang->bgcolor = '배경색';

	$lang->textColor_feature = '글자색 기능';
	$lang->bgColor_feature = '배경색 기능';
	$lang->help_feature = '도움말 기능';
	$lang->ad_notify = '광고 알림 기능';
	$lang->ad_notify_id = '광고 알림 ID';
	$lang->ad_time_range = '광고 시간 범위';
	$lang->allowed_text_color = '허용 글자색';
	$lang->allowed_bg_color = '허용 배경색';
	$lang->today_ad = '오늘의 광고';
	$lang->unlimited = '무제한';
	$lang->start_date = '시작일';
	$lang->start_hour = '시작 시간';
	$lang->start_minute = '시작 분';
	$lang->start_second = '시작 초';
	$lang->display = '표시';
	$lang->cmd_delete_old_config = '기존 설정 삭제';

	// 대시보드에서 사용되는 메뉴
	$lang->cmd_ad_module_list = '광고 모듈 목록';
	$lang->cmd_total_ad_list = '전체 광고 목록';
	$lang->cmd_ad_list = '광고 목록';

	// 대시보드에서 사용되는 입력 항목 설명
	$lang->about_textColor_feature = '등록자가 글자색을 지정할 수 있게 합니다.';
	$lang->about_bgColor_feature = '등록자가 배경색을 지정할 수 있게 합니다.';
	$lang->about_help_feature = '도움말 기능을 사용하면 대시보드 이용 시 도움을 받을 수 있습니다';
	$lang->about_ad_time = '등록자가 원하는 광고 시간을 선택할 수 있습니다.';
	$lang->about_ad_option = '광고 등록 시 옵션을 선택할 수 있습니다.';
	$lang->about_ad_notify = '광고가 종료되면 등록자에게 쪽지로 알려줍니다.';
	$lang->about_ad_notify_id = '광고 알림 시 쪽지를 보낼 ID를 입력해 주세요. 입력 하지 않으시면 최고 관리자 ID로 보내게 됩니다.';
	$lang->about_ad_time_range = '광고 등록 시 지정할 수 있는 광고 시간을 추가해 주세요. (시간 단위)';
	$lang->about_ad_color_range = '광고 등록 시 지정할 수 있는 글자색을 추가해 주세요.';
	$lang->about_ad_bgcolor_range = '광고 등록 시 지정할 수 있는 배경색을 추가해 주세요.';
	$lang->about_ad_point = '광고 등록 시 소모될 포인트를 입력해 주세요.';
	$lang->about_highlight_point = '굵게, 밑줄, 기울임꼴, 글자색 지정, 배경식 지정 할 시 추가적으로 소모될 포인트를 입력해 주세요.';
	$lang->about_each_highlight_point = '사용한 옵션 갯수에 비례';
	$lang->about_ad_limit = '등록할 수 있는 광고를 제한할 수 있습니다..';
	$lang->about_daily_limit = '하루에 등록할 수 있는 광고를 제한할 수 있습니다..';
	$lang->about_weekly_limit = '일 주일 이내에 등록할 수 있는 광고를 제한할 수 있습니다.';
	$lang->about_monthly_limit = '한 달 이내에 등록할 수 있는 광고를 제한할 수 있습니다.';
	$lang->about_select_ad_module = '공지를 등록할 모듈을 선택해 주세요.';
	$lang->about_start_date = '광고 시작일을 선택해 주세요.';
	$lang->about_ad_type = '한 번 설정하시면 변경할 수 없으니 신중히 선택해주세요.';
	$lang->about_ad_type2= '광고 유형이 바뀌면 기존 데이터에 영향을 끼칠 수 있어 변경하실 수 없습니다.<br />(※ 부득이하게 변경하고자 한다면 phpMyAdmin 등의 툴을 이용하여 변경해 주세요.)';

	// 대시보드에서 사용되는 도움말
	$lang->help_manage_grant = '대시보드를 사용할 수 있는 회원을 지정할 수 있습니다.';
	$lang->help_mid = '모듈 이름은 접속 주소와 관련이 있습니다.\n\n예를 들어 모듈 이름을 Test 라고 하면 실제 접속 주소는\n\n'.getFullUrl('','mid','Test').'\n\n가 됩니다.';
	$lang->help_module_category = '생성된 모듈을 효율적으로 분류하여 관리하기 위한 기능입니다.\n\n그다지 중요하지 않은 항목입니다.';
	$lang->help_browser_title = '브라우저의 제목에 나타날 모듈의 이름을 입력해 주세요.\n\n위의 모듈 이름과는 다릅니다.';
	$lang->help_openapi = 'OpenAPI란 누구나 사용할 수 있도록 공개된 API를 말합니다.\n\n광고 서비스를 사이트 외의 다른 사람들에게도 서비스를 할 수 있도록 하는 기능입니다.';

	// 대시보드에서 검색할 대상
	$lang->_search_target_list = array(
		'content' => '내용',
		'user_id' => '아이디',
		'member_srl' => '회원 번호',
		'user_name' => '사용자 이름',
		'nick_name' => '닉네임',
		'is_notice' => '공지사항',
		'tags' => '검색 키워드',
		'click_count' => '클릭 수 (이상)',
		'regdate' => '등록일',
		'ipaddress' => 'IP 주소'
	);

	// 대시보드 메뉴
	$lang->_dashboard = '대시보드';
	$lang->dashboard_menus = array(
		'DefaultSetup' => array('title' => '기본 설정', 'act' => 'dispAdAdminConfig', 'help' => '대시보드와 관련된 설정을 합니다'),
		'ManageAd' => array('title' => '광고 관리', 'act' => 'dispAdAdminList', 'is_total' => 'Y', 'help' => '등록된 광고를 관리합니다'),
		'ManageNotice' => array('title' => '공지 관리', 'act' => 'dispAdAdminNoticeList', 'help' => '등록된 공지를 관리하고 새로운 공지를 등록할 수 있습니다'),
		'ManageModule' => array('title' => '모듈 관리', 'act' => 'dispAdAdminModuleList', 'help' => '광고를 등록할 수 있는 모듈을 관리합니다'),
		'PluginSetup' => array('title' => '연동 설정', 'act' => 'dispAdAdminPluginSetup', 'help' => '다른 모듈과 연동 되어 작동할 수 있는 추가 기능을 관리합니다'),
	);

	// 스킨에서 사용 되는  메뉴
	$lang->cmd_homepage = '처음 화면';
	$lang->cmd_my_ad = '내가 등록한 광고';

	// 공통으로 사용되는 메뉴
	$lang->cmd_view_progressing_ad = '진행중인 광고 보기';

	// 공통으로 사용되는 버튼
	$lang->cmd_manage_ad = '광고 관리';

	// 공통으로 사용되는 입력 항목 및 단어
	$lang->ad_count = '광고 수';
	$lang->ad_type = '광고 유형';
	$lang->ad_content = '광고 내용';
	$lang->ad_reg = '광고 등록';
	$lang->ad_modify = '광고 수정';
	$lang->ad_time = '광고 시간';
	$lang->ad_style = '광고 스타일';
	$lang->ad_option = '광고 옵션';
	$lang->remaining_time = '남은 시간';
	$lang->url_target = 'URL 열기 대상';
	$lang->new_window = '새 창';
	$lang->current_window = '현재 창';
	$lang->ad_point = '광고 포인트';
	$lang->highlight_point = '강조 포인트';
	$lang->ad_point_rate = '광고 포인트 단위';
	$lang->daily_limit = '하루 등록 제한';
	$lang->weekly_limit = '일 주일 등록 제한';
	$lang->monthly_limit = '월 등록 제한';
	$lang->ad_limit = '전체 등록 제한';
	$lang->bold = '굵게';
	$lang->underline = '밑줄';
	$lang->italic = '기울임꼴';
	$lang->text_color = '글자색';
	$lang->bg_color = '배경색';
	$lang->hourly = '시간 당';
	$lang->daily = '일 당';
	$lang->point = '포인트';
	$lang->start_date = '시작일';
	$lang->end_date = '종료일';
	$lang->linead = '한줄 광고';
	$lang->banner = '배너';
	$lang->banner_file = '배너 파일';
	$lang->banner_image = '배너 이미지';
	$lang->image_banner = '배너 (이미지)';
	$lang->movie_banner = '배너 (동영상)';
	$lang->flash_banner = '배너 (플래쉬)';
	$lang->about_banner_image = '유효한 배너 주소를 입력해 주세요.<br />외부에서 접속이 불가능한 경우 등록이 불가능합니다.';

	$lang->hour = '시';
	$lang->minute = '분';
	$lang->second = '초';

	/* dashboard */
	$lang->ads->dashboard = '대시보드';
	$lang->ads->dashboard_menus = array(
		'dispAdAdminConfig' => '기본 설정',
		'dispAdAdminList' => '광고 관리',
		'dispAdAdminNoticeList' => '공지 관리',
		'dispAdAdminModuleList' => '모듈 관리'
		//'dispAdAdminPluginSetup' => '연동 설정'
	);

	/* status */
	$lang->ads->status->published = '발행';
	$lang->ads->status->wait = '대기';
	$lang->ads->status->end = '종료';

	/* unit time */
	$lang->ads->unit_time->year = '년';
	$lang->ads->unit_time->month = '개월';
	$lang->ads->unit_time->week = '주';
	$lang->ads->unit_time->day = '일';
	$lang->ads->unit_time->hour = '시간';
	$lang->ads->unit_time->minute = '분';
	$lang->ads->unit_time->second = '초';

	$lang->ads->author = '게시자';
	$lang->ads->regdate = '게시일';

	$lang->ads->ad_network = '광고 네트워크';
	$lang->ads->join = '참여';

	$lang->ads->help->ad_network = '광고 네트워크는 효율적인 광고를 위한 네트워크입니다.\n\n광고를 광고 네트워크에 참여한 다른 사이트에 동시에 게시할 수 있어\n\n효율적인 광고를 할 수 있게 합니다.';

	/* message */
	$lang->ads->msg->ad_limit_over = '광고 등록 제한 %s를 초과하셨습니다.';
	$lang->ads->msg->daily_limit_over = '하루 등록 제한 %s개를 초과하셨습니다.';
	$lang->ads->msg->weekly_limit_over = '일 주일 등록 제한 %s개를 초과하셨습니다.';
	$lang->ads->msg->monthly_limit_over = '월 등록 제한 %s개를 초과하셨습니다.';
	$lang->ads->msg->not_enough_point = '포인트가 부족합니다.';
	$lang->ads->msg->not_exists_ad = '존재하지 않는 광고입니다.';
	$lang->ads->msg->not_exists_remaining_ads = '진행중인 광고가 없습니다.';
	$lang->ads->msg->not_exists_registered_ads = '등록된 광고가 없습니다.';
	$lang->ads->msg->not_exists_own_registered_ads = '등록하신 광고가 없습니다.';
	$lang->ads->msg->not_exists_notice = '등록된 공지가 없습니다.';
	$lang->ads->msg->please_login = '로그인 후 사용하실 수 있습니다.';
	$lang->ads->msg->invalid_color = '글자색과 배경색이 같을 수는 없습니다.';
	$lang->ads->msg->not_exists_today_ad = '오늘 등록된 광고가 없습니다.';
	$lang->ads->msg->choose_color = '색을 선택해 주세요.';
	$lang->ads->msg->choose_correct_color = '올바른 색을 입력해 주세요.';
	$lang->ads->msg->already_inserted = '이미 추가하셨습니다.';
	$lang->ads->msg->input_ad_time = '광고 시간을 선택해 주세요.';
	$lang->ads->msg->invalid_ad_time = '올바른 방법으로 광고 시간을 선택해 주세요.';
	$lang->ads->msg->invalid_banner_image = '존재하지 않는 이미지 파일입니다.';
	$lang->ads->msg->invalid_banner_video = '존재하지 않는 동영상 파일입니다.';
	$lang->ads->msg->invalid_banner_flash = '존재하지 않는 플래쉬 파일입니다.';
	$lang->ads->msg->isnot_banner_image = '올바른 이미지 파일이 아닙니다.';
	$lang->ads->msg->isnot_banner_video = '올바른 동영상 파일이 아닙니다.';
	$lang->ads->msg->isnot_banner_flash = '올바른 플래쉬 파일이 아닙니다.';
	$lang->ads->msg->cannot_delete_default_color = '기본 색은 삭제하실 수 없습니다.';

	$lang->ads->msg->ad_deleted = '%d/%d개의 광고가 삭제되었습니다.';
	$lang->ads->msg->notice_deleted = '%d/%d개의 공지가 삭제되었습니다.';
	$lang->ads->msg->need_update = '기존 전광판 모듈의 설정이 있습니다.<br />광고 모듈로 데이터를 이전하시겠습니까?<br /><br />';

	$lang->ads->confirm->delete_ad = '선택하신 광고를 삭제하시겠습니까?\n\n삭제하시는 경우 되돌릴 수 없으니 주의하시기 바랍니다';
	$lang->ads->confirm->delete_notice = '선택하신 공지를 삭제하시겠습니까?\n\n삭제하시는 경우 되돌릴 수 없으니 주의하시기 바랍니다.';

	$lang->ads->notify->linead->title = '[알림] 한줄 광고가 종료되었습니다.';
	$lang->ads->notify->linead->content = '안녕하세요, %s(%s)님!<br /><br />%s님이 등록하신 한줄 광고가 종료되었습니다.<br /><br />------------------------<br />내용 : %s';
	$lang->ads->notify->banner->title = '[알림] 배너 광고가 종료되었습니다.';
	$lang->ads->notify->banner->content = '안녕하세요, %s(%s)님!<br /><br />%s님이 등록하신 배너 광고가 종료되었습니다.<br /><br />------------------------<br />사이트 이름 : %s<br />사이트 주소 : %s';


	// 대시보드에서 사용되는 메시지
	$lang->msg_is_not_administrator = '관리자만 접속이 가능합니다.';
	$lang->msg_cart_is_null2 = '삭제하실 광고를 선택해 주세요.';
	$lang->msg_checked_ad_is_deleted = '%d개의 광고가 삭제되었습니다.';
	$lang->msg_checked_notice_is_deleted = '%d개의 공지가 삭제되었습니다.';
	$lang->msg_delete_old_config = '정말 기존 설정을 삭제하시겠습니까?<br />삭제하시는 경우 되돌릴 수 없으니 주의하세요.';
	$lang->msg_need_migration = '기존 전광판 모듈의 설정이 남아있습니다.<br />광고 모듈로 데이터를 이전하시겠습니까?<br /><br />';
	$lang->confirm_delete_ad = '선택하신 광고를 삭제하시겠습니까?\n\n삭제하시는 경우 되돌릴 수 없으니 주의하세요\n\n(관리자 페이지에서 삭제하는 경우 등록한 회원에게 포인트를 돌려주지 않습니다.)';
	$lang->confirm_delete_notice = '선택하신 공지를 삭제하시겠습니까?\n\n삭제하시는 경우 되돌릴 수 없으니 주의하세요.';
	$lang->confirm_save = '정말 저장하시겠습니까?';

	// 광고 시간 알림에서 사용되는 메시지
	$lang->notify_title = array(
		'typeA' => '[알림] 전광판 광고가 종료되었습니다.',
		'typeB' => '[알림] 배너 광고가 종료되었습니다.'
	);
	$lang->notify_content = array(
		'typeA' => '안녕하세요, %s님!<br />%s님이 등록하신 전광판 광고가 종료되었음을 알려 드립니다.<br/>종료된 광고는 삭제 되어 보관되지 않습니다.<br />감사합니다 :D<br /><br /><strong>내용</strong> : %s<br /><strong>등록일</strong> : %s<br /><strong>종료일</strong> : %s',
		'typeB' => '안녕하세요, %s님!<br />%s님이 등록하신 배너 광고가 종료되었음을 알려 드립니다.<br />종료된 광고는 삭제 되어 보관되지 않습니다.<br/>감사합니다 :D<br /><br />사이트 이름 : %s'
	);

	$lang->help_index = '<p>광고 모듈을 처음 사용하세요? 혹은 의문점이 있으시다면 잘 찾아오셨습니다!</p>이곳은 광고 모듈 도움말입니다.';
	$lang->xe_admin_page = 'XE 관리자 페이지';
	$lang->cmd_clean_data = '데이터 정리';
	$lang->cmd_data_init = '데이터 초기화';

	$lang->site_name = '사이트 이름';
?>

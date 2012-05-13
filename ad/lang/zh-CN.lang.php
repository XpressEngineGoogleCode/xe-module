<?php
    /**
     * @file zh-CN.lang.php
     * @author 周彤 (tong5945@gmail.com)
     * @brief  广告(ad) 模块的基本语言包
     **/
	
	// 模块名称
	$lang->ad = '广告';
	$lang->ad_module = '广告模块';

	// 模块介绍
	$lang->about_ad = '消耗一定的积分，而后可以在某一时段显示广告的模块。';
	$lang->about_linead = '消耗一定的积分，而后可以在某一时段显示广告的模块。';
	$lang->current_version = '现今版本';

	// 板块使用文字
	$lang->textColor_feature = '文字颜色职能';
	$lang->bgColor_feature = '背景颜色职能';
	$lang->help_feature = '帮助职能';
	$lang->ad_notify = '广告公告职能';
	$lang->ad_time_range = '广告时间范围';
	$lang->allowed_text_color = '允许文字颜色';
	$lang->allowed_bg_color = '允许背景颜色';
	$lang->cmd_module_setup = '模块设置';
	$lang->cmd_make_module = '模块生成';
	$lang->cmd_delete_module = '模块删除';
	$lang->cmd_select_module = '选择模块';
	$lang->cmd_insert_notice = '公告登记';
	$lang->cmd_modify_notice = '公告修改';
	$lang->cmd_delete_notice = '公告删除';
	$lang->cmd_view_help_ballon = '如果想要浏览帮助，请点击！';
	$lang->today_ad = '今日广告';
	$lang->unlimited = '无限制';
	$lang->start_date = '开始日期';
	$lang->start_hour = '开始时间';
	$lang->start_minute = '开始分钟';
	$lang->start_second = '开始秒钟';

	// 板块使用菜单
	$lang->cmd_ad_module_list = '广告模块目录';
	$lang->cmd_total_ad_list = '全部广告目录';
	$lang->cmd_ad_list = '广告目录';

	// 板块使用输入目录介绍
	$lang->about_textColor_feature = '登记者允许改变文字颜色。';
	$lang->about_bgColor_feature = '登记者允许改变文字颜色。';
	$lang->about_help_feature = '使用帮助职能，可以在使用板块时有所帮助。';
	$lang->about_ad_time = '登记者可以选择广告时间。';
	$lang->about_ad_notify = '广告结束时会向登记者发送短信提醒。';
	$lang->about_ad_time_range = '广告登记时，添加可选择的广告时间。（时间段）';
	$lang->about_ad_color_range = '广告登记时，添加可选择的文字颜色。';
	$lang->about_ad_bgcolor_range = '广告登记时，添加可选择的背景颜色。';
	$lang->about_ad_point = '填写广告登记时所消耗的积分。';
	$lang->about_highlight_point = '加粗，下划线，斜体，选择文字颜色，背景颜色，登记广告时若使用以上修饰文字功能，请添加追加消耗的积分。';
	$lang->about_each_highlight_point = '使用的修饰文字功能个数比例。';
	$lang->about_daily_limit = '一天登记广告限制。';
	$lang->about_weekly_limit = '一周登记广告限制。';
	$lang->about_monthly_limit = '一个月广告登记限制。';
	$lang->about_select_module = '请选择广告登记模块。';
	$lang->about_start_date = '请选择开始日期。
如果你不选择登记日期，将按照今天的日期自动选择。';

	// 板块使用的帮助文字
	$lang->help_manage_grant = '可以选择使用板块的使用者。';
	$lang->help_mid = '模块名称链接地址有关联。\n\n例如，模块名称为 Test ，可以真实连接地址\n\n'.getFullUrl('','mid','Test').'\n\n。';
	$lang->help_module_category = '已生成的模块有可以分类有效率地管理的职能。\n\n不是很重要的项目。';
	$lang->help_browser_title = '书写浏览器模块名称。\n\n上端的模块名称不同。';
	$lang->help_openapi = 'OpenAPI란 누구나 사용할 수 있도록 공개된 API를 말합니다.\n\n광고 서비스를 사이트 외의 다른 사람들에게도 서비스를 할 수 있도록 하는 기능입니다.';

    // 대시보드에서 검색할 대상
    $lang->_search_target_list = array(
        'content' => '内容',
        'user_id' => '用户名',
        'member_srl' => '会员号码',
        'user_name' => '使用者名称',
        'nick_name' => '昵称',
        'is_notice' => '公告',
        'tags' => '搜索关键词',
        'click_count' => '点击次数（以上）',
        'regdate' => '登记日期',
        'ipaddress' => 'IP地址',
    );

	// 대시보드 메뉴
	$lang->dashboard = '板块';
	$lang->dashboard_menus = array(
		'DefaultSetup' => array('title' => '基本设置', 'act' => 'dispAdAdminConfig', 'help' => '板块有关项目设置'),
		'ManageAd' => array('title' => '广告管理', 'act' => 'dispAdAdminList', 'is_total' => 'Y', 'help' => '登记的广告管理'),
		'ManageModule' => array('title' => '模块管理', 'act' => 'dispAdAdminModuleList', 'help' => '广告登记的模块管理'),
		'ManageNotice' => array('title' => '公告管理', 'act' => 'dispAdAdminNoticeList', 'help' => '公告进行管理'),
		'PluginSetup' => array('title' => '添加职能设置', 'act' => 'dispAdAdminPluginSetup', 'help' => '其他模块连锁运转的添加职能设置管理'),
		'help' => array('title' => '帮助', 'act' => 'dispAdAdminHelp', 'help' => '查看广告模块帮助')
	);

	// 스킨에서 사용 되는  메뉴
	$lang->cmd_homepage = '首页';
	$lang->cmd_my_ad = '我的广告';

	// 공통으로 사용되는 메뉴
	$lang->cmd_view_progressing_ad = '正在进行中的广';

	// 공통으로 사용되는 버튼
	$lang->cmd_manage_ad = '广告管理';

	// 공통으로 사용되는 입력 항목 및 단어
	$lang->ad_count = '广告数量';
	$lang->ad_purpose = '广告用途';
	$lang->ad_content = '广告内容';
	$lang->ad_reg = '广告登记';
	$lang->ad_time = '广告时间';
	$lang->ad_style = '广告风格';
	$lang->ad_option = '广告选项';
	$lang->remaining_time = '剩余时间';
	$lang->url_target = 'URL打开对象';
	$lang->new_window = '打开新窗口';
	$lang->current_window = '打开现有窗口';
	$lang->ad_point = '广告积分';
	$lang->highlight_point = '强调积分';
	$lang->ad_point_rate = '广告积分单位';
	$lang->daily_limit = '一天登记极限';
	$lang->weekly_limit = '一周登记极限';
	$lang->monthly_limit = '一个月登记极限';
	$lang->bold = '加粗';
	$lang->underline = '下划线';
	$lang->italic = '斜体';
	$lang->text_color = '文字颜色';
	$lang->bg_color = '背景颜色';
	$lang->hourly = '每个小时';
	$lang->daily = '一天';
	$lang->point = '积分';
	$lang->start_date = '开始时间';
	$lang->end_date = '结束时间';

	$lang->hour = '时';
	$lang->minute = '分';
	$lang->second = '秒';

	$lang->time_word = array('year' => '年', 'month' => '个月', 'week' => '周','day' => '日', 'hour' => '个小时', 'minute' => '分', 'second' => '秒');

	// 공통으로 사용되는 메시지
	$lang->msg_daily_limit_over = '一天超过登记极限 %s个。';
	$lang->msg_weekly_limit_over = '一周超过登记极限 %s个。';
	$lang->msg_monthly_limit_over = '一个月超过登记极限 %s个。';
	$lang->msg_not_enough_point = '积分不足。';
	$lang->msg_not_exists_ad = '此广告不存在。';
	$lang->msg_not_exists_ads = '现在没有正在进行中的广告。';
	$lang->msg_not_exists_ads2 = '没有已登记的广告。';
	$lang->msg_please_login = '登录后可以使用。';
	$lang->msg_invalid_color = '文字颜色和背景颜色不可以选择同一颜色。';
	$lang->msg_not_exists_today_ad = '今天没有登记的广告。';
	$lang->msg_choose_color = '请选择颜色。';
	$lang->msg_choose_correct_color = '请选择准确的颜色。';
	$lang->msg_already_inserted = '已经添加。';
	$lang->msg_input_ad_time = '请选择广告时间。';
	$lang->msg_invalid_ad_time = '请选择正确的广告时间。';

	// 대시보드에서 사용되는 메시지
	$lang->msg_is_not_administrator = '只允许管理员访问。';
	$lang->msg_cart_is_null2 = '请选择需要删除的广告。';
    $lang->msg_checked_ad_is_deleted = '%d个广告已经删除。';
	$lang->confirm_delete_ad = '确定要删除所选择的广告吗？\n\n在管理员的管理网页删除广告将无法返还登记者所消耗的积分。';
	$lang->confirm_delete_notice = '确定要删除所选择的公告吗？\n\n如果删除，将无法复原。';

	// 광고 시간 알림에서 사용되는 메시지
	$lang->notify_title = array(
		'typeA' => '[通知] 电光版广告已经结束。',
		'typeB' => '[通知] 横幅广告已经结束。'
	);
	$lang->notify_content = array(
		'typeA' => '你好, %s。<br />%s登记的电光版已经结束，以此通知。<br/>已经结束的广告将会删除，无法保留。<br />谢谢。 :D<br /><br /><strong>内容</strong> : %s<br /><strong>登记日期</strong> : %s<br /><strong>结束日期</strong> : %s',
		'typeB' => '你好, %s。<br />%s登记的横幅广告已经结束，以此通知。<br /><br />网站名称 : %s<br /><br />横幅广告形象 : %s'
	);

	$lang->help_index = '<p>第一次使用广告模块吗？或者有什么疑点，欢迎到来。</p>这里是广告模块的帮助处。';
	$lang->xe_admin_page = 'XE 관리자 페이지';
?>

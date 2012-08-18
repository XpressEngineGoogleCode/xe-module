<?php
/**
 * @class loginlogAdminView
 * @author SMaker (admin@funnyxe.com)
 * @brief loginlog 모듈의 controller class
 **/

class loginlogAdminView extends loginlog
{
	/**
	 * @brief 초기화
	 */
	function init()
	{
		// 템플릿 폴더 지정
		$this->setTemplatePath($this->module_path.'tpl');
	}

	/**
	 * @brief 로그인 기록 열람
	 */
	function dispLoginlogAdminList()
	{
		$oModel = &getModel('loginlog');
		$config = $oModel->getModuleConfig();

		if(!isset($config->listSetting) || !count($config->listSetting))
		{
			$config->listSetting = array('member.nick_name', 'member.user_id', 'member.email_address', 'loginlog.ipaddress', 'loginlog.regdate');
		}

		$columnList = $config->listSetting;
		array_push($columnList, 'loginlog.is_succeed');
		array_push($columnList, 'loginlog.member_srl');

		Context::set('loginlog_config', $config);

		// 목록을 구하기 위한 옵션
		$args->page = Context::get('page'); ///< 페이지
		$args->list_count = 30; ///< 한페이지에 보여줄 기록 수
		$args->page_count = 10; ///< 페이지 네비게이션에 나타날 페이지의 수
		$args->sort_index = 'loginlog.regdate';
		$args->order_type = 'desc';

		$search_keyword = Context::get('search_keyword');
		$search_target = trim(Context::get('search_target'));

		if($search_keyword)
		{
			switch($search_target)
			{
				case 'member_srl':
					$args->member_srl = (int)$search_keyword;
					break;
				case 'user_id':
					$args->s_user_id = $search_keyword;
					array_push($columnList, 'member.user_id');
					break;
				case 'user_name':
					$args->s_user_name = $search_keyword;
					array_push($columnList, 'member.user_name');
					break;
				case 'nick_name':
					$args->s_nick_name = $search_keyword;
					array_push($columnList, 'member.nick_name');
					break;
				case 'regdate':
					$args->s_regdate = $search_keyword;
					array_push($columnList, 'loginlog.regdate');
					break;
				case 'ipaddress':
					$args->s_ipaddress = $search_keyword;
					array_push($columnList, 'loginlog.ipaddress');
					break;
			}
		}

		$columnList = array_unique($columnList);

		$output = executeQueryArray('loginlog.getLoginlogListWithinMember', $args, $columnList);

		// 템플릿에 쓰기 위해 Context::set
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('log_list', $output->data);
		Context::set('page_navigation', $output->page_navigation);

		// 템플릿 파일 지정
		$this->setTemplateFile('index');
	}

	function dispLoginlogAdminSetting()
	{
		$oModel = &getModel('loginlog');
		$config = $oModel->getModuleConfig();

		Context::set('config', $config);

		$this->setTemplateFile('setting');
	}

	function dispLoginlogAdminArrange()
	{
		$this->setTemplateFile('arrange');
	}
}

/* End of file : loginlog.admin.view.php */
/* Location : ./modules/loginlog/loginlog.admin.view.php */
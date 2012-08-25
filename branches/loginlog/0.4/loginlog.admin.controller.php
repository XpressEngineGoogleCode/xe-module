<?php
/**
 * @class loginlogAdminController
 * @author 퍼니엑스이 (admin@funnyxe.com)
 * @brief loginlog 모듈의 admin controller class
 **/

class loginlogAdminController extends loginlog {
	/**
	 * @brief 초기화
	 */
	function init() {
	}

	/**
	 * @brief 설정 저장
	 */
	function procLoginlogAdminInsertConfig() {
		$oLoginlogModel = &getModel('loginlog');
		$config = $oLoginlogModel->getModuleConfig();

		$config->delete_logs = Context::get('delete_logs');
		if(!$config->delete_logs) $config->delete_logs = 'N';

		unset($config->body);
		unset($config->_filter);
		unset($config->error_return_url);
		unset($config->act);
		unset($config->module);

		$oModuleController = &getController('module');
		$oModuleController->insertModuleConfig('loginlog', $config);

		$this->setMessage('success_saved');
	}

	/**
	 * @brief 설정 저장
	 */
	function procLoginlogAdminSaveListSetting()
	{
		$oLoginlogModel = &getModel('loginlog');
		$config = $oLoginlogModel->getModuleConfig();

		unset($config->body);
		unset($config->_filter);
		unset($config->error_return_url);
		unset($config->act);
		unset($config->module);

		$config->listSetting = explode('|@|', Context::get('listsetting'));

		$oModuleController = &getController('module');
		$oModuleController->insertModuleConfig('loginlog', $config);

		$this->setMessage('success_saved');
	}
	/**
	 * @brief 기록 초기화
	 */
	function procLoginlogAdminInitLogs() {
		if(Context::get('expire_date'))
		{
			$args->expire_date = Context::get('expire_date');
		}

		$msg_code = 'success_reset';

		$output = executeQuery('loginlog.initLoginlogs', $args);
		debugPrint(Context::getRequestVars());
		if(!$output->toBool()) $msg_code = 'msg_failed_reset_logs';

		$this->setMessage($msg_code);
	}
}
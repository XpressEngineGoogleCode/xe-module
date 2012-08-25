<?php
/**
 * @class loginlogModel
 * @author SMaker (admin@funnyxe.com)
 * @brief loginlog 모듈의 model class
 **/

class loginlogModel extends loginlog
{
	/**
	 * @brief 초기화
	 */
	function init()
	{
	}

	/**
	 * @brief 모듈의 global 설정 구함
	 */
	function getModuleConfig()
	{
		static $config = null;
		if(is_null($config))
		{
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('loginlog');
		}

		return $config;
	}

	function getLoginlogList()
	{
	}

	function getLoginlogListForAdmin()
	{
	}
}
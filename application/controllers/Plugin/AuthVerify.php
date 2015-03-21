<?php
class AuthVerify extends Zend_Controller_Plugin_Abstract
{
	

	protected $_userid;

	public function __construct()
	{
		$this -> _userid = Zend_Auth::getInstance()->getIdentity();
	}
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		
		$module     = $request -> module;
		$controller = $request -> controller;
		$action     = $request -> action;

		$resource = strtoupper($controller);
		$action   = strtoupper($action);


		if (!$this -> _userid)
		{
			$controller = 'users';
			$action     = 'login';
		}

		$request -> setModuleName($module);
		$request -> setControllerName($controller);
		$request -> setActionName($action);
	}




}
<?php
class My_Center_Controller extends Zend_Controller_Action
{

	/**
	 *
	 * Enter description here ...
	 * @var Zend_Controller_Request_Http
	 */
	protected $_request = null;
	
	
	
	
	/**
	 * @var Zend_Auth
	 */
	protected $auth = null;
	
	/**
	 * @var Zend_Log
	 */
	protected $logger = null;
	

	
	
	protected $apiConfig = null;
	
	
	public function init() {
		parent::init();
		$this->_helper->viewRenderer->setNoRender(true);
	
	}
	
	
	
	public function hasAccess($APIKey){
		if ($this->apiConfig["key"] == $APIKey){
			return true;
		}
		return false;
	}
	


	
	public function returnJson($status, $msg, $data=array(), $debugArray = array()){
		$theMsg['status'] = (int) $status;
		$theMsg['message'] = $msg;
		$theMsg['data'] = $data;
		$theMsg['debug'] = $debugArray;
		$theMsg['post'] = $_POST;
		$theMsg['get'] = $_GET;
		$this->_response->appendBody(Zend_Json::encode($theMsg));
		$this->_response->sendResponse();
		exit(1);
	}
	
}
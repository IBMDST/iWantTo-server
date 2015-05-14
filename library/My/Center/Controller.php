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
	
	
	public function init() 
	{
		parent::init();
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_response->setHeader("Access-Control-Allow-Origin", "*");
		$this->_response->setHeader("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
		$this->_response->setHeader("Access-Control-Allow-Methods", " OPTIONS, HEAD, POST, PUT, GET, DELETE");	
		
		if ($this->_request->getMethod() == 'DELETE'  )
		{
			try 
			{
				$id = new MongoId($this->_request->getActionName('id'));
			}
			catch (Exception $e)
			{
				$this->returnJson(400, 'Parameters error');
			}
		
			$this->_request->setParam('id', strtolower($this->_request->getActionName('id')));
			$this->_request->setActionName('index');		
		}
		
		if ($this->_request->getMethod() == 'PUT'  )
		{
			try
			{
				$id = new MongoId($this->_request->getActionName('id'));
			}
			catch (Exception $e)
			{
				$this->returnJson(400, 'Parameters error');
			}
			
			parse_str(file_get_contents('php://input'), $data);
			$data = array_merge($_GET, $_POST, $data);
			
			if (array_key_exists('subject', $data) && array_key_exists('description', $data) ) {
				$this->setParam('subject', $data['subject']);
				$this->setParam('description', $data['description']);
				if (array_key_exists('when', $data)) {
					$this->setParam('when', $data['when']);;
				}
				if (array_key_exists('where', $data)) {
					$this->setParam('where', $data['where']);;
				}
				if (array_key_exists('fixed', $data)) {
					$this->setParam('fixed', $data['fixed']);;
				}
			}
			
			if (array_key_exists('comment', $data) && array_key_exists('stars', $data)) {
				$this->setParam('comment', $data['comment']);
				$this->setParam('stars', $data['stars']);;
			}
			
			
			
			$this->setParam('id', strtolower($this->_request->getActionName('id')));
			$this->_request->setActionName('index');
		}
		
		
		

		
	}
	
	
	
	public function hasAccess($APIKey){
		if ($this->apiConfig["key"] == $APIKey){
			return true;
		}
		return false;
	}
	


	
	public function returnJson($status, $msg, $data=array(), $debugArray = array()){
		
		if($status == 200)
		{
			$this->_response->setHttpResponseCode($status);
			$this->_response->appendBody(Zend_Json::encode($data));
			$this->_response->sendResponse();
			exit(1);	
		}
		else
		{
			$this->_response->setHttpResponseCode($status);
			$this->_response->appendBody($msg);
			$this->_response->sendResponse();
			exit(1);
			
		}
		
	}
	
	public function __call($method, $args)
	{
		if ('Action' == substr($method, -6)) {
			// If the action method was not found, render the error template
			$this->returnJson(404, "Page not found");
		}
	
		// all other methods throw an exception
		throw new Exception('Invalid method "' . $method . '" called', 500);
	}
	
	
}
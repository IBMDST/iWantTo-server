<?php

class UsersController extends My_Center_Controller
{
	public function loginAction()
	{

			
	
		if($this->_request->getMethod() == 'GET')
		{

			
// 			if (!$this->_request->has('username') || !$this->_request->has('password') ) 
// 			{
// 				$this->returnJson(0, 'Please login first');
// 			}
			
			
			
			$username = $this->_request->getParam('username');
			$password = $this->_request->getParam('password');
			
			$username = 'Shaon';
			$password = "123";
			try 
			{
				if (Zend_Auth::getInstance()->hasIdentity()) 
				{
					$this->returnJson(1, "You have already login");
				}
				
				
				$authAdapter = new My_Auth_Auth($username, $password);
				$loginResult = $authAdapter->authenticate();
				if($loginResult -> isValid())
				{
					$this->returnJson(1, 'Login Successfully');
				}
				else 
				{
					$this->returnJson(0, '请检查用户名密码');
				}
			}
			catch (Zend_Auth_Exception $e)
			{
				$this->returnJson(0, 'Sorry System has some issues during the auth processing');
			}
			catch (Zend_Exception $e)
			{
				$this->returnJson(0, 'Sorry System has some issues');
			}
			
			

		}
		
		
		
	
	}
	
	public function testAction()
	{
		$userCollection = new Application_Model_DbCollections_Users();
		
		var_dump(Zend_Auth::getInstance()->getIdentity());
		$result = $userCollection->findone( array("username" => "Shaon","password" => "123"));
		
		echo $result['_id']."\n";
		foreach($result as $item)
		{
			echo $item;
		}
	}
}

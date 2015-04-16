<?php

class UsersController extends My_Center_Controller
{
	
	public function indexAction()
	{
		try 
		{
			$method = $this->_request->getMethod();
			switch ($method)
			{
				case "GET":
					if (count($_GET) == 0)
					{
						$result = array();
						
						//init fields
						$fields = array('_id' => 1,'username' => 1,'isAdmin' => 1,'email' => 1);
						$userCollection = new Application_Model_DbCollections_Users();
						try
						{	
							$cursor = $userCollection->find(array(), $fields);
							if ($cursor instanceof MongoCursor)
							{//Check the return result
								$usersProfile = iterator_to_array($cursor);
						
								foreach ($usersProfile as $item)
								{
										
									$temp = array('id' => $item['_id'] ->__toString(), 'username' => $item['username'],
											'isAdmin' => $item['isAdmin'], 'email' => $item['email']);
									$result[] = $temp;
								}
						
								$this->returnJson(200, "Get all users profiles successfully", $result);
							}
							else
							{
								$this->returnJson(200, 'no available data');
							}
						
								
						}
						catch (Zend_Exception $e)
						{
							$this->returnJson(500, 'Some errors occoured during get user profiles'.$e->getMessage());
						}
						
					}
					
					if (count($_GET) == 1 && $this->_request->has('id'))
					{
						$userCollection = new Application_Model_DbCollections_Users();
						
						try
						{
							$id = new MongoId($this->_request->getParam('id'));
							$temp = array("_id" => $id);
							$userProfile = $userCollection->findOne($temp);
								
							if(is_null($userProfile))
								$this->returnJson(0, "The user did not exist");
								
							$result = array('id' => $userProfile['_id'] ->__toString(), 'username' => $userProfile['username'],
									'isAdmin' => $userProfile['isAdmin'], 'email' => $userProfile['email']);
								
								
							$this->returnJson(1, "Get 1 user profile successfully",$result);
								
						}
						catch (MongoException $e)
						{
							$this->returnJson(0, 'Please input the correct user id format');
						}
						catch (Zend_Exception $e)
						{
							$this->returnJson(0, 'Some errors occoured during get 1 user profile',$e->getMessage());
						}
						
					}
					
				case "POST":
					if (!$this->_request->has('username') || !$this->_request->has('password')
					|| !$this->_request->has('email'))
					{
						$this->returnJson(400, 'Parameters error');
					}
					
					$username = $this->_request->getParam('username');
					$password = md5($this->_request->getParam('password'));
					$email = $this->_request->getParam('email');
					
					$userCollection = new Application_Model_DbCollections_Users();
					
					try
					{
						$temp = array("username" => $username);
						$userProfile = $userCollection->findOne($temp);
							
						if(isset($userProfile)&&!empty($userProfile))
							$this->returnJson(400, "The user has already exist");
							
							
						$newUser = array('username' => $username , 'password' => $password, 'isAdmin' => false,
								'email' => $email, 'createdOn' => new MongoDate(time()));
							
						$result = $userCollection->insert($newUser);
						
						
							
						if(!$result)
						{
							$this->returnJson(400, 'Insert action failed');
						}
						$user = array();
						$authAdapter = new My_Auth_Auth($username, $password);
						$loginResult = $authAdapter->authenticate();
						if($loginResult -> isValid())
						{
							$userTemp = $userCollection -> findOne(array('username' => $username));
							$user['uid'] = $userTemp['_id']->__toString();
							$user['username'] = $userTemp['username'];
							$user['createdOn'] = $userTemp['createdOn']->sec;
							$user['isAdmin']= $userTemp['isAdmin'];
							$user['email']= $userTemp['email'];
						
							$this->returnJson(200, 'login successfully',$user);
							//$this->returnJson(200, 'Login successfully',array('uid'=>Zend_Auth::getInstance()->getStorage()->read()['id']));
						}
						

							
						$this->returnJson(200, 'Insert successfully',$user);
							

							
					}
					catch (Zend_Exception $e)
					{
						$this->returnJson(500, 'Some errors occoured during create user profile',$e->getMessage());
					}
					
					break;
				case "PUT":
					$this->returnJson(400, 'error request method');
					break;
				case "DELETE":
					$this->returnJson(400, 'error request method');
					break;
				default:
					$this->returnJson(400, 'error request method');
			}
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Sorry System has some issues',$e->getMessage());
		}
	}
	
	
	public function loginAction()
	{

		if($this->_request->getMethod() == 'POST')
		{
			$username = $this->_request->getParam('username');
			$password = $this->_request->getParam('password');	
			$userCollection = new Application_Model_DbCollections_Users();	
			try 
			{
				
				$userTemp = $userCollection -> findOne(array('username' => $username));
				$user['uid'] = $userTemp['_id']->__toString();
				$user['username'] = $userTemp['username'];
				$user['createdOn'] = $userTemp['createdOn']->sec;
				$user['isAdmin']= $userTemp['isAdmin'];
				$user['email']= $userTemp['email'];

				if (Zend_Auth::getInstance()->hasIdentity()) 
				{
					$this->returnJson(200, "You have already login",$user);
				}			
				$authAdapter = new My_Auth_Auth($username, $password);
				$loginResult = $authAdapter->authenticate();
				if($loginResult -> isValid())
				{	
					$this->returnJson(200, 'login successfully',$user);
				}
				else 
				{
					$this->returnJson(401,"Pleas check the user name and pasword");
				}
			}
			catch (Zend_Auth_Exception $e)
			{
				$this->returnJson(500, 'Sorry System has some issues during the auth processing'.$e->getMessage());
			}
			catch (Zend_Exception $e)
			{
				$this->returnJson(500, 'Sorry System has some issues'.$e->getMessage());
			}

		}
		
	}
	
	
	
	public function logoutAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(400, "Please use the right request method");
		}
		
		try 
		{
			Zend_Session::destroy();
		
			$this->returnJson(200, "Logout successfully");
		}
		catch (Zend_Session_Exception $e)
		{
			$this->returnJson(500, "Logout failed");
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, "Logout failed");
		}
		
		
	}
	
	/**
	 * Author: Shaon
	 * 
	 * List All Users Info
	 * path: /user/get-all-users-profile
	 * Result: {"id":"550ba28c0cde3a0305c027a1","username":"Shaon","isAdmin":true,"email":"tanxdl.cn.ibm.com"}
	 */
	
	public function getAllUsersProfileAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		$result = array();
		
		//init fields
		$fields = array('_id' => 1,'username' => 1,'isAdmin' => 1,'email' => 1); 
		$userCollection = new Application_Model_DbCollections_Users();
		try 
		{
			
			
			
			$cursor = $userCollection->find(array(), $fields);
			if ($cursor instanceof MongoCursor)
			{//Check the return result
				$usersProfile = iterator_to_array($cursor);
				
				foreach ($usersProfile as $item)
				{				
					
 					$temp = array('id' => $item['_id'] ->__toString(), 'username' => $item['username'],
						'isAdmin' => $item['isAdmin'], 'email' => $item['email']);
 					$result[] = $temp;
				}
		
				$this->returnJson(1, "Get all users profiles successfully", $result);
			}
			else 
			{
				$this->returnJson(0, 'no available data');
			}
				
			
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during get user profiles',$e->getMessage());
		}

	}
	
	
	public function getOneUserProfileAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		if (!$this->_request->has('id'))
		{
			$this->returnJson(0, 'Parameters error');
		}
		
		$userCollection = new Application_Model_DbCollections_Users();
		
		try 
		{
			$id = new MongoId($this->_request->getParam('id'));
			$temp = array("_id" => $id);
			$userProfile = $userCollection->findOne($temp);
			
			

				
			if(is_null($userProfile))
				$this->returnJson(0, "The user did not exist");
			
			$result = array('id' => $userProfile['_id'] ->__toString(), 'username' => $userProfile['username'],
					'isAdmin' => $userProfile['isAdmin'], 'email' => $userProfile['email']);
			
			
			$this->returnJson(1, "Get 1 user profile successfully",$result);
			
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Some errors occoured during delete user profile',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during delete user profile',$e->getMessage());
		}
		
	}
	
	
	
	
	/**
	 * Author: Shaon
	 * Create a user via username, password, email
	 * Path: users/create-user/
	 * Result: return status 1 if insert succefully.
	 * 
	 */
	public function createUserAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		
		if (!$this->_request->has('username') || !$this->_request->has('password') 
 					|| !$this->_request->has('email'))
		{
			$this->returnJson(0, 'Parameters error');
		}
		
		$username = $this->_request->getParam('username');
		$password = md5($this->_request->getParam('password'));
		$email = $this->_request->getParam('email');
		
		$userCollection = new Application_Model_DbCollections_Users();
		
		try 
		{
			$temp = array("username" => $username);
			$userProfile = $userCollection->findOne($temp);
			
			if(isset($userProfile)&&!empty($userProfile))
				$this->returnJson(0, "The user has already exist");
			
			
			$newUser = array('username' => $username , 'password' => $password, 'isAdmin' => false,
						'email' => $email, 'createdOn' => new MongoDate(time()));
			
			$result = $userCollection->insert($newUser);
			
			if(!$result)
			{
				$this->returnJson(0, 'Insert action failed');
			}
			
			$this->returnJson(1, 'Insert Successfully',$result);
			
			
				
			
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during create user profile',$e->getMessage());
		}
		
	
	
	}
	
	
	/**
	 * Author: 谭潇
	 * Delete a user
	 * Path : users/del-user
	 * Result: return status 1 if insert succefully.
	 * 
	 */
	public function delUserAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		
		if (!$this->_request->has('id'))
		{
			$this->returnJson(0, 'Parameters error');
		}
		
		$userCollection = new Application_Model_DbCollections_Users();
		
		try 
		{
			$id = new MongoId($this->_request->getParam('id'));
			$temp = array("_id" => $id);
			$userProfile = $userCollection->findOne($temp);
				
			if(is_null($userProfile))
				$this->returnJson(0, "The user did not exist");
			
			$result = $userCollection->remove($temp);
			
			if (!$result)
			{
				$this->returnJson(0, "Delete action failed");
			}
			
			$this->returnJson(1, "Delete successfully",$result);
			
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Some errors occoured during delete user profile',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during delete user profile',$e->getMessage());
		}
	
		
	}
	
	
	/**
	 * Author: Shaon
	 * Update a user profile
	 * Paht: users/update-user
	 * 
	 */
	public function updateUserAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		
		if (!$this->_request->has('id') || !$this->_request->has('email')
		|| !$this->_request->has('password'))
		{
			$this->returnJson(0, 'Parameters error');
		}
		
		
		$password = md5($this->_request->getParam('password'));
		$email = $this->_request->getParam('email');
		
		$userCollection = new Application_Model_DbCollections_Users();
		
		try
		{
			$id = new MongoId($this->_request->getParam('id'));
			$temp = array("_id" => $id);
			$userProfile = $userCollection->findOne($temp);
				
			if(is_null($userProfile))
				$this->returnJson(0, "The user did not exist");
				
				
			$newUserInfo = array('username' => $userProfile['username'] , 'password' => $password, 'isAdmin' => false,
					'email' => $email, 'createdOn' => new MongoDate(time()));
				
			$result = $userCollection->update($temp,$newUserInfo);
				
			if(!$result)
			{
				$this->returnJson(0, 'Update action failed');
			}
				
			$this->returnJson(1, 'Update Successfully',$result);
				
				
		
				
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Some errors occoured during update user profile',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during update user profile',$e->getMessage());
		}
		
		
	}
	
	
	public function testAction()
	{
// 		$userCollection = new Application_Model_DbCollections_Users();

		
 		
//   		var_dump(Zend_Auth::getInstance()->getStorage()->read());
  		$interestsCollection = new Application_Model_DbCollections_Interests();
  		$interestsInfo = $interestsCollection -> find(array('speechID' => '550faef111be313074a8399c'),
  															array('userID' => 1,'createdOn' => 1));
  		//var_dump($interestsInfo);
  		foreach ($interestsInfo as $item)
  		{
  			var_dump($item);
  		}
  		
  		
	}
}

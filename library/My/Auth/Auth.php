<?php
class My_Auth_Auth implements Zend_Auth_Adapter_Interface
{
	private $_username;
	private $_password;
	
	public function __construct($username, $password)
	{
		$this->_username = $username;
		$this->_password = $password;
	
 	}
 	
 	public function authenticate()
 	{
 		
        $authResult = array(
            'code'     => Zend_Auth_Result::FAILURE,
            'identity' => '',
            'info' => array()
           );
           
        
          
        $userProfile = $this->_getUserInfo();
		
        try 
        {
	        if(isset($userProfile)&&!empty($userProfile))
	        {
	            $authResult = array(
	                'code'     => Zend_Auth_Result::SUCCESS,
	                'identity' => $userProfile['username']
	            );
	            //角色存储  个人缓存空间
	            $userNameSpace = Zend_Auth::getInstance() -> getStorage() -> getNamespace();
	
	            $storage = Zend_Auth::getInstance()->getStorage();
	            unset($userProfile['password']);
	            
	            $storageData = array('id' => $userProfile['_id'] ->__toString(), 'username' => $userProfile['username'],
						'isAdmin' => $userProfile['isAdmin'], 'email' => $userProfile['email']);
	            
	            $storage->write($storageData);
	          
	            $userSession = new Zend_Session_Namespace($userNameSpace);
	            
	            $userSession -> id = $userProfile['_id'];
	            $userSession -> username =  $userProfile['username'];
	            $userSession -> isAdmin =  $userProfile['isAdmin'];
	            $userSession -> email =  $userProfile['email'];
	            
	        }
        }
        catch(Zend_Auth_Exception $e)
        {
        
        }
        catch (Exception $e)
        {
    
        }
        
        return new Zend_Auth_Result($authResult['code'], $authResult['identity'], array());
 	}
 	
 	private function _getUserInfo()
 	{
 		$userLoginInfo = array("username" => $this->_username,"password" => md5($this->_password));
 		$userCollection = new Application_Model_DbCollections_Users();
 		
 		try
 		{
 			$userProfile = $userCollection->findOne($userLoginInfo);
 			return $userProfile;
 			
 		}
 		catch (Exception $e)
 		{
 			throw new Exception("Error occoured during login");
 			exit(0);
 		}
 	}
 	
 	
 	
 	
 	
	
}
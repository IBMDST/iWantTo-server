<?php
class My_Db_MongoCollections extends MongoCollection
{
	
	protected $_name = null;
	
	
	public function __construct()
	{
		$dbConfig = Zend_Registry::get("dbConfigs");
		if(!Zend_Registry::isRegistered('dbconn'))
		{
			try {
				//mongodb://[username:password@]host1[:port1][,host2[:port2:],...]/db
				
				
				$connectionString = "mongodb://";
				
				if (!isset($dbConfig['params']['username']) )
				{
					$connectionString .= $dbConfig['params']['username'].
					$dbConfig['params']['password']."@";
				}
				
				$connectionString.= $dbConfig['params']['host'].':'.
						$dbConfig['params']['port'];
				
				$conn = new My_Db_MongoDb($connectionString);
				$db = $conn -> selectDB($dbConfig['params']['dbname']);
			}
			catch (Exception $e)
			{
				throw new Exception('连接数据库异常，请检查参数');
			}
			Zend_Registry::set('dbconn', $db);
		}
		
		$db = Zend_Registry::get('dbconn');
		
		$this->_setupCollectionName();
		parent::__construct($db,$this->_name);
		
	}
	
    protected function _setupCollectionName()
    {
        if (! $this->_name) 
        {
            $this->_name = get_class($this);
        } else 
        {
            $this->_name = $this->_name;
        }
    }
	
}
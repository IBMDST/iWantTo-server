<?php
class My_Db_MongoCollections extends MongoCollection
{
	
	protected $_name = null;
	
	
	public function __construct()
	{
		if(!Zend_Registry::isRegistered('dbconn'))
		{
			try {
				$conn = new My_Db_MongoDb();
				$db = $conn -> selectDB('iwant');
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
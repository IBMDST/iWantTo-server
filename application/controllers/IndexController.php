<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	
    }

    public function indexAction()
    {
        $dbConfig = Zend_Registry::get("dbConfigs");
				var_dump($dbConfig);
				die();
    }


}


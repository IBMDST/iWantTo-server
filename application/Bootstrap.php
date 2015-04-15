<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoload()
	{
	
		Zend_Loader_Autoloader::getInstance()->registerNamespace("My")->pushAutoloader(
		new Zend_Application_Module_Autoloader(array(
		'namespace' => '',
		'basePath' => dirname(__FILE__),
		)));
	
	}
	
	protected function _initConfigs(){
		$dbConfigs = $this->getOptions()["db"];
		Zend_Registry::set("dbConfigs", $dbConfigs);
	}
	

}


<?php
class SmartySingleton{
	private static $_instance = null;

	private function __construct(){

	}

	public static function getInstance(){
		if(is_null(self::$_instance)){
			self::$_instance = new Smarty();
            self::$_instance->setTemplateDir(ABS_PATH . "/templates");
            self::$_instance->setCompileDir(ABS_PATH . "/templates_c");
			self::$_instance->debugging = SMARTY_DEBUG;
			self::$_instance->caching = SMARTY_CACHE;
			self::$_instance->cache_lifetime = SMARTY_CACHE_LIFE;
		}

		return self::$_instance;
	}
}
?>
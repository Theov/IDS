<?php
class mysqlSingleton{
	private static $_instance = null;

	private function __construct(){

	}

	public static function getInstance(){
		if(is_null(self::$_instance)){
			try
			{
			    self::$_instance = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', ''.DB_USER.'', ''.DB_PWD.'');
			}
			catch (Exception $e)
			{
			    die('Erreur : ' . $e->getMessage());
			}
		}

		return self::$_instance;
	}
}
?>
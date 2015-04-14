<?php
class Controller{
	private $tpl;
	private $db;
	private $router;

	public function __construct(){
		$this->init();
		$this->exec();
	}

	private function init(){
		$this->router = new Routeur();
		$this->db = mysqlSingleton::getInstance();
		$this->tpl = smartySingleton::getInstance();
	}

	private function exec(){
		$url = substr(split("&a=", $this->router->getUserRequest())[0], 3);
		$act = split("&a=", $this->router->getUserRequest())[1];
		$s_ctrl = $url . "_ctrl";
		$ctrl = new $s_ctrl($this->tpl, $this->db);

		$ctrl->$act();

		$ctrl->callback();
		$this->tpl->display('index.html');
	}
}

?>
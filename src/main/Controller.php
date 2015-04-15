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
        $this->preventInjectionAttack();

		$url = substr(split("&a=", $this->router->getUserRequest())[0], 3);
		$act = split("&a=", $this->router->getUserRequest())[1];
		$s_ctrl = $url . "_ctrl";

        $execCanTriggered = FALSE;

        if(class_exists($s_ctrl)){
            if(method_exists($s_ctrl, $act)){
                $execCanTriggered = TRUE;
            }
        }

        if($execCanTriggered){
            $ctrl = new $s_ctrl($this->tpl, $this->db);
            $ctrl->callpre();
            $ctrl->$act();
            $ctrl->callback();
        }else{
            $ctrl = new auth_ctrl($this->tpl, $this->db);
            $ctrl->callpre();
            $ctrl->welcome();
            $ctrl->callback();
        }

        $this->tpl->display('index.html');
	}

    private function preventInjectionAttack(){
        foreach($_GET as $key => $get){
            $_GET[$key] = helper::securiseString($get);
        }

        foreach($_POST as $key =>  $post){
            $_POST[$key] = helper::securiseString($post);
        }
    }
}

?>
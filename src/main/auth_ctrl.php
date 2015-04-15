<?php
class auth_ctrl{
	private $tpl;
	private $db;

	public function __construct($_tpl, $_db){
		$this->db = $_db;
		$this->tpl = $_tpl;
	}

	private function loadView(){
		$this->tpl->assign('tpl','auth.html');
	}

	public function log(){
		$authIsOKay = FALSE;

		if($this->inputAreFilled()){
			$authIsOKay = TRUE;
		}

		if($authIsOKay){
			$queryResult = $this->queryDatabaseForAuthentification();

			if($queryResult == 1)
				$authIsOKay = TRUE;
			else
				$authIsOKay = FALSE;
		}

		if($authIsOKay){
			$this->tpl->assign('res','Auth ok !');
            $_SESSION["token"] = helper::encrypt($_POST["pass"]);
			helper::redirect("?p=index&a=all");
		}else if(!$this->inputAreFilled()){
			$this->tpl->assign('res','Bienvenue');
		}else{
            $this->tpl->assign('res','Authentification érronée !');
        }
	}

	public function welcome(){
		$this->tpl->assign('res','Veuillez vous authentifier');
	}

	private function queryDatabaseForAuthentification(){
		$query = "SELECT count(id) FROM secret123 WHERE name = '".$_POST["name"]."' AND pass = '". helper::encrypt($_POST["pass"]) ."'";
		$sth = $this->db->prepare($query);
		$sth->execute();
		$queryResult = $sth->fetchAll();

		return $queryResult[0][0];
	}

    public function callpre(){
    }

	public function callback(){
		$this->loadView();
	}

    public function inputAreFilled()
    {
        return isset($_POST["name"]) && isset($_POST["pass"]);
    }
}
?>
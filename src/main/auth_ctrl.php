<?php
class auth_ctrl extends MainController{
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
            $this->tpl->assign('alert','Veuillez remplir tout les champs !');
		}else{
            $this->tpl->assign('res','Authentification érronée !');
            $this->tpl->assign('alert','Authentification érronée !');
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

    public function inputAreFilled()
    {
        return isset($_POST["name"]) && isset($_POST["pass"]);
    }
}
?>
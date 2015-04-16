<?php
class conf_ctrl extends MainController{
    public function callpre(){
        if(!isset($_SESSION["token"])){
            session_unset();
            helper::redirect("");
        }
    }

    public function callback(){
        $this->templateName = "conf";
    }

    public function all(){
        if(isset($_POST["dir"]) && isset($_POST["fil"])){
            $this->db->exec("UPDATE config SET file = '" . $_POST["fil"] . "', directory = '" . $_POST["dir"] . "' WHERE id = 1");
        }

        $this->tpl->assign("cfgData", $this->getConfigData());
    }

    private function getConfigData(){
        $query = "SELECT file, directory FROM config WHERE id = 1";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        return $queryResult[0];
    }
}
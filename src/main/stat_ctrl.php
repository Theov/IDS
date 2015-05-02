<?php
class stat_ctrl extends MainController{
    public function callpre(){
        if(!isset($_SESSION["token"])){
            session_unset();
            helper::redirect("");
        }
    }

    public function callback(){
        $this->templateName = "stat";
    }

    public function all(){

        for($i = 0; $i <= 5; $i++){
            $query = "SELECT COUNT(id) FROM file WHERE scan_id = (SELECT MAX(id) - ".$i." FROM scan)";
            $sth = $this->db->prepare($query);
            $sth->execute();
            $queryResult = $sth->fetchAll();

            $this->tpl->assign("scanData" . $i, $queryResult[0]);

            $query = "SELECT COUNT(id) FROM file WHERE scan_id = (SELECT MAX(id) - ".$i." FROM scan) AND valid = 0";
            $sth = $this->db->prepare($query);
            $sth->execute();
            $queryResult = $sth->fetchAll();

            $this->tpl->assign("scanDataV" . $i, $queryResult[0]);

            $query = "SELECT COUNT(id) FROM file WHERE scan_id = (SELECT MAX(id) - ".$i." FROM scan) AND valid = 1";
            $sth = $this->db->prepare($query);
            $sth->execute();
            $queryResult = $sth->fetchAll();

            $this->tpl->assign("scanDataF" . $i, $queryResult[0]);
        }
    }
}
<?php
class index_ctrl extends MainController{
    public function all(){
        $this->getAllScanData();
    }

    public function delete(){
        $this->deleteScan();
    }

    public function callpre(){
        if(!isset($_SESSION["token"])){
            session_unset();
            helper::redirect("");
        }
    }

    public function callback(){
        $this->templateName = "scan";
    }

    private function getAllScanData(){
        $query = "SELECT id, date_debut, date_fin FROM scan";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        $this->tpl->assign("scanData", $queryResult);
    }

    private function deleteScan(){
        if(isset($_GET["id"])){
            $idToDelete = $_GET["id"];
            $this->db->exec("DELETE FROM scan WHERE id = " . $idToDelete);
        }

        helper::redirect("?p=index&a=all");
    }
}

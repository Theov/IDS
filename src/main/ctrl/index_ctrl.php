<?php
class index_ctrl extends MainController{
    public function all(){
        $this->getAllScanData();
    }

    public function delete(){
        $this->deleteScan();
    }

    public function ref(){
        $this->refScan();
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

        $query = "SELECT scan_id FROM ref WHERE id = 1";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        $this->tpl->assign("refId", $queryResult[0][0]);
    }

    private function deleteScan(){
        if(isset($_GET["id"])){
            $idToDelete = $_GET["id"];
            $this->db->exec("DELETE FROM scan WHERE id = " . $idToDelete);
        }

        helper::redirect("?p=index&a=all");
    }

    private function refScan(){
        if(isset($_GET["id"])){
            $idToRef = $_GET["id"];
            $this->db->exec("UPDATE ref SET scan_id = " . $idToRef . " WHERE id = 1");
        }

        helper::redirect("?p=index&a=all");
    }
}

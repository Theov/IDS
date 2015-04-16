<?php
class scan_ctrl extends MainController{
    public function callpre(){
        if(!isset($_SESSION["token"])){
            session_unset();
            helper::redirect("");
        }
    }

    public function callback(){
        $this->templateName = "scan-exec";
    }

    public function launch(){
        $this->addBeginningData();

        $cfgData = $this->getConfigData();
        $xmlParser = new XMLParser($cfgData["file"]);

        $fileManager = new FileManager($cfgData["directory"], $xmlParser->listOfFileToIgnore(), $xmlParser->listOfFileToIgnore());
        $fileManager->launchFileScan();
        $scanResult = $fileManager->fileAndSumMap();

        $this->addCoreData($scanResult);

        $this->addEndingData();

        helper::redirect("?p=index&a=all");

    }

    private function getConfigData(){
        $query = "SELECT file, directory FROM config WHERE id = 1";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        return $queryResult[0];
    }

    private function addCoreData($scanResult){
        foreach($scanResult as $f){
            $this->db->exec("INSERT INTO file (`file`, `sum`, `scan_id`) VALUES ('". $f["file"] ."', '". $f["sum"] ."', ". $this->getScanId() .")");
        }
    }

    private function addBeginningData(){
        $this->db->exec("INSERT INTO scan (`date_debut`) VALUES (NOW())");
    }

    private function addEndingData(){
        $this->db->exec("UPDATE scan SET date_fin = NOW() WHERE id = " . $this->getScanId());
    }

    private function getScanId(){
        $query = "SELECT MAX(id) FROM scan";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        return $queryResult[0][0];
    }
}
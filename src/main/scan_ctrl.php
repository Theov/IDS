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
        $cfgData = $this->getConfigData();
        $xmlParser = new XMLParser($cfgData["file"]);

        $fileManager = new FileManager($cfgData["directory"], $xmlParser->listOfFileToIgnore(), $xmlParser->listOfFileToIgnore());
        $fileManager->launchFileScan();
        $scanResult = $fileManager->fileAndSumMap();

        echo "<pre>"; print_r($scanResult); echo "</pre>"; die;

    }

    private function getConfigData(){
        $query = "SELECT file, directory FROM config WHERE id = 1";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        return $queryResult[0];
    }
}
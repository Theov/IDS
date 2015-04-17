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

    public function check(){
        $lastScanId = $this->getScanId();
        $previousScanId = $lastScanId - 1;

        $lastScanData = $this->getScanData($lastScanId);
        $previousScanData = $this->getScanData($previousScanId);

        $scanAreEquivalent = FALSE;

        if($this->checkForEquivalentNumberOfElement($lastScanData, $previousScanData)){
            $scanAreEquivalent = TRUE;
        }

        if($scanAreEquivalent){
            $intrusionDetected = FALSE;

            foreach($lastScanData as $entry){
                if(!$intrusionDetected){
                    if(!$this->checkEntryCorrespondance($entry, $previousScanData)){
                        $intrusionDetected = TRUE;
                    }
                }
            }
        }

        if($intrusionDetected){
            helper::sendMail("Intrusion détéctée !");
        }else{
            helper::sendMail("aucun intrusion détéctée !");
        }

        helper::redirect("?p=index&a=all");
    }

    private function checkEntryCorrespondance($entry, $previousScanData){
        $uncorrespondance = TRUE;

        foreach($previousScanData as $previousEntry){
            if($previousEntry["file"] == $entry["file"]){
                if($previousEntry["sum"] != $entry["sum"]){
                    $uncorrespondance = FALSE;
                }
            }

            if(!$uncorrespondance)
                break;
        }

        return $uncorrespondance;
    }

    private function checkForEquivalentNumberOfElement($arr1, $arr2){
        return count($arr1) == count($arr2);
    }

    private function getScanData($scanId){
        $query = "SELECT file, sum FROM file WHERE scan_id = " . $scanId;
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        return $queryResult;
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
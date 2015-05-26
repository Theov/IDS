<?php
class scan_ctrl extends MainController{
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
        $previousScanId = $this->getScanRefId();

        $lastScanData = $this->getScanData($lastScanId);
        $previousScanData = $this->getScanData($previousScanId);

        $scanAreEquivalent = FALSE;

        if($this->checkForEquivalentNumberOfElement($lastScanData, $previousScanData)){
            $scanAreEquivalent = TRUE;
        }

        $intrusionDetected = FALSE;


        foreach($lastScanData as $entry){
           if(!$this->checkEntryCorrespondance($entry, $previousScanData)) {
               $intrusionDetected = TRUE;
               $this->db->exec("UPDATE file SET valid = 1 WHERE id = " . $entry["id"]);
           }
        }


        if($intrusionDetected || !$scanAreEquivalent){
            $this->db->exec("UPDATE scan SET result = 1 WHERE id = " . $this->getScanId());
            //helper::sendMail("Intrusion détéctée !");
        }else{
            $this->updateRef($this->getScanId());
            $this->db->exec("UPDATE scan SET result = 0 WHERE id = " . $this->getScanId());
            //helper::sendMail("aucun intrusion détéctée !");
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
        $query = "SELECT id, file, sum, valid FROM file WHERE scan_id = " . $scanId;
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
            $this->db->exec("INSERT INTO file (`file`, `sum`, `valid`, `scan_id`) VALUES ('". $f["file"] ."', '". $f["sum"] ."', 0, ". $this->getScanId() .")");
        }
    }

    private function addBeginningData(){
        $this->db->exec("INSERT INTO scan (`date_debut`, `result`) VALUES (NOW(), 1)");
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

    public function getScanRefId(){
        $query = "SELECT scan_id FROM ref WHERE id = 1";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        return $queryResult[0][0];
    }

    public function updateRef($id){
        $this->db->exec("UPDATE ref SET scan_id = ".$id." WHERE id = " . 1);
    }
}
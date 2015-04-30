<?php
define("DB_HOST", "127.0.0.1");
define("DB_USER", "root");
define("DB_PWD", "");
define("DB_NAME", "hids");

class FileManager
{
    private $directoryToScan = "";
    private $fileToIgnore = array();
    private $directoryToIgnore = array();
    private $fileList = array();
    private $fileAndSumMap = array();

    function __construct($_directoryToScan, $listOfFileToIgnore, $listOfDirectoryToIgnore) {
        $this->fileToIgnore = $listOfFileToIgnore;
        $this->directoryToIgnore = $listOfDirectoryToIgnore;
        $this->directoryToScan = $_directoryToScan;
    }

    private function recursiveScanDir($dir) {
        $dir = rtrim($dir, '\\/');
        $result = array();

        foreach (scandir($dir) as $f) {
            if ($f !== '.' and $f !== '..') {
                if (is_dir("$dir/$f") && !in_array("$dir/$f", $this->directoryToIgnore))
                    $result = array_merge($result, $this->recursiveScanDir("$dir/$f", "$f/"));
                else
                    if(!in_array($f, $this->fileToIgnore) && !is_dir("$dir/$f"))
                        $result[] = "$dir/$f";
            }
        }

        return $result;
    }

    private function calculateChecksum(){
        $fileAndSumMap = array();

        foreach ($this->fileList as $file)
            array_push($fileAndSumMap, array("file" => $file,"sum" => sha1_file($file)));

        return $fileAndSumMap;
    }

    public function launchFileScan(){
        $this->fileList = $this->recursiveScanDir($this->directoryToScan);
        $this->fileAndSumMap = $this->calculateChecksum();
    }

    public function fileList(){
        return $this->fileList;
    }

    public function fileAndSumMap(){
        return $this->fileAndSumMap;
    }

    public function homeDirectory(){
        return $this->directoryToScan;
    }
}

class XMLParser
{
    private $configFile;
    private $listOfFileToIgnore = array();
    private $XMLFile;

    function __construct($_configurationFile) {
        $this->configFile = $_configurationFile;
        $this->XMLFile = new DomDocument();
        $this->XMLFile->load($this->configFile);
        $this->parseXMLForFile();
    }

    private function parseXMLForFile(){
        foreach ($this->XMLFile->getElementsByTagName("file") as $fileToAdd) {
            array_push($this->listOfFileToIgnore, $fileToAdd->textContent);
        }
    }

    public function listOfFileToIgnore(){
        return $this->listOfFileToIgnore;
    }
}

class Agent{
    private $db;
    private $xmlParser;
    private $fileManager;

    public function __construct(){
        $this->initializeDb();
        $this->initializeXMLParser();
        $this->initializeFileManager();
        $this->launchScan();
    }

    private function launchScan(){
        $this->addBeginningData();
        $this->fileManager->launchFileScan();
        $scanResult = $this->fileManager->fileAndSumMap();
        $this->addCoreData($scanResult);
        $this->addEndingData();
        $this->check();
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

    private function getScanData($scanId){
        $query = "SELECT id, file, sum, valid FROM file WHERE scan_id = " . $scanId;
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        return $queryResult;
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

        $intrusionDetected = FALSE;

        if($scanAreEquivalent){
            foreach($lastScanData as $entry){
                if(!$this->checkEntryCorrespondance($entry, $previousScanData)) {
                    $intrusionDetected = TRUE;
                    $this->db->exec("UPDATE file SET valid = 1 WHERE id = " . $entry["id"]);
                }
            }
        }

        if($intrusionDetected){
            $this->db->exec("UPDATE scan SET result = 1 WHERE id = " . $this->getScanId());
            //helper::sendMail("Intrusion détéctée !");
        }else{
            $this->db->exec("UPDATE scan SET result = 0 WHERE id = " . $this->getScanId());
            //helper::sendMail("aucun intrusion détéctée !");
        }
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

    private function initializeDb(){
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', ''.DB_USER.'', ''.DB_PWD.'');
    }

    private function initializeXMLParser(){
        $this->xmlParser = new XMLParser($this->getConfigData()["file"]);
    }

    private function initializeFileManager(){
        $this->fileManager = new FileManager($this->getConfigData()["directory"], $this->xmlParser->listOfFileToIgnore(), $this->xmlParser->listOfFileToIgnore());
    }

    private function getConfigData(){
        $query = "SELECT file, directory FROM config WHERE id = 1";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        return $queryResult[0];
    }

}

$Agent = new Agent();
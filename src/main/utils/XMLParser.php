<?php
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
?>
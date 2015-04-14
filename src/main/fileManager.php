<?php
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
  	$index = 0;

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
?>
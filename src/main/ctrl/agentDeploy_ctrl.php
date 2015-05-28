<?php
class agentDeploy_ctrl extends MainController{
    public function callback(){
        $this->templateName = "agentDeploy";
    }

    public function all(){
        if(isset($_POST["location"])){
            $dirToDeploy = $_POST["location"];
            if(is_dir($dirToDeploy)){
                $this->copydir(CLI_LOCATION, $dirToDeploy);
                $call = '"'.JRE_PATH . '" -jar "' . $dirToDeploy . '/agent.jar' . '" > clientslog.txt &';
                exec('start /B "agent-cli '. rand() .' " ' . $call ,$output,$return);
                $this->tpl->assign("alert", "Agent déployé !");
            }else{
                $this->tpl->assign("alert", "Le dossier indiqué n'existe pas !");
            }
        }
    }


    public function copydir($source,$dest)
    {
        mkdir($dest, 0755);
        foreach (
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }
}
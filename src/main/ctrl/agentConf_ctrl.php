<?php
class agentConf_ctrl extends MainController{
    public function callback(){
        $this->templateName = "agentConf";
    }

    public function all(){
        if(isset($_POST["adress"]) && isset($_POST["port"])){
            $this->db->exec("UPDATE agentconfig SET port = '" . $_POST["port"] . "', adress = '" . $_POST["adress"] . "' WHERE id = 1");
            $this->tpl->assign("alert", "Configuration mis à jour !");
        }

        $this->tpl->assign("agentConfData", $this->getAgentConfigData());
    }


    private function getAgentConfigData(){
        $query = "SELECT adress, port FROM agentconfig WHERE id = 1";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();
        return $queryResult[0];
    }
}
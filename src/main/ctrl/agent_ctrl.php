<?php
class agent_ctrl extends MainController{
    public function callback(){
        $this->templateName = "agent";
    }

    public function all(){
        $this->tpl->assign("agentData", $this->getAgentsData());
    }

    public function launch(){
        if($this->checkIfCommandLineAlreadyExist()){
            $this->updateCommand($this->getDataFromAgentById($_GET["id"]));
        }else{
            $this->insertCommand($this->getDataFromAgentById($_GET["id"]));
        }

        helper::redirect("?p=agent&a=all");
    }

    private function updateCommand($agentData){
        $this->db->exec("UPDATE agentcmd SET location = '" + $agentData["location"] + "' AND command = 'goToWork' WHERE id = 1");
    }

    private function insertCommand($agentData){
        $this->db->exec("INSERT INTO agentcmd (`id`, `location`, `command`) VALUES (1, '" . $agentData["location"] . "', 'goToWork')");
    }

    private function getAgentsData(){
        $query = "SELECT id, location, state FROM agent";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        return $queryResult;
    }

    private function getDataFromAgentById($id){
        $query = "SELECT location, state FROM agent WHERE id = " . $id;
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll()[0];

        return $queryResult;
    }

    private function checkIfCommandLineAlreadyExist(){
        $query = "SELECT COUNT(id) FROM agentcmd WHERE id = 1";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll()[0][0];

        return $queryResult == "1";
    }
}
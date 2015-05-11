<?php
class agent_ctrl extends MainController{
    public function callback(){
        $this->templateName = "agent";
    }

    public function all(){
        $query = "SELECT id, location, state FROM agent";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();
        $this->tpl->assign("agentData", $queryResult);
    }
}
<?php
class result_ctrl extends MainController
{
    public function callback(){
        $this->templateName = "result";
    }

    public function detail(){
        $this->tpl->assign("fileData", $this->getFileData());
    }

    private function getFileData(){
        $query = "SELECT file, sum, valid FROM file WHERE scan_id = " . $_GET["id"];
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        return $queryResult;
    }
}
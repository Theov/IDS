<?php
class index_ctrl{

    public function __construct($_tpl, $_db){
        $this->db = $_db;
        $this->tpl = $_tpl;
    }

    private function loadView(){
        $this->tpl->assign('tpl','scan.html');
    }

    public function all(){

    }

    public function callpre(){
        if(!isset($_SESSION["token"])){
            session_unset();
            helper::redirect("");
        }
    }

    public function callback(){
        $this->loadView();
    }
}

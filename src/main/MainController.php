<?php
class MainController{
    protected $tpl;
    protected $db;
    protected $templateName;

    public function __construct($_tpl, $_db){
        $this->db = $_db;
        $this->tpl = $_tpl;
        $this->templateName = "auth";
    }

    public function callpre(){
    }

    public function callback(){
        $this->templateName = "auth";
    }

    public function loadView(){
        $this->tpl->assign('tpl', $this->templateName . '.html');
    }
}
<?php
class index_ctrl extends MainController{
    public function all(){

    }

    public function callpre(){
        if(!isset($_SESSION["token"])){
            session_unset();
            helper::redirect("");
        }
    }

    public function callback(){
        $this->templateName = "scan";
    }
}

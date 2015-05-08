<?php
class user_ctrl extends MainController{
    public function callpre(){
        if(!isset($_SESSION["token"])){
            session_unset();
            helper::redirect("");
        }
    }

    public function callback(){
        $this->templateName = "user";
    }

    public function all(){
        $this->getAllUserData();
    }

    public function delete(){
        $this->deleteUser();
    }

    public function add(){
        $this->addUser();
    }

    public function deco(){
        session_unset();
        helper::redirect("");
    }

    private function getAllUserData(){
        $query = "SELECT id, name FROM secret123";
        $sth = $this->db->prepare($query);
        $sth->execute();
        $queryResult = $sth->fetchAll();

        $this->tpl->assign("userData", $queryResult);
    }

    private function deleteUser(){
        if(isset($_GET["id"])){
            $idToDelete = $_GET["id"];
            $this->db->exec("DELETE FROM secret123 WHERE id = " . $idToDelete);
        }

        helper::redirect("?p=user&a=all");
    }

    private function addUser(){
        if(isset($_POST["name"]) && isset($_POST["pass"]) && isset($_POST["verifPass"])){
            if($_POST["pass"] == $_POST["verifPass"]){
                $this->db->exec("INSERT INTO secret123 (`id`, `name`, `pass`) VALUES ('', '".$_POST["name"]."', '".md5(B_SALT . $_POST["pass"] . E_SALT)."')");
            }
        }

        helper::redirect("?p=user&a=all");
    }

}
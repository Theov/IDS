<?php
class Routeur{
	private $allowedPage;
	private $allowedAction;
    private $defaultRedirectionPage;
    private $defaultAction;

    function __construct(){
        $this->allowedPage = ["auth", "index"];
        $this->allowedAction = ["log", "welcome", "all"];
        $this->defaultRedirectionPage = "auth";
        $this->defaultAction = "welcome";
    }

    public function resolve(){
        helper::redirect($this->getUserRequest());
    }

    private function userRequestIsLegit(){
        $requestIsLegit = TRUE;

        if(isset($_GET["p"]))
            $userRequest = $_GET["p"];

        if(!isset($userRequest) || trim($userRequest) == "" || $userRequest == null){
            $requestIsLegit = FALSE;
        }else{
            if(!in_array($userRequest, $this->allowedPage)){
              $requestIsLegit = FALSE; 
            }
        }

        if($requestIsLegit){
	        if(isset($_GET["a"]))
	        	$userAction = $_GET["a"];

	        if(!isset($userAction) || trim($userAction) == "" || $userAction == null){
                $requestIsLegit = FALSE;
	        }else{
	            if(!in_array($userAction, $this->allowedAction)){
	              $requestIsLegit = FALSE; 
	            }
	        }
	    }

        return $requestIsLegit;
    }
        
    public function getUserRequest(){
        if($this->userRequestIsLegit() && isset($_GET["p"]) && isset($_GET["a"])){
        	$userRequest = $_GET["p"];
        	$userAction = $_GET["a"];
        }else{
        	$userRequest = $this->defaultRedirectionPage;
        	$userAction = $this->defaultAction;
        }
            

        return "?p=" . $userRequest . "&a=" .$userAction;
    }
}
?>
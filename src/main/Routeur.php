<?php
class Routeur{
	private $allowedPage;
	private $allowedAction;
    private $defaultRedirectionPage;
    private $defaultAction;
    private $requestIsLegit;

    function __construct(){
        $this->allowedPage = ["auth", "index"];
        $this->allowedAction = ["log", "welcome", "all", "detail", "delete"];
        $this->defaultRedirectionPage = "auth";
        $this->defaultAction = "welcome";
        $this->requestIsLegit = FALSE;
    }

    private function checkUserRequest(){
        $this->pageParameterIsLegit();
        $this->actionParameterIsLegit();
    }
        
    public function getUserRequest(){
        $this->checkUserRequest();

        if($this->requestIsLegit){
        	$userRequest = $_GET["p"];
        	$userAction = $_GET["a"];
        }else{
            session_unset();
        	$userRequest = $this->defaultRedirectionPage;
        	$userAction = $this->defaultAction;
        }

        return "?p=" . $userRequest . "&a=" .$userAction;
    }

    private function pageParameterIsLegit()
    {
        $this->requestIsLegit = FALSE;
        $userRequest = "notAllowed";

        if (isset($_GET["p"]))
            $userRequest = $_GET["p"];

        if (in_array($userRequest, $this->allowedPage)) {
            $this->requestIsLegit = TRUE;
        }
    }

    private function actionParameterIsLegit()
    {
        $userAction= "notAllowed";

        if ($this->requestIsLegit) {
            $this->requestIsLegit = FALSE;

            if (isset($_GET["a"]))
                $userAction = $_GET["a"];

            if (in_array($userAction, $this->allowedAction)) {
                $this->requestIsLegit = TRUE;
            }
        }
    }
}
?>
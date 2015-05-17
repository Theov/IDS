<?php

include "../main/Routeur.php";
include "../../lib/helper.php";

class RouteurTest extends PHPUnit_Framework_TestCase
{
    public function testInitialRouteIsGood()
    {
        $rt = new Routeur();
        $this->assertEquals($rt->getUserRequest(), "?p=auth&a=welcome");
    }
}

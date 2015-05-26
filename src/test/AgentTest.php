<?php
include "../agent/scanner.php";

class AgentTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $Agent = new Agent();
        $this->assertEquals(true, $Agent->db() != null);
        $this->assertEquals(true, $Agent->XMLParser() != null);
    }

}
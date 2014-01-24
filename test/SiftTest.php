<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

class SiftTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        Sift::init('awesomeApiKey');
    }

    public function testGetInstance() {
        $instance = Sift::getInstance();
        $instance2 = Sift::getInstance();
        $this->assertTrue(!!$instance);
        $this->assertEquals($instance, $instance2);
    }

}
 
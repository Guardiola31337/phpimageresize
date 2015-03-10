<?php

require_once 'Resizer.php';
require_once 'Configuration.php';

class ResizerTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidOptionsCollaboration() {
        $resizer = new Resizer('', 'nonOptionsObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('Resizer', new Resizer());
        $this->assertInstanceOf('Resizer', new Resizer(''));
        $this->assertInstanceOf('Resizer', new Resizer('', array()));
    }

}

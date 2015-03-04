<?php

require_once 'Resizer.php';
require_once 'Configuration.php';

class ResizerTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testOptionalConfigurationCollaboration() {
        $resizer = new Resizer('', 'nonConfigurationObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('Resizer', new Resizer());
        $this->assertInstanceOf('Resizer', new Resizer(''));
        $this->assertInstanceOf('Resizer', new Resizer('', new Configuration()));
    }

}

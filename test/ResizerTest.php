<?php

require_once 'Resizer.php';
require_once 'Configuration.php';

class ResizerTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidOptionsCollaboration() {
        $image = new Image('', null, 'nonConfigurationObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('Resizer', new Resizer());
        $this->assertInstanceOf('Resizer', new Resizer(''));
        $this->assertInstanceOf('Resizer', new Resizer('', null));
        $this->assertInstanceOf('Resizer', new Resizer('', null, array()));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidOptions() {
        $opts = array(
            'width' => '',
            'height' => '',
            'output-filename' => ''
        );
        $resizer = new Resizer('', null, $opts);
    }

}

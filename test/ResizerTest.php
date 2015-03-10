<?php

require_once 'Resizer.php';
require_once 'Configuration.php';

class ResizerTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidCacheCollaboration() {
        $resizer = new Resizer('', 'nonCacheObject');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidOptionsCollaboration() {
        $image = new Image('', new FileSystem(), 'nonConfigurationObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('Resizer', new Resizer());
        $this->assertInstanceOf('Resizer', new Resizer(''));
        $this->assertInstanceOf('Resizer', new Resizer('', new FileSystem()));
        $this->assertInstanceOf('Resizer', new Resizer('', new FileSystem(), array()));
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
        $resizer = new Resizer('', $opts);
    }

}

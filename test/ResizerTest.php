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

    public function testComposePathWithOutputFilename() {
        $opts = array(
            'width' => null,
            'height' => null,
            'output-filename' => 'foo-output-filename'
        );
        $configuration = new Configuration($opts);
        $resizer = new Resizer('', $configuration);

        $this->assertEquals('foo-output-filename', $resizer->composePath());
    }

}

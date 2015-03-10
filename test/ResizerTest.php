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

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidImageInstantiation() {
        $resizer = new Resizer('', 'nonCacheObject');
    }

    public function testObtainImage() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);
        $cache->method('md5_file')
            ->willReturn('a90d6abb5d7c3eccfdbb80507f5c6b51');
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'width' => '30',
            'height' => '20',
            'output-filename' => null
        );
        $resizer = new Resizer($url, $cache, $opts);

        $this->assertEquals('./cache/a90d6abb5d7c3eccfdbb80507f5c6b51_w30_h20.jpg', $resizer->obtainImage());
    }

}

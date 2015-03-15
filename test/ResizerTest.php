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

    public function testObtainImagePath() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'width' => '30',
            'height' => '20',
            'output-filename' => null
        );

        $resizer = new Resizer($url, $cache, $opts);

        $this->assertEquals('./cache/remote/mf.jpg', $resizer->obtainImagePath());

    }

    public function testObtainOrignalPathAfterObtainingImage() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';
        $opts = array(
            'width' => '30',
            'height' => '20',
            'output-filename' => null
        );
        $resizer = new Resizer($url, $cache, $opts);

        $resizer->obtainImage();

        $this->assertEquals('./cache/remote/mf.jpg', $resizer->obtainImagePath());

    }

    public function testComposeResizeOptions() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);
        $cache->method('getimagesize')
            ->willReturn(array('30', '20'));

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'crop' => true,
            'width' => '30',
            'height' => '20',
            'output-filename' => null
        );

        $resizer = new Resizer($url, $cache, $opts);

        $this->assertEquals('x20', $resizer->composeResizeOptions());

    }

    public function testComposeResizeOptionsWithNoCropAndPanoramic() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);
        $cache->method('getimagesize')
            ->willReturn(array('30', '20'));

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'width' => '30',
            'height' => '20',
            'output-filename' => null
        );

        $resizer = new Resizer($url, $cache, $opts);

        $this->assertEquals('30', $resizer->composeResizeOptions());

    }

    public function testComposeResizeOptionsWithCropAndNoPanoramic() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);
        $cache->method('getimagesize')
            ->willReturn(array('20', '30'));

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'crop' => true,
            'width' => '30',
            'height' => '20',
            'output-filename' => null
        );

        $resizer = new Resizer($url, $cache, $opts);

        $this->assertEquals('30', $resizer->composeResizeOptions());

    }

    public function testCommandWithCrop() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);
        $cache->method('md5_file')
            ->willReturn('a90d6abb5d7c3eccfdbb80507f5c6b51');
        $cache->method('getimagesize')
            ->willReturn(array('20', '30'));

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'crop' => true,
            'width' => '30',
            'height' => '20',
            'output-filename' => null
        );

        $resizer = new Resizer($url, $cache, $opts);

        $originalPath = $resizer->obtainImagePath();
        $newPath = $resizer->obtainImage();

        $this->assertEquals('convert \'./cache/remote/mf.jpg\' -resize \'30\' -size \'30x20\' xc:\'transparent\' +swap -gravity center -composite -quality \'90\' \'./cache/a90d6abb5d7c3eccfdbb80507f5c6b51_w30_h20_cp.jpg\'', $resizer->commandWithCrop($newPath, $originalPath));

    }

    public function testCommandWithScale() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);
        $cache->method('md5_file')
            ->willReturn('a90d6abb5d7c3eccfdbb80507f5c6b51');
        $cache->method('getimagesize')
            ->willReturn(array('20', '30'));

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'scale' => true,
            'width' => '30',
            'height' => '20',
            'output-filename' => null
        );

        $resizer = new Resizer($url, $cache, $opts);

        $originalPath = $resizer->obtainImagePath();
        $newPath = $resizer->obtainImage();

        $this->assertEquals('convert \'./cache/remote/mf.jpg\' -resize \'x20\' -quality \'90\' \'./cache/a90d6abb5d7c3eccfdbb80507f5c6b51_w30_h20_sc.jpg\'', $resizer->commandWithScale($newPath, $originalPath));

    }

    public function testDefaultCommand() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'maxOnly' => true,
            'width' => null,
            'height' => null,
            'output-filename' => './foo/mj.png'
        );

        $resizer = new Resizer($url, $cache, $opts);

        $originalPath = $resizer->obtainImagePath();
        $newPath = $resizer->obtainImage();

        $this->assertEquals('convert \'./cache/remote/mf.jpg\' -thumbnail \> -quality \'90\' \'./foo/mj.png\'', $resizer->defaultCommand($newPath, $originalPath));

    }

    public function testResizeWithCropCommand() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);
        $cache->method('md5_file')
            ->willReturn('a90d6abb5d7c3eccfdbb80507f5c6b51');
        $cache->method('getimagesize')
            ->willReturn(array('20', '30'));

        $cache->expects($this->once())
            ->method('exec')
            ->with(
                $this->equalTo('convert \'./cache/remote/mf.jpg\' -resize \'30\' -size \'30x20\' xc:\'transparent\' +swap -gravity center -composite -quality \'90\' \'./cache/a90d6abb5d7c3eccfdbb80507f5c6b51_w30_h20_cp.jpg\''),
                '',
                0)
            ->willReturn(0);

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'crop' => true,
            'width' => '30',
            'height' => '20',
            'output-filename' => null
        );

        $resizer = new Resizer($url, $cache, $opts);

        $originalPath = $resizer->obtainImagePath();
        $newPath = $resizer->obtainImage();

        $this->assertEquals(0, $resizer->executeCommand($newPath, $originalPath));

    }

    public function testResizeWithScaleCommand() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);
        $cache->method('md5_file')
            ->willReturn('a90d6abb5d7c3eccfdbb80507f5c6b51');
        $cache->method('getimagesize')
            ->willReturn(array('20', '30'));

        $cache->expects($this->once())
            ->method('exec')
            ->with(
                $this->equalTo('convert \'./cache/remote/mf.jpg\' -resize \'x20\' -quality \'90\' \'./cache/a90d6abb5d7c3eccfdbb80507f5c6b51_w30_h20_sc.jpg\''),
                '',
                0)
            ->willReturn(0);

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'scale' => true,
            'width' => '30',
            'height' => '20',
            'output-filename' => null
        );

        $resizer = new Resizer($url, $cache, $opts);

        $originalPath = $resizer->obtainImagePath();
        $newPath = $resizer->obtainImage();

        $this->assertEquals(0, $resizer->executeCommand($newPath, $originalPath));

    }

    public function testResizeWithDefaultCommand() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);

        $cache->expects($this->once())
            ->method('exec')
            ->with(
                $this->equalTo('convert \'./cache/remote/mf.jpg\' -thumbnail \> -quality \'90\' \'./foo/mj.png\''),
                '',
                0)
            ->willReturn(0);

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'maxOnly' => true,
            'width' => null,
            'height' => null,
            'output-filename' => './foo/mj.png'
        );

        $resizer = new Resizer($url, $cache, $opts);

        $originalPath = $resizer->obtainImagePath();
        $newPath = $resizer->obtainImage();

        $this->assertEquals(0, $resizer->executeCommand($newPath, $originalPath));

    }

    /**
     * @expectedException RuntimeException
     */
    public function testResizeCommandFail() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);

        $cache->expects($this->once())
            ->method('exec')
            ->with(
                $this->equalTo('convert \'./cache/remote/mf.jpg\' -thumbnail \> -quality \'90\' \'./foo/mj.png\''),
                '',
                0)
            ->willReturn(1);

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'maxOnly' => true,
            'width' => null,
            'height' => null,
            'output-filename' => './foo/mj.png'
        );

        $resizer = new Resizer($url, $cache, $opts);

        $originalPath = $resizer->obtainImagePath();
        $newPath = $resizer->obtainImage();

        $resizer->executeCommand($newPath, $originalPath);

    }

    public function testResize() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);
        $cache->method('date')
            ->will($this->onConsecutiveCalls('20090417000926', '20060214132246'));
        $cache->method('md5_file')
            ->willReturn('a90d6abb5d7c3eccfdbb80507f5c6b51');
        $cache->method('getimagesize')
            ->willReturn(array('20', '30'));

        $cache->expects($this->once())
            ->method('exec')
            ->with(
                $this->equalTo('convert \'./cache/remote/mf.jpg\' -resize \'30\' -size \'30x20\' xc:\'transparent\' +swap -gravity center -composite -quality \'90\' \'./cache/a90d6abb5d7c3eccfdbb80507f5c6b51_w30_h20_cp.jpg\''),
                '',
                0)
            ->willReturn(0);

        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'crop' => true,
            'width' => '30',
            'height' => '20',
            'output-filename' => null
        );

        $resizer = new Resizer($url, $cache, $opts);

        $this->assertEquals('./cache/a90d6abb5d7c3eccfdbb80507f5c6b51_w30_h20_cp.jpg', $resizer->resize());

    }
}

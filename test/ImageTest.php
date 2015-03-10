<?php
require_once 'Image.php';
date_default_timezone_set('Europe/Berlin');

class ImageTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testOptionalCacheCollaboration() {
        $image = new Image('', 'nonCacheObject');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testOptionalConfigurationCollaboration() {
        $image = new Image('', new FileSystem(), 'nonConfigurationObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('Image', new Image());
        $this->assertInstanceOf('Image', new Image(''));
        $this->assertInstanceOf('Image', new Image('', new FileSystem()));
        $this->assertInstanceOf('Image', new Image('', new FileSystem(), new Configuration()));
    }

    public function testIsSanitizedAtInstantiation() {
        $url = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php%20define%20dictionary';
        $expected = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php define dictionary';

        $image = new Image($url);

        $this->assertEquals($expected, $image->sanitizedPath());
    }

    public function testNullImageIsSanitizedAtInstantiation() {
        $image = new Image(null);

        $this->assertEquals('', $image->sanitizedPath());
    }

    public function testIsHttpProtocol() {
        $url = 'https://example.com';

        $image = new Image($url);

        $this->assertTrue($image->isHttpProtocol());

        $image = new Image('ftp://example.com');

        $this->assertFalse($image->isHttpProtocol());

        $image = new Image(null);

        $this->assertFalse($image->isHttpProtocol());
    }

    public function testObtainFileName() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $image = new Image($url);

        $this->assertEquals('mf.jpg', $image->obtainFileName());
    }

    public function testObtainImageMD5() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('md5_file')
            ->willReturn('a90d6abb5d7c3eccfdbb80507f5c6b51');
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $image = new Image($url, $cache);

        $this->assertEquals('a90d6abb5d7c3eccfdbb80507f5c6b51', $image->obtainMD5());
    }

    public function testObtainExtensionSignal() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $image = new Image($url);

        $this->assertEquals('.jpg', $image->obtainExtensionSignal());
    }

    public function testObtainFilePathLocallyCached() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);
        $image = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', $cache, null);

        $this->assertEquals('./cache/remote/mf.jpg', $image->obtainFilePath());
    }

    public function testObtainFilePathLocallyCachedFail() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);

        $cache->method('filemtime')
            ->willReturn(21 * 60);
        $image = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', $cache, null);

        $this->assertEquals('./cache/remote/mf.jpg', $image->obtainFilePath());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testObtainFilePathFileDoesNotExist() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(false);
        $image = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', $cache, null);

        $image->obtainFilePath();
    }

    public function testComposePathWithOutputFilename() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $opts = array(
            'width' => null,
            'height' => null,
            'output-filename' => 'foo-output-filename'
        );
        $configuration = new Configuration($opts);
        $image = new Image($url, $cache, $configuration);

        $this->assertEquals('foo-output-filename', $image->composePath());
    }

    public function testComposePath() {
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
        $configuration = new Configuration($opts);
        $image = new Image($url, $cache, $configuration);

        $this->assertEquals('./cache/a90d6abb5d7c3eccfdbb80507f5c6b51_w30_h20.jpg', $image->composePath());
    }

}

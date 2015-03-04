<?php
require_once 'Image.php';

class ImagePathTest extends PHPUnit_Framework_TestCase {

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
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $image = new Image($url);

        $this->assertEquals('a90d6abb5d7c3eccfdbb80507f5c6b51', $image->obtainMD5());
    }

    public function testObtainExtensionSignal() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $image = new Image($url);

        $this->assertEquals('.jpg', $image->obtainExtensionSignal());
    }

    public function testObtainLocallyCachedFilePath() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_get_contents')
            ->willReturn('foo');

        $cache->method('file_exists')
            ->willReturn(true);
        $configuration = new Configuration();
        $image = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', $cache, $configuration);

        $this->assertEquals('./cache/remote/mf.jpg', $image->obtainFilePath());
    }

    public function testLocallyCachedFilePathFail() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(true);

        $cache->method('filemtime')
            ->willReturn(21 * 60);
        $configuration = new Configuration();
        $image = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', $cache, $configuration);

        $this->assertEquals('./cache/remote/mf.jpg', $image->obtainFilePath());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFilePathDoesNotExist() {
        $cache = $this->getMockBuilder('FileSystem')
            ->getMock();
        $cache->method('file_exists')
            ->willReturn(false);
        $configuration = new Configuration();
        $image = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', $cache, $configuration);

        $image->obtainFilePath();
    }

}

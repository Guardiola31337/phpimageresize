<?php
require_once 'ImagePath.php';

class ImagePathTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testOptionalCollaboration() {
        $imagePath = new ImagePath('', 'nonCacheObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('ImagePath', new ImagePath());
        $this->assertInstanceOf('ImagePath', new ImagePath(''));
        $this->assertInstanceOf('ImagePath', new ImagePath('', new FileSystem()));
    }

    public function testIsSanitizedAtInstantiation() {
        $url = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php%20define%20dictionary';
        $expected = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php define dictionary';

        $imagePath = new ImagePath($url);

        $this->assertEquals($expected, $imagePath->sanitizedPath());
    }

    public function testIsHttpProtocol() {
        $url = 'https://example.com';

        $imagePath = new ImagePath($url);

        $this->assertTrue($imagePath->isHttpProtocol());

        $imagePath = new ImagePath('ftp://example.com');

        $this->assertFalse($imagePath->isHttpProtocol());

        $imagePath = new ImagePath(null);

        $this->assertFalse($imagePath->isHttpProtocol());
    }

    public function testObtainFileName() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $imagePath = new ImagePath($url);

        $this->assertEquals('mf.jpg', $imagePath->obtainFileName());
    }

    public function testObtainImageMD5() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $imagePath = new ImagePath($url);

        $this->assertEquals('a90d6abb5d7c3eccfdbb80507f5c6b51', $imagePath->obtainMD5());
    }

    public function testObtainExtensionSignal() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $imagePath = new ImagePath($url);

        $this->assertEquals('.jpg', $imagePath->obtainExtensionSignal());
    }

}

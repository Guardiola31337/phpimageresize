<?php

class ConfigurationTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMinimunOptionsSet() {
        $subMinimumOptionsSet = array(
            'crop' => false,
            'scale' => 'false',
            'thumbnail' => false,
            'maxOnly' => false,
            'canvas-color' => 'transparent',
            'output-filename' => false,
            'quality' => 90,
            'cache_http_minutes' => 20,
            'width' => null,
            'height' => null);

        $configuration = new Configuration($subMinimumOptionsSet);
    }

    public function testObtainEmptyCropSignalWhenMinimunOptions() {
        $configuration = new Configuration();

        $this->assertEquals('', $configuration->obtainCropSignal());
    }

    public function testObtainCropSignal() {
        $opts = array(
            'crop' => true,
            'width' => null,
            'height' => null,
            'output-filename' => 'default-output-filename'
        );
        $configuration = new Configuration($opts);

        $this->assertEquals('_cp', $configuration->obtainCropSignal());
    }

    public function testObtainEmptyScaleSignalWhenMinimunOptions() {
        $configuration = new Configuration();

        $this->assertEquals('', $configuration->obtainScaleSignal());
    }

    public function testObtainScaleSignal() {
        $opts = array(
            'scale' => true,
            'width' => null,
            'height' => null,
            'output-filename' => 'default-output-filename'
        );
        $configuration = new Configuration($opts);

        $this->assertEquals('_sc', $configuration->obtainScaleSignal());
    }

    public function testObtainEmptyWidthSignalWhenMinimunOptions() {
        $configuration = new Configuration();

        $this->assertEquals('', $configuration->obtainWidthSignal());
    }

    public function testObtainWidthSignal() {
        $opts = array(
            'width' => '20',
            'height' => null,
            'output-filename' => 'default-output-filename'
        );
        $configuration = new Configuration($opts);

        $this->assertEquals('_w20', $configuration->obtainWidthSignal());
    }

    public function testObtainEmptyHeightSignalWhenMinimunOptions() {
        $configuration = new Configuration();

        $this->assertEquals('', $configuration->obtainHeightSignal());
    }

    public function testObtainHeightSignal() {
        $opts = array(
            'width' => null,
            'height' => '30',
            'output-filename' => 'default-output-filename'
        );
        $configuration = new Configuration($opts);

        $this->assertEquals('_h30', $configuration->obtainHeightSignal());
    }

    public function testObtainDefaultOutputFilename() {
        $configuration = new Configuration();

        $this->assertEquals('default-output-filename', $configuration->obtainOutputFilename());
    }

    public function testObtainOutputFilename() {
        $opts = array(
            'width' => null,
            'height' => null,
            'output-filename' => 'foo-output-filename'
        );
        $configuration = new Configuration($opts);

        $this->assertEquals('foo-output-filename', $configuration->obtainOutputFilename());
    }

    public function testObtainSignals() {
        $opts = array(
            'width' => '20',
            'height' => '30',
            'crop' => true,
            'scale' => true
        );
        $configuration = new Configuration($opts);

        $this->assertEquals('_w20_h30_cp_sc', $configuration->obtainSignals());
    }

    public function testOpts() {
        $this->assertInstanceOf('Configuration', new Configuration);
    }

    public function testNullOptsDefaults() {
        $defaults = array(
            'crop' => false,
            'scale' => false,
            'thumbnail' => false,
            'maxOnly' => false,
            'canvas-color' => 'transparent',
            'output-filename' => 'default-output-filename',
            'cacheFolder' => './cache/',
            'remoteFolder' => './cache/remote/',
            'quality' => 90,
            'cache_http_minutes' => 20,
            'width' => null,
            'height' => null
        );
        $configuration = new Configuration(null);

        $this->assertEquals($defaults, $configuration->asHash());
    }

    public function testDefaults() {
        $defaults = array(
            'crop' => false,
            'scale' => false,
            'thumbnail' => false,
            'maxOnly' => false,
            'canvas-color' => 'transparent',
            'output-filename' => 'default-output-filename',
            'cacheFolder' => './cache/',
            'remoteFolder' => './cache/remote/',
            'quality' => 90,
            'cache_http_minutes' => 20,
            'width' => null,
            'height' => null
        );
        $configuration = new Configuration();

        $asHash = $configuration->asHash();

        $this->assertEquals($defaults, $asHash);
    }

    public function testDefaultsNotOverwriteConfiguration() {

        $opts = array(
            'thumbnail' => true,
            'maxOnly' => true,
            'width' => null,
            'height' => null,
            'output-filename' => 'default-output-filename'
        );

        $configuration = new Configuration($opts);
        $configured = $configuration->asHash();

        $this->assertTrue($configured['thumbnail']);
        $this->assertTrue($configured['maxOnly']);
    }

    public function testObtainCache() {
        $configuration = new Configuration();

        $this->assertEquals('./cache/', $configuration->obtainCache());
    }

    public function testObtainRemote() {
        $configuration = new Configuration();

        $this->assertEquals('./cache/remote/', $configuration->obtainRemote());
    }

    public function testObtainConvertPath() {
        $configuration = new Configuration();

        $this->assertEquals('convert', $configuration->obtainConvertPath());
    }

    public function testObtainCrop() {
        $opts = array(
            'crop' => true,
            'width' => null,
            'height' => null,
            'output-filename' => 'default-output-filename'
        );
        $configuration = new Configuration($opts);

        $this->assertTrue($configuration->obtainCrop());
    }

    public function testObtainCropFail() {
        $configuration = new Configuration();

        $this->assertFalse($configuration->obtainCrop());
    }

    public function testObtainCanvasColor() {
        $configuration = new Configuration();

        $this->assertEquals('transparent', $configuration->obtainCanvasColor());
    }

    public function testObtainQuality() {
        $configuration = new Configuration();

        $this->assertEquals(90, $configuration->obtainQuality());
    }

    public function testObtainMaxOnly() {
        $opts = array(
            'maxOnly' => true,
            'width' => null,
            'height' => null,
            'output-filename' => 'default-output-filename'
        );
        $configuration = new Configuration($opts);

        $this->assertTrue($configuration->obtainMaxOnly());
    }

    public function testObtainMaxOnlyFail() {
        $configuration = new Configuration();

        $this->assertFalse($configuration->obtainMaxOnly());
    }

    public function testObtainScale() {
        $opts = array(
            'scale' => true,
            'width' => null,
            'height' => null,
            'output-filename' => 'default-output-filename'
        );
        $configuration = new Configuration($opts);

        $this->assertTrue($configuration->obtainScale());
    }

    public function testObtainScaleFail() {
        $configuration = new Configuration();

        $this->assertFalse($configuration->obtainScale());
    }

    public function testHasDimensions() {
        $configuration = new Configuration();

        $this->assertTrue($configuration->hasDimensions());
    }
}
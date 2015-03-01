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

}
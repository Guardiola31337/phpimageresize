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

    public function testObtainCropSignal() {
        $configuration = new Configuration();

        $this->assertEquals('_cp', $configuration->obtainCropSignal());
    }

}
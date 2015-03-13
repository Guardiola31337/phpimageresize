<?php

class Resizer {

    private $cache;
    private $configuration;
    private $image;

    public function __construct($url='', $cache=null, $opts=array()) {
        $this->cache = $cache;
        $this->checkOptions($opts);
        $this->instantiateConfiguration($opts);
        $this->instantiateImage($url, $cache, $this->configuration);
    }

    public function obtainImage() {
        return $this->image->composePath();
    }

    public function obtainImagePath() {
        return $this->image->obtainFilePath();
    }

    public function obtainOriginalPath() {
        return $this->image->sanitizedPath();
    }

    public function composeResizeOptions() {
        $w = $this->configuration->obtainWidth();
        $h = $this->configuration->obtainHeight();

        $resize = "x".$h;

        $hasCrop = $this->configuration->obtainCrop();
        return $resize;
    }

    private function checkOptions($opts) {
        if (!(is_array($opts))) throw new InvalidArgumentException();
    }

    private function instantiateConfiguration($opts) {
        try {
            $this->configuration = new Configuration($opts);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException;
        }
    }

    private function instantiateImage($url, $cache, $configuration) {
        try {
            $this->image = new Image($url, $cache, $configuration);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException;
        }
    }

}
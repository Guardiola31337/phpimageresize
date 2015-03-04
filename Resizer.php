<?php

require 'FileSystem.php';

class Resizer {

    private $path;
    private $configuration;

    public function __construct($path='', $configuration=null) {
        if ($configuration == null) $configuration = new Configuration();
        $this->checkConfiguration($configuration);
        $this->configuration = $configuration;
        $this->path = $path;
    }

    public function composeNewPath() {
        $image = new Image($this->path);
        $filename = $image->obtainMD5();

        $cropSignal = $this->configuration->obtainCropSignal();
        $scaleSignal = $this->configuration->obtainScaleSignal();
        $widthSignal = $this->configuration->obtainWidthSignal();
        $heightSignal = $this->configuration->obtainHeightSignal();
        $extension = $image->obtainExtensionSignal();

        $newPath = $this->configuration->obtainCache() .$filename.$widthSignal.$heightSignal.$cropSignal.$scaleSignal.$extension;

        if($this->configuration->obtainOutputFilename()) {
            $newPath = $this->configuration->obtainOutputFilename();
        }

        return $newPath;
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }

}
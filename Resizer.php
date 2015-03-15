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

    public function composeResizeOptions() {
        $w = $this->configuration->obtainWidth();
        $h = $this->configuration->obtainHeight();

        $hasCrop = $this->configuration->obtainCrop();
        $isPanoramic = $this->image->isPanoramic();

        $noCropAndPanoramic = !$hasCrop && $isPanoramic;
        $cropAndNoPanoramic = $hasCrop && !$isPanoramic;

        $resize = ($noCropAndPanoramic || $cropAndNoPanoramic) ? $w : "x".$h;

        return $resize;
    }

    public function commandWithCrop() {
        $w = $this->configuration->obtainWidth();
        $h = $this->configuration->obtainHeight();
        $originalPath = $this->obtainImagePath();
        $newPath = $this->obtainImage();
        $resize = $this->composeResizeOptions();

        $cmd = $this->configuration->obtainConvertPath() ." ". escapeshellarg($originalPath) ." -resize ". escapeshellarg($resize) .
            " -size ". escapeshellarg($w ."x". $h) .
            " xc:". escapeshellarg($this->configuration->obtainCanvasColor()) .
            " +swap -gravity center -composite -quality ". escapeshellarg($this->configuration->obtainQuality())." ".escapeshellarg($newPath);

        return $cmd;
    }

    public function commandWithScale() {
        $originalPath = $this->obtainImagePath();
        $newPath = $this->obtainImage();
        $resize = $this->composeResizeOptions();

        $cmd = $this->configuration->obtainConvertPath() ." ". escapeshellarg($originalPath) ." -resize ". escapeshellarg($resize) .
            " -quality ". escapeshellarg($this->configuration->obtainQuality()) . " " . escapeshellarg($newPath);

        return $cmd;
    }

    public function defaultCommand() {
        $w = $this->configuration->obtainWidth();
        $h = $this->configuration->obtainHeight();
        $originalPath = $this->obtainImagePath();
        $newPath = $this->obtainImage();

        $cmd = $this->configuration->obtainConvertPath() ." " . escapeshellarg($originalPath) .
            " -thumbnail ". (!empty($h) ? 'x':'') . $w ."".
            ($this->configuration->obtainMaxOnly() == true ? "\>" : "") .
            " -quality ". escapeshellarg($this->configuration->obtainQuality()) ." ". escapeshellarg($newPath);

        return $cmd;
    }

    public function executeCommand() {
        if($this->configuration->hasDimensions()):
            $cmd = $this->commandWithCrop();
            if($this->configuration->obtainScale()):
                $cmd = $this->commandWithScale();
            endif;
        else:
            $cmd = $this->defaultCommand();
        endif;

        $code_return = $this->cache->exec($cmd, $output, $return_code);

        if($code_return != 0) {
            error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
            throw new RuntimeException();
        }

        return $return_code;
    }

    public function resize() {
        $originalPath = $this->obtainImagePath();
        $newPath = $this->obtainImage();

        if(!$this->image->isImageInCache($newPath, $originalPath)):
            try {
                $this->executeCommand();
            } catch (RuntimeException $e) {
                throw new RuntimeException();
            }
        endif;

        return $newPath;
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
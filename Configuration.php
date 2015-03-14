<?php

class Configuration {
    const CACHE_PATH = './cache/';
    const REMOTE_PATH = './cache/remote/';
    const CONVERT_PATH = 'convert';
    const DEFAULT_OUTPUT_FILENAME = 'default-output-filename';
    const DEFAULT_CANVAS_COLOR = 'transparent';
    const DEFAULT_QUALITY = 90;

    const CACHE_KEY = 'cacheFolder';
    const REMOTE_KEY = 'remoteFolder';
    const CACHE_MINUTES_KEY = 'cache_http_minutes';
    const WIDTH_KEY = 'width';
    const HEIGHT_KEY = 'height';
    const CROP_KEY = 'crop';
    const SCALE_KEY = 'scale';
    const OUTPUT_FILENAME_KEY = 'output-filename';
    const CANVAS_COLOR_KEY = 'canvas-color';
    const QUALITY_KEY = 'quality';
    const MAX_ONLY_KEY = 'maxOnly';

    const CROP_SIGNAL = '_cp';
    const SCALE_SIGNAL = '_sc';
    const WIDTH_SIGNAL = '_w';
    const HEIGHT_SIGNAL = '_h';

    private $opts;

    public function __construct($opts=array()) {
        $sanitized= $this->sanitize($opts);

        $defaults = array(
            self::CROP_KEY => false,
            self::SCALE_KEY => false,
            'thumbnail' => false,
            self::MAX_ONLY_KEY => false,
            self::CANVAS_COLOR_KEY => self::DEFAULT_CANVAS_COLOR,
            self::OUTPUT_FILENAME_KEY => self::DEFAULT_OUTPUT_FILENAME,
            self::CACHE_KEY => self::CACHE_PATH,
            self::REMOTE_KEY => self::REMOTE_PATH,
            self::QUALITY_KEY => self::DEFAULT_QUALITY,
            self::CACHE_MINUTES_KEY => 20,
            self::WIDTH_KEY => null,
            self::HEIGHT_KEY => null);

        if(empty($opts)) {
            $opts = $defaults;
        }

        if(empty($opts[self::OUTPUT_FILENAME_KEY]) && empty($opts[self::HEIGHT_KEY]) && empty($opts[self::WIDTH_KEY])) {
            throw new InvalidArgumentException;
        }

        $this->opts = array_merge($defaults, $sanitized);
    }

    public function asHash() {
        return $this->opts;
    }

    public function obtainCache() {
        return $this->opts[self::CACHE_KEY];
    }

    public function obtainRemote() {
        return $this->opts[self::REMOTE_KEY];
    }

    public function obtainConvertPath() {
        return self::CONVERT_PATH;
    }

    public function obtainWidth() {
        return $this->opts[self::WIDTH_KEY];
    }

    public function obtainHeight() {
        return $this->opts[self::HEIGHT_KEY];
    }

    public function obtainCacheMinutes() {
        return $this->opts[self::CACHE_MINUTES_KEY];
    }

    public function obtainCropSignal() {
        return isset($this->opts[self::CROP_KEY]) && $this->opts[self::CROP_KEY] == true ? self::CROP_SIGNAL : "";
    }

    public function obtainScaleSignal() {
        return isset($this->opts[self::SCALE_KEY]) && $this->opts[self::SCALE_KEY] == true ? self::SCALE_SIGNAL : "";
    }

    public function obtainWidthSignal() {
        return isset($this->opts[self::WIDTH_KEY]) ? self::WIDTH_SIGNAL.$this->obtainWidth() : '';
    }

    public function obtainHeightSignal() {
        return isset($this->opts[self::HEIGHT_KEY]) ? self::HEIGHT_SIGNAL.$this->obtainHeight() : '';
    }

    public function obtainOutputFilename() {
        return $this->opts[self::OUTPUT_FILENAME_KEY];
    }

    public function obtainSignals() {
        $widthSignal = $this->obtainWidthSignal();
        $heightSignal = $this->obtainHeightSignal();
        $cropSignal = $this->obtainCropSignal();
        $scaleSignal = $this->obtainScaleSignal();

        return $widthSignal.$heightSignal.$cropSignal.$scaleSignal;
    }

    public function obtainCrop() {
        return $this->opts[self::CROP_KEY];
    }

    public function obtainCanvasColor() {
        return $this->opts[self::CANVAS_COLOR_KEY];
    }

    public function obtainQuality() {
        return $this->opts[self::QUALITY_KEY];
    }

    public function obtainMaxOnly() {
        return $this->opts[self::MAX_ONLY_KEY];
    }

    public function obtainScale() {
        return $this->opts[self::SCALE_KEY];
    }

    public function hasDimensions() {
        return (!empty($this->opts[self::HEIGHT_KEY]) && !empty($this->opts[self::WIDTH_KEY]));
    }

    private function sanitize($opts) {
        if($opts == null) return array();

        return $opts;
    }

}
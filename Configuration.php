<?php

class Configuration {
    const CACHE_PATH = './cache/';
    const REMOTE_PATH = './cache/remote/';
    const CONVERT_PATH = 'convert';

    const CACHE_KEY = 'cacheFolder';
    const REMOTE_KEY = 'remoteFolder';
    const CACHE_MINUTES_KEY = 'cache_http_minutes';
    const WIDTH_KEY = 'width';
    const HEIGHT_KEY = 'height';
    const CROP_KEY = 'crop';
    const SCALE_KEY = 'scale';

    const CROP_SIGNAL = '_cp';
    const SCALE_SIGNAL = '_sc';
    const WIDTH_SIGNAL = '_w';

    private $opts;

    public function __construct($opts=array()) {
        $sanitized= $this->sanitize($opts);

        $defaults = array(
            self::CROP_KEY => false,
            self::SCALE_KEY => false,
            'thumbnail' => false,
            'maxOnly' => false,
            'canvas-color' => 'transparent',
            'output-filename' => 'default-output-filename',
            self::CACHE_KEY => self::CACHE_PATH,
            self::REMOTE_KEY => self::REMOTE_PATH,
            'quality' => 90,
            'cache_http_minutes' => 20,
            self::WIDTH_KEY => null,
            'height' => null);

        if(empty($opts)) {
            $opts = $defaults;
        }

        if(empty($opts['output-filename']) && empty($opts['height']) && empty($opts[self::WIDTH_KEY])) {
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
    private function sanitize($opts) {
        if($opts == null) return array();

        return $opts;
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
        return 'height';
    }

}
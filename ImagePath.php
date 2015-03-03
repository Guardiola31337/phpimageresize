<?php

class ImagePath {

    const EXTENSION_KEY = 'extension';

    const EXTENSION_SIGNAL = '.';

    private $path;
    private $valid_http_protocols = array('http', 'https');
    private $cache;

    public function __construct($url='', $cache=null) {
        $this->path = $this->sanitize($url);
        if (!($cache instanceof FileSystem)) throw new InvalidArgumentException();
        $this->cache = $cache;
    }

    public function sanitizedPath() {
        return $this->path;
    }

    public function isHttpProtocol() {
        return in_array($this->obtainScheme(), $this->valid_http_protocols);
    }

    public function obtainFileName() {
        $finfo = pathinfo($this->path);
        list($filename) = explode('?',$finfo['basename']);
        return $filename;
    }

    public function obtainMD5() {
        return md5_file($this->path);
    }

    public function obtainExtensionSignal() {
        return self::EXTENSION_SIGNAL .$this->obtainExtension();
    }

    private function obtainExtension() {
        $filename = $this->obtainFileName();
        $finfo = pathinfo($filename);
        return $finfo[self::EXTENSION_KEY];
    }

    private function sanitize($path) {
        return urldecode($path);
    }

    private function obtainScheme() {
        if ($this->path == '') return '';
        $purl = parse_url($this->path);
        return $purl['scheme'];
    }
}
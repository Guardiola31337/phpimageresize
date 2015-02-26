<?php

class ImagePath {

    const EXTENSION_KEY = 'extension';

    private $path;
    private $valid_http_protocols = array('http', 'https');

    public function __construct($url='') {
        $this->path = $this->sanitize($url);
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

    public function obtainExtension() {
        $filename = $this->obtainFileName();
        $finfo = pathinfo($filename);
        $ext = $finfo[self::EXTENSION_KEY];
        return $ext;
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
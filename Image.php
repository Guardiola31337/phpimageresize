<?php

require 'FileSystem.php';

class Image {

    const EXTENSION_KEY = 'extension';

    const EXTENSION_SIGNAL = '.';

    const DATE_FORMAT = "YmdHis";

    private $path;
    private $valid_http_protocols = array('http', 'https');
    private $cache;

    private $configuration;

    public function __construct($url='', $cache=null, $configuration=null) {
        if ($cache == null) $cache = new FileSystem();
        if ($configuration == null) $configuration = new Configuration();
        $this->path = $this->sanitize($url);
        $this->checkCache($cache);
        $this->cache = $cache;
        $this->checkConfiguration($configuration);
        $this->configuration = $configuration;
    }

    public function sanitizedPath() {
        return $this->path;
    }

    public function isHttpProtocol() {
        return in_array($this->obtainScheme(), $this->valid_http_protocols);
    }

    public function obtainFileName() {
        $finfo = pathinfo($this->path);
        list($filename) = explode('?', $finfo['basename']);
        return $filename;
    }

    public function obtainMD5() {
        return $this->cache->md5_file($this->path);
    }

    public function obtainExtensionSignal() {
        return self::EXTENSION_SIGNAL .$this->obtainExtension();
    }

    public function obtainFilePath() {
        $imagePath = '';

        if($this->isHttpProtocol()):
            $filename = $this->obtainFileName();
            $local_filepath = $this->configuration->obtainRemote() .$filename;
            $inCache = $this->isInCache($local_filepath);

            if(!$inCache):
                $this->download($local_filepath);
            endif;
            $imagePath = $local_filepath;
        endif;

        if(!$this->cache->file_exists($imagePath)):
            $imagePath = $_SERVER['DOCUMENT_ROOT'].$imagePath;
            if(!$this->cache->file_exists($imagePath)):
                throw new RuntimeException();
            endif;
        endif;

        return $imagePath;
    }

    public function composePath() {
        $this->path = $this->obtainFilePath();

        if ($this->configuration->obtainOutputFilename()) {
            $newPath = $this->configuration->obtainOutputFilename();
            return $newPath;
        }

        $filename = $this->obtainMD5();
        $extension = $this->obtainExtensionSignal();

        $confSignals = $this->configuration->obtainSignals();

        $newPath = $this->configuration->obtainCache() .$filename.$confSignals.$extension;

        return $newPath;
    }

    public function isImageInCache($composePath, $originalPath) {
        $composeFileExists = $this->cache->file_exists($composePath);
        $fileNotOutdated = $this->fileNotOutdated($composePath, $originalPath);

        return $composeFileExists && $fileNotOutdated;
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

    private function checkCache($cache) {
        if (!($cache instanceof FileSystem)) throw new InvalidArgumentException();
    }

    private function checkConfiguration($configuration){
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }

    private function isInCache($filePath) {
        $fileExists = $this->cache->file_exists($filePath);
        $fileValid = $this->fileNotExpired($filePath);

        return $fileExists && $fileValid;
    }

    private function fileNotExpired($filePath) {
        $cacheMinutes = $this->configuration->obtainCacheMinutes();
        return $this->cache->filemtime($filePath) < strtotime('+'. $cacheMinutes. ' minutes');
    }

    private function fileNotOutdated($composePath, $originalPath) {
        $origFileTime = $this->cache->date(self::DATE_FORMAT, $this->cache->filemtime($originalPath));
        $newFileTime = $this->cache->date(self::DATE_FORMAT, $this->cache->filemtime($composePath));
        return $newFileTime >= $origFileTime;
    }

    private function download($filePath) {
        $img = $this->cache->file_get_contents($this->path);
        $this->cache->file_put_contents($filePath, $img);
    }
}
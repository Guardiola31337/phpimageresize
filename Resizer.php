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

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }

}
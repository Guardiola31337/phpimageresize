<?php

class Resizer {

    private $path;
    private $configuration;

    public function __construct($path='', $opts=array()) {
        $this->path = $path;
        $this->checkOptions($opts);
        $this->instantiateConfiguration($opts);
    }

    private function checkOptions($opts) {
        if (!(is_array($opts))) throw new InvalidArgumentException();
    }

    private function instantiateConfiguration($opts) {
        try {
            $configuration = new Configuration($opts);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException;
        }
    }

}
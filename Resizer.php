<?php

class Resizer {

    private $path;
    private $opts;

    public function __construct($path='', $opts=array()) {
        $this->path = $path;
        $this->checkOptions($opts);
        $this->opts = $opts;
    }

    private function checkOptions($opts) {
        if (!(is_array($opts))) throw new InvalidArgumentException();
    }

}
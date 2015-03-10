<?php

class Resizer {

    private $path;
    private $opts;

    public function __construct($path='', $opts=array()) {
        $this->path = $path;
        $this->opts = $opts;
    }

}
<?php

require 'Resizer.php';

function resize($originalImage, $opts=null){
    try {
        $resizer = new Resizer($originalImage, null, $opts);
    } catch (InvalidArgumentException $e) {
        return 'needed more arguments for resize';
    }

    try {
        $newPath = $resizer->resize();
    } catch (Exception $e) {
        return 'something went wrong';
    }

	$cacheFilePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $newPath);

	return $cacheFilePath;
	
}

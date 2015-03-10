<?php

require 'Image.php';
require 'Configuration.php';
require 'Resizer.php';

function isInCache($path, $image) {
	$isInCache = false;
	if(file_exists($path) == true):
		$isInCache = true;
		$origFileTime = date("YmdHis",filemtime($image));
		$newFileTime = date("YmdHis",filemtime($path));
		if($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
			$isInCache = false;
		endif;
	endif;

	return $isInCache;
}

function defaultShellCommand($configuration, $image, $newPath) {
	$opts = $configuration->asHash();
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();

	$command = $configuration->obtainConvertPath() ." " . escapeshellarg($image) .
	" -thumbnail ". (!empty($h) ? 'x':'') . $w ."".
	(isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "") .
	" -quality ". escapeshellarg($opts['quality']) ." ". escapeshellarg($newPath);

	return $command;
}

function isPanoramic($image) {
	list($width,$height) = getimagesize($image);
	return $width > $height;
}

function composeResizeOptions($image, $configuration) {
	$opts = $configuration->asHash();
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();

	$resize = "x".$h;

	$hasCrop = (true === $opts['crop']);

	if(!$hasCrop && isPanoramic($image)):
		$resize = $w;
	endif;

	if($hasCrop && !isPanoramic($image)):
		$resize = $w;
	endif;

	return $resize;
}

function commandWithScale($image, $newPath, $configuration) {
	$opts = $configuration->asHash();
	$resize = composeResizeOptions($image, $configuration);

	$cmd = $configuration->obtainConvertPath() ." ". escapeshellarg($image) ." -resize ". escapeshellarg($resize) .
		" -quality ". escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);

	return $cmd;
}

function commandWithCrop($image, $newPath, $configuration) {
	$opts = $configuration->asHash();
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();
	$resize = composeResizeOptions($image, $configuration);

	$cmd = $configuration->obtainConvertPath() ." ". escapeshellarg($image) ." -resize ". escapeshellarg($resize) .
		" -size ". escapeshellarg($w ."x". $h) .
		" xc:". escapeshellarg($opts['canvas-color']) .
		" +swap -gravity center -composite -quality ". escapeshellarg($opts['quality'])." ".escapeshellarg($newPath);

	return $cmd;
}

function doResize($image, $newPath, $configuration) {
	$opts = $configuration->asHash();
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();

	if(!empty($w) and !empty($h)):
		$cmd = commandWithCrop($image, $newPath, $configuration);
		if(true === $opts['scale']):
			$cmd = commandWithScale($image, $newPath, $configuration);
		endif;
	else:
		$cmd = defaultShellCommand($configuration, $image, $newPath);
	endif;

	$c = exec($cmd, $output, $return_code);
	if($return_code != 0) {
		error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
		throw new RuntimeException();
	}
}

function resize($originalImage, $opts=null){
	try {
		$configuration = new Configuration($opts);
	} catch (InvalidArgumentException $e) {
		return 'needed more arguments for resize';
	}

	$image = new Image($originalImage, null, $configuration);

	// This has to be done in resizer resize

    try {
        $newPath = $image->composePath();
    } catch (Exception $e) {
        return 'image not found';
    }

	$originalImage = $image->sanitizedPath();

    $create = !isInCache($newPath, $originalImage);

	if($create == true):
		try {
			doResize($originalImage, $newPath, $configuration);
		} catch (Exception $e) {
			return 'cannot resize the image';
		}
	endif;

	// The new path must be the return value of resizer resize

	$cacheFilePath = str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);

	return $cacheFilePath;
	
}

<?php

require 'ImagePath.php';
require 'Configuration.php';
require 'Resizer.php';

function sanitize($path) {
	return urldecode($path);
}

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

function composeNewPath($image, $configuration) {
	$filename = $image->obtainMD5();

	$cropSignal = $configuration->obtainCropSignal();
	$scaleSignal = $configuration->obtainScaleSignal();
	$widthSignal = $configuration->obtainWidthSignal();
	$heightSignal = $configuration->obtainHeightSignal();
	$extension = $image->obtainExtensionSignal();

	$newPath = $configuration->obtainCache() .$filename.$widthSignal.$heightSignal.$cropSignal.$scaleSignal.$extension;

	if($configuration->obtainOutputFilename()) {
		$newPath = $configuration->obtainOutputFilename();
	}

	return $newPath;
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

function resize($originalImage,$opts=null){
	$path = new Image($originalImage);

	try {
		$configuration = new Configuration($opts);
	} catch (InvalidArgumentException $e) {
		return 'needed more arguments for resize';
	}

	$resizer = new Resizer($path, $configuration);

	// This has to be done in resizer resize

	try {
		$originalImage = $resizer->obtainFilePath();
	} catch (Exception $e) {
		return 'image not found';
	}


	$newPath = composeNewPath($originalImage, $configuration);

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

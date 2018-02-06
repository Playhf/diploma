<?php
session_start();
function randString(){
	$nChars = 5;
	$string = "1234567890ABCDEFGHIJKLMNPQRSTUVWXYZ";
	$result = "";
	while(strlen($result) != $nChars){
		$result .= substr(str_shuffle($string), rand(0, strlen($string)), 1);
	}
	return $result;
}
$_SESSION["captcha"] = $string = randString();
$img = imageCreateFromJPEG('noise.jpg');
$colors = array(
				$yellow 	= imageColorAllocate($img, 70,70,0),
				$red		= imageColorAllocate($img, 255,0,0),
				$green 		= imageColorAllocate($img, 13,153,40),
				$blue 		= imageColorAllocate($img, 0,0,255));
for($i = 0; $i < strlen($string); $i++){
	imageTtfText($img, 25, rand(0,50), 10+30*($i+1), 35, $colors[rand(0,3)], "fonts-japanese-gothic.ttf", $string{$i});
}

header("Content-type: image/jpg");
imageJPEG($img, null, 90);

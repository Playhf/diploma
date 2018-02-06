<?php
session_start();
header("Content-type: image/jpeg");
$filename = isset($_SESSION[md5(session_id())]["img"])  ? "user_images/" . $_SESSION[md5(session_id())]["img"]
                                                        : "user.jpg";
list($width, $height) = getimagesize($filename);
$new_width 	= 50;
$new_height = 50;
$image_p 	= imageCreateTrueColor($new_width, $new_height);
$image 		= imageCreateFromJPEG($filename);
imageCopyResampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
imageJPEG($image_p, null, 100);

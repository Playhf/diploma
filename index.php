<?php
#ini_set("display_errors", 1);
#error_reporting(E_ALL & ~E_NOTICE);
define('DS', DIRECTORY_SEPARATOR);

set_include_path( get_include_path()     . PATH_SEPARATOR . "controllers/"
                                                        . PATH_SEPARATOR . "main/"
                                                        . PATH_SEPARATOR . "models/"
                                                        . PATH_SEPARATOR . "template/"
                                                        . PATH_SEPARATOR . "template/layout/"
                                                        . PATH_SEPARATOR . "template/images/"
                                                        . PATH_SEPARATOR . "errors/"
                                                        . PATH_SEPARATOR . 'jslib/');

require_once ("main/autoload.php");

session_start();

$front = new FrontController();
$front->run();
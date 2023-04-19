<?php

if(file_exists("../includes/config.php")){
    require_once('../includes/config.php');
}

// Default timezone
date_default_timezone_set("UTC"); 

// Defaullt multibyte encoding
mb_internal_encoding("UTF-8"); 

// ROOTPATH
define("ROOTPATH", dirname(__FILE__)."/..");

// Check if SSL enabled
$ssl = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] && $_SERVER["HTTPS"] != "off" 
     ? true : false;
define("SSL_ENABLED", $ssl);

// Define APPURL
$app_url = (SSL_ENABLED ? "https" : "http")
         . "://"
         . $_SERVER["SERVER_NAME"]
         . (dirname($_SERVER["SCRIPT_NAME"]) == DIRECTORY_SEPARATOR ? "" : "/")
         . trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");

$p = strrpos($app_url, "install");
if ($p !== false) {
    $app_url = substr_replace($app_url, "", $p, strlen("install"));
}

define("APPURL", $app_url);
define("VERSION", $config['version']);


require_once ROOTPATH."/install/app/helpers/common.helper.php";
require_once ROOTPATH."/install/app/helpers/password.helper.php";
require_once ROOTPATH."/install/app/vendor/RandomCompat/random.php";
require_once ROOTPATH."/install/app/core/Autoloader.php";
$loader = new Autoloader;
$loader->register();
$loader->addBaseDir(ROOTPATH.'/install/app/core');
$loader->addBaseDir(ROOTPATH.'/install/app/vendor');

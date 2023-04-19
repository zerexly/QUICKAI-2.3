<?php
define("ROOTPATH", dirname(__DIR__));
define("APPPATH", ROOTPATH."/php/");
define("ADMINPATH", __DIR__);

// Check site url
$protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] && $_SERVER["HTTPS"] != "off"
    ? "https://" : "http://";
$admin_url = $protocol
    . $_SERVER["HTTP_HOST"]
    . (dirname($_SERVER["SCRIPT_NAME"]) == DIRECTORY_SEPARATOR ? "" : "/")
    . trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");

define("ADMINURL", dirname($admin_url));
define("SITEURL", dirname(dirname($admin_url)));
$config['site_url'] = SITEURL."/";

require_once ROOTPATH . '/includes/autoload.php';
require_once ROOTPATH . '/includes/functions/func.admin.php';

admin_session_start();
if (!checkloggedadmin()) {
    redirect_parent('login.php'());
}
<?php
/**
 * @author Sahil
 * @package QuickAI - OpenAI Content & Image Generator
 * @Copyright (c) 2015-23 Sahil.com
 */

// Path to root directory of app.
define("ROOTPATH", dirname(__FILE__));
// Path to app folder.
define("APPPATH", ROOTPATH."/php/");

// Check if SSL enabled
if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']))
    $protocol = $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https" ? "https://" : "http://";
else
    $protocol = !empty($_SERVER['HTTPS']) && $_SERVER["HTTPS"] != "off" ? "https://" : "http://";

// Define APPURL
$site_url = $protocol
    . $_SERVER["HTTP_HOST"]
    . (dirname($_SERVER["SCRIPT_NAME"]) == DIRECTORY_SEPARATOR ? "" : "/")
    . trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");

define("SITEURL", $site_url);

require_once ROOTPATH . '/includes/config.php';

if(!isset($config['installed'])) {
    $site_url = $protocol . $_SERVER['HTTP_HOST'] . str_replace ("index.php", "", $_SERVER['PHP_SELF']);
    header("Location: ".$site_url."install/");
    exit;
}

require_once ROOTPATH . '/includes/lib/AltoRouter.php';

// Start routing.
$router = new AltoRouter();
$bp = trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");
$router->setBasePath($bp ? "/".$bp : "");
/* Setup the URL routing. This is production ready. */
require_once APPPATH.'_route.php';

// API Routes
require_once ROOTPATH . '/includes/autoload.php';
define("TEMPLATE_PATH", ROOTPATH.'/templates/'.$config['tpl_name']);
define("TEMPLATE_URL", SITEURL.'/templates/'.$config['tpl_name']);

$config['app_url'] = get_site_url(SITEURL)."/php/";

/* Match the current request */
$match=$router->match();

if(isset($match['params']['lang'])) {
    if ($match['params']['lang'] != ""){
        change_user_lang($match['params']['lang']);
    }
}
if(file_exists(ROOTPATH . '/includes/lang/lang_'.$config['lang'].'.php')){
    require_once ROOTPATH . '/includes/lang/lang_'.$config['lang'].'.php';
}else{
    require_once ROOTPATH . '/includes/lang/lang_english.php';
}

run_cron_job();
if($match) {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $_GET = array_merge($match['params'],$_GET);
    }

    sec_session_start();
    $mysqli = db_connect();

    if(get_option('enable_maintenance_mode')){

        $protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : '';
        if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0' ), true ) ) {
            $protocol = 'HTTP/1.0';
        }
        header( "$protocol 503 Service Unavailable", true, 503 );
        header( 'Content-Type: text/html; charset=utf-8' );
        header( 'Retry-After: 30' );

        // current page
        define("CURRENT_PAGE", 'maintenance_mode');

        require APPPATH.'global/maintenance_mode.php';
        exit;
    }

    // current page
    define("CURRENT_PAGE", str_replace('.php', '', $match['target']));

    check_affiliate();
    require APPPATH.$match['target'];
}
else {
    // current page
    define("CURRENT_PAGE", '404');

   header("HTTP/1.0 404 Not Found");
   require APPPATH.'global/404.php';
}
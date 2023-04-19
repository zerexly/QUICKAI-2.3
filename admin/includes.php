<?php
define("ROOTPATH", dirname(__DIR__));
define("APPPATH", ROOTPATH."/php/");
define("ADMINPATH", __DIR__);

require_once ROOTPATH . '/includes/autoload.php';

if(isset($_GET['lang'])) {
    if ($_GET['lang'] != ""){
        change_user_lang($_GET['lang']);
    }
}

require_once ROOTPATH . '/includes/lang/lang_'.$config['lang'].'.php';

$admin_url = $config['site_url']."admin/";
define("SITEURL", $config['site_url']);
define("ADMINURL", $admin_url);

$mysqli = db_connect();
admin_session_start();
if (!checkloggedadmin()) {
    headerRedirect(ADMINURL.'login.php?redirect_to='.get_current_page_url());
}
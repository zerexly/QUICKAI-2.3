<?php

define("ROOTPATH", dirname(__DIR__));
define("APPPATH", ROOTPATH."/php/");

require_once ROOTPATH . '/includes/autoload.php';

$result['success'] = false;
$result['message'] = $result['error'] = 'Please verify your purchase code <a href="'.$config['site_url'].'admin/global/settings.php#quick_purchase_code">here</a>.';
die(json_encode($result));

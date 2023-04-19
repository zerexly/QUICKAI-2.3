<?php
if(isset($match['params']['i'])){
    if (file_exists(ROOTPATH.'/includes/payments/' . $match['params']['i'] . '/webhook.php')) {
        require_once(ROOTPATH.'/includes/payments/' . $match['params']['i'] . '/webhook.php');
    }
}
die();
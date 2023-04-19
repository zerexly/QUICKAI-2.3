<?php

$access_token = $_GET['access_token'];
global $link;

if(isset($access_token)){
    message(__('Success'), __('Your payment is processing.'), $link['TRANSACTION']);
}
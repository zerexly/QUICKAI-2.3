<?php

require_once('IyzipayBootstrap.php');

IyzipayBootstrap::init();

class Config
{
    public static function options()
    {
        global $access_token;
        $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
        if($payment_type == "order") {
            $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
            $iyzico_sandbox_mode = get_restaurant_option($restaurant_id,'restaurant_iyzico_sandbox_mode');
            $iyzico_api_key = get_restaurant_option($restaurant_id,'restaurant_iyzico_api_key');
            $iyzico_secret_key = get_restaurant_option($restaurant_id,'restaurant_iyzico_secret_key');
        }else{
            $iyzico_sandbox_mode = get_option('iyzico_sandbox_mode');
            $iyzico_api_key = get_option('iyzico_api_key');
            $iyzico_secret_key = get_option('iyzico_secret_key');
        }
        if($iyzico_sandbox_mode == 'test'){
            $payment_link = 'https://sandbox-api.iyzipay.com';
        }else{
            $payment_link = 'https://api.iyzipay.com';
        }
        $options = new \Iyzipay\Options();
        $options->setApiKey($iyzico_api_key);
        $options->setSecretKey($iyzico_secret_key);
        $options->setBaseUrl($payment_link);
        return $options;
    }
}
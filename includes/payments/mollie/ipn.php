<?php

if (!empty($_GET['access_token'])) {
    $access_token = filter_var($_GET['access_token'], FILTER_SANITIZE_STRING);
    if (!empty($_SESSION['quickad'][$access_token]['mollie_id'])) {
        $mollie_id = filter_var($_SESSION['quickad'][$access_token]['mollie_id'], FILTER_SANITIZE_STRING);
        include_once 'Mollie/API/Autoloader.php';
        $api = new \Mollie_API_Client();

        $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
        if($payment_type == "order") {
            $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
            $mollie_api_key = get_restaurant_option($restaurant_id,'restaurant_mollie_api_key');
        } else {
            $mollie_api_key = get_option('mollie_api_key');
        }

        $api->setApiKey($mollie_api_key);

        $mollie_payment = $api->payments->get($mollie_id);

        if ($mollie_payment->isOpen() || $mollie_payment->isPending() || $mollie_payment->isPaid()) {
            payment_success_save_detail($access_token);
        }
    }
    payment_fail_save_detail($access_token);
    $error_msg = __('Transaction was not successful');
    payment_error("error", $error_msg, $access_token);
    exit();
}
error(__('Page Not Found'), __LINE__, __FILE__, 1);
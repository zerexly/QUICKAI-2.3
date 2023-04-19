<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

if (isset($_SESSION['quickad'][$access_token]['payment_type'])) {
    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];

    if($payment_type == "order") {
        $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
        $restaurant = ORM::for_table($config['db']['pre'] . 'restaurant')
            ->find_one($restaurant_id);

        $userdata = get_user_data(null, $restaurant['user_id']);
        $currency = !empty($userdata['currency'])?$userdata['currency']:get_option('currency_code');

        $mollie_api_key = get_restaurant_option($restaurant_id,'restaurant_mollie_api_key');
    } else {
        $currency = filter_var($config['currency_code'], FILTER_SANITIZE_STRING);
        $mollie_api_key = get_option('mollie_api_key');
    }

    if ($currency != 'EUR') {
        error(__('Mollie accepts payments in Euro only.'), __LINE__, __FILE__, 1);
        exit();
    }

    $title = filter_var($_SESSION['quickad'][$access_token]['name'], FILTER_SANITIZE_STRING);
    $amount = filter_var($_SESSION['quickad'][$access_token]['amount'], FILTER_SANITIZE_STRING);

    try {
        include_once 'Mollie/API/Autoloader.php';
        $api = new \Mollie_API_Client();
        $api->setApiKey($mollie_api_key);

        $mollie_payment = $api->payments->create(array(
            'amount' => $amount,
            'description' => $title,
            'redirectUrl' => $link['IPN'] . "/?access_token=" . $access_token . "&i=mollie",
            'metadata' => array('access_token' => $access_token),
            'issuer' => null
        ));
        if ($mollie_payment->isOpen()) {
            $_SESSION['quickad'][$access_token]['mollie_id'] = $mollie_payment->id;
            header('Location: ' . $mollie_payment->getPaymentUrl());
            exit;
        } else {

            payment_fail_save_detail($access_token);
            email($config['admin_email'], $config['site_title'] . ' Admin', 'Mollie error in ' . $config['site_title'], 'Mollie error in ' . $config['site_title']);

            payment_error("error", __('Mollie error.'), $access_token);
            exit();
        }

    } catch (\Exception $e) {
        payment_fail_save_detail($access_token);
        echo $error_msg = $e->getMessage();

        email($config['admin_email'], $config['site_title'] . ' Admin', 'Mollie error in ' . $config['site_title'], 'Mollie error in ' . $config['site_title'] . '. Error Message: ' . $error_msg);

        payment_error("error", $error_msg, $access_token);
        exit();
    }

} else {
    error(__('Invalid Payment Processor'), __LINE__, __FILE__, 1);
    exit();
}
<?php
require_once('iyzico/config.php');
$token=$_POST['token'];

$request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
$request->setLocale(\Iyzipay\Model\Locale::TR);
$request->setToken("$token");
$checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, Config::options());

//print_r($checkoutForm->getPaymentStatus());
$payment_status = $checkoutForm->getStatus();
$transaction_number = $checkoutForm->getpaymentId();

if ($payment_status=="failure") {

    payment_fail_save_detail($access_token);

    $error_msg = __("Transaction was not successful: Last gateway response was: ").$payment_status;
    payment_error("error",$error_msg,$access_token);
    exit();

} elseif ($payment_status=="success") {

    payment_success_save_detail($access_token);
}
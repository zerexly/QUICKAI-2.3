<?php
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;
$error = "Payment Failed";

if (empty($_POST['razorpay_payment_id']) === false)
{
    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
    if($payment_type == "order") {
        $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
        $restaurant = ORM::for_table($config['db']['pre'] . 'restaurant')
            ->find_one($restaurant_id);

        $razorpay_api_key = get_restaurant_option($restaurant_id,'restaurant_razorpay_api_key');
        $razorpay_secret_key = get_restaurant_option($restaurant_id,'restaurant_razorpay_secret_key');
    }else{
        $razorpay_api_key = get_option('razorpay_api_key');
        $razorpay_secret_key = get_option('razorpay_secret_key');
    }
    $api = new Api($razorpay_api_key, $razorpay_secret_key);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}

if ($success === true)
{
    payment_success_save_detail($access_token);
}
else
{
    payment_fail_save_detail($access_token);
    payment_error("error",$error,$access_token);
}
exit();


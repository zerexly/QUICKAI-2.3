<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

global $config,$lang,$link;

if(isset($access_token)){
    $user_id = $_SESSION['user']['id'];
    $username = $_SESSION['user']['username'];
    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
    $title = $_SESSION['quickad'][$access_token]['name'];
    $amount = $_SESSION['quickad'][$access_token]['amount'];

    if($payment_type == "order") {
        $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
        $restaurant = ORM::for_table($config['db']['pre'] . 'restaurant')
            ->find_one($restaurant_id);

        $userdata = get_user_data(null, $restaurant['user_id']);
        $user_email = $userdata['email'];
        $user_phone = $userdata['phone'];
        $currency = !empty($userdata['currency'])?$userdata['currency']:get_option('currency_code');

        $payumoney_payment_mode = get_restaurant_option($restaurant_id,'restaurant_payumoney_sandbox_mode');
        $payumoney_merchant_id = get_restaurant_option($restaurant_id,'restaurant_payumoney_merchant_id');
        $payumoney_merchant_key = get_restaurant_option($restaurant_id,'restaurant_payumoney_merchant_key');
        $payumoney_merchant_salt = get_restaurant_option($restaurant_id,'restaurant_payumoney_merchant_salt');
    }else{
        $currency = $config['currency_code'];
        $userdata = get_user_data($username);
        $user_email = $userdata['email'];
        $user_phone = $userdata['phone'];
        $payumoney_payment_mode = get_option('payumoney_sandbox_mode');
        $payumoney_merchant_id = get_option('payumoney_merchant_id');
        $payumoney_merchant_key = get_option('payumoney_merchant_key');
        $payumoney_merchant_salt = get_option('payumoney_merchant_salt');
    }

    $order_id = isset($_SESSION['quickad'][$access_token]['order_id'])? $_SESSION['quickad'][$access_token]['order_id'] : rand(1,400);

}else{
    error(__('Invalid Payment Processor'), __LINE__, __FILE__, 1);
    exit();
}

$return_url = $link['IPN']."?access_token=".$access_token."&i=payumoney";
$cancel_url = $link['PAYMENT']."?access_token=".$access_token."&status=cancel";

if($payumoney_payment_mode == 'test'){
    $url = 'https://test.payu.in/_payment';
}else{
    $url = 'https://secure.payu.in/_payment';
}
$txnid = "Txn" . rand(10000,99999999);
$hash_string = $payumoney_merchant_key.'|'.$txnid.'|'.$amount.'|'.$title.'|'.$username.'|'.$user_email.'|||||||||||'.$payumoney_merchant_salt;
$hash = hash('sha512', $hash_string);
?>
<html>
<body onload="document.forms['payumoney_form'].submit()">
<form action='<?php echo $url; ?>' name="payumoney_form" method='post' submit="onload()">
    <input type="hidden" name="key" value="<?php echo $payumoney_merchant_key; ?>" />
    <input type="hidden" name="txnid" value="<?php echo $txnid; ?>" />
    <input type="hidden" name="productinfo" value="<?php echo $title; ?>" />
    <input type="hidden" name="amount" value="<?php echo $amount; ?>" />
    <input type="hidden" name="email" value="<?php echo $user_email; ?>" />
    <input type="hidden" name="firstname" value="<?php echo $username; ?>" />
    <input type="hidden" name="lastname" value="" />
    <input type="hidden" name="surl" value="<?php echo $return_url; ?>" />
    <input type="hidden" name="furl" value="<?php echo $cancel_url; ?>" />
    <input type="hidden" name="phone" value="<?php echo $user_phone; ?>" />
<input type="hidden" name="hash" value="<?php echo $hash; ?>" />
</form>
</body>
</html>




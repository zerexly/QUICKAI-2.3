<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

global $config,$lang,$link;

if(isset($access_token)){
    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
    $title = $_SESSION['quickad'][$access_token]['name'];
    $amount = $_SESSION['quickad'][$access_token]['amount'];

    if($payment_type == "order") {
        $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
        $restaurant = ORM::for_table($config['db']['pre'] . 'restaurant')
            ->find_one($restaurant_id);

        $userdata = get_user_data(null, $restaurant['user_id']);
        $currency = !empty($userdata['currency'])?$userdata['currency']:get_option('currency_code');


        $telr_store_id = get_restaurant_option($restaurant_id,'restaurant_telr_store_id');
        $telr_authkey = get_restaurant_option($restaurant_id,'restaurant_telr_authkey');
        $telr_sandbox_mode = get_restaurant_option($restaurant_id,'restaurant_telr_sandbox_mode');
    }else{
        $currency = $config['currency_code'];

        $telr_store_id = get_option('telr_store_id');
        $telr_authkey = get_option('telr_authkey');
        $telr_sandbox_mode = get_option('telr_sandbox_mode');
    }

    $order_id = isset($_SESSION['quickad'][$access_token]['order_id'])? $_SESSION['quickad'][$access_token]['order_id'] : rand(1,400);

}else{
    error(__('Invalid Payment Processor'), __LINE__, __FILE__, 1);
    exit();
}

$return_url = $link['IPN']."/?access_token=".$access_token."&i=telr";
$cancel_url = $link['PAYMENT']."/?access_token=".$access_token."&status=cancel";

if($telr_sandbox_mode == 'test'){
    $payment_mode = 1;
}else{
    $payment_mode = 0;
}
//echo $currency = 'SAR';
$params = [
    'ivp_framed' => 2,
    'ivp_method' => 'create',
    'ivp_store' => $telr_store_id,
    'ivp_authkey' => $telr_authkey,
    'ivp_desc' => $title,
    'ivp_cart' => $order_id,
    'ivp_currency' => $currency,
    'ivp_amount' => $amount,
    'ivp_test' => $payment_mode,
    'return_auth' => $return_url,
    'return_decl' => $cancel_url,
    'return_can' => $cancel_url,
    /*
    'bill_fname' => $buyer_fname,
    'bill_sname' => $buyer_lname,
    'bill_email' => $buyer_email,
    'bill_addr1' => $buyer_address,
    'bill_city' => $buyer_city,
    'bill_region' => $buyer_state,
    'bill_zip' => $buyer_zipcode,
    'bill_phone' => $buyer_phone,
    'bill_country' => $buyer_country,*/
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://secure.telr.com/gateway/order.json");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
$request = curl_exec($ch);
curl_close($ch);
$result = json_decode($request, true);
/*echo "<pre>";
print_r($result);
echo "</pre>";*/

if (array_key_exists("error",$result))
{
    $error_msg = $result['error']['message'];
    $error_code = substr($error_msg, 0, 3);
    if($error_code == 'E05'){
        $error_msg = 'Telr only accept SAR. Try to change the Currency code because '.$currency.' not supported.';
    }
    payment_fail_save_detail($access_token);
    payment_error("error",$error_msg,$access_token);
    exit();
}
else
{
    $_SESSION['quickad'][$access_token]['telr_order_ref'] = $result['order']['ref'];
    header("Location: ".$result['order']['url']);
}

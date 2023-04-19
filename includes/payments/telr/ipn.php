<?php
global $config;

$payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
$order_ref = $_SESSION['quickad'][$access_token]['telr_order_ref'];

if($payment_type == "order") {
    $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
    $telr_store_id = get_restaurant_option($restaurant_id,'restaurant_telr_store_id');
    $telr_authkey = get_restaurant_option($restaurant_id,'restaurant_telr_authkey');
}else{
    $telr_store_id = get_option('telr_store_id');
    $telr_authkey = get_option('telr_authkey');
}

$params = [
    'ivp_method' => 'check',
    'ivp_store' => $telr_store_id,
    'ivp_authkey' => $telr_authkey,
    'order_ref' => $order_ref
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

if ($result['order']['status']['code'] != 3) {

    payment_fail_save_detail($access_token);

    mail($config['admin_email'],'Telr error in '.$config['site_title'],'Telr error in '.$config['site_title'].', status from Telr');

    $error_msg = "Transaction was not successful: Last gateway response was: ".$result['order']['transaction']['message'];
    payment_error("error",$error_msg,$access_token);
    exit();

} else {
    payment_success_save_detail($access_token);
}
?>

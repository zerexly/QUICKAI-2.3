<?php
global $config;

if (isset($_GET['txref'])) {
    $ref = $_GET['txref'];
    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
    $amount = $_SESSION['quickad'][$access_token]['amount'];

    if($payment_type == "order") {
        $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
        $restaurant = ORM::for_table($config['db']['pre'] . 'restaurant')
            ->find_one($restaurant_id);

        $userdata = get_user_data(null, $restaurant['user_id']);
        $currency = !empty($userdata['currency'])?$userdata['currency']:get_option('currency_code');
        $flutterwave_api_key = get_restaurant_option($restaurant_id,'restaurant_flutterwave_api_key');
        $flutterwave_secret_key = get_restaurant_option($restaurant_id,'restaurant_flutterwave_secret_key');
    }else{
        $currency = $config['currency_code'];

        $flutterwave_api_key = get_option('flutterwave_api_key');
        $flutterwave_secret_key = get_option('flutterwave_secret_key');
    }

    $amount = $amount; //Correct Amount from Server
    $currency = $currency; //Correct Currency from Server
    $query = array(
        "SECKEY" => $flutterwave_secret_key,
        "txref" => $ref
    );

    $data_string = json_encode($query);

    $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    $response = curl_exec($ch);

    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);

    curl_close($ch);

    $resp = json_decode($response, true);

    $paymentStatus = $resp['data']['status'];
    $chargeResponsecode = $resp['data']['chargecode'];
    $chargeAmount = $resp['data']['amount'];
    $chargeCurrency = $resp['data']['currency'];

    if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($chargeAmount == $amount)  && ($chargeCurrency == $currency)) {
        // transaction was successful...
        payment_success_save_detail($access_token);
        // please check other things like whether you already gave value for this ref
        // if the email matches the customer who owns the product etc
        //Give Value and return to Success page
    } else {
        //Dont Give Value and return to Failure page
        payment_fail_save_detail($access_token);
        payment_error("error","Transaction was Failed",$access_token);
    }
    exit();
}
else {
    payment_fail_save_detail($access_token);
    payment_error("error","No reference supplied",$access_token);
    die('No reference supplied');
}

?>
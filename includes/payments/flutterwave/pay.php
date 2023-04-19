<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

// Prevent direct access to this class
define("BASEPATH", 1);

include('library/rave.php');
include('library/raveEventHandlerInterface.php');
use Flutterwave\Rave;
use Flutterwave\EventHandlerInterface;

global $config,$lang,$link;

if(isset($access_token)){
    $user_id = $_SESSION['user']['id'];
    $username = $_SESSION['user']['username'];
    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
    $title = $_SESSION['quickad'][$access_token]['name'];
    $amount = $_SESSION['quickad'][$access_token]['amount'];
    $trans_desc = isset($_SESSION['quickad'][$access_token]['trans_desc']) ? $_SESSION['quickad'][$access_token]['trans_desc'] : $title;

    if($payment_type == "order") {
        $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
        $restaurant = ORM::for_table($config['db']['pre'] . 'restaurant')
            ->find_one($restaurant_id);

        $userdata = get_user_data(null, $restaurant['user_id']);
        $user_email = ''; //Please Pass buyer valid email id here its required.
        $currency = !empty($userdata['currency'])?$userdata['currency']:get_option('currency_code');

        $flutterwave_api_key = get_restaurant_option($restaurant_id,'restaurant_flutterwave_api_key');
        $flutterwave_secret_key = get_restaurant_option($restaurant_id,'restaurant_flutterwave_secret_key');
    }else{
        $currency = $config['currency_code'];

        $flutterwave_api_key = get_option('flutterwave_api_key');
        $flutterwave_secret_key = get_option('flutterwave_secret_key');

        $userdata = get_user_data(null, $user_id);
        $user_email = $userdata['email'];
    }

    $order_id = isset($_SESSION['quickad'][$access_token]['order_id'])? $_SESSION['quickad'][$access_token]['order_id'] : rand(1,400);

}else{
    error(__('Invalid Payment Processor'), __LINE__, __FILE__, 1);
    exit();
}

$return_url = $link['IPN']."?access_token=".$access_token."&i=flutterwave";
$cancel_url = $link['PAYMENT']."?access_token=".$access_token."&status=cancel";

$curl = curl_init();

$customer_email = $user_email;
$amount = $amount;
$currency = $currency;
$txref = 'txn_'.rand().'_'.rand(); // ensure you generate unique references per transaction.
$PBFPubKey = $flutterwave_api_key; // get your public key from the dashboard.
$redirect_url = $return_url;
$payment_plan = ""; // this is only required for recurring payments.


curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        'amount' => $amount,
        'customer_email' => $customer_email,
        'currency' => $currency,
        'txref' => $txref,
        'PBFPubKey' => $PBFPubKey,
        'redirect_url' => $redirect_url,
        'payment_plan' => $payment_plan
    ]),
    CURLOPT_HTTPHEADER => [
        "content-type: application/json",
        "cache-control: no-cache"
    ],
));

$response = curl_exec($curl);
$err = curl_error($curl);

if ($err) {
    // there was an error contacting the rave API
    die('Curl returned error: ' . $err);
}

$transaction = json_decode($response);
if($transaction->status == 'error'){
    payment_fail_save_detail($access_token);
    payment_error("error", $transaction->message,$access_token);
    exit();
}
if (!$transaction->data && !$transaction->data->link) {
    // there was an error from the API
    payment_fail_save_detail($access_token);
    payment_error("error", 'API returned error: ' . $transaction->message,$access_token);
}

// redirect to page so User can pay
// uncomment this line to allow the user redirect to the payment page
header('Location: ' . $transaction->data->link);
<?php

require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

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

        $razorpay_api_key = get_restaurant_option($restaurant_id,'restaurant_razorpay_api_key');
        $razorpay_secret_key = get_restaurant_option($restaurant_id,'restaurant_razorpay_secret_key');
        $theme_color = get_restaurant_option($restaurant_id,'restaurant_color');
    }else{
        $currency = $config['currency_code'];

        $razorpay_api_key = get_option('razorpay_api_key');
        $razorpay_secret_key = get_option('razorpay_secret_key');
        $theme_color = get_option('theme_color');
    }

    $order_id = isset($_SESSION['quickad'][$access_token]['order_id'])? $_SESSION['quickad'][$access_token]['order_id'] : rand(1,400);

}else{
    error(__('Invalid Payment Processor'), __LINE__, __FILE__, 1);
    exit();
}

$return_url = $link['IPN']."/?access_token=".$access_token."&i=razorpay";
$cancel_url = $link['PAYMENT']."/?access_token=".$access_token."&status=cancel";
// Create the Razorpay Order

$displayCurrency = $currency;
$api = new Api($razorpay_api_key, $razorpay_secret_key);
//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders
//
$orderData = [
    'receipt'         => rand(),
    'amount'          => $amount * 100, // 2000 rupees in paise
    'currency'        => $currency,
    'payment_capture' => 1 // auto capture
];

try{
    $razorpayOrder = $api->order->create($orderData);
}catch(Exception $exception){
    payment_fail_save_detail($access_token);
    payment_error("error",$exception->getMessage(),$access_token);
}

$razorpayOrderId = $razorpayOrder['id'];
$_SESSION['razorpay_order_id'] = $razorpayOrderId;
$displayAmount = $amount = $orderData['amount'];

if ($displayCurrency !== 'INR') {
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
    $exchange = json_decode(file_get_contents($url), true);
    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
}

$data = [
    "key"               => $razorpay_api_key,
    "amount"            => $amount,
    "name"              => $title,
    "description"       => '',
    "image"             => "",
    "prefill"           => [
        "name"              => "",
        "email"             => ""
    ],
    "notes"             => [
        "Title"           => $title
    ],
    "theme"             => [
        "color"             => $theme_color
    ],
    "order_id"          => $razorpayOrderId,
];

if ($displayCurrency !== 'INR') {
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);

?>
<!--  The entire list of Checkout fields is available at
 https://docs.razorpay.com/docs/checkout-form#checkout-fields -->
<!DOCTYPE html>
<html>
<body onload="paynow()">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<form name='razorpayform' action="<?php echo $return_url; ?>" method="POST">
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
</form>
<script>
    // Checkout details as a json
    var options = <?php echo $json?>;

    /**
     * The entire list of Checkout fields is available at
     * https://docs.razorpay.com/docs/checkout-form#checkout-fields
     */
    options.handler = function (response){
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_signature').value = response.razorpay_signature;
        document.razorpayform.submit();
    };

    // Boolean whether to show image inside a white frame. (default: true)
    options.theme.image_padding = false;

    options.modal = {
        ondismiss: function() {
            window.location.href = '<?php echo $cancel_url; ?>';
            return false;
        },
        escape: false
    };

    var rzp = new Razorpay(options);

    var paynow = function(){
        rzp.open();
    }
</script>
</body>
</html>

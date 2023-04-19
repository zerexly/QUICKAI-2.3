<?php
namespace Midtrans;

require_once 'Midtrans.php';

global $config, $lang, $link;

if (isset($access_token)) {

    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];

    if ($payment_type == "order") {
        error(__('Invalid Payment Processor'), __LINE__, __FILE__, 1);
        exit();
    } else {
        $mt_client_key = get_option('midtrans_client_key');
        $mt_server_key = get_option('midtrans_server_key');
        $mt_payment_mode = get_option('midtrans_sandbox_mode');
    }

} else {
    error(__('Invalid Payment Processor'), __LINE__, __FILE__, 1);
    exit();
}

if ($mt_payment_mode == 'test') {
    $payment_link = 'https://app.sandbox.midtrans.com/snap/snap.js';
} else {
    $payment_link = 'https://app.midtrans.com/snap/snap.js';
    Config::$isProduction = true;
}

//Set Your server key
Config::$serverKey = $mt_server_key;
Config::$isSanitized = Config::$is3ds = true;

$title = $_SESSION['quickad'][$access_token]['name'];
$amount = $_SESSION['quickad'][$access_token]['amount'];
$base_amount = isset($_SESSION['quickad'][$access_token]['base_amount'])? $_SESSION['quickad'][$access_token]['base_amount'] : $amount;
$folder = $_SESSION['quickad'][$access_token]['folder'];
$payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
$user_id = $_SESSION['user']['id'];

$billing = array(
    'type' => get_user_option($_SESSION['user']['id'],'billing_details_type'),
    'tax_id' => get_user_option($_SESSION['user']['id'],'billing_tax_id'),
    'name' => get_user_option($_SESSION['user']['id'],'billing_name'),
    'address' => get_user_option($_SESSION['user']['id'],'billing_address'),
    'city' => get_user_option($_SESSION['user']['id'],'billing_city'),
    'state' => get_user_option($_SESSION['user']['id'],'billing_state'),
    'zipcode' => get_user_option($_SESSION['user']['id'],'billing_zipcode'),
    'country' => get_user_option($_SESSION['user']['id'],'billing_country')
);

$taxes_ids = isset($_SESSION['quickad'][$access_token]['taxes_ids'])? $_SESSION['quickad'][$access_token]['taxes_ids'] : null;

$mysqli = db_connect();
if($payment_type == "subscr") {
    $trans_desc = $title;
    $subcription_id = $_SESSION['quickad'][$access_token]['sub_id'];
    $plan_interval = $_SESSION['quickad'][$access_token]['plan_interval'];

    $query = "INSERT INTO " . $config['db']['pre'] . "transaction set
                product_name = '".mysqli_real_escape_string($mysqli, validate_input($title))."',
                product_id = '$subcription_id',
                seller_id = '" . $_SESSION['user']['id'] . "',
                status = 'pending',
                amount = '$amount',
                base_amount = '$base_amount',
                transaction_gatway = '".validate_input($folder)."',
                transaction_ip = '" . encode_ip($_SERVER, $_ENV) . "',
                transaction_time = '" . time() . "',
                transaction_description = '".mysqli_real_escape_string($mysqli, validate_input($trans_desc))."',
                transaction_method = 'Subscription',
                frequency = '$plan_interval',
                billing = '".mysqli_real_escape_string($mysqli, json_encode($billing, JSON_UNESCAPED_UNICODE))."',
                taxes_ids = '$taxes_ids'
                ";
}
elseif($payment_type == "banner-advertise"){
    $item_pro_id = $_SESSION['quickad'][$access_token]['product_id'];
    $trans_desc = $_SESSION['quickad'][$access_token]['trans_desc'];

    $query = "INSERT INTO " . $config['db']['pre'] . "transaction set
                    product_name = '".mysqli_real_escape_string($mysqli, validate_input($title))."',
                    product_id = '$item_pro_id',
                    seller_id = '" . $user_id . "',
                    status = 'pending',
                    amount = '$amount',
                    base_amount = '$base_amount',
                    transaction_gatway = '".validate_input($folder)."',
                    transaction_ip = '" . encode_ip($_SERVER, $_ENV) . "',
                    transaction_time = '" . time() . "',
                    transaction_description = '".mysqli_real_escape_string($mysqli, validate_input($trans_desc))."',
                    transaction_method = 'banner-advertise',
                    billing = '".mysqli_real_escape_string($mysqli, json_encode($billing, JSON_UNESCAPED_UNICODE))."',
                    taxes_ids = '$taxes_ids'";
}
elseif($payment_type == "premium"){
    $item_pro_id = $_SESSION['quickad'][$access_token]['product_id'];
    $item_featured = $_SESSION['quickad'][$access_token]['featured'];
    $item_urgent = $_SESSION['quickad'][$access_token]['urgent'];
    $item_highlight = $_SESSION['quickad'][$access_token]['highlight'];
    $trans_desc = $_SESSION['quickad'][$access_token]['trans_desc'];

    $query = "INSERT INTO " . $config['db']['pre'] . "transaction set
                    product_name = '".mysqli_real_escape_string($mysqli, validate_input($title))."',
                    product_id = '$item_pro_id',
                    seller_id = '" . $user_id . "',
                    status = 'pending',
                    amount = '$amount',
                    base_amount = '$base_amount',
                    featured = '$item_featured',
                    urgent = '$item_urgent',
                    highlight = '$item_highlight',
                    transaction_gatway = '".validate_input($folder)."',
                    transaction_ip = '" . encode_ip($_SERVER, $_ENV) . "',
                    transaction_time = '" . time() . "',
                    transaction_description = '".mysqli_real_escape_string($mysqli, validate_input($trans_desc))."',
                    transaction_method = 'Premium Ad',
                    billing = '".mysqli_real_escape_string($mysqli, json_encode($billing, JSON_UNESCAPED_UNICODE))."',
                    taxes_ids = '$taxes_ids'";
}


$mysqli->query($query) OR error(mysqli_error($mysqli));

$transaction_id = $mysqli->insert_id;

$_SESSION['quickad'][$access_token]['transaction_id'] = $transaction_id;

$return_url = $link['IPN'] . "/?access_token=" . $access_token . "&i=midtrans";
$cancel_url = $link['PAYMENT'] . "/?access_token=" . $access_token . "&status=cancel";

Config::$overrideNotifUrl = $config['site_url'].'webhook/midtrans';

// Required
$transaction_details = array(
    'order_id' => $transaction_id,
    'gross_amount' => $amount, // no decimal allowed for creditcard
);
// Optional
$item_details = array(
    array(
        'id' => rand(),
        'price' => $amount,
        'quantity' => 1,
        'name' => $title
    ),
);

// Fill transaction details
$transaction = array(
    'transaction_details' => $transaction_details,
    'item_details' => $item_details,
);

try {
    $snapToken = Snap::getSnapToken($transaction);
} catch (\Exception $e) {
    payment_error("error", $e->getMessage(), $access_token);
    exit();
}
?>

<!DOCTYPE html>
<html>
<body onload="paynow()">
<script src="<?php echo $payment_link ?>" data-client-key="<?php echo $mt_client_key ?>"></script>
<script type="text/javascript">
    paynow = function () {
        // SnapToken acquired from previous step
        snap.pay('<?php echo $snapToken?>', {
            // Optional
            onSuccess: function (result) {
                //console.log(result);
                window.location = '<?php echo $return_url ?>';
            },
            // Optional
            onPending: function (result) {
                window.location = '<?php echo $return_url ?>';
            },
            // Optional
            onError: function (result) {
                window.location = '<?php echo $cancel_url ?>';
            },
            // Optional
            onClose: function (result) {
                window.location = '<?php echo $cancel_url ?>';
            }
        });
    };
</script>
</body>
</html>
<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

require_once 'paytm.php';

// manually set action for paytm payments
if (isset($_REQUEST['access_token']) && isset($_REQUEST['i']) && $_REQUEST['i'] == 'paytm') {
    paytmReturn();
}

if (isset($_SESSION['quickad'][$access_token]['payment_type'])) {

    $title = $_SESSION['quickad'][$access_token]['name'];
    $amount = $_SESSION['quickad'][$access_token]['amount'];

    $_SESSION['quickad'][$access_token]['merchantOrderId'] = $access_token;

    //URL
    $PAYTM_STATUS_QUERY_NEW_URL_SANDBOX = 'https://securegw-stage.paytm.in/merchant-status/getTxnStatus';
    $PAYTM_TXN_URL_SANDBOX = 'https://securegw-stage.paytm.in/theia/processTransaction';
    $PAYTM_STATUS_QUERY_NEW_URL = 'https://securegw.paytm.in/merchant-status/getTxnStatus';
    $PAYTM_TXN_URL = 'https://securegw.paytm.in/theia/processTransaction';

    $current_url = $link['PAYMENT']."/?access_token=".$access_token."&i=paytm";

    $paytm_order = $access_token;

    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];

    if($payment_type == "order") {
        $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];

        $PAYTM_MERCHANT_KEY = get_restaurant_option($restaurant_id,'restaurant_paytm_merchant_key');
        $PAYTM_MERCHANT_MID = get_restaurant_option($restaurant_id,'restaurant_paytm_merchant_mid');
        $PAYTM_MERCHANT_WEBSITE = get_restaurant_option($restaurant_id,'restaurant_paytm_merchant_website');
        $PAYTM_SANDBOX = get_restaurant_option($restaurant_id,'restaurant_paytm_sandbox_mode');

        $data = array(
            "MID" => $PAYTM_MERCHANT_MID,
            "WEBSITE" => $PAYTM_MERCHANT_WEBSITE,
            "ORDER_ID" => $paytm_order,
            "CUST_ID" => $restaurant_id,
            "INDUSTRY_TYPE_ID" => 'Retail',
            "CHANNEL_ID" => 'WEB',
            "CALLBACK_URL" => $current_url
        );
    }else{
        $PAYTM_MERCHANT_KEY = get_option('PAYTM_MERCHANT_KEY');
        $PAYTM_MERCHANT_MID = get_option('PAYTM_MERCHANT_MID');
        $PAYTM_MERCHANT_WEBSITE = get_option('PAYTM_MERCHANT_WEBSITE');
        $PAYTM_SANDBOX = get_option('PAYTM_ENVIRONMENT');

        $user_id = $_SESSION['user']['id'];
        $userdata = get_user_data(null, $user_id);
        $user_email = $userdata['email'];

        $data = array(
            "MID" => $PAYTM_MERCHANT_MID,
            "WEBSITE" => $PAYTM_MERCHANT_WEBSITE,
            "ORDER_ID" => $paytm_order,
            "CUST_ID" => $user_id,
            "INDUSTRY_TYPE_ID" => 'Retail',
            "CHANNEL_ID" => 'WEB',
            "CALLBACK_URL" => $current_url,
            "EMAIL" => $user_email, //Email ID of customer
            "VERIFIED_BY" => 'EMAIL',
            "IS_USER_VERIFIED" => 'YES'
        );
    }

    $data['TXN_AMOUNT'] = $amount;
    //Here checksum string will return by getChecksumFromArray() function.
    $checkSum = getChecksumFromArray($data, $PAYTM_MERCHANT_KEY);
    $data['CHECKSUMHASH'] = $checkSum;
    $url = ($PAYTM_SANDBOX == 'TEST') ? $PAYTM_TXN_URL_SANDBOX : $PAYTM_TXN_URL;
    ?>
    <html>
    <head>
        <title>Redirecting...</title>
    </head>
    <body>
    <p>Please do not refresh this page...</p>
    <form method="post" action="<?php echo $url ?>" name="f1">
        <?php
        foreach ($data as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
        }
        ?>
    </form>
    <script type="text/javascript">
        document.f1.submit();
    </script>
    </body>
    </html>
    <?php
    exit;
}
else {
    error(__('Invalid Transaction'), __LINE__, __FILE__, 1);
    exit();
}
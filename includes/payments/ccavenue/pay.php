<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

include('Crypto.php');

if (isset($_SESSION['quickad'][$access_token]['payment_type'])) {

    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];

    if($payment_type == "order") {
        $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];

        $merchant_id = get_restaurant_option($restaurant_id,'RESTAURANT_CCAVENUE_MERCHANT_KEY');
        $access_code = get_restaurant_option($restaurant_id,'RESTAURANT_CCAVENUE_ACCESS_CODE');
        $working_key = get_restaurant_option($restaurant_id,'RESTAURANT_CCAVENUE_WORKING_KEY');
    }else{
        $merchant_id = get_option('CCAVENUE_MERCHANT_KEY');//Shared by CCAVENUES
        $access_code = get_option('CCAVENUE_ACCESS_CODE');//Shared by CCAVENUES
        $working_key = get_option('CCAVENUE_WORKING_KEY');//Shared by CCAVENUES
    }

    /**
     * Execute purchase product after successful payment
     */
    function responseReturn()
    {
        global $config, $lang, $working_key;
        $error = '';
        $access_token = $_GET["access_token"];

        if (isset($_GET["access_token"])) {
            $encResponse=$_POST["encResp"];			//This is the response sent by the CCAvenue Server
            $rcvdString=decrypt($encResponse,$working_key);		//Crypto Decryption used as per the specified working key.
            $order_status="";
            $decryptValues=explode('&', $rcvdString);
            $dataSize=sizeof($decryptValues);

            for($i = 0; $i < $dataSize; $i++)
            {
                $information=explode('=',$decryptValues[$i]);
                if($i==3)	$order_status=$information[1];
            }

            if($order_status==="Success")
            {
                payment_success_save_detail($access_token);

            }
            else if($order_status==="Aborted")
            {
                payment_fail_save_detail($access_token);
                $error_msg = __('Transaction was not successful');
                payment_error("error", $error_msg, $access_token);

            }
            else if($order_status==="Failure")
            {
                payment_fail_save_detail($access_token);
                $error_msg = __('Transaction was not successful');
                payment_error("error", $error_msg, $access_token);
            }
            else
            {
                payment_fail_save_detail($access_token);
                $error_msg = __('Transaction was not successful');
                payment_error("error", $error_msg, $access_token);

            }
            /*echo "<br><br>";

            echo "<table cellspacing=4 cellpadding=4>";
            for($i = 0; $i < $dataSize; $i++)
            {
                $information=explode('=',$decryptValues[$i]);
                echo '<tr><td>'.$information[0].'</td><td>'.$information[1].'</td></tr>';
            }

            echo "</table><br>";
            echo "</center>";*/
            exit();
        } else {
            // the transaction was not successful, do not deliver value'
            // print_r($result);  //uncomment this line to inspect the result, to check why it failed.

            payment_fail_save_detail($access_token);
            mail($config['admin_email'],'Paystack error in '.$config['site_title'],'Paystack error in '.$config['site_title'].', status from Paystack');

            $error_msg = "Transaction was not successful: Last gateway response was: ".$_POST["RESPMSG"];
            payment_error("error",$error_msg,$access_token);
            exit();
        }
    }

    // manually set action for paytm payments
    if (isset($_REQUEST['access_token']) && isset($_REQUEST['i']) && $_REQUEST['i'] == 'ccavenue') {
        responseReturn();
    }

    $title = $_SESSION['quickad'][$access_token]['name'];
    $amount = $_SESSION['quickad'][$access_token]['amount'];

    $_SESSION['quickad'][$access_token]['merchantOrderId'] = $access_token;

    $_POST['merchant_id'] = $merchant_id;
    $_POST['order_id'] = uniqid();
    $_POST['amount'] = $amount;
    $_POST['currency'] = 'INR';
    $_POST['redirect_url'] = urlencode($link['PAYMENT']."/?access_token=".$access_token."&i=ccavenue");
    $_POST['cancel_url'] = urlencode($link['PAYMENT']."/?access_token=".$access_token."&status=cancel");
    $_POST['language'] = 'EN';

    $merchant_data='';

    foreach ($_POST as $key => $value){
        $merchant_data.=$key.'='.$value.'&';
    }

    $encrypted_data=encrypt($merchant_data,$working_key); // Method for encrypting the data.

    $production_url='https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction&encRequest='.$encrypted_data.'&access_code='.$access_code;

    ?>
    <html>
    <head>
        <title>Redirecting...</title>
    </head>
    <body>
    <p>Please do not refresh this page...</p>
    <form method="post" name="redirect" action="<?php echo 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction' ?>">
        <?php
        echo "<input type=hidden name=encRequest value=$encrypted_data>";
        echo "<input type=hidden name=access_code value=$access_code>";
        ?>
    </form>
    <script language='javascript'>document.redirect.submit();</script>
    </body>
    </html>
    <?php
    exit;
}
else {
    error(__('Invalid Transaction'), __LINE__, __FILE__, 1);
    exit();
}
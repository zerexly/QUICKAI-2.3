<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");
if(!checkloggedin()){
    header("Location: ".$link['LOGIN']);
    exit();
}
if (isset($_SESSION['quickad'][$access_token]['payment_type'])) {
    $postdata = $_POST;
    $msg = '';
    if (isset($postdata ['key'])) {
        $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
        if($payment_type == "order") {
            $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
            $payumoney_merchant_key = get_restaurant_option($restaurant_id,'restaurant_payumoney_merchant_key');
            $payumoney_merchant_salt = get_restaurant_option($restaurant_id,'restaurant_payumoney_merchant_salt');
        }else{
            $payumoney_merchant_key = get_option('payumoney_merchant_key');
            $payumoney_merchant_salt = get_option('payumoney_merchant_salt');
        }

        $salt				=   $payumoney_merchant_salt;
        $key				=   $postdata['key'];
        $txnid 				= 	$postdata['txnid'];
        $amount      		= 	$postdata['amount'];
        $productInfo  		= 	$postdata['productinfo'];
        $firstname    		= 	$postdata['firstname'];
        $email        		=	$postdata['email'];
        $udf5				=   $postdata['udf5'];
        $mihpayid			=	$postdata['mihpayid'];
        $status				= 	$postdata['status'];
        $resphash			= 	$postdata['hash'];
        //Calculate response hash to verify
        $keyString 	  		=  	$key.'|'.$txnid.'|'.$amount.'|'.$productInfo.'|'.$firstname.'|'.$email.'|||||'.$udf5.'|||||';
        $keyArray 	  		= 	explode("|",$keyString);
        $reverseKeyArray 	= 	array_reverse($keyArray);
        $reverseKeyString	=	implode("|",$reverseKeyArray);
        $CalcHashString 	= 	strtolower(hash('sha512', $salt.'|'.$status.'|'.$reverseKeyString));


        if ($status == 'success'  && $resphash == $CalcHashString) {
            $msg = "Transaction Successful and Hash Verified...";
            //Do success order processing here...
            payment_success_save_detail($access_token);
            exit();
        }
        else {
            //tampered or failed
            $msg = "Payment failed for Hasn not verified...";
            payment_fail_save_detail($access_token);
            mail($config['admin_email'],'Paystack error in '.$config['site_title'],'Paystack error in '.$config['site_title'].', status from Payumoney');

            $error_msg = "Transaction was not successful: Last Payumoney gateway response was: ".$msg;
            payment_error("error",$error_msg,$access_token);
            exit();
        }
    }
}else {
    error(__('Invalid Transaction'), __LINE__, __FILE__, 1);
    exit();
}
?>
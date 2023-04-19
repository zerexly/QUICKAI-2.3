<?php
$postdata = $_POST;
$msg = '';
$payumoney_merchant_salt = get_option('payumoney_merchant_salt');
if (isset($postdata ['key'])) {
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

    $upgrade 			= 	$postdata['payu_upgarde'];
    $payment_method_id  = 	$postdata['payu_method_id'];
	//Calculate response hash to verify	
	$keyString 	  		=  	$key.'|'.$txnid.'|'.$amount.'|'.$productInfo.'|'.$firstname.'|'.$email.'|||||'.$udf5.'|||||';
	$keyArray 	  		= 	explode("|",$keyString);
	$reverseKeyArray 	= 	array_reverse($keyArray);
	$reverseKeyString	=	implode("|",$reverseKeyArray);
	$CalcHashString 	= 	strtolower(hash('sha512', $salt.'|'.$status.'|'.$reverseKeyString));
	
	
	if ($status == 'success'  && $resphash == $CalcHashString) {
		$msg = "Transaction Successful and Hash Verified...";
		//Do success order processing here...
	}
	else {
		//tampered or failed
		$msg = "Payment failed for Hasn not verified...";
	} 
}
else exit(0);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PayUmoney BOLT PHP7 Kit</title>
</head>
<style type="text/css">
	.main {
		margin-left:30px;
		font-family:Verdana, Geneva, sans-serif, serif;
	}
	.text {
		float:left;
		width:180px;
	}
	.dv {
		margin-bottom:5px;
	}
</style>
<body>
<div class="main">
	<div>
    	<img src="logo/logo.png" />
    </div>
    <div>
    	<h3>PHP7 BOLT Kit Response</h3>
    </div>

    <div class="dv">
        <span class="text"><label>Upgrade:</label></span>
        <span><?php echo $upgrade; ?></span>
    </div>

    <div class="dv">
        <span class="text"><label>Payment Method Id:</label></span>
        <span><?php echo $payment_method_id; ?></span>
    </div>
    <br>
    <div class="dv">
        <span class="text"><label>Merchant Key:</label></span>
        <span><?php echo $key; ?></span>
    </div>
    
    <div class="dv">
        <span class="text"><label>Merchant Salt:</label></span>
        <span><?php echo $salt; ?></span>
    </div>


    
    <div class="dv">
        <span class="text"><label>Transaction/Order ID:</label></span>
        <span><?php echo $txnid; ?></span>
    </div>
    
    <div class="dv">
        <span class="text"><label>Amount:</label></span>
        <span><?php echo $amount; ?></span>
    </div>
    
    <div class="dv">
        <span class="text"><label>Product Info:</label></span>
        <span><?php echo $productInfo; ?></span>
    </div>
    
    <div class="dv">
        <span class="text"><label>First Name:</label></span>
        <span><?php echo $firstname; ?></span>
    </div>
    
    <div class="dv">
        <span class="text"><label>Email ID:</label></span>
        <span><?php echo $email; ?></span>
    </div>
    
    <div class="dv">
        <span class="text"><label>Mihpayid:</label></span>
        <span><?php echo $mihpayid; ?></span>
    </div>
    
    <div class="dv">
        <span class="text"><label>Hash:</label></span>
        <span><?php echo $resphash; ?></span>
    </div>
    
    <div class="dv">
        <span class="text"><label>Transaction Status:</label></span>
        <span><?php echo $status; ?></span>
    </div>
    
    <div class="dv">
        <span class="text"><label>Message:</label></span>
        <span><?php echo $msg; ?></span>
    </div>
</div>
</body>
</html>
	
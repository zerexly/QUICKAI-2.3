<?php
$_paymentResult = $_POST;

if ($_paymentResult['respStatus'] != "A") {

    payment_fail_save_detail($access_token);

    $error_msg = "Transaction was not successful: Last gateway response was: ".$_paymentResult['respMessage'];
    payment_error("error",$error_msg,$access_token);
    exit();

} else {
    payment_success_save_detail($access_token);
}
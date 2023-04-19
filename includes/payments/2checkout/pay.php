<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");
if (isset($_SESSION['quickad'][$access_token]['payment_type'])) {

    require_once("lib/Twocheckout.php");

    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];

    if($payment_type == "order") {
        $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
        $restaurant = ORM::for_table($config['db']['pre'] . 'restaurant')
            ->find_one($restaurant_id);

        $userdata = get_user_data(null, $restaurant['user_id']);
        $currency = !empty($userdata['currency'])?$userdata['currency']:get_option('currency_code');

        $checkout_seller_id = get_restaurant_option($restaurant_id,'restaurant_2checkout_account_number');
        $checkout_private_key = get_restaurant_option($restaurant_id,'restaurant_2checkout_private_key');
        $sandbox = get_restaurant_option($restaurant_id,'restaurant_2checkout_sandbox_mode');

        $email = $_SESSION['quickad'][$access_token]['BillingEmail'];
        $phone = $_SESSION['quickad'][$access_token]['BillingPhone'];
    } else {
        $checkout_seller_id = get_option('checkout_account_number');
        $checkout_private_key = get_option('checkout_private_key');
        $sandbox = get_option('2checkout_sandbox_mode');

        $currency = $config['currency_code'];
        $userdata = get_user_data($_SESSION['user']['username']);
        $email = $userdata['email'];
        $phone = $userdata['phone'];
    }

    Twocheckout::privateKey($checkout_private_key); //Private Key
    Twocheckout::sellerId($checkout_seller_id); // 2Checkout Account Number
    //Twocheckout::sandbox((bool) get_option('2checkout_sandbox_mode') == 'sandbox'); // Set to false for production accounts.
    Twocheckout::verifySSL(false);

    $title = $_SESSION['quickad'][$access_token]['name'];
    $amount = $_SESSION['quickad'][$access_token]['amount'];

    $fname =$_SESSION['quickad'][$access_token]['firstname'];
    $lname = $_SESSION['quickad'][$access_token]['lastname'];
    $fullname = $fname." ".$lname;
    $address = $_SESSION['quickad'][$access_token]['BillingAddress'];
    $city = $_SESSION['quickad'][$access_token]['BillingCity'];
    $state = $_SESSION['quickad'][$access_token]['BillingState'];
    $zipcode = $_SESSION['quickad'][$access_token]['BillingZipcode'];
    $country = $_SESSION['quickad'][$access_token]['BillingCountry'];



    $_SESSION['quickad'][$access_token]['merchantOrderId'] = $access_token;

    try {
        $data = array(
            "merchantOrderId" => $access_token,
            "token"      => $_POST['2checkoutToken'],
            "currency" => $currency,
            "total" => $amount,
            "billingAddr" => array(
                "name" => $fullname,
                "addrLine1" => $address,
                "city" => $city,
                "state" => $state,
                "zipCode" => $zipcode,
                "country" => $country,
                "email" => $email,
                "phoneNumber" => $phone
            ),
            "shippingAddr" => array(
                "name" => $fullname,
                "addrLine1" => $address,
                "city" => $city,
                "state" => $state,
                "zipCode" => $zipcode,
                "country" => $country,
                "email" => $email,
                "phoneNumber" => $phone
            )
        );
        if($sandbox == 'sandbox')
            $data['demo'] = true;

        $charge = Twocheckout_Charge::auth($data);


        if ($charge['response']['responseCode'] == 'APPROVED') {
            /*Success*/
            payment_success_save_detail($access_token);
        } else {
            payment_fail_save_detail($access_token);
            mail($config['admin_email'],'2Checkout error in '.$config['site_title'],'2Checkout error in '.$config['site_title'].', status from 2Checkout');

            $error_msg = __('Invalid Transaction');
            payment_error("error",$error_msg,$access_token);

            exit();
        }

    } catch (Twocheckout_Error $e) {
        $error_msg = $e->getMessage();
        payment_error("error",$error_msg,$access_token);

        exit();
    }

    exit;
}
else {
    error(__('Invalid Transaction'), __LINE__, __FILE__, 1);
    exit();
}
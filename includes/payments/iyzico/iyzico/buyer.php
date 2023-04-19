<?php
require_once('IyzipayBootstrap.php');
IyzipayBootstrap::init();
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

        $iyzico_sandbox_mode = get_restaurant_option($restaurant_id,'restaurant_iyzico_sandbox_mode');
        $iyzico_api_key = get_restaurant_option($restaurant_id,'restaurant_iyzico_api_key');
        $iyzico_secret_key = get_restaurant_option($restaurant_id,'restaurant_iyzico_secret_key');

        $user_id = rand();
        $contactName = $_SESSION['quickad'][$access_token]['customer_name'];
        $billing_name = $_SESSION['quickad'][$access_token]['customer_name'];
        $name = split_name($billing_name);
        $buyer_fname = ($name[0] != "")? $name[0] : $contactName;
        $buyer_lname = ($name[1] != "")? $name[1] : $contactName;

        $buyer_email = $userdata['email'];

        $location = getLocationInfoByIp();

        $buyer_address = $location['country'];
        $buyer_city = $location['country'];
        $buyer_country = $location['country'];
        $buyer_zipcode = '';
    }else{
        $currency = $config['currency_code'];

        $iyzico_sandbox_mode = get_option('iyzico_sandbox_mode');
        $iyzico_api_key = get_option('iyzico_api_key');
        $iyzico_secret_key = get_option('iyzico_secret_key');

        $user_id = $_SESSION['user']['id'];
        $contactName = $_SESSION['user']['username'];
        $billing_name = get_user_option($_SESSION['user']['id'], 'billing_name');
        $name = split_name($billing_name);
        $buyer_fname = ($name[0] != "")? $name[0] : $contactName;
        $buyer_lname = ($name[1] != "")? $name[1] : $contactName;

        $user_data = get_user_data($_SESSION['user']['username']);

        $buyer_email = $user_data['email'];
        $buyer_address = get_user_option($_SESSION['user']['id'], 'billing_address');
        $buyer_city = get_user_option($_SESSION['user']['id'], 'billing_city');
        $buyer_country = get_user_option($_SESSION['user']['id'], 'billing_country');
        $buyer_zipcode = get_user_option($_SESSION['user']['id'], 'billing_zipcode');
    }

    $order_id = isset($_SESSION['quickad'][$access_token]['order_id'])? $_SESSION['quickad'][$access_token]['order_id'] : rand(1,400);

}else{
    error(__('Invalid Payment Processor'), __LINE__, __FILE__, 1);
    exit();
}

if($iyzico_sandbox_mode == 'test'){
    $payment_link = 'https://sandbox-api.iyzipay.com';
}else{
    $payment_link = 'https://api.iyzipay.com';
}

$return_url = $link['IPN']."/?access_token=".$access_token."&i=iyzico";

# create request class
$request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();

if(check_user_lang() == 'turkish'){
    $request->setLocale(\Iyzipay\Model\Locale::TR);
} else {
    $request->setLocale(\Iyzipay\Model\Locale::EN);
}


$request->setConversationId("$order_id");
$request->setPrice($amount);
$request->setPaidPrice($amount);
$request->setCurrency($currency);
$request->setBasketId("$order_id");
$request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
$request->setCallbackUrl($return_url);
$request->setEnabledInstallments(array(2, 3, 6, 9));


$buyer = new \Iyzipay\Model\Buyer();
$buyer->setId($user_id);
$buyer->setName($buyer_fname);
$buyer->setSurname($buyer_lname);
$buyer->setEmail($buyer_email);
$buyer->setIdentityNumber(rand());
$buyer->setRegistrationAddress($buyer_address);
$buyer->setIp($_SERVER['REMOTE_ADDR']);
$buyer->setCity($buyer_city);
$buyer->setCountry($buyer_country);
$buyer->setZipCode($buyer_zipcode);
$request->setBuyer($buyer);

$shippingAddress = new \Iyzipay\Model\Address();
$shippingAddress->setContactName("$contactName");
$shippingAddress->setCity("$buyer_city");
$shippingAddress->setCountry("$buyer_country");
$shippingAddress->setAddress("$buyer_address");
$shippingAddress->setZipCode("$buyer_zipcode");
$request->setShippingAddress($shippingAddress);

$billingAddress = new \Iyzipay\Model\Address();
$billingAddress->setContactName("$contactName");
$billingAddress->setCity("$buyer_city");
$billingAddress->setCountry("$buyer_country");
$billingAddress->setAddress("$buyer_address");
$billingAddress->setZipCode("$buyer_zipcode");
$request->setBillingAddress($billingAddress);

$basketItems = array();
$firstBasketItem = new \Iyzipay\Model\BasketItem();
$firstBasketItem->setId("$order_id");
$firstBasketItem->setName($title);
$firstBasketItem->setCategory1("Collectibles");
$firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
$firstBasketItem->setPrice($amount);
$basketItems[0] = $firstBasketItem;
$request->setBasketItems($basketItems);

$options = new \Iyzipay\Options();
$options->setApiKey($iyzico_api_key);
$options->setSecretKey($iyzico_secret_key);
$options->setBaseUrl($payment_link);
# make request
$checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);

# print result
//print_r($checkoutFormInitialize);
//print_r($checkoutFormInitialize->getstatus());
print_r($checkoutFormInitialize->getErrorMessage());
print_r($checkoutFormInitialize->getCheckoutFormContent());
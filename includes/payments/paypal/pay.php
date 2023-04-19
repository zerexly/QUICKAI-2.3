<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

include 'paypal-sdk/autoload.php';

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');


// manually set action for paypal payments
if (empty($action) && isset($_REQUEST['paypal_return'])) {
    $action = 'paypal_ipn';
}
else if ( empty($action) ) {
    $action = 'paypal_payment';
}


if(isset($access_token)){
    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];

    $title = $_SESSION['quickad'][$access_token]['name'];
    $total = $_SESSION['quickad'][$access_token]['amount'];

    if($payment_type == "subscr") {
        $currency = $config['currency_code'];
        $user_id = $_SESSION['user']['id'];


        $base_amount = $_SESSION['quickad'][$access_token]['base_amount'];
        $plan_interval = $_SESSION['quickad'][$access_token]['plan_interval'];
        $payment_mode = $_SESSION['quickad'][$access_token]['payment_mode'];
        $package_id = $_SESSION['quickad'][$access_token]['sub_id'];
        $taxes_ids = isset($_SESSION['quickad'][$access_token]['taxes_ids']) ? $_SESSION['quickad'][$access_token]['taxes_ids'] : null;

        if ($plan_interval == 'LIFETIME') {
            $payment_mode = 'one_time';
        }

        $cancel_url = $link['PAYMENT']."/?access_token=".$access_token."&status=cancel";

        $paypal_client_id = get_option('paypal_api_client_id');
        $paypal_secret = get_option('paypal_api_secret');
        $paypal_sandbox = get_option('paypal_sandbox_mode');
    }else{
        $payment_mode = 'one_time';
        $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
        $restaurant = ORM::for_table($config['db']['pre'] . 'restaurant')
            ->find_one($restaurant_id);

        $userdata = get_user_data(null, $restaurant['user_id']);
        $currency = !empty($userdata['currency'])?$userdata['currency']:get_option('currency_code');

        $cancel_url = $link['PAYMENT']."/?access_token=".$access_token;

        $paypal_client_id = get_restaurant_option($restaurant_id,'restaurant_paypal_api_client_id');
        $paypal_secret = get_restaurant_option($restaurant_id,'restaurant_paypal_api_secret');
        $paypal_sandbox = get_restaurant_option($restaurant_id,'restaurant_paypal_sandbox_mode','Yes');
    }

}

$plan_interval_count = 1;
$enable_trial = 0;
$trial_days = 7;

if ( !empty($action) ) {

    switch ($action) {
        case 'paypal_payment':

            /* Initiate paypal */
            $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($paypal_client_id, $paypal_secret));
            $paypal->setConfig(array(
                    'mode' => ($paypal_sandbox == 'Yes') ?
                        'sandbox' :
                        'live')
            );

            $price = in_array($currency, ['JPY', 'TWD', 'HUF']) ? number_format($total, 0, '.', '') : number_format($total, 2, '.', '');

            switch($payment_mode) {
                case 'one_time':

                    $flowConfig = new \PayPal\Api\FlowConfig();
                    $flowConfig->setLandingPageType('Billing');
                    $flowConfig->setUserAction('commit');
                    $flowConfig->setReturnUriHttpMethod('GET');

                    $presentation = new \PayPal\Api\Presentation();
                    $presentation->setBrandName('');

                    $inputFields = new \PayPal\Api\InputFields();
                    $inputFields->setAllowNote(true)
                        ->setNoShipping(1)
                        ->setAddressOverride(0);

                    $webProfile = new \PayPal\Api\WebProfile();
                    $webProfile->setName(uniqid())
                        ->setFlowConfig($flowConfig)
                        ->setPresentation($presentation)
                        ->setInputFields($inputFields)
                        ->setTemporary(true);

                    try {
                        $createdProfileResponse = $webProfile->create($paypal);
                    } catch (Exception $exception) {
                        payment_fail_save_detail($access_token);
                        //error_log($exception->getData());
                        payment_error("error",$exception->getMessage(),$access_token);
                    }

                    $payer = new \PayPal\Api\Payer();
                    $payer->setPaymentMethod('paypal');

                    $item = new \PayPal\Api\Item();
                    $item->setName($title)
                        ->setCurrency($currency)
                        ->setQuantity(1)
                        ->setPrice($price);

                    $itemList = new \PayPal\Api\ItemList();
                    $itemList->setItems([$item]);

                    $amount = new \PayPal\Api\Amount();
                    $amount->setCurrency($currency)
                        ->setTotal($price);

                    $transaction = new \PayPal\Api\Transaction();
                    $transaction->setAmount($amount)
                        ->setItemList($itemList)
                        ->setInvoiceNumber(uniqid());

                    $redirectUrls = new \PayPal\Api\RedirectUrls();
                    $redirectUrls->setReturnUrl($link['PAYMENT']."/?access_token=".$access_token."&i=paypal&payment_mode=one_time&paypal_return=1")
                        ->setCancelUrl($cancel_url);

                    $payment = new \PayPal\Api\Payment();
                    $payment->setIntent('sale')
                        ->setPayer($payer)
                        ->setRedirectUrls($redirectUrls)
                        ->setTransactions([$transaction])
                        ->setExperienceProfileId($createdProfileResponse->getId());

                    try {
                        $payment->create($paypal);
                    } catch (Exception $exception) {
                        payment_fail_save_detail($access_token);
                        //error_log($exception->getData());
                        payment_error("error",$exception->getMessage(),$access_token);
                    }

                    $payment_url = $payment->getApprovalLink();

                    header('Location: ' . $payment_url);

                    break;

                case 'recurring':

                    $plan = new \PayPal\Api\Plan();
                    $plan->setName($title)
                        ->setDescription($title)
                        ->setType('fixed');

                    $payment_definition = new \PayPal\Api\PaymentDefinition();
                    $payment_definition->setName('Regular Payments')
                        ->setType('REGULAR')
                        ->setFrequency($plan_interval == 'MONTHLY' ? 'Month' : 'Year')
                        ->setFrequencyInterval('1')
                        ->setCycles($plan_interval == 'MONTHLY' ? '12' : '5')
                        ->setAmount(new \PayPal\Api\Currency(array('value' => $price, 'currency' => $currency)));

                    $merchant_preferences = new \PayPal\Api\MerchantPreferences();
                    $merchant_preferences->setReturnUrl($link['PAYMENT']."/?access_token=".$access_token."&i=paypal&payment_mode=recurring&paypal_return=1")
                        ->setCancelUrl($link['PAYMENT']."/?access_token=".$access_token."&status=cancel")
                        ->setAutoBillAmount('yes')
                        ->setInitialFailAmountAction('CONTINUE')
                        ->setMaxFailAttempts('0')
                        ->setSetupFee(new \PayPal\Api\Currency(array('value' => $price, 'currency' => $currency)));

                    $plan->setPaymentDefinitions([$payment_definition]);
                    $plan->setMerchantPreferences($merchant_preferences);

                    try {
                        $plan = $plan->create($paypal);
                    } catch (Exception $exception) {
                        payment_fail_save_detail($access_token);
                        //error_log($exception->getData());
                        payment_error("error",$exception->getMessage(),$access_token);
                    }

                    try {
                        $patch = new \PayPal\Api\Patch();
                        $value = new \PayPal\Common\PayPalModel('{"state":"ACTIVE"}');
                        $patch->setOp('replace')
                            ->setPath('/')
                            ->setValue($value);
                        $patchRequest = new \PayPal\Api\PatchRequest();
                        $patchRequest->addPatch($patch);
                        $plan->update($patchRequest, $paypal);
                        $plan = \PayPal\Api\Plan::get($plan->getId(), $paypal);
                    } catch (Exception $exception) {
                        payment_fail_save_detail($access_token);
                        //error_log($exception->getData());
                        payment_error("error",$exception->getMessage(),$access_token);
                    }

                    $agreement = new \PayPal\Api\Agreement();
                    $agreement->setName($title)
                        ->setDescription($user_id . '###' . $package_id . '###' . $plan_interval . '###' . $base_amount . '###' . $taxes_ids . '###' . time())
                        ->setStartDate((new \DateTime())->modify($plan_interval == 'MONTHLY' ? '+30 days' : '+1 year')->format(DATE_ISO8601));

                    $agreement_plan = new \PayPal\Api\Plan();
                    $agreement_plan->setId($plan->getId());
                    $agreement->setPlan($agreement_plan);

                    $payer = new \PayPal\Api\Payer();
                    $payer->setPaymentMethod('paypal');
                    $agreement->setPayer($payer);

                    try {
                        $agreement = $agreement->create($paypal);
                    } catch (Exception $exception) {
                        payment_fail_save_detail($access_token);
                        //error_log($exception->getData());
                        payment_error("error",$exception->getMessage(),$access_token);
                    }

                    $payment_url = $agreement->getApprovalLink();

                    header('Location: ' . $payment_url);

                    break;
            }
            break;

/***********************************************************************************************************************/

        case 'paypal_ipn':

            /* Initiate paypal */
            $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($paypal_client_id, $paypal_secret));
            $paypal->setConfig(array(
                    'mode' => ($paypal_sandbox == 'Yes') ?
                        'sandbox' :
                        'live')
            );

            if($_GET['payment_mode'] == 'one_time') {
                $payment_id = $_GET['paymentId'];
                $payer_id = $_GET['PayerID'];
                $payment_type = 'one_time';

                $subscription_id = '';
                $payment_subscription_id =  '';

                try {
                    $payment = \PayPal\Api\Payment::get($payment_id, $paypal);

                    $payer_info = $payment->getPayer()->getPayerInfo();
                    $payer_email = $payer_info->getEmail();
                    $payer_name = $payer_info->getFirstName() . ' ' . $payer_info->getLastName();

                    $payment_total = $payment->getTransactions()[0]->getAmount()->getTotal();
                    $payment_currency = $payment->getTransactions()[0]->getAmount()->getCurrency();

                    $execute = new \PayPal\Api\PaymentExecution();
                    $execute->setPayerId($payer_id);

                    $result = $payment->execute($execute, $paypal);

                    $payment_status = $payment->getState();

                } catch (Exception $exception) {
                    payment_fail_save_detail($access_token);
                    //error_log($exception->getData());
                    payment_error("error",$exception->getMessage(),$access_token);
                }

                if($payment_status != 'approved') {
                    payment_fail_save_detail($access_token);
                    payment_error("error", __('Invalid Transaction'),$access_token);
                }

                if($payment_type == "subscr") {
                    if (ORM::for_table($config['db']['pre'] . 'transaction')
                        ->where('id', $payment_id)
                        ->where('transaction_gatway', 'paypal')
                        ->count()) {
                        payment_fail_save_detail($access_token);
                        payment_error("error", __('Invalid Transaction'), $access_token);
                    }

                    $subsc_check = ORM::for_table($config['db']['pre'] . 'upgrades')
                        ->where('user_id', $user_id)
                        ->find_one();
                    if (isset($subsc_check['user_id'])) {
                        $txn_type = 'subscr_update';

                        if ($subsc_check['unique_id'] != $payment_subscription_id) {
                            try {
                                cancel_recurring_payment($user_id);
                            } catch (\Exception $exception) {
                                error_log($exception->getCode());
                                error_log($exception->getMessage());
                            }
                        }
                    }
                }

                /*Success*/
                payment_success_save_detail($access_token);
            }

            elseif($_GET['payment_mode'] == 'recurring') {

                $token = $_GET['token'];
                $agreement = new \PayPal\Api\Agreement();
                $payment_type = 'recurring';

                try {
                    $agreement->execute($token, $paypal);
                } catch (Exception $exception) {
                    payment_fail_save_detail($access_token);
                    //error_log($exception->getData());
                    payment_error("error", $exception->getMessage(),$access_token);
                }


                try {
                    $agreement = \PayPal\Api\Agreement::get($agreement->getId(), $paypal);
                } catch (Exception $exception) {
                    payment_fail_save_detail($access_token);
                    //error_log($exception->getData());
                    payment_error("error", $exception->getMessage(),$access_token);
                }


                $agreement_status = $agreement->getState();


                if($agreement_status != 'Active' && $agreement_status != 'Pending') {
                    payment_fail_save_detail($access_token);
                    payment_error("error", __('Invalid Transaction'),$access_token);
                }

                /* Success */
                unset($_SESSION['quickad'][$access_token]);
                message(__('Success'), __('Payment Successful'), $link['TRANSACTION']);
                exit();
            }
            break;

    }
}
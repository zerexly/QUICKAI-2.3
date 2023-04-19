<?php
include 'paypal-sdk/autoload.php';

$payload = @file_get_contents('php://input');
$data = json_decode($payload);

if($payload && $data && $data->event_type == 'PAYMENT.SALE.COMPLETED') {

    /* Initiate paypal */
    $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential(get_option('paypal_api_client_id'), get_option('paypal_api_secret')));
    $paypal->setConfig(array(
            'mode' => (get_option('paypal_sandbox_mode') == 'Yes') ?
                'sandbox' :
                'live')
    );

    try {
        $agreement = \PayPal\Api\Agreement::get($data->resource->billing_agreement_id, $paypal);
    } catch (Exception $exception) {
        error_log($exception->getCode());
        error_log($exception->getMessage());

        http_response_code(400);

    }

    $payer_info = $agreement->getPayer()->getPayerInfo();
    $payer_email = $payer_info->getEmail();
    $payer_name = $payer_info->getFirstName() . ' ' . $payer_info->getLastName();
    $payer_id = $payer_info->getPayerId();
    $subscription_id = $agreement->getId();

    $payment_id = $data->resource->id;
    $payment_total = $data->resource->amount->total;
    $payment_currency = $data->resource->amount->currency;

    $extra = explode('###', $agreement->getDescription());

    $user_id = (int) $extra[0];
    $package_id = (int) $extra[1];
    $payment_frequency = $extra[2];
    $base_amount = $extra[3];
    $taxes_ids = $extra[4];
    $payment_type = 'recurring';
    $payment_subscription_id = 'paypal###' . $subscription_id;


    $package = ORM::for_table($config['db']['pre'].'plans')
        ->where('id', $package_id)
        ->find_one();


    if(!isset($package['id'])) {
        http_response_code(400);
        die();
    }


    if(ORM::for_table($config['db']['pre'].'transaction')
        ->where('id', $payment_id)
        ->where('transaction_gatway', 'paypal')
        ->count()) {
        http_response_code(400);
        die();
    }


    $user = ORM::for_table($config['db']['pre'].'user')
        ->where('id', $user_id)
        ->find_one();

    if(!isset($user['id'])) {
        http_response_code(400);
        die();
    }

    /* Unsubscribe from the previous plan */
    $subsc_check = ORM::for_table($config['db']['pre'].'upgrades')
        ->where('user_id', $user_id)
        ->find_one();
    if(isset($subsc_check['user_id']))
    {
        $txn_type = 'subscr_update';

        if($subsc_check['unique_id'] != $payment_subscription_id) {
            try {
                cancel_recurring_payment($user_id);
            } catch (\Exception $exception) {
                error_log($exception->getCode());
                error_log($exception->getMessage());
            }
        }
    }
    else
    {
        $txn_type = 'subscr_signup';
    }

    $term = 0;
    switch($payment_frequency) {
        case 'MONTHLY':
            $term = 2678400;
            break;

        case 'YEARLY':
            $term = 31536000;
            break;

        case 'LIFETIME':
            $term = 3153600000;
            break;
    }

    $expires = (time()+$term);

    $pdo = ORM::get_db();

    if($txn_type == 'subscr_update')
    {

        $query = "UPDATE `".$config['db']['pre']."upgrades` SET 
            `sub_id` = '".validate_input($package_id)."',
            `upgrade_expires` = '".validate_input($expires)."', 
            `pay_mode` = 'recurring', 
            `unique_id` = '".validate_input($payment_subscription_id)."', 
            `upgrade_lasttime` = '".time()."' 
        WHERE `user_id` = '".validate_input($user_id)."' LIMIT 1";
        $pdo->query($query);

        // update user data
        $user->group_id = $package_id;
        $user->save();

    }
    elseif($txn_type == 'subscr_signup')
    {
        $subscription_status = "Active";

        $upgrades_insert = ORM::for_table($config['db']['pre'].'upgrades')->create();
        $upgrades_insert->sub_id = $package_id;
        $upgrades_insert->user_id = $user_id;
        $upgrades_insert->upgrade_lasttime = time();
        $upgrades_insert->upgrade_expires = $expires;
        $upgrades_insert->pay_mode = 'recurring';
        $upgrades_insert->unique_id = $payment_subscription_id;
        $upgrades_insert->status = $subscription_status;
        $upgrades_insert->save();

        $user->group_id = $package_id;
        $user->save();
    }

    //Update Amount in balance table
    $balance = ORM::for_table($config['db']['pre'].'balance')->find_one(1);
    $current_amount=$balance['current_balance'];
    $total_earning=$balance['total_earning'];

    $updated_amount=($payment_total+$current_amount);
    $total_earning=($payment_total+$total_earning);

    $balance->current_balance = $updated_amount;
    $balance->total_earning = $total_earning;
    $balance->save();

    $billing = array(
        'type' => get_user_option($user_id,'billing_details_type'),
        'tax_id' => get_user_option($user_id,'billing_tax_id'),
        'name' => get_user_option($user_id,'billing_name'),
        'address' => get_user_option($user_id,'billing_address'),
        'city' => get_user_option($user_id,'billing_city'),
        'state' => get_user_option($user_id,'billing_state'),
        'zipcode' => get_user_option($user_id,'billing_zipcode'),
        'country' => get_user_option($user_id,'billing_country')
    );

    $ip = encode_ip($_SERVER, $_ENV);
    $trans_insert = ORM::for_table($config['db']['pre'].'transaction')->create();
    $trans_insert->product_name = $package['name'];
    $trans_insert->product_id = $package_id;
    $trans_insert->seller_id = $user_id;
    $trans_insert->status = 'success';
    $trans_insert->base_amount = $base_amount;
    $trans_insert->amount = $payment_total;
    $trans_insert->transaction_gatway = 'paypal';
    $trans_insert->transaction_ip = $ip;
    $trans_insert->transaction_time = time();
    $trans_insert->transaction_description = $package['name'];
    $trans_insert->payment_id = $payment_id;
    $trans_insert->transaction_method = 'Subscription';
    $trans_insert->frequency = $payment_frequency;
    $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
    $trans_insert->taxes_ids = $taxes_ids;
    $trans_insert->save();

    // check for affiliate payment
    check_affiliate_payment($user, $txn_type, $payment_total, 'paypal', $trans_insert->id);

    // send success
    http_response_code(200);
}

die();
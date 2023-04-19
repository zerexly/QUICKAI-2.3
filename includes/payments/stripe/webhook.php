<?php
include 'stripe-php/init.php';

/* Initiate Stripe */
if (isset($_GET['restaurant']) && function_exists('get_restaurant_option')) {
    $restaurant_id = $_GET['restaurant'];
    $stripe_secret_key = get_restaurant_option($restaurant_id, 'restaurant_stripe_secret_key');
    $stripe_webhook_key = get_restaurant_option($restaurant_id, 'restaurant_stripe_webhook_secret');
} else {
    $stripe_secret_key = get_option('stripe_secret_key');
    $stripe_webhook_key = get_option('stripe_webhook_secret');
}

\Stripe\Stripe::setApiKey($stripe_secret_key);
\Stripe\Stripe::setApiVersion('2020-08-27');

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = $order_id = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $stripe_webhook_key
    );
} catch (\UnexpectedValueException $e) {
    /* Invalid payload */
    http_response_code(400);
    echo $e->getMessage();
    error_log($e->getMessage());
    die();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    /* Invalid signature */
    http_response_code(400);
    echo $e->getMessage();
    error_log($e->getMessage());
    die();
}


if (!in_array($event->type, ['invoice.paid', 'checkout.session.completed'])) {
    die();
}

$session = $event->data->object;

$payment_id = $session->id;
$payer_id = $session->customer;
$payer_object = \Stripe\Customer::retrieve($payer_id);
$payer_email = $payer_object->email;
$payer_name = $payer_object->name;

switch ($event->type) {
    /* recurring */
    case 'invoice.paid':

        $payment_currency = strtoupper($session->currency);
        $payment_total = in_array($payment_currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? $session->amount_paid : $session->amount_paid / 100;

        /* Process meta data */
        $metadata = $session->lines->data[0]->metadata;

        $user_id = (int)$metadata->user_id;
        $package_id = (int)$metadata->package_id;
        $payment_frequency = $metadata->payment_frequency;
        $payment_type = $metadata->payment_type;
        $base_amount = $metadata->base_amount;
        $taxes_ids = $metadata->taxes_ids;

        $pay_mode = $session->subscription ? 'recurring' : 'one_time';
        $payment_subscription_id = $pay_mode == 'recurring' ? 'stripe###' . $session->subscription : '';

        break;

    /* one time */
    case 'checkout.session.completed':

        if ($session->subscription) {
            die();
        }

        $payment_currency = strtoupper($session->currency);
        $payment_total = in_array($payment_currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? $session->amount_total : $session->amount_total / 100;

        $metadata = $session->metadata;
        $payment_type = $metadata->payment_type;
        if ($payment_type == "subscr") {
            $user_id = (int)$metadata->user_id;
            $package_id = (int)$metadata->package_id;
            $payment_frequency = $metadata->payment_frequency;
            $base_amount = $metadata->base_amount;
            $taxes_ids = $metadata->taxes_ids;

        } else if ($payment_type == "premium" || $payment_type == "banner-advertise") {
            $user_id = (int)$metadata->user_id;
            $product_id = (int)$metadata->product_id;
            $title = $metadata->title;
            $amount = $metadata->amount;
            $trans_desc = $metadata->trans_desc;
            $taxes_ids = $metadata->taxes_ids;
            $item_featured = $metadata->item_featured;
            $item_urgent = $metadata->item_urgent;
            $item_highlight = $metadata->item_highlight;
        } else {
            $order_id = (int)$metadata->order_id;
            $restaurant_id = (int)$metadata->restaurant_id;
        }

        $pay_mode = $session->subscription ? 'recurring' : 'one_time';
        $payment_subscription_id = $pay_mode == 'recurring' ? 'stripe###' . $session->subscription : '';

        break;
}

if ($order_id && function_exists('get_restaurant_option')) {
    /* mark order as paid */
    $order = ORM::for_table($config['db']['pre'] . 'orders')
        ->find_one($order_id);
    $order->is_paid = 1;
    $order->payment_gateway = 'stripe';
    $order->status = 'pending';
    $order->save();

    $wallet_amount = get_restaurant_option($restaurant_id, 'wallet_amount', 0);
    $wallet_amount += $payment_total;
    update_restaurant_option($restaurant_id, 'wallet_amount', $wallet_amount);

    // send success
    echo 'successful';
    exit();
}

$user = ORM::for_table($config['db']['pre'] . 'user')
    ->where('id', $user_id)
    ->find_one();

// check user exists
if (!isset($user['id'])) {
    http_response_code(400);
    die();
}

$ip = encode_ip($_SERVER, $_ENV);
$billing = array(
    'type' => escape(get_user_option($user_id, 'billing_details_type')),
    'tax_id' => escape(get_user_option($user_id, 'billing_tax_id')),
    'name' => escape(get_user_option($user_id, 'billing_name')),
    'address' => escape(get_user_option($user_id, 'billing_address')),
    'city' => escape(get_user_option($user_id, 'billing_city')),
    'state' => escape(get_user_option($user_id, 'billing_state')),
    'zipcode' => escape(get_user_option($user_id, 'billing_zipcode')),
    'country' => escape(get_user_option($user_id, 'billing_country'))
);

if ($payment_type == "subscr") {
    $package = ORM::for_table($config['db']['pre'] . 'plans')
        ->where('id', $package_id)
        ->find_one();

    // check plan exists
    if (!isset($package['id'])) {
        http_response_code(400);
        die();
    }

    /* Make sure transaction is not already exist */
    if (ORM::for_table($config['db']['pre'] . 'transaction')
        ->where('payment_id', $payment_id)
        ->where('transaction_gatway', 'stripe')
        ->count()) {
        http_response_code(400);
        die();
    }

    $subsc_check = ORM::for_table($config['db']['pre'] . 'upgrades')
        ->where('user_id', $user_id)
        ->find_one();
    if (isset($subsc_check['user_id'])) {
        $txn_type = 'subscr_update';

        if ($subsc_check['unique_id'] != $payment_subscription_id) {
            try {
                cancel_recurring_payment($user_id);
            } catch (\Exception $e) {
                error_log($e->getCode());
                error_log($e->getMessage());
            }
        }
    } else {
        $txn_type = 'subscr_signup';
    }

    $term = 0;
    switch ($payment_frequency) {
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

    // Add time to their subscription
    $expires = (time() + $term);
    $pdo = ORM::get_db();

    if ($txn_type == 'subscr_update') {
        $query = "UPDATE `" . $config['db']['pre'] . "upgrades` SET 
            `sub_id` = '" . validate_input($package_id) . "',
            `upgrade_expires` = '" . validate_input($expires) . "', 
            `pay_mode` = '$pay_mode', 
            `unique_id` = '" . validate_input($payment_subscription_id) . "', 
            `upgrade_lasttime` = '" . time() . "' 
        WHERE `user_id` = '" . validate_input($user_id) . "' LIMIT 1";
        $pdo->query($query);

        // update user data
        $user->group_id = $package_id;
        $user->save();
    } elseif ($txn_type == 'subscr_signup') {
        $subscription_status = "Active";

        $upgrades_insert = ORM::for_table($config['db']['pre'] . 'upgrades')->create();
        $upgrades_insert->sub_id = $package_id;
        $upgrades_insert->user_id = $user_id;
        $upgrades_insert->upgrade_lasttime = time();
        $upgrades_insert->upgrade_expires = $expires;
        $upgrades_insert->pay_mode = $pay_mode;
        $upgrades_insert->unique_id = $payment_subscription_id;
        $upgrades_insert->status = $subscription_status;
        $upgrades_insert->save();

        $user->group_id = $package_id;
        $user->save();
    }

    $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
    $trans_insert->product_name = $package['name'];
    $trans_insert->product_id = $package_id;
    $trans_insert->seller_id = $user_id;
    $trans_insert->status = 'success';
    $trans_insert->base_amount = $base_amount;
    $trans_insert->amount = $payment_total;
    $trans_insert->transaction_gatway = 'stripe';
    $trans_insert->transaction_ip = $ip;
    $trans_insert->transaction_time = time();
    $trans_insert->transaction_description = $package['name'];
    $trans_insert->payment_id = $payment_id;
    $trans_insert->transaction_method = 'Subscription';
    $trans_insert->frequency = $payment_frequency;
    $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
    $trans_insert->taxes_ids = $taxes_ids;
    $trans_insert->save();

    //Update Amount in balance table
    $balance = ORM::for_table($config['db']['pre'] . 'balance')->find_one(1);
    $current_amount = $balance['current_balance'];
    $total_earning = $balance['total_earning'];

    $updated_amount = ($payment_total + $current_amount);
    $total_earning = ($payment_total + $total_earning);

    $balance->current_balance = $updated_amount;
    $balance->total_earning = $total_earning;
    $balance->save();

    // check for affiliate payment
    check_affiliate_payment($user, $txn_type, $payment_total, 'stripe', $trans_insert->id);

    // send success
    echo 'successful';
} elseif ($payment_type == "banner-advertise") {

    $pdo = ORM::get_db();
    $txn_id = $amount;
    $payment_status = "Completed";
    $item_number = $product_id;
    $userdata = get_user_data(null, $user_id);
    $payer_id = $userdata['id'];
    $payer_email = $userdata['email'];
    $payer_name = $userdata['name'];
    $transaction_type = 'wire_transfer';
    $gross_total = $amount;
    $mc_currency = $config['currency_code'];

    $query = "SELECT t1.*, t2.title AS type_title FROM qbm_banners t1 LEFT JOIN qbm_types t2 ON t1.type_id = t2.id WHERE t1.id = '" . $item_number . "'";
    $banner_details = ORM::for_table('qbm_banners')->raw_query($query)->find_one();

    if (!empty($banner_details)) {
        $type_title = $banner_details["type_title"];
        $banner_title = $banner_details["title"];
    } else {
        $payment_status = "Unrecognized";
    }

    $sql = "INSERT INTO qbm_transactions (
			banner_id, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, created) VALUES (
			'" . $item_number . "',
			'" . $payer_name . "',
			'" . $payer_email . "',
			'" . floatval($gross_total) . "',
			'" . $mc_currency . "',
			'" . $payment_status . "',
			'" . $transaction_type . "',
			'" . $txn_id . "',
			'" . time() . "'
		)";

    $pdo->query($sql);
    $status_active = '1';
    $status_pending = '7';
    $registered = time();
    $banner_approval = get_option('qbm_enable_approval');

    if ($banner_approval) {
        $pdo->query("UPDATE qbm_banners SET status = '" . $status_pending . "', registered = '" . $registered . "', blocked = '" . $registered . "' WHERE id = '" . $item_number . "'");
    } else {
        $pdo->query("UPDATE qbm_banners SET status = '" . $status_active . "', registered = '" . $registered . "', blocked = '0' WHERE id = '" . $item_number . "'");
    }

    $tags = array("{payer_name}", "{payer_email}", "{amount}", "{currency}", "{type_title}", "{banner_title}", "{transaction_date}", "{gateway}");
    $vals = array($payer_name, $payer_id, $gross_total, $mc_currency, $type_title, $banner_title, date("Y-m-d H:i:s") . " (server time)", "EgoPay");

    //TODO: Setup send_thanksgiving_email
    // send_thanksgiving_email($tags, $vals, $payer_email);

    $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
    $trans_insert->product_name = $title;
    $trans_insert->product_id = $product_id;
    $trans_insert->seller_id = $user_id;
    $trans_insert->status = 'success';
    $trans_insert->amount = $amount;
    $trans_insert->base_amount = $amount;
    $trans_insert->transaction_gatway = 'stripe';
    $trans_insert->transaction_ip = $ip;
    $trans_insert->transaction_time = time();
    $trans_insert->transaction_description = $trans_desc;
    $trans_insert->payment_id = $payment_id;
    $trans_insert->transaction_method = 'banner-advertise';
    $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
    $trans_insert->taxes_ids = $taxes_ids;
    $trans_insert->save();

    //Update Amount in balance table
    $balance = ORM::for_table($config['db']['pre'] . 'balance')->find_one(1);
    $current_amount = $balance['current_balance'];
    $total_earning = $balance['total_earning'];

    $updated_amount = ($amount + $current_amount);
    $total_earning = ($amount + $total_earning);

    $balance->current_balance = $updated_amount;
    $balance->total_earning = $total_earning;
    $balance->save();

    // send success
    echo 'successful';
} else {

    $group_info = get_user_membership_detail($user_id);
    $featured_duration = $group_info['settings']['featured_duration'];
    $urgent_duration = $group_info['settings']['urgent_duration'];
    $highlight_duration = $group_info['settings']['highlight_duration'];
    if ($item_featured == '1') {
        $f_duration_timestamp = $featured_duration * 86400;
        $featured_exp_date = (time() + $f_duration_timestamp);
        $featured_insert = ORM::for_table($config['db']['pre'] . 'product')->find_one($product_id);
        $featured_insert->featured = '1';
        $featured_insert->featured_exp_date = $featured_exp_date;
        $featured_insert->save();
    }
    if ($item_urgent == '1') {
        $u_duration_timestamp = $urgent_duration * 86400;
        $urgent_exp_date = (time() + $u_duration_timestamp);
        $urgent_insert = ORM::for_table($config['db']['pre'] . 'product')->find_one($product_id);
        $urgent_insert->urgent = '1';
        $urgent_insert->urgent_exp_date = $urgent_exp_date;
        $urgent_insert->save();
    }
    if ($item_highlight == '1') {
        $h_duration_timestamp = $highlight_duration * 86400;
        $highlight_exp_date = (time() + $h_duration_timestamp);
        $highlight_insert = ORM::for_table($config['db']['pre'] . 'product')->find_one($product_id);
        $highlight_insert->highlight = '1';
        $highlight_insert->highlight_exp_date = $highlight_exp_date;
        $highlight_insert->save();
    }

    if (check_valid_resubmission($product_id)) {
        if ($item_featured == '1') {
            $f_duration_timestamp = $featured_duration * 86400;
            $featured_exp_date = (time() + $f_duration_timestamp);
            $query = "UPDATE " . $config['db']['pre'] . "product_resubmit set featured = '1',featured_exp_date='$featured_exp_date' where product_id='" . $product_id . "' LIMIT 1";
            $pdo->query($query);
        }
        if ($item_urgent == '1') {
            $u_duration_timestamp = $urgent_duration * 86400;
            $urgent_exp_date = (time() + $u_duration_timestamp);
            $query = "UPDATE " . $config['db']['pre'] . "product_resubmit set urgent = '1',urgent_exp_date='$urgent_exp_date' where product_id='" . $product_id . "' LIMIT 1";
            $pdo->query($query);
        }
        if ($item_highlight == '1') {
            $h_duration_timestamp = $highlight_duration * 86400;
            $highlight_exp_date = (time() + $h_duration_timestamp);
            $query = "UPDATE " . $config['db']['pre'] . "product_resubmit set highlight = '1',highlight_exp_date='$highlight_exp_date' where product_id='" . $product_id . "' LIMIT 1";
            $pdo->query($query);
        }
    }

    $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
    $trans_insert->product_name = $title;
    $trans_insert->product_id = $product_id;
    $trans_insert->seller_id = $user_id;
    $trans_insert->status = 'success';
    $trans_insert->amount = $amount;
    $trans_insert->base_amount = $amount;
    $trans_insert->featured = $item_featured;
    $trans_insert->urgent = $item_urgent;
    $trans_insert->highlight = $item_highlight;
    $trans_insert->transaction_gatway = 'stripe';
    $trans_insert->transaction_ip = $ip;
    $trans_insert->transaction_time = time();
    $trans_insert->transaction_description = $trans_desc;
    $trans_insert->payment_id = $payment_id;
    $trans_insert->transaction_method = 'Premium Ad';
    $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
    $trans_insert->taxes_ids = $taxes_ids;
    $trans_insert->save();

    //Update Amount in balance table
    $balance = ORM::for_table($config['db']['pre'] . 'balance')->find_one(1);
    $current_amount = $balance['current_balance'];
    $total_earning = $balance['total_earning'];

    $updated_amount = ($amount + $current_amount);
    $total_earning = ($amount + $total_earning);

    $balance->current_balance = $updated_amount;
    $balance->total_earning = $total_earning;
    $balance->save();

    // send success
    echo 'successful';
}


die();
<?php
namespace Midtrans;

    require_once  'Midtrans.php';

    if (isset($_GET['restaurant']) && function_exists('get_restaurant_option')) {
        $restaurant_id = $_GET['restaurant'];

        $mt_client_key = get_restaurant_option($restaurant_id, 'restaurant_midtrans_client_key');
        $mt_server_key = get_restaurant_option($restaurant_id, 'restaurant_midtrans_server_key');
        $mt_payment_mode = get_restaurant_option($restaurant_id, 'restaurant_midtrans_sandbox_mode');
    } else {

        $mt_client_key = get_option('midtrans_client_key');
        $mt_server_key = get_option('midtrans_server_key');
        $mt_payment_mode = get_option('midtrans_sandbox_mode');
    }

    if ($mt_payment_mode != 'test') {
        Config::$isProduction = true;
    }

    //Set Your server key
    Config::$serverKey = $mt_server_key;

    try {
        $notif = new Notification();
    } catch (\Exception $e) {
        error_log($e->getMessage());
        exit($e->getMessage());
    }

    $notif = $notif->getResponse();
    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $transaction_id = $notif->order_id;
    $fraud = $notif->fraud_status;

    $success = false;

    if ($transaction == 'capture') {
        // For credit card transaction, we need to check whether transaction is challenge by FDS or not
        if ($type == 'credit_card') {
            if ($fraud == 'challenge') {
                $msg = __('Challenge by FDS');
            } else {
                $success = true;
            }
        }
    } else if ($transaction == 'settlement') {
        $success = true;
    } else if ($transaction == 'pending') {
        $msg = __('Transaction is pending.');
    } else if ($transaction == 'deny') {
        $msg = __('Transaction was not successful');
    } else if ($transaction == 'expire') {
        $msg = __('Transaction was not successful');
    } else if ($transaction == 'cancel') {
        $msg = __('Transaction was not successful');
    }

    if($success) {
        transaction_success($transaction_id);
    } else {
        $transaction = ORM::for_table($config['db']['pre'].'transaction')->find_one($transaction_id);
        $transaction->status = 'failed';
        $transaction->save();
    }
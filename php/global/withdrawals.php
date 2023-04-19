<?php

// if disabled by admin
if (!$config['enable_affiliate_program']) {
    page_not_found();
}
if (checkloggedin()) {

    $userdata = ORM::for_table($config['db']['pre'] . 'user')->find_one($_SESSION['user']['id']);
    $balance = $userdata['balance'];

    $error = "";
    if (isset($_POST['submit']) && isset($_POST['payment_id'])) {
        if (is_numeric($_POST['amount']) || $_POST['amount'] > 0) {
            if ($balance > $_POST['amount']) {
                if ($_POST['amount'] <= get_option('affiliate_minimum_payout')) {
                    if (!empty($_POST['payment_id']) && !empty($_POST['account_details'])) {

                        // minus balance
                        $total = $balance - $_POST['amount'];
                        $userdata->balance = number_format($total, 2, '.', '');
                        $userdata->save();

                        $now = date("Y-m-d H:i:s");
                        $create_withdraw = ORM::for_table($config['db']['pre'] . 'withdrawal')->create();
                        $create_withdraw->user_id = $_SESSION['user']['id'];
                        $create_withdraw->amount = validate_input($_POST['amount']);
                        $create_withdraw->payment_method_id = validate_input($_POST['payment_id']);
                        $create_withdraw->account_details = validate_input($_POST['account_details']);
                        $create_withdraw->created_at = $now;
                        $create_withdraw->save();

                        /* Admin : new request */
                        $html = $config['email_sub_withdraw_request'];
                        $html = str_replace('{SITE_TITLE}', $config['site_title'], $html);
                        $html = str_replace('{SITE_URL}', $config['site_url'], $html);
                        $html = str_replace('{USER_ID}', $userdata['id'], $html);
                        $html = str_replace('{USERNAME}', $userdata['username'], $html);
                        $html = str_replace('{EMAIL}', $userdata['email'], $html);
                        $html = str_replace('{USER_FULLNAME}', $userdata['name'], $html);
                        $html = str_replace('{AMOUNT}', validate_input($_POST['amount']), $html);
                        $email_subject = $html;

                        $html = $config['emailHTML_withdraw_request'];
                        $html = str_replace('{SITE_TITLE}', $config['site_title'], $html);
                        $html = str_replace('{SITE_URL}', $config['site_url'], $html);
                        $html = str_replace('{USER_ID}', $userdata['id'], $html);
                        $html = str_replace('{USERNAME}', $userdata['username'], $html);
                        $html = str_replace('{EMAIL}', $userdata['email'], $html);
                        $html = str_replace('{USER_FULLNAME}', $userdata['name'], $html);
                        $html = str_replace('{AMOUNT}', validate_input($_POST['amount']), $html);
                        $email_body = $html;

                        email($config['admin_email'], $config['site_title'], $email_subject, $email_body);

                        message(__("Success"), __("Amount added Successfully to withdrawal."), $link['WITHDRAWALS']);
                        exit();
                    } else {
                        $error = __("Payment details are required.");
                    }
                } else {
                    $error = __("Minimum withdrawal amount is:") . price_format(get_option('affiliate_minimum_payout'));
                }
            } else {
                $error = __("Insufficient fund, withdrawal amount must be lower than your wallet amount.");
            }

        } else {
            $error = __("Amount is not valid");
        }
    }

    $payment_methods = ORM::for_table($config['db']['pre'] . 'payments')
        ->where('payment_install', '1')
        ->find_many();

    $withdrawals = ORM::for_table($config['db']['pre'] . 'withdrawal')
        ->table_alias('w')
        ->select_many_expr('w.*', 'p.payment_title')
        ->join($config['db']['pre'] . 'payments', 'w.payment_method_id = p.payment_id', 'p')
        ->where('user_id', $_SESSION['user']['id'])
        ->order_by_desc('id')
        ->find_array();

    HtmlTemplate::display('global/withdrawals', array(
        'affiliates' => array(),
        'error' => $error,
        'payment_methods' => $payment_methods,
        'withdrawals' => $withdrawals
    ));
} else {
    headerRedirect($link['LOGIN']);
}
<?php
// if disabled by admin
if (!$config['enable_affiliate_program']) {
    page_not_found();
}
if (checkloggedin()) {
    $ses_userdata = get_user_data($_SESSION['user']['username']);
    $referral_key = $ses_userdata['referral_key'];
    if (empty($referral_key)) {
        // create referral_key for old users
        $referral_key = uniqid(get_random_string(5));
        $user = ORM::for_table($config['db']['pre'] . 'user')->find_one($_SESSION['user']['id']);
        $user->referral_key = $referral_key;
        $user->save();
    }

    $total_referred = ORM::for_table($config['db']['pre'] . 'user')
        ->where('referred_by', $_SESSION['user']['id'])
        ->count();

    $total_earning = ORM::for_table($config['db']['pre'] . 'affiliates')
        ->where('referrer_id', $_SESSION['user']['id'])
        ->sum('commission');

    $affiliates = ORM::for_table($config['db']['pre'] . 'affiliates')
        ->where('referrer_id', $_SESSION['user']['id'])
        ->order_by_desc('id')
        ->find_array();


    HtmlTemplate::display('global/affiliate-program', array(
        'wallet' => price_format($ses_userdata['balance']),
        'total_referred' => number_format($total_referred),
        'total_earning' => price_format($total_earning),
        'affiliates' => $affiliates,
        'affiliate_url' => $config['site_url'] . '?ref=' . $referral_key
    ));
} else {
    headerRedirect($link['LOGIN']);
}
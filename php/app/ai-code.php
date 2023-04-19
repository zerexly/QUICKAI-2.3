<?php
global $config;

// if disabled by admin
if(!$config['enable_ai_code']) {
    page_not_found();
}

if (checkloggedin()) {

    $start = date('Y-m-01');
    $end = date_create(date('Y-m-t'))->modify('+1 day')->format('Y-m-d');

    $total_words_used = ORM::for_table($config['db']['pre'] . 'word_used')
        ->where('user_id', $_SESSION['user']['id'])
        ->where_raw("(`date` BETWEEN '$start' AND '$end')")
        ->sum('words');

    $membership = get_user_membership_detail($_SESSION['user']['id']);
    $words_limit = $membership['settings']['ai_words_limit'];
    $membership_ai_code = $membership['settings']['ai_code'];

    HtmlTemplate::display('ai-code', array(
        'total_words_used' => $total_words_used,
        'words_limit' => $words_limit,
        'membership_ai_code' => $membership_ai_code,
    ));
} else {
    headerRedirect($link['LOGIN']);
}
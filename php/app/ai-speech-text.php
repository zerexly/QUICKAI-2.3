<?php
global $config;

// if disabled by admin
if(!$config['enable_speech_to_text']) {
    page_not_found();
}

if (checkloggedin()) {

    $start = date('Y-m-01');
    $end = date_create(date('Y-m-t'))->modify('+1 day')->format('Y-m-d');

    $total_speech_text_used = ORM::for_table($config['db']['pre'] . 'speech_to_text_used')
        ->where('user_id', $_SESSION['user']['id'])
        ->where_raw("(`date` BETWEEN '$start' AND '$end')")
        ->count();

    $membership = get_user_membership_detail($_SESSION['user']['id']);
    $speech_text_limit = $membership['settings']['ai_speech_to_text_limit'];
    $speech_text_file_limit = $membership['settings']['ai_speech_to_text_file_limit'];

    HtmlTemplate::display('ai-speech-text', array(
        'total_speech_text_used' => $total_speech_text_used,
        'speech_text_limit' => $speech_text_limit,
        'speech_text_file_limit' => $speech_text_file_limit
    ));
} else {
    headerRedirect($link['LOGIN']);
}
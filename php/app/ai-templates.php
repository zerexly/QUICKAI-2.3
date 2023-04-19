<?php
global $config;
if(checkloggedin())
{
    $start = date('Y-m-01');
    $end = date_create(date('Y-m-t'))->modify('+1 day')->format('Y-m-d');

    $total_words_used = ORM::for_table($config['db']['pre'] . 'word_used')
        ->where('user_id', $_SESSION['user']['id'])
        ->where_raw("(`date` BETWEEN '$start' AND '$end')")
        ->sum('words');

    $membership = get_user_membership_detail($_SESSION['user']['id']);
    $words_limit = $membership['settings']['ai_words_limit'];

    HtmlTemplate::display('ai-templates', array(
        'ai_templates' => get_ai_templates(),
        'total_words_used' => $total_words_used,
        'words_limit' => $words_limit,
        'plan_templates' => $membership['settings']['ai_templates']
    ));
}
else{
    headerRedirect($link['LOGIN']);
}
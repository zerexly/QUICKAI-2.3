<?php
global $config;

// if disabled by admin
if(!$config['enable_ai_chat']) {
    page_not_found();
}

if (checkloggedin()) {

    $chats = ORM::for_table($config['db']['pre'] . 'ai_chat')
        ->where('user_id', $_SESSION['user']['id'])
        ->find_array();

    $start = date('Y-m-01');
    $end = date_create(date('Y-m-t'))->modify('+1 day')->format('Y-m-d');

    $total_words_used = ORM::for_table($config['db']['pre'] . 'word_used')
        ->where('user_id', $_SESSION['user']['id'])
        ->where_raw("(`date` BETWEEN '$start' AND '$end')")
        ->sum('words');

    $membership = get_user_membership_detail($_SESSION['user']['id']);
    $words_limit = $membership['settings']['ai_words_limit'];
    $membership_ai_chat = $membership['settings']['ai_chat'];

    $ai_chat_bot_name = !empty($config['ai_chat_bot_name']) ? $config['ai_chat_bot_name'] : __('AI Chat Bot');
    $ai_chat_bot_avatar = !empty($config['chat_bot_avatar']) ? $config['chat_bot_avatar'] : 'default_user.png';

    HtmlTemplate::display('ai-chat', array(
        'total_words_used' => $total_words_used,
        'words_limit' => $words_limit,
        'membership_ai_chat' => $membership_ai_chat,
        'chats' => $chats,
        'ai_chat_bot_name' => $ai_chat_bot_name,
        'ai_chat_bot_avatar' => $ai_chat_bot_avatar
    ));
} else {
    headerRedirect($link['LOGIN']);
}
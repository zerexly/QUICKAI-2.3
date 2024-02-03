<?php
global $config;

// if disabled by admin
if(!$config['enable_ai_chat']) {
    page_not_found();
}

if (isset($current_user['id'])) {

    $chat_bots_available = ORM::for_table($config['db']['pre'] . 'ai_chat_bots')
        ->where('active', 1)
        ->count();

    /* redirect to the default bot if there is no chatbots */
    if(!$chat_bots_available){
        headerRedirect(url('AI_CHAT'));
    }

    $membership = $current_user['plan'];
    $words_limit = $membership['settings']['ai_words_limit'];
    $membership_ai_chat = $membership['settings']['ai_chat'];
    $membership_ai_chatbots = !empty($membership['settings']['ai_chatbots']) ? $membership['settings']['ai_chatbots'] : [];

    $cats = ORM::for_table($config['db']['pre'] . 'ai_chat_bots_categories')
        ->where('active', '1')
        ->order_by_asc('position')
        ->find_array();

    foreach ($cats as $key => $cat) {
        $chat_bots = ORM::for_table($config['db']['pre'] . 'ai_chat_bots')
            ->where('active', '1')
            ->where('category_id', $cat['id'])
            ->order_by_asc('position')
            ->find_array();

        $cats[$key]['chat_bots'] = $chat_bots;

        if(get_option('hide_plan_disabled_features')) {
            $cat_chat_bots = array();
            foreach ($chat_bots as $bot) {
                if (in_array($bot['id'], $membership_ai_chatbots)) {
                    $cat_chat_bots[] = $bot;
                }
            }
            $cats[$key]['chat_bots'] = $cat_chat_bots;
        }
    }

    $start = date('Y-m-01');
    $end = date_create(date('Y-m-t'))->modify('+1 day')->format('Y-m-d');

    $total_words_used = get_user_option($_SESSION['user']['id'], 'total_words_used', 0);

    $ai_chat_bot_name = !empty($config['ai_chat_bot_name']) ? $config['ai_chat_bot_name'] : __('AI Chat Bot');
    $ai_chat_bot_avatar = !empty($config['chat_bot_avatar']) ? $config['chat_bot_avatar'] : 'default_user.png';

    HtmlTemplate::display('ai-chat-bots', array(
        'total_words_used' => $total_words_used,
        'words_limit' => $words_limit,
        'membership_ai_chat' => $membership_ai_chat,
        'membership_ai_chatbots' => $membership_ai_chatbots,
        'chat_bots' => $cats,
        'ai_chat_bot_name' => $ai_chat_bot_name,
        'ai_chat_bot_avatar' => $ai_chat_bot_avatar
    ));
} else {
    headerRedirect($link['LOGIN']);
}

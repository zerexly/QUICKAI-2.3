<?php
global $config;

// if disabled by admin
if(!$config['enable_ai_chat']) {
    page_not_found();
}

if (isset($current_user['id'])) {

    if(!empty($_GET['id']) && is_numeric($_GET['id'])) {
        $chat_bot = ORM::for_table($config['db']['pre'] . 'ai_chat_bots')
            ->find_one($_GET['id']);

        if(empty($chat_bot['id'])){
            page_not_found();
        }

        $translations = json_decode((string) $chat_bot['translations'], true);

        $chat_bot_id = $bot_id = $chat_bot['id'];
        $bot_welcome_msg = !empty($translations[$config['lang_code']]['welcome_message'])
            ? $translations[$config['lang_code']]['welcome_message']
            : $chat_bot['welcome_message'];
        $bot_role = !empty($translations[$config['lang_code']]['role'])
            ? $translations[$config['lang_code']]['role']
            : $chat_bot['role'];
        $ai_chat_bot_name = $chat_bot['name'];

        if(!empty($chat_bot['image']))
            $ai_chat_bot_avatar = $config['site_url'].'storage/chat-bots/'.$chat_bot['image'];
        else
            $ai_chat_bot_avatar = get_avatar_url_by_name($chat_bot['name']);

        /* get conversations */
        $conversations = ORM::for_table($config['db']['pre'] . 'ai_chat_conversations')
            ->where('bot_id', $_GET['id'])
            ->where('user_id', $_SESSION['user']['id'])
            ->order_by_desc('updated_at')
            ->find_array();

        $default_chats = ORM::for_table($config['db']['pre'] . 'ai_chat')
            ->where_null('conversation_id')
            ->where('bot_id', $_GET['id'])
            ->where('user_id', $_SESSION['user']['id'])
            ->count();
    } else {
        /* if default bot is disabled */
        if(!get_option("enable_default_chat_bot",1))
            page_not_found();

        $chat_bot_id = 'default';
        $bot_id = $bot_welcome_msg = $bot_role = null;
        $ai_chat_bot_name = !empty($config['ai_chat_bot_name']) ? $config['ai_chat_bot_name'] : __('AI Chat Bot');

        $ai_chat_bot_avatar = !empty($config['chat_bot_avatar']) ? $config['chat_bot_avatar'] : 'default_user.png';
        $ai_chat_bot_avatar = $config['site_url'].'storage/profile/'.$ai_chat_bot_avatar;

        /* get conversations */
        $conversations = ORM::for_table($config['db']['pre'] . 'ai_chat_conversations')
            ->where_null('bot_id')
            ->where('user_id', $_SESSION['user']['id'])
            ->order_by_desc('updated_at')
            ->find_array();

        $default_chats = ORM::for_table($config['db']['pre'] . 'ai_chat')
            ->where_null('conversation_id')
            ->where_null('bot_id')
            ->where('user_id', $_SESSION['user']['id'])
            ->count();
    }

    /* add default conversation for older version's chats */
    if($default_chats){
        $conversation = [
            'id' => 'default',
            'title' => __('New Conversation'),
            'last_message' => '...',
            'updated_at' =>  date('Y-m-d H:i:s')
        ];
        $conversations[] = $conversation;
    }

    /* add default conversation if it's empty */
    if(empty($conversations)){
        $conversation = [
            'id' => '',
            'title' => __('New Conversation'),
            'last_message' => '...',
            'updated_at' =>  date('Y-m-d H:i:s')
        ];
        $conversations[] = $conversation;
    }

    /* chat prompts */
    $chat_prompts = ORM::for_table($config['db']['pre'] . 'ai_chat_prompts')
        ->where('active', 1)
        ->where_raw('(chat_bots IS NULL OR FIND_IN_SET("'.$chat_bot_id.'", chat_bots))')
        ->order_by_asc('position')
        ->find_array();

    $start = date('Y-m-01');
    $end = date_create(date('Y-m-t'))->modify('+1 day')->format('Y-m-d');

    $total_words_used = get_user_option($_SESSION['user']['id'], 'total_words_used', 0);

    $membership = get_user_membership_detail($_SESSION['user']['id']);
    $words_limit = $membership['settings']['ai_words_limit'];
    $membership_ai_chat = $membership['settings']['ai_chat'];
    $membership_ai_chatbots = !empty($membership['settings']['ai_chatbots']) ? $membership['settings']['ai_chatbots'] : [];

    HtmlTemplate::display('ai-chat', array(
        'total_words_used' => $total_words_used,
        'words_limit' => $words_limit,
        'membership_ai_chat' => $membership_ai_chat,
        'membership_ai_chatbots' => $membership_ai_chatbots,
        'conversations' => $conversations,
        'bot_id' => $bot_id,
        'bot_role' => $bot_role,
        'bot_welcome_msg' => $bot_welcome_msg,
        'ai_chat_bot_name' => $ai_chat_bot_name,
        'ai_chat_bot_avatar' => $ai_chat_bot_avatar,
        'chat_prompts' => $chat_prompts,
    ));
} else {
    headerRedirect($link['LOGIN']);
}

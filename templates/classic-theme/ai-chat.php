<?php

overall_header(__("AI Chat"));
?>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <?php
        include_once TEMPLATE_PATH . '/dashboard_sidebar.php';
        ?>
        <!-- Dashboard Content
        ================================================== -->
        <div class="dashboard-content-container" data-simplebar>
            <div class="dashboard-content-inner">
                <?php print_adsense_code('header_bottom'); ?>
                <!-- Dashboard Headline -->
                <div class="dashboard-headline">
                    <h3 class="d-flex align-items-center">
                        <?php _e("AI Chat") ?>
                        <div class="word-used-wrapper margin-left-10">
                            <i class="icon-feather-bar-chart-2"></i>
                            <?php echo '<i id="quick-words-left">' .
                                _esc(number_format((float)$total_words_used), 0) . '</i> / ' .
                                ($words_limit == -1
                                    ? __('Unlimited')
                                    : _esc(number_format($words_limit + get_user_option($_SESSION['user']['id'], 'total_words_available', 0)), 0));
                            ?>
                            <strong><?php _e('Words Used'); ?></strong>
                        </div>
                    </h3>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><?php _e("AI Chat") ?></li>
                        </ul>
                    </nav>
                </div>
                <?php if ($membership_ai_chat && ($bot_id == null || in_array($bot_id, $membership_ai_chatbots))) { ?>
                    <div class="notification notice">
                        <?php _e("Here you can chat with the AI. Ask your questions or just have fun.") ?>
                    </div>
                <?php } else { ?>
                    <div class="notification small-notification error">
                        <?php _e("Upgrade your membership plan to use this feature.") ?>
                    </div>
                <?php } ?>

                <div class="messages-container margin-top-0">
                    <div class="messages-container-inner">
                        <!-- Messages -->
                        <div class="messages-inbox">
                            <div class="messages-headline">
                                <div class="input-with-icon">
                                    <input id="conversation-search" type="text" placeholder="Search">
                                    <i class="icon-material-outline-search"></i>
                                </div>
                            </div>
                            <ul id="conversations-wrapper">
                                <?php foreach ($conversations as $conversation) { ?>
                                    <li>
                                        <a href="javascript:void(0)"
                                           class="conversation"
                                           data-id="<?php _esc($conversation['id']) ?>">
                                            <div class="message-by margin-left-0">
                                                <div class="message-by-headline">
                                                    <h5><?php _esc($conversation['title']) ?></h5>
                                                    <input class="conversation-title with-border small-input"
                                                           type="text"
                                                           value="<?php _esc(escape($conversation['title'])) ?>">
                                                    <span class="conversation-time"><?php _esc(timeAgo($conversation['updated_at'])) ?></span>
                                                    <span class="conversation-edit"><i
                                                                class="icon-feather-edit"></i> <?php _e('Edit') ?></span>
                                                </div>
                                                <p class="conversation-msg"><?php _esc(strlimiter($conversation['last_message'], 100)) ?></p>
                                            </div>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                            <div class="messages-inbox-bottom">
                                <a href="javascript:void(0)" id="new-conversation"
                                   class="button full-width button-sliding-icon"><?php _e('New Conversation'); ?> <i
                                            class="icon-feather-plus"></i></a>
                            </div>
                        </div>
                        <!-- Messages / End -->
                        <!-- Message Content -->
                        <div class="message-content">
                            <div class="messages-headline">
                                <h4 class="d-flex align-items-center">
                                    <div class="user-avatar margin-right-10">
                                        <img src="<?php _esc($ai_chat_bot_avatar) ?>"
                                             alt="<?php _esc($ai_chat_bot_name) ?>" width="25"/>
                                    </div>
                                    <div class="line-height-1">
                                        <span><?php _esc($ai_chat_bot_name) ?></span>
                                        <?php if ($bot_role) { ?>
                                            <br>
                                            <div class="margin-top-3"><small><?php _esc($bot_role) ?></small></div>
                                        <?php } ?>
                                    </div>
                                </h4>
                                <div class="message-action">
                                    <a href="#" class="button ripple-effect btn-sm d-md-none d-inline-block"
                                       id="show-conversations" title="<?php _e("Show Conversations") ?>"
                                       data-tippy-placement="top"><i class="icon-feather-menu"></i></a>
                                    <a href="#" class="button ripple-effect btn-sm" id="export-chats"
                                       title="<?php _e("Export Conversation") ?>"
                                       data-tippy-placement="top"><i class="icon-feather-download"></i></a>
                                    <a href="#" class="button ripple-effect btn-sm red" id="delete-chats"
                                       title="<?php _e("Delete Conversation") ?>"
                                       data-tippy-placement="top"><i class="icon-feather-trash-2"></i></a>
                                </div>
                            </div>

                            <!-- Message Content Inner -->
                            <div class="message-content-inner">
                                <?php if ($bot_welcome_msg) { ?>
                                    <div class="message-bubble">
                                        <div class="message-bubble-inner">
                                            <div class="message-avatar">
                                                <img src="<?php _esc($ai_chat_bot_avatar) ?>"
                                                     alt="<?php _esc($ai_chat_bot_name) ?>"/>
                                            </div>
                                            <div class="message-text">
                                                <p><?php _esc($bot_welcome_msg) ?></p>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                <?php } ?>
                                <div id="dynamic-messages">
                                </div>
                                <div id="conversation-loader" class="button-progress"></div>
                            </div>
                            <!-- Message Content Inner / End -->

                            <!-- Reply Area -->
                            <form id="ai-chat-form">
                                <div class="message-reply">
                                    <input type="hidden" name="bot_id" id="bot_id" value="<?php _esc($bot_id) ?>">
                                    <textarea
                                            placeholder="<?php _e('Type your message here (Use shift + Enter to send)') ?>"
                                            id="ai-chat-textarea" rows="2"></textarea>
                                    <div>
                                        <?php if (get_option("enable_ai_chat_prompts", 1)) { ?>
                                            <a id="chat-prompts" href="javascript:void(0)"
                                               title="<?php _e('Prompt Library'); ?>" data-tippy-placement="top"><i
                                                        class="fa fa-book"></i></a>
                                        <?php } ?>
                                        <?php if (get_option("enable_ai_chat_mic", 1)) { ?>
                                            <a id="chat-microphone" href="javascript:void(0)"><i
                                                        class="fa fa-microphone"></i></a>
                                        <?php } ?>
                                        <button id="chat-send-button" type="submit"
                                                class="button ripple-effect">
                                            <?php _e('Send') ?>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-error message-reply padding-top-10 padding-bottom-10"></div>
                            </form>

                        </div>
                        <!-- Message Content -->

                    </div>
                </div>
                <?php print_adsense_code('footer_top'); ?>
                <!-- Footer -->
                <div class="dashboard-footer-spacer"></div>
                <div class="small-footer margin-top-15">
                    <div class="footer-copyright">
                        <?php _esc($config['copyright_text']); ?>
                    </div>
                    <ul class="footer-social-links">
                        <?php
                        if ($config['facebook_link'] != "")
                            echo '<li><a href="' . _esc($config['facebook_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-facebook"></i></a></li>';
                        if ($config['twitter_link'] != "")
                            echo '<li><a href="' . _esc($config['twitter_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-twitter"></i></a></li>';
                        if ($config['instagram_link'] != "")
                            echo '<li><a href="' . _esc($config['instagram_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-instagram"></i></a></li>';
                        if ($config['linkedin_link'] != "")
                            echo '<li><a href="' . _esc($config['linkedin_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-linkedin"></i></a></li>';
                        if ($config['pinterest_link'] != "")
                            echo '<li><a href="' . _esc($config['pinterest_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-pinterest"></i></a></li>';
                        if ($config['youtube_link'] != "")
                            echo '<li><a href="' . _esc($config['youtube_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-youtube"></i></a></li>';
                        ?>
                    </ul>
                    <div class="clearfix"></div>
                </div>

            </div>
        </div>
    </div>
<?php ob_start() ?>
<?php if (get_option("enable_ai_chat_prompts", 1)) { ?>
    <!-- Prompt library -->
    <div id="prompt-library-popup" class="zoom-anim-dialog mfp-hide dialog-with-tabs">
        <!--Tabs -->
        <div class="sign-in-form">
            <ul class="popup-tabs-nav">
                <li><a><?php _e('Prompt Library'); ?></a></li>
            </ul>

            <div class="popup-tabs-container">
                <!-- Tab -->
                <div class="popup-tab-content">
                    <input id="prompt-search" type="search" placeholder="<?php _e('Search Prompts') ?>"
                           class="with-border">
                    <div id="chat-prompts-list">
                        <?php foreach ($chat_prompts as $chat_prompt) {
                            $translations = json_decode((string) $chat_prompt['translations'], true);
                            $description = !empty($translations[$config['lang_code']]['description'])
                                ? $translations[$config['lang_code']]['description']
                                : $chat_prompt['description'];
                            ?>
                            <a href="javascript:void(0)"
                               data-search-key="<?php _esc(escape($chat_prompt['prompt'] . ' ' . $description)) ?>"
                               data-prompt="<?php _esc(escape($chat_prompt['prompt'])) ?>">
                                <div class="dashboard-box ai-templates">
                                    <div class="content">
                                        <h4>
                                            <?php _esc(!empty($translations[$config['lang_code']]['title'])
                                                ? $translations[$config['lang_code']]['title']
                                                : $chat_prompt['title']) ?>
                                        </h4>
                                        <p class="margin-bottom-0"><?php _esc($description) ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

    <script>
        const BOT_IMG = "<?php _esc($ai_chat_bot_avatar)?>";
        const PERSON_IMG = "<?php _esc($config['site_url']);?>storage/profile/<?php _esc($userpic)?>";
        const BOT_NAME = "<?php _esc($ai_chat_bot_name) ?>";
        const PERSON_NAME = "<?php _esc($username) ?>";

        const LANG_COPY = "<?php _e('Copy') ?>";
        const LANG_NEW_CONVERSATION = "<?php _e('New Conversation') ?>";

        const ENABLE_ENTER_TO_SEND = <?php _esc(get_option("enable_ai_chat_enter_send",0)); ?>;
    </script>

    <link href="<?php _esc(TEMPLATE_URL); ?>/css/markdown.css" rel="stylesheet">

    <link href="<?php _esc(TEMPLATE_URL); ?>/css/highlight.dark.min.css" rel="stylesheet">
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/highlight.min.js"></script>
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/showdown.min.js"></script>

    <script src="<?php _esc(TEMPLATE_URL); ?>/js/ai-chat.js?ver=<?php _esc($config['version']); ?>"></script>
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';

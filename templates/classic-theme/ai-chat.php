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
                                    : _esc(number_format($words_limit), 0));
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
                <?php if ($membership_ai_chat) { ?>
                    <div class="notification notice">
                        <?php _e("Here you can chat with the AI. Ask your questions or just have fun.") ?>
                    </div>
                <?php } else { ?>
                    <div class="notification small-notification error">
                        <?php _e("You can not use the chat feature with your OpenAI model. Upgrade your membership plan to use this feature.") ?>
                    </div>
                <?php } ?>

                <div class="messages-container margin-top-0">
                    <div class="messages-container-inner">
                        <!-- Message Content -->
                        <div class="message-content">
                            <div class="messages-headline">
                                <h4><div class="user-avatar margin-right-10">
                                        <img src="<?php _esc($config['site_url']); ?>storage/profile/<?php _esc($ai_chat_bot_avatar) ?>" alt="<?php _esc($ai_chat_bot_name) ?>" width="25"/>
                                    </div>
                                    <?php _esc($ai_chat_bot_name) ?></h4>
                                <div class="message-action">
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
                                <?php
                                $last_time = null;
                                foreach ($chats as $chat) {

                                    if ($last_time) {
                                        $date1 = new DateTime($last_time);
                                    } else {
                                        $date1 = new DateTime();
                                    }
                                    $date2 = new DateTime($chat['date']);
                                    $interval = $date1->diff($date2);
                                    if ($interval->d) { ?>
                                        <!-- Time Sign -->
                                        <div class="message-time-sign">
                                            <span><?php _esc($date2->format('F d, Y')) ?></span>
                                        </div>
                                    <?php }

                                    $last_time = $chat['date'];
                                    ?>
                                    <div class="message-bubble me">
                                        <div class="message-bubble-inner">
                                            <div class="message-avatar">
                                                <img src="<?php _esc($config['site_url']); ?>storage/profile/<?php _esc($userpic) ?>" alt="<?php _esc($username) ?>"/>
                                            </div>
                                            <div class="message-text"><p><?php _esc(nl2br(escape($chat['user_message']))) ?></p></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <?php if (!empty($chat['ai_message'])) { ?>
                                        <div class="message-bubble">
                                            <div class="message-bubble-inner">
                                                <div class="message-avatar">
                                                    <img src="<?php _esc($config['site_url']); ?>storage/profile/<?php _esc($ai_chat_bot_avatar) ?>" alt="<?php _esc($ai_chat_bot_name) ?>"/>
                                                </div>
                                                <div class="message-text"><p><?php _esc(nl2br(escape($chat['ai_message']))) ?></p>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                            <!-- Message Content Inner / End -->

                            <!-- Reply Area -->
                            <form id="ai-chat-form">
                                <div class="message-reply">
                                    <input type="text" placeholder="<?php _e('Type your message here...') ?>" id="ai-chat-textarea">
                                    <button id="chat-send-button" type="submit"
                                            class="button ripple-effect"><?php _e('Send') ?></button>
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
    <script>
        const BOT_IMG = "<?php _esc($config['site_url']);?>storage/profile/<?php _esc($ai_chat_bot_avatar)?>";
        const PERSON_IMG = "<?php _esc($config['site_url']);?>storage/profile/<?php _esc($userpic)?>";
        const BOT_NAME = "<?php _esc($ai_chat_bot_name) ?>";
        const PERSON_NAME = "<?php _esc($username) ?>";
    </script>
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/ai-chat.js"></script>
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';
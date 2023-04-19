<!-- Page Sidebar Start-->
<div class="iconsidebar-menu iconbar-mainmenu-close">
    <div class="sidebar">
        <ul class="iconMenu-bar custom-scrollbar">
            <li data-page="dashboard">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>index.php">
                    <i class="icon-feather-home"></i><span><?php _e('Dashboard') ?></span>
                </a>
            </li>
            <li data-page="ai-templates">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>app/ai-templates.php">
                    <i class="icon-feather-layers"></i><span><?php _e('AI Templates') ?></span>
                </a>
                <ul class="iconbar-mainmenu custom-scrollbar">
                    <li class="iconbar-header"><?php _e('AI Templates') ?></li>
                    <li><a href="<?php echo ADMINURL; ?>app/ai-templates.php" data-page="ai-templates"><?php _e('AI Templates') ?></a></li>
                    <li><a href="<?php echo ADMINURL; ?>app/ai-custom-templates.php" data-page="ai-custom-templates"><?php _e('Custom Templates') ?></a></li>
                    <li><a href="<?php echo ADMINURL; ?>app/ai-template-categories.php" data-page="ai-template-categories"><?php _e('Categories') ?></a></li>
                </ul>
            </li>
            <li data-page="ai-documents">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>app/ai-documents.php">
                    <i class="icon-feather-file"></i><span><?php _e('AI Documents') ?></span>
                </a>
            </li>
            <li data-page="ai-images">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>app/ai-images.php">
                    <i class="icon-feather-image"></i><span><?php _e('AI Images') ?></span>
                </a>
            </li>
            <li data-page="membership">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/membership_plan.php">
                    <i class="icon-feather-gift"></i><span><?php _e('Membership') ?></span>
                </a>
                <ul class="iconbar-mainmenu custom-scrollbar">
                    <li class="iconbar-header"><?php _e('Membership') ?></li>
                    <li><a href="<?php echo ADMINURL; ?>global/membership_plan.php" data-page="membership-plans"><?php _e('Plans') ?></a></li>
                    <li><a href="<?php echo ADMINURL; ?>global/membership_plan_custom.php" data-page="membership-custom"><?php _e('Custom Settings') ?></a></li>
                    <li><a href="<?php echo ADMINURL; ?>global/upgrades.php" data-page="membership-upgrades"><?php _e('Upgrades') ?></a></li>
                    <li><a href="<?php echo ADMINURL; ?>global/cron_logs.php" data-page="cron-logs"><?php _e('Cron Logs') ?></a></li>
                    <li><a href="<?php echo ADMINURL; ?>global/taxes.php" data-page="taxes"><?php _e('Taxes') ?></a></li>
                </ul>
            </li>
            <li data-page="payment-methods">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/payment_methods.php">
                    <i class="icon-feather-server"></i><span><?php _e('Payment Methods') ?></span>
                </a>
            </li>
            <li data-page="taxes">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/taxes.php">
                    <i class="icon-feather-file-text"></i><span><?php _e('Taxes') ?></span>
                </a>
            </li>
            <li data-page="transactions">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/transactions.php">
                    <i class="icon-feather-trending-up"></i><span><?php _e('Transactions') ?></span>
                </a>
            </li>
            <li data-page="withdrawals">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/withdrawals.php">
                    <i class="fa fa-money"></i><span><?php _e('Withdrawals') ?></span>
                </a>
            </li>
            <li data-page="email-template">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/email-template.php">
                    <i class="icon-feather-mail"></i><span><?php _e('Email Template') ?></span>
                </a>
            </li>
            <li data-page="languages">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/languages.php">
                    <i class="icon-feather-globe"></i><span><?php _e('Languages') ?></span>
                </a>
            </li>
            <li data-page="currencies">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/currency.php">
                    <i class="icon-feather-credit-card"></i><span><?php _e('Currencies') ?></span>
                </a>
            </li>
            <li data-page="timezones">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/timezones.php">
                    <i class="icon-feather-clock"></i><span><?php _e('Time Zones') ?></span>
                </a>
            </li>
            <li data-page="advertising">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/advertising.php">
                    <i class="icon-feather-monitor"></i><span><?php _e('Advertising') ?></span>
                </a>
            </li>
            <li data-page="blog">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/blog.php">
                    <i class="icon-feather-gift"></i><span><?php _e('Blog') ?></span>
                </a>
                <ul class="iconbar-mainmenu custom-scrollbar">
                    <li class="iconbar-header"><?php _e('Blog') ?></li>
                    <li><a href="<?php echo ADMINURL; ?>global/blog.php" data-page="all-blogs"><?php _e('All Blog') ?></a></li>
                    <li><a href="<?php echo ADMINURL; ?>global/blog-post.php" data-page="blog-post"><?php _e('Add New') ?></a></li>
                    <li><a href="<?php echo ADMINURL; ?>global/blog-cat.php" data-page="blog-cat"><?php _e('Categories') ?></a></li>
                    <li><a href="<?php echo ADMINURL; ?>global/blog-comments.php" data-page="blog-comments"><?php _e('Comments') ?></a></li>
                </ul>
            </li>
            <li data-page="testimonials">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/testimonials.php">
                    <i class="icon-feather-star"></i><span><?php _e('Testimonials') ?></span>
                </a>
            </li>
            <li data-page="pages">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/pages.php">
                    <i class="icon-feather-file"></i><span><?php _e('Pages') ?></span>
                </a>
            </li>
            <li data-page="faq_entries">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/faq_entries.php">
                    <i class="icon-feather-file"></i><span><?php _e('FAQ') ?></span>
                </a>
            </li>
            <li data-page="users">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/users.php">
                    <i class="icon-feather-user-check"></i><span><?php _e('Users') ?></span>
                </a>
            </li>
            <li data-page="admins">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/admins.php">
                    <i class="icon-feather-users"></i><span><?php _e('Admins') ?></span>
                </a>
            </li>
            <li data-page="subscriber">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/subscriber.php">
                    <i class="icon-feather-mail"></i><span><?php _e('Subscribers') ?></span>
                </a>
            </li>
            <li data-page="api-keys">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>app/api-keys.php">
                    <i class="icon-feather-hash"></i><span><?php _e('API Keys') ?></span>
                </a>
            </li>
            <li data-page="settings">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/settings.php">
                    <i class="icon-feather-settings"></i><span><?php _e('Settings') ?></span>
                </a>
                <ul class="iconbar-mainmenu custom-scrollbar nav">
                    <li class="iconbar-header"><?php _e('Settings') ?></li>
                    <li class="active">
                        <a href="#quick_settings_general" data-toggle="tab" class="active"><?php _e('General') ?></a>
                    </li>
                    <li>
                        <a href="#quick_logo_watermark" data-toggle="tab"><?php _e('Logo') ?></a>
                    </li>
                    <li>
                        <a href="#quick_map" data-toggle="tab"><?php _e('Map') ?></a>
                    </li>
                    <li>
                        <a href="#quick_international" data-toggle="tab"><?php _e('International') ?></a>
                    </li>
                    <li>
                        <a href="#quick_email" data-toggle="tab"><?php _e('Email Setting') ?></a>
                    </li>
                    <li>
                        <a href="#quick_theme_setting" data-toggle="tab"><?php _e('Theme Setting') ?></a>
                    </li>
                    <li>
                        <a href="#quick_ai_setting" data-toggle="tab"><?php _e('AI Settings') ?></a>
                    </li>
                    <li>
                        <a href="#quick_affiliate_settings" data-toggle="tab"><?php _e('Affiliate Program') ?></a>
                    </li>
                    <li>
                        <a href="#quick_live_chat" data-toggle="tab"><?php _e('Live Chat') ?></a>
                    </li>
                    <li>
                        <a href="#quick_billing_details" data-toggle="tab"><?php _e('Billing Details') ?></a>
                    </li>
                    <li>
                        <a href="#quick_social_login_setting" data-toggle="tab"><?php _e('Social Login Setting') ?></a>
                    </li>
                    <li>
                        <a href="#quick_recaptcha" data-toggle="tab"><?php _e('Google reCAPTCHA') ?></a>
                    </li>
                    <li>
                        <a href="#quick_blog" data-toggle="tab"><?php _e('Blog Setting') ?></a>
                    </li>
                    <li>
                        <a href="#quick_testimonials" data-toggle="tab"><?php _e('Testimonials Setting') ?></a>
                    </li>
                    <li>
                        <a href="#quick_purchase_code" data-toggle="tab"><?php _e('Purchase Code') ?></a>
                    </li>
                </ul>
            </li>
            <li data-page="themes">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/themes.php">
                    <i class="icon-feather-monitor"></i><span><?php _e('Change Theme') ?></span>
                </a>
            </li>
            <li data-page="update">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>global/update.php">
                    <i class="icon-feather-zap"></i><span><?php _e('Update') ?></span>
                </a>
            </li>
            <li data-page="logout">
                <a class="bar-icons" href="<?php echo ADMINURL; ?>logout.php">
                    <i class="icon-feather-log-out"></i><span><?php _e('Logout') ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>
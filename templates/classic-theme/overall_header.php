<!DOCTYPE html>
<html lang="<?php _esc($config['lang_code']); ?>" dir="<?php _esc($lang_direction); ?>">
<head>
    <title><?php _esc($page_title); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="author" content="<?php _esc($config['site_title']); ?>">
    <meta name="keywords" content="<?php _esc($config['meta_keywords']); ?>">
    <meta name="description"
          content="<?php ($meta_desc == '') ? _esc($config['meta_description']) : _esc($meta_desc); ?>">

    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//google.com">
    <link rel="dns-prefetch" href="//apis.google.com">
    <link rel="dns-prefetch" href="//ajax.googleapis.com">
    <link rel="dns-prefetch" href="//www.google-analytics.com">
    <link rel="dns-prefetch" href="//pagead2.googlesyndication.com">
    <link rel="dns-prefetch" href="//gstatic.com">
    <link rel="dns-prefetch" href="//oss.maxcdn.com">

    <meta property="fb:app_id" content="<?php _esc($config['facebook_app_id']); ?>"/>
    <meta property="og:site_name" content="<?php _esc($config['site_title']); ?>"/>
    <meta property="og:locale" content="en_US"/>
    <meta property="og:url" content="<?php _esc($page_link); ?>"/>
    <meta property="og:title" content="<?php _esc($page_title); ?>"/>
    <meta property="og:description" content="<?php _esc($meta_desc); ?>"/>
    <meta property="og:type" content="<?php _esc($meta_content); ?>"/>
    <?php if ($meta_content == 'article') { ?>
        <meta property="article:author" content="#"/>
        <meta property="article:publisher" content="#"/>
        <meta property="og:image" content="<?php _esc($meta_image); ?>"/>
        <?php
    }
    if ($meta_content == 'website') {
        echo '<meta property="og:image" content="' . $meta_image . '"/>';
    }
    ?>

    <meta property="twitter:card" content="summary">
    <meta property="twitter:title" content="<?php _esc($page_title); ?>">
    <meta property="twitter:description" content="<?php _esc($meta_desc); ?>">
    <meta property="twitter:domain" content="<?php _esc($config['site_url']); ?>">
    <meta name="twitter:image:src" content="<?php _esc($meta_image); ?>"/>
    <link rel="shortcut icon"
          href="<?php _esc($config['site_url']); ?>storage/logo/<?php _esc($config['site_favicon']); ?>">

    <script async>
        var themecolor = '<?php _esc($config['theme_color']);?>';
        var mapcolor = '<?php _esc($config['map_color']);?>';
        var siteurl = '<?php _esc($config['site_url']);?>';
        var template_name = '<?php _esc($config['tpl_name']);?>';
        var ajaxurl = "<?php _esc(str_replace(['https:', 'http:'], ['', ''], $config['app_url']));?>user-ajax.php";
    </script>

    <meta name="theme-color" content="<?php _esc($config['theme_color']);?>">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="<?php _esc($config['pwa_app_name']);?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="manifest" href="<?php _esc($config['site_url']); ?>manifest.json">
    <script src="<?php _esc($config['site_url']); ?>includes/assets/js/serviceWorkerRegister.js"></script>
    <?php
    if (!empty($config['quickad_user_secret_file'])) {
        ?>
        <script>
            var ajaxurl = '<?php _esc(str_replace(['https:', 'http:'], ['', ''], $config['app_url']) . $config['quickad_user_secret_file'] . '.php'); ?>';
        </script>
        <?php
    }
    ?>

    <!--Loop for Theme Color codes-->
    <style>
        :root {
        <?php
        $themecolor = $config['theme_color'];
        $colors = array();
        list($r, $g, $b) = sscanf($themecolor, "#%02x%02x%02x");
        $i = 0.01;
        while($i <= 1){
            echo "--theme-color-".str_replace('.','_',$i).": rgba($r,$g,$b,$i);";
            $i += 0.01;
        }
        echo "--theme-color-1: rgba($r,$g,$b,1);";
        ?>
        }
    </style>
    <!--Loop for Theme Color codes-->

    <link rel="stylesheet" href="<?php _esc($config['site_url']); ?>includes/assets/css/icons.css">
    <?php if ($lang_direction == 'rtl') {
        echo '<link rel="stylesheet" href="' . TEMPLATE_URL . '/css/rtl.css?ver=' . $config['version'] . '">';
    } else {
        echo '<link rel="stylesheet" href="' . TEMPLATE_URL . '/css/style.css?ver=' . $config['version'] . '">';
    } ?>
    <link rel="stylesheet" href="<?php _esc(TEMPLATE_URL); ?>/css/color.css?ver=<?php _esc($config['version']); ?>">
    <?php if (isset($_COOKIE['quickapp'])) { ?>
        <link rel="stylesheet" href="<?php _esc(TEMPLATE_URL); ?>/css/app.css?ver=<?php _esc($config['version']); ?>">
    <?php } ?>
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/jquery.min.js"></script>

    <?php if (CURRENT_PAGE == 'app/home') { ?>
        <link rel="stylesheet" href="<?php _esc(TEMPLATE_URL); ?>/css/landing/bootstrap.min.css"/>
        <link rel="stylesheet" href="<?php _esc(TEMPLATE_URL); ?>/css/landing/animate.css"/>
        <link rel="stylesheet" href="<?php _esc(TEMPLATE_URL); ?>/css/landing/lineicons.css"/>
        <link rel="stylesheet" href="<?php _esc(TEMPLATE_URL); ?>/css/landing/styles.css?ver=<?php _esc($config['version']); ?>"/>
    <?php } ?>

    <?php print_live_chat_code() ?>

    <!-- ===External Code=== -->
    <?php _esc($config['external_code']); ?>
    <!-- ===/External Code=== -->
</head>
<body data-role="page" class="<?php _esc($lang_direction); ?>" id="page">
<!--[if lt IE 8]>
<p class="browserupgrade">
    You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.
</p>
<![endif]-->
<?php
global $current_user;
$plan_settings = $current_user['plan']['settings']; ?>

<?php if (CURRENT_PAGE == 'app/home'){ ?>
<div>
    <!-- ====== Header Start ====== -->
    <header class="ud-header">
        <?php print_adsense_code('header_top'); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg">
                        <a class="navbar-brand" href="<?php url("INDEX") ?>">
                            <?php
                            $logo_dark = $config['site_url'] . 'storage/logo/' . $config['site_logo'];
                            $logo_white = $config['site_url'] . 'storage/logo/' . $config['site_logo_footer'];
                            ?>
                            <img src="<?php _esc($logo_white) ?>" data-logo="<?php _esc($logo_white); ?>"
                                 data-stickylogo="<?php _esc($logo_dark); ?>"
                                 alt="<?php _esc($config['site_title']); ?>"/>
                        </a>
                        <div class="navbar-collapse">
                            <ul id="nav" class="navbar-nav mx-auto">
                                <li class="nav-item">
                                    <a class="ud-menu-scroll" href="#home"><?php _e("Home") ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="ud-menu-scroll" href="#features"><?php _e("Features") ?></a>
                                </li>
                                <?php if ($config['show_membershipplan_home']) { ?>
                                    <li class="nav-item">
                                        <a class="ud-menu-scroll" href="#pricing"><?php _e("Pricing") ?></a>
                                    </li>
                                <?php }
                                if (get_option("enable_faqs", '1')) {
                                    ?>
                                    <li class="nav-item">
                                        <a class="ud-menu-scroll" href="#faq"><?php _e("FAQs") ?></a>
                                    </li>
                                    <?php
                                }
                                if ($config['blog_enable'] && $config['show_blog_home']) { ?>
                                    <li class="nav-item">
                                        <a class="ud-menu-scroll" href="#blog"><?php _e("Blog") ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>

                        <div class="navbar-btn d-flex align-items-center">
                            <?php
                            if ($is_login) {
                                ?>
                                <div class="header-notifications user-menu">
                                    <div class="header-notifications-trigger">
                                        <a href="#" title="<?php _esc($username); ?>">
                                            <div class="user-avatar status-online"><img
                                                        src="<?php _esc($config['site_url']); ?>storage/profile/<?php _esc($userpic) ?>"
                                                        alt="<?php _esc($username); ?>"></div>
                                        </a>
                                    </div>
                                    <!-- Dropdown -->
                                    <div class="header-notifications-dropdown">
                                        <ul class="user-menu-small-nav">
                                            <li><a href="<?php url("DASHBOARD") ?>"><i
                                                            class="icon-feather-grid"></i> <?php _e("Dashboard") ?></a>
                                            </li>
                                            <?php if (get_option("enable_ai_templates", 1)) { ?>
                                                <li><a href="<?php url("AI_TEMPLATES") ?>"><i
                                                                class="icon-feather-layers"></i> <?php _e("Templates") ?>
                                                    </a>
                                                </li>
                                            <?php }
                                            if ($config['enable_ai_images']) {
                                                if (!get_option('hide_plan_disabled_features') || (get_option('hide_plan_disabled_features') && $plan_settings['ai_images_limit'])) { ?>
                                                    <li><a href="<?php url("AI_IMAGES") ?>"><i
                                                                    class="icon-feather-image"></i> <?php _e("AI Images") ?>
                                                        </a>
                                                    </li>
                                                <?php }
                                            }
                                            if ($config['enable_ai_chat']) {
                                                if (!get_option('hide_plan_disabled_features') || (get_option('hide_plan_disabled_features') && $plan_settings['ai_chat'])) { ?>
                                                    <li><a href="<?php url("AI_CHAT_BOTS") ?>"><i
                                                                    class="icon-feather-message-circle"></i> <?php _e("AI Chat") ?>
                                                        </a></li>
                                                <?php }
                                            }
                                            if ($config['enable_speech_to_text']) {
                                                if (!get_option('hide_plan_disabled_features') || (get_option('hide_plan_disabled_features') && $plan_settings['ai_speech_to_text_limit'])) { ?>
                                                    <li><a href="<?php url("AI_SPEECH_TEXT") ?>"><i
                                                                    class="icon-feather-headphones"></i> <?php _e("Speech to Text") ?>
                                                        </a></li>
                                                <?php }
                                            }
                                            if (get_option('enable_text_to_speech', 0)) {
                                                if (!get_option('hide_plan_disabled_features') || (get_option('hide_plan_disabled_features') && $plan_settings['ai_text_to_speech_limit'])) { ?>
                                                    <li><a href="<?php url("AI_TEXT_SPEECH") ?>"><i
                                                                    class="icon-feather-volume-2"></i> <?php _e("Text to Speech") ?>
                                                        </a></li>
                                                <?php }
                                            }
                                            if ($config['enable_ai_code']) {
                                                if (!get_option('hide_plan_disabled_features') || (get_option('hide_plan_disabled_features') && $plan_settings['ai_code'])) { ?>
                                                    <li><a href="<?php url("AI_CODE") ?>"><i
                                                                    class="icon-feather-code"></i> <?php _e("AI Code") ?>
                                                        </a>
                                                    </li>
                                                <?php }
                                            } ?>
                                            <li><a href="<?php url("ALL_DOCUMENTS") ?>"><i
                                                            class="icon-feather-file-text"></i> <?php _e("All Documents") ?>
                                                </a></li>
                                            <li><a href="<?php url("MEMBERSHIP") ?>"><i
                                                            class="icon-feather-gift"></i> <?php _e("Membership") ?></a>
                                            </li>
                                            <li><a href="<?php url("ACCOUNT_SETTING") ?>"><i
                                                            class="icon-feather-settings"></i> <?php _e("Account Setting") ?>
                                                </a></li>
                                            <?php
                                            foreach ($html_pages as $html_page) { ?>
                                                <li>
                                                    <a href="<?php _esc($config['site_url'] . 'page/' . $html_page['slug']) ?>"><i
                                                                class="icon-feather-file-text"></i> <?php _esc($html_page['title']) ?>
                                                    </a></li>
                                            <?php } ?>
                                            <li><a href="<?php url("LOGOUT") ?>"><i
                                                            class="icon-feather-log-out"></i> <?php _e("Logout") ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <a href="#sign-in-dialog"
                                   class="popup-with-zoom-anim ud-main-btn ud-white-btn ripple-effect">
                                    <span><?php _e("Join Now") ?>&nbsp;</span><i class="icon-feather-log-in"></i>
                                </a>
                            <?php } ?>
                            <?php if ($config['userlangsel']) { ?>
                                <div class="btn-group bootstrap-select language-switcher">
                                    <button type="button" class="btn dropdown-toggle btn-default"
                                            data-toggle="dropdown">
                                    <span class="filter-option pull-left"
                                          id="selected_lang"><?php _esc($config['lang_code']); ?></span>&nbsp;
                                        <span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu scrollable-menu open">
                                        <ul class="dropdown-menu inner">
                                            <?php
                                            foreach ($languages as $lang) {
                                                echo '<li  data-lang="' . $lang['file_name'] . '">
                                                    <a role="menuitem" tabindex="-1" rel="alternate" href="' . url("HOME", false) . '/' . $lang['code'] . '">' . $lang['name'] . '</a>
                                                  </li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- ====== Header End ====== -->
    <?php } else { ?>
    <!-- Wrapper -->
    <div id="wrapper" class="">
        <!-- Header Container
        ================================================== -->
        <header id="header-container" class="fullwidth">
            <?php print_adsense_code('header_top'); ?>

            <?php
            if ($config['non_active_msg'] == 1 && $userstatus == 0) { ?>
                <div class="user-status-message">
                    <div class="container container-active-msg">
                        <div class="row">
                            <div class="col-lg-8">
                                <i class="icon-lock text-18"></i>
                                <span><?php _e('Your email address is not verified. Please verify your email address to use all the features.'); ?></span>
                            </div>
                            <div class="col-lg-4">
                                <a class="button ripple-effect gray resend_buttons<?php _esc($user_id) ?> resend"
                                   href='javascript:void(0);'
                                   id="<?php _esc($user_id) ?>"><?php _e('Resend Email'); ?></a>
                                <span class='resend_count' id='resend_count<?php _esc($user_id) ?>'></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <!-- Header -->
            <div id="header">
                <div class="container">
                    <!-- Left Side Content -->
                    <div class="left-side">
                        <!-- Logo -->
                        <div id="logo">
                            <a href="<?php url("INDEX") ?>">
                                <?php
                                $logo_dark = $config['site_url'] . 'storage/logo/' . $config['site_logo'];
                                $logo_white = $config['site_url'] . 'storage/logo/' . $config['site_logo_footer'];
                                ?>
                                <img src="<?php _esc($logo_dark); ?>" data-sticky-logo="<?php _esc($logo_dark); ?>"
                                     data-transparent-logo="<?php _esc($logo_white); ?>"
                                     alt="<?php _esc($config['site_title']); ?>">
                            </a>
                        </div>

                        <a href="javascript:void(0);" class="header-icon d-none">
                            <i class="fa fa-bars"></i>
                        </a>
                        <a href="javascript:void(0);" class="header-icon toggleFullScreen d-none">
                            <i class="icon-feather-maximize"></i>
                        </a>

                    </div>
                    <!-- Left Side Content / End -->


                    <!-- Right Side Content / End -->
                    <div class="right-side">
                        <?php
                        if ($is_login) {
                            ?>

                            <!-- User Menu -->
                            <div class="header-widget">

                                <!-- Messages -->
                                <div class="header-notifications user-menu">
                                    <div class="header-notifications-trigger">
                                        <a href="#" title="<?php _esc($username); ?>">
                                            <div class="user-avatar status-online"><img
                                                        src="<?php _esc($config['site_url']); ?>storage/profile/<?php _esc($userpic) ?>"
                                                        alt="<?php _esc($username); ?>"></div>
                                        </a>
                                    </div>
                                    <!-- Dropdown -->
                                    <div class="header-notifications-dropdown">
                                        <ul class="user-menu-small-nav">
                                            <li><a href="<?php url("DASHBOARD") ?>"><i
                                                            class="icon-feather-grid"></i> <?php _e("Dashboard") ?></a>
                                            </li>
                                            <?php if (get_option("enable_ai_templates", 1)) { ?>
                                                <li><a href="<?php url("AI_TEMPLATES") ?>"><i
                                                                class="icon-feather-layers"></i> <?php _e("Templates") ?>
                                                    </a>
                                                </li>
                                            <?php }
                                            if ($config['enable_ai_images']) {
                                                if (!get_option('hide_plan_disabled_features') || (get_option('hide_plan_disabled_features') && $plan_settings['ai_images_limit'])) { ?>
                                                    <li><a href="<?php url("AI_IMAGES") ?>"><i
                                                                    class="icon-feather-image"></i> <?php _e("AI Images") ?>
                                                        </a>
                                                    </li>
                                                <?php }
                                            }
                                            if ($config['enable_ai_chat']) {
                                                if (!get_option('hide_plan_disabled_features') || (get_option('hide_plan_disabled_features') && $plan_settings['ai_chat'])) { ?>
                                                    <li><a href="<?php url("AI_CHAT_BOTS") ?>"><i
                                                                    class="icon-feather-message-circle"></i> <?php _e("AI Chat") ?>
                                                        </a></li>
                                                <?php }
                                            }
                                            if ($config['enable_speech_to_text']) {
                                                if (!get_option('hide_plan_disabled_features') || (get_option('hide_plan_disabled_features') && $plan_settings['ai_speech_to_text_limit'])) { ?>
                                                    <li><a href="<?php url("AI_SPEECH_TEXT") ?>"><i
                                                                    class="icon-feather-headphones"></i> <?php _e("Speech to Text") ?>
                                                        </a></li>
                                                <?php }
                                            }
                                            if (get_option('enable_text_to_speech', 0)) {
                                                if (!get_option('hide_plan_disabled_features') || (get_option('hide_plan_disabled_features') && $plan_settings['ai_text_to_speech_limit'])) { ?>
                                                    <li><a href="<?php url("AI_TEXT_SPEECH") ?>"><i
                                                                    class="icon-feather-volume-2"></i> <?php _e("Text to Speech") ?>
                                                        </a></li>
                                                <?php }
                                            }
                                            if ($config['enable_ai_code']) {
                                                if (!get_option('hide_plan_disabled_features') || (get_option('hide_plan_disabled_features') && $plan_settings['ai_code'])) { ?>
                                                    <li><a href="<?php url("AI_CODE") ?>"><i
                                                                    class="icon-feather-code"></i> <?php _e("AI Code") ?>
                                                        </a>
                                                    </li>
                                                <?php }
                                            } ?>
                                            <li><a href="<?php url("ALL_DOCUMENTS") ?>"><i
                                                            class="icon-feather-file-text"></i> <?php _e("All Documents") ?>
                                                </a></li>
                                            <li><a href="<?php url("MEMBERSHIP") ?>"><i
                                                            class="icon-feather-gift"></i> <?php _e("Membership") ?></a>
                                            </li>
                                            <li><a href="<?php url("ACCOUNT_SETTING") ?>"><i
                                                            class="icon-feather-settings"></i> <?php _e("Account Setting") ?>
                                                </a></li>
                                            <?php
                                            foreach ($html_pages as $html_page) { ?>
                                                <li>
                                                    <a href="<?php _esc($config['site_url'] . 'page/' . $html_page['slug']) ?>"><i
                                                                class="icon-feather-file-text"></i> <?php _esc($html_page['title']) ?>
                                                    </a></li>
                                            <?php } ?>
                                            <li><a href="<?php url("LOGOUT") ?>"><i
                                                            class="icon-feather-log-out"></i> <?php _e("Logout") ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                            <!-- User Menu / End -->
                        <?php } else { ?>
                            <div class="header-widget">
                                <a href="#sign-in-dialog"
                                   class="popup-with-zoom-anim button ripple-effect">
                                    <span><?php _e("Join Now") ?>&nbsp;</span><i class="icon-feather-log-in"></i>
                                </a>
                            </div>
                        <?php } ?>

                        <?php if ($config['userlangsel']) { ?>
                            <div class="header-widget">
                                <div class="btn-group bootstrap-select language-switcher">
                                    <button type="button" class="btn dropdown-toggle btn-default"
                                            data-toggle="dropdown">
                                    <span class="filter-option pull-left"
                                          id="selected_lang"><?php _esc($config['lang_code']); ?></span>&nbsp;
                                        <span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu scrollable-menu open">
                                        <ul class="dropdown-menu inner">
                                            <?php
                                            foreach ($languages as $lang) {
                                                echo '<li  data-lang="' . $lang['file_name'] . '">
                                                    <a role="menuitem" tabindex="-1" rel="alternate" href="' . url("HOME", false) . '/' . $lang['code'] . '">' . $lang['name'] . '</a>
                                                  </li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <!-- Right Side Content / End -->

                </div>
            </div>
            <!-- Header / End -->
        </header>
        <div class="clearfix"></div>
        <!-- Header Container / End -->
<?php } ?>

<?php overall_header(); ?>
    <!-- ====== Hero Start ====== -->
    <section class="ud-hero" id="home">
        <div class="anim-elements">
            <div class="anim-element"></div>
            <div class="anim-element"></div>
            <div class="anim-element"></div>
            <div class="anim-element"></div>
            <div class="anim-element"></div>
        </div>
        <div class="container position-relative">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ud-hero-content wow fadeInUp" data-wow-delay=".2s">
                        <img width="180" class="mb-4"
                             src="<?php _esc(TEMPLATE_URL . '/images/home-hero-icon.png'); ?>"
                             alt="<?php _e("Best AI Content Writer") ?>">
                        <h1 class="ud-hero-title">
                            <?php _e("Best AI Content Writer") ?>
                        </h1>
                        <p class="ud-hero-desc">
                            <?php _e("Create SEO-optimized and unique content for your blogs, ads, emails, and website 10X faster & save hours of work.") ?>
                        </p>
                        <ul class="ud-hero-buttons">
                            <li>
                                <a href="<?php url("SIGNUP") ?>" class="ud-main-btn ud-white-btn">
                                    <?php _e("Get Started For Free") ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div
                            class="ud-hero-brands-wrapper wow fadeInUp"
                            data-wow-delay=".3s"
                    >
                        <?php _e("No credit card required.") ?>
                    </div>
                    <div class="ud-hero-image wow fadeInUp" data-wow-delay=".3s">
                        <img class="ud-hero-image-main" src="<?php _esc(TEMPLATE_URL); ?>/images/hero-image.png"
                             alt="hero-image"/>
                        <img
                                src="<?php _esc(TEMPLATE_URL); ?>/images/dotted-shape.svg"
                                alt="shape"
                                class="shape shape-1"
                        />
                        <img
                                src="<?php _esc(TEMPLATE_URL); ?>/images/dotted-shape.svg"
                                alt="shape"
                                class="shape shape-2"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ====== Hero End ====== -->
<?php print_adsense_code('header_bottom'); ?>
    <!-- ====== Features Start ====== -->
    <section id="features" class="ud-features">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ud-section-title wow fadeInUp" data-wow-delay=".1s">
                        <span><?php _e("How?") ?></span>
                        <h2><?php _e("How does it work?") ?></h2>
                        <p>
                            <?php _e("You need to follow a few simple steps to generate your content. Use the AI and save your time.") ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="ud-single-feature wow fadeInUp" data-wow-delay=".1s">
                        <div class="ud-feature-icon">
                            1
                        </div>
                        <div class="ud-feature-content">
                            <h3 class="ud-feature-title"><?php _e("Select a template") ?></h3>
                            <p class="ud-feature-desc">
                                <?php _e("Choose a content creation template. Multiple templates are available for your all needs.") ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ud-single-feature wow fadeInUp" data-wow-delay=".15s">
                        <div class="ud-feature-icon">
                            2
                        </div>
                        <div class="ud-feature-content">
                            <h3 class="ud-feature-title"><?php _e("Fill the form") ?></h3>
                            <p class="ud-feature-desc">
                                <?php _e("Enter a detailed description of your content. Tell the AI what do you want.") ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ud-single-feature wow fadeInUp" data-wow-delay=".2s">
                        <div class="ud-feature-icon">
                            3
                        </div>
                        <div class="ud-feature-content">
                            <h3 class="ud-feature-title"><?php _e("Get your content") ?></h3>
                            <p class="ud-feature-desc">
                                <?php _e("Get a unique high quality content. The content is plagiarism free and you can use it anywhere.") ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php print_adsense_code('home_page_1');

if (get_option("enable_ai_templates", 1)) { ?>
    <section class="ud-templates">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ud-section-title wow fadeInUp" data-wow-delay=".1s">
                        <span><?php _e("Templates") ?></span>
                        <h2><?php _e("AI Content Writer") ?></h2>
                        <p>
                            <?php _e("Generate your required content with over 60+ content creation templates.") ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="template-categories home-templates wow fadeInUp" data-wow-delay=".15s">
                <ul>
                    <li class="active"><a href="javascript:void();" class="ai-templates-category"
                                          data-category="all"><?php _e("All templates") ?></a></li>
                    <?php
                    foreach ($ai_templates as $category) {
                    if (!empty($category['templates'])) {
                        $translations = json_decode((string) $category['translations'], true);
                        ?>
                        <li>
                            <a href="javascript:void();" class="ai-templates-category"
                               data-category="<?php _esc($category['id']) ?>">
                                <?php echo !empty($translations[$config['lang_code']]['title'])
                                    ? $translations[$config['lang_code']]['title']
                                    : $category['title']; ?>
                            </a>
                        </li>
                    <?php }
                    }
                    ?>
                </ul>
            </div>
            <div class="ai-template-blocks-toggle show-blocks-toggle wow fadeInUp" data-wow-delay=".2s">
                <div class="row ai-template-blocks">
                    <?php
                    foreach ($ai_templates as $category) {
                    if (!empty($category['templates'])) {
                        $translations = json_decode((string) $category['translations'], true);
                        ?>
                        <div class="col-md-12 ai-templates-category-title">
                            <h4>
                                <?php echo !empty($translations[$config['lang_code']]['title'])
                                    ? $translations[$config['lang_code']]['title']
                                    : $category['title']; ?>
                            </h4>
                        </div>
                        <?php
                        foreach ($category['templates'] as $template) {
                            $translations = json_decode((string) $template['translations'], true);
                            ?>
                            <div class="col-md-4 col-sm-6 category-<?php _esc($category['id']) ?>">
                                <a href="<?php echo url('AI_TEMPLATES', false) . '/' . $template['slug'] ?>"
                                    <?php if (!is_null($plan_templates) && !in_array($template['slug'], $plan_templates)) { ?>
                                        title="<?php _e("Not available in the free plan") ?>" data-tippy-placement="top"
                                    <?php } ?>>
                                    <div class="dashboard-box ai-templates <?php echo (!is_null($plan_templates) && !in_array($template['slug'], $plan_templates)) ? 'ai-templates-pro' : ''; ?>">
                                        <div class="content">
                                            <div class="ai-templates-icon">
                                                <i class="<?php _esc($template['icon']) ?>"></i>
                                            </div>
                                            <h4>
                                                <?php echo !empty($translations[$config['lang_code']]['title'])
                                                    ? $translations[$config['lang_code']]['title']
                                                    : $template['title']; ?>
                                                <?php if (!is_null($plan_templates) && !in_array($template['slug'], $plan_templates)) { ?>
                                                    <span class="dashboard-status-button yellow"><i
                                                                class="fa fa-gift"></i> <?php _e("Pro") ?></span>
                                                <?php } ?>
                                            </h4>
                                            <p class="margin-bottom-0">
                                                <?php echo !empty($translations[$config['lang_code']]['description'])
                                                    ? $translations[$config['lang_code']]['description']
                                                    : $template['description']; ?>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php }
                    }
                    }
                    ?>
                </div>
                <div class="ai-template-blocks-toggle-button">
                    <a href="#">
                        <span><?php _e('Show More') ?> <i class="lni lni-chevron-down-circle"></i></span>
                        <span><?php _e('Show Less') ?> <i class="lni lni-chevron-up-circle"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php
}

print_adsense_code('home_page_2'); ?>
<?php if (get_option("enable_ai_images") && get_option('show_ai_images_home')) {
    if (!empty($ai_images)) {
        ?>
        <section class="ud-images">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ud-section-title wow fadeInUp" data-wow-delay=".1s">
                            <span><?php _e("Images") ?></span>
                            <h2><?php _e("AI Image Generator") ?></h2>
                            <p>
                                <?php _e("Here's our latest generated AI images.") ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row image-lightbox wow fadeInUp" data-wow-delay=".15s">
                    <?php foreach ($ai_images as $ai_image) { ?>
                        <div class="col-sm-4 col-md-2 col-6">
                            <div class="margin-bottom-30">
                                <a href="<?php echo _esc($config['site_url'], 0) . 'storage/ai_images/' . $ai_image['image']; ?>"
                                   target="_blank" class="ai-lightbox-image">
                                    <img
                                            src="<?php echo _esc($config['site_url'], 0) . 'storage/ai_images/small_' . $ai_image['image']; ?>"
                                            alt="<?php _esc($ai_image['description']) ?>" data-tippy-placement="top"
                                            title="<?php _esc($ai_image['description']) ?>">
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    <?php }
} ?>
<?php print_adsense_code('home_page_3'); ?>
<?php if (get_option("enable_ai_chat")) {
    ?>
    <section class="ud-about">
        <div class="container">
            <div class="ud-about-wrapper wow fadeInUp" data-wow-delay=".2s">
                <div class="ud-about-content-wrapper">
                    <div class="ud-about-content">
                        <span class="tag"><?php _e("ChatBots") ?></span>
                        <h2><?php _e("AI Chat Assistants") ?></h2>
                        <p><?php _e("AI ChatBots use artificial intelligence to understand and respond to your questions and conversations. Chatbots are really helpful because they can give you instant and personalized help.") ?></p>
                        <p><?php _e("We offer a diverse range of specialized chatbots across various industries. Eg. Relationship Advisor, Bussiness Coach, Motivational Speaker, Life Coach, Lawyer, Doctor etc.") ?></p>
                        <a href="<?php url("AI_CHAT_BOTS") ?>" class="ud-main-btn"><?php _e("Chat Now") ?></a>
                    </div>
                </div>
                <div class="ud-about-image">
                    <img src="<?php _esc(TEMPLATE_URL); ?>/images/chat-image.png" alt="about-image"/>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
    <!-- ====== Features End ====== -->
<?php if ($config['show_membershipplan_home']) { ?>
    <!-- ====== Pricing Start ====== -->
    <section id="pricing" class="ud-pricing">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ud-section-title mx-auto text-center wow fadeInUp" data-wow-delay=".1s">
                        <span><?php _e("Pricing") ?></span>
                        <h2><?php _e("Our Pricing Plans") ?></h2>
                        <p><?php _e("We offer flexible pricing plans to suit the diverse needs of our clients.") ?></p>
                    </div>
                </div>
            </div>
            <div class="row g-0 align-items-center justify-content-center">
                <div class="col-xl-12">
                    <div class="tabs pricing-tabs">
                        <?php if (!empty($prepaid_plans)) { ?>
                            <div class="tabs-header">
                                <ul>
                                    <li class="active"><a href="#tab-1"
                                                          data-tab-id="1"><?php _e('Membership Plans'); ?></a></li>
                                    <li><a href="#tab-2" data-tab-id="2"><?php _e('Prepaid Plans'); ?></a></li>
                                </ul>
                                <div class="tab-hover"></div>
                            </div>
                        <?php } ?>
                        <!-- Tab Content -->
                        <div class="tabs-content">
                            <div class="tab active" data-tab-id="1">
                                <form name="form1" method="post" action="<?php url("MEMBERSHIP") ?>">
                                    <div class="billing-cycle-radios margin-bottom-70 wow fadeInUp"
                                         data-wow-delay=".15s">
                                        <?php if ($total_monthly) { ?>
                                            <div class="radio billed-monthly-radio">
                                                <input id="radio-monthly" name="billed-type" type="radio"
                                                       value="monthly"
                                                       checked="">
                                                <label for="radio-monthly"><span
                                                            class="radio-label"></span> <?php _e("Monthly") ?>
                                                </label>
                                            </div>
                                            <?php
                                        }
                                        if ($total_annual) {
                                            ?>
                                            <div class="radio billed-yearly-radio">
                                                <input id="radio-yearly" name="billed-type" type="radio" value="yearly">
                                                <label for="radio-yearly"><span
                                                            class="radio-label"></span> <?php _e("Yearly") ?>
                                                </label>
                                            </div>
                                            <?php
                                        }
                                        if ($total_lifetime) {
                                            ?>
                                            <div class="radio billed-lifetime-radio">
                                                <input id="radio-lifetime" name="billed-type" type="radio"
                                                       value="lifetime">
                                                <label for="radio-lifetime"><span
                                                            class="radio-label"></span> <?php _e("Lifetime") ?></label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <!-- Pricing Plans Container -->
                                    <div class="pricing-plans-container wow fadeInUp" data-wow-delay=".2s">
                                        <?php
                                        $x = 1;
                                        foreach ($sub_types as $plan) {
                                            ?>
                                            <!-- Plan -->
                                            <div class="pricing-plan <?= (isset($plan['recommended']) && $plan['recommended'] == "yes") ? 'recommended' : ''; ?>"
                                                 data-monthly-price="<?php _esc($plan['monthly_price_number']) ?>"
                                                 data-annual-price="<?php _esc($plan['annual_price_number']) ?>"
                                                 data-lifetime-price="<?php _esc($plan['lifetime_price_number']) ?>">
                                                <?php
                                                if (isset($plan['recommended']) && $plan['recommended'] == "yes") {
                                                    echo '<div class="recommended-badge">' . __("Recommended") . '</div> ';
                                                }
                                                ?>
                                                <h3><?php _esc($plan['title']) ?></h3>
                                                <?php
                                                if ($plan['id'] == "free" || $plan['id'] == "trial") {
                                                    ?>
                                                    <div class="pricing-plan-label"><strong>
                                                            <?php
                                                            if ($plan['id'] == "free")
                                                                _e("Free");
                                                            else
                                                                _e("Trial");
                                                            ?>
                                                        </strong></div>

                                                    <?php
                                                } else {
                                                    if ($total_monthly != 0)
                                                        echo '<div class="pricing-plan-label billed-monthly-label"><strong>' . _esc($plan['monthly_price'], false) . '</strong>/ ' . __("Monthly") . '</div>';
                                                    if ($total_annual != 0)
                                                        echo '<div class="pricing-plan-label billed-yearly-label"><strong>' . _esc($plan['annual_price'], false) . '</strong>/ ' . __("Yearly") . '</div>';
                                                    if ($total_lifetime != 0)
                                                        echo '<div class="pricing-plan-label billed-lifetime-label"><strong>' . _esc($plan['lifetime_price'], false) . '</strong>/ ' . __("Lifetime") . '</div>';
                                                }
                                                ?>

                                                <div class="pricing-plan-features">
                                                    <strong><?php _e("Features of") ?>
                                                        &nbsp;<?php _esc($plan['title']) ?></strong>
                                                    <ul>
                                                        <?php if (!get_option('single_model_for_plans')) { ?>
                                                            <li class="pricing-table-ai-models">
                                                                <strong>
                                                                    <?php
                                                                    $models = get_opeai_models();
                                                                    _esc($models[$plan['ai_model']])
                                                                    ?>
                                                                </strong>&nbsp;<br><em><small><?php _e("Open AI Model") ?></small></em>
                                                            </li>
                                                        <?php }
                                                        if (get_option("enable_ai_templates", 1)) {
                                                            ?>
                                                            <li>
                                                                <strong><?php _esc(count($plan['ai_templates'])) ?></strong> <?php _e("AI Document Templates") ?>
                                                                <i class="icon-feather-help-circle margin-left-2"
                                                                   data-tippy-placement="top"
                                                                   title="<?php echo escape(str_replace(',', ', ', $plan['ai_template_titles'])) ?>"></i>
                                                            </li>
                                                        <?php } ?>
                                                        <li>
                                                            <strong><?php _esc(is_string($plan['ai_words_limit']) ? $plan['ai_words_limit'] : number_format($plan['ai_words_limit'])) ?></strong> <?php _e("Words per month") ?>
                                                        </li>
                                                        <?php if ($config['enable_ai_images']) { ?>
                                                            <li>
                                                                <strong><?php _esc(is_string($plan['ai_images_limit']) ? $plan['ai_images_limit'] : number_format($plan['ai_images_limit'])) ?></strong> <?php _e("Images per month") ?>
                                                            </li>
                                                        <?php }

                                                        if ($config['enable_text_to_speech']) { ?>
                                                            <li>
                                                                <strong><?php _esc(is_string($plan['ai_text_to_speech_limit']) ? $plan['ai_text_to_speech_limit'] : number_format($plan['ai_text_to_speech_limit'])) ?></strong> <?php _e("Characters for Text to Speech per month") ?>
                                                            </li>
                                                        <?php }

                                                        if ($config['enable_speech_to_text']) { ?>
                                                            <li>
                                                                <strong><?php _esc(is_string($plan['ai_speech_to_text_limit']) ? $plan['ai_speech_to_text_limit'] : number_format($plan['ai_speech_to_text_limit'])) ?></strong> <?php _e("Speech to Text per month") ?>
                                                                <i class="icon-feather-help-circle margin-left-2"
                                                                   data-tippy-placement="top"
                                                                   title="<?php _e("Create audio transcription") ?>"></i>
                                                            </li>
                                                            <li>
                                                                <strong><?php _esc(is_string($plan['ai_speech_to_text_file_limit']) ? $plan['ai_speech_to_text_file_limit'] : number_format($plan['ai_speech_to_text_file_limit']) . ' MB') ?></strong> <?php _e("Audio file size limit") ?>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if ($config['enable_ai_chat']) { ?>
                                                            <li>
                                                                <?php if ($plan['ai_chat']) { ?>
                                                                    <span class="icon-text yes"><i
                                                                                class="icon-feather-check-circle margin-right-2"></i></span>
                                                                <?php } else { ?>
                                                                    <span class="icon-text no"><i
                                                                                class="icon-feather-x-circle margin-right-2"></i></span>
                                                                <?php } ?>
                                                                <?php _e("AI Chat") ?>
                                                                <i class="icon-feather-help-circle margin-left-2"
                                                                   data-tippy-placement="top"
                                                                   title="<?php _e("Chat with the AI bot") ?>"></i>
                                                            </li>
                                                            <li>
                                                                <strong><?php _esc(count($plan['ai_chatbots'])) ?></strong> <?php _e("AI Chat Bots") ?>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if ($config['enable_ai_code']) { ?>
                                                            <li>
                                                                <?php if ($plan['ai_code']) { ?>
                                                                    <span class="icon-text yes"><i
                                                                                class="icon-feather-check-circle margin-right-2"></i></span>
                                                                <?php } else { ?>
                                                                    <span class="icon-text no"><i
                                                                                class="icon-feather-x-circle margin-right-2"></i></span>
                                                                <?php } ?>
                                                                <?php _e("AI Code") ?>
                                                                <i class="icon-feather-help-circle margin-left-2"
                                                                   data-tippy-placement="top"
                                                                   title="<?php _e("Generate code of any programming language with the AI") ?>"></i>
                                                            </li>
                                                        <?php } ?>
                                                        <li>
                                                            <?php if (!$plan['show_ads']) { ?>
                                                                <span class="icon-text yes"><i
                                                                            class="icon-feather-check-circle margin-right-2"></i></span>
                                                            <?php } else { ?>
                                                                <span class="icon-text no"><i
                                                                            class="icon-feather-x-circle margin-right-2"></i></span>
                                                            <?php } ?>
                                                            <?php _e("Hide Ads") ?>
                                                        </li>
                                                        <?php if ($config['enable_live_chat'] && $config['tawkto_membership']) { ?>
                                                            <li>
                                                                <?php if ($plan['live_chat']) { ?>
                                                                    <span class="icon-text yes"><i
                                                                                class="icon-feather-check-circle margin-right-2"></i></span>
                                                                <?php } else { ?>
                                                                    <span class="icon-text no"><i
                                                                                class="icon-feather-x-circle margin-right-2"></i></span>
                                                                <?php } ?>
                                                                <?php _e("Live Chat Support") ?>
                                                            </li>
                                                        <?php } ?>
                                                        <?php _esc($plan['custom_settings']) ?>
                                                    </ul>
                                                </div>
                                                <?php
                                                if ($plan['Selected'] == 0) {
                                                    echo '<button type="submit" class="button full-width margin-top-20 ripple-effect" name="upgrade" value="' . _esc($plan['id'], false) . '">' . __("Choose Plan") . '</button>';
                                                }
                                                if ($plan['Selected'] == 1) {
                                                    echo '<a href="javascript:void(0);" class="button full-width margin-top-20 ripple-effect">' . __("Current Plan") . '</a>';
                                                }
                                                ?>
                                            </div>
                                            <?php
                                            if ($x++ % 3 === 0 && count($sub_types) != 4) {
                                                print "</div>\n<div class='pricing-plans-container margin-top-60'>\n";

                                            }
                                        } ?>
                                    </div>
                                </form>
                            </div>
                            <?php if (!empty($prepaid_plans)) { ?>
                                <div class="tab" data-tab-id="2">
                                    <form name="form1" method="post" action="<?php url("MEMBERSHIP") ?>">
                                        <!-- Pricing Plans Container -->
                                        <div class="pricing-plans-container margin-top-70">
                                            <?php
                                            $x = 1;
                                            foreach ($prepaid_plans as $plan) {
                                                ?>
                                                <!-- Plan -->
                                                <div class="pricing-plan <?= (isset($plan['recommended']) && $plan['recommended'] == "yes") ? 'recommended' : ''; ?>">
                                                    <?php
                                                    if (isset($plan['recommended']) && $plan['recommended'] == "yes") {
                                                        echo '<div class="recommended-badge">' . __("Recommended") . '</div> ';
                                                    }
                                                    ?>
                                                    <h3><?php _esc($plan['title']) ?></h3>
                                                    <div class="pricing-plan-label">
                                                        <strong><?php _esc($plan['price']) ?></strong>
                                                    </div>
                                                    <div class="pricing-plan-features">
                                                        <strong><?php _e("Features of") ?>
                                                            &nbsp;<?php _esc($plan['title']) ?></strong>
                                                        <ul>
                                                            <li>
                                                                <strong><?php _esc(number_format($plan['ai_words_limit'])) ?></strong> <?php _e("Words") ?>
                                                            </li>
                                                            <?php if ($config['enable_ai_images']) { ?>
                                                                <li>
                                                                    <strong><?php _esc(number_format($plan['ai_images_limit'])) ?></strong> <?php _e("Images") ?>
                                                                </li>
                                                            <?php }

                                                            if ($config['enable_text_to_speech']) { ?>
                                                                <li>
                                                                    <strong><?php _esc(number_format($plan['ai_text_to_speech_limit'])) ?></strong> <?php _e("Characters for Text to Speech") ?>
                                                                </li>
                                                            <?php }

                                                            if ($config['enable_speech_to_text']) { ?>
                                                                <li>
                                                                    <strong><?php _esc(number_format($plan['ai_speech_to_text_limit'])) ?></strong> <?php _e("Speech to Text") ?>
                                                                    <i class="icon-feather-help-circle margin-left-2"
                                                                       data-tippy-placement="top"
                                                                       title="<?php _e("Create audio transcription") ?>"></i>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                    <?php
                                                    echo '<button type="submit" class="button full-width margin-top-20 ripple-effect" name="buy-prepaid-plan" value="' . _esc($plan['id'], false) . '">' . __("Choose Plan") . '</button>';

                                                    ?>
                                                </div>
                                                <?php
                                                if ($x++ % 3 === 0  && count($prepaid_plans) != 4) {
                                                    print "</div>\n<div class='pricing-plans-container margin-top-60'>\n";

                                                }
                                            } ?>
                                        </div>
                                    </form>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- ====== Pricing End ====== -->
<?php } ?>
<?php print_adsense_code('home_page_4'); ?>
<?php if (get_option("enable_faqs", '1')) { ?>
    <!-- ====== FAQ Start ====== -->
    <section id="faq" class="ud-faq">
        <div class="shape">
            <img src="<?php _esc(TEMPLATE_URL); ?>/images/shape.svg" alt="shape"/>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ud-section-title text-center mx-auto wow fadeInUp" data-wow-delay=".1s">
                        <span><?php _e("FAQ") ?></span>
                        <h2><?php _e("Any Questions? Answered") ?></h2>
                        <p><?php _e("Here, we aim to provide you with answers to some of the most frequently asked questions about our product.") ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                $delay = 0.1;
                foreach ($faq as $f) { ?>
                    <div class="col-lg-6">
                        <div class="ud-single-faq wow fadeInUp" data-wow-delay="<?php _esc($delay) ?>s">
                            <div class="accordion">
                                <button class="ud-faq-btn collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?php _esc($f['id']) ?>">
                  <span class="icon flex-shrink-0">
                    <i class="lni lni-chevron-down"></i>
                  </span>
                                    <span><?php _esc($f['title']) ?></span>
                                </button>
                                <div id="collapse<?php _esc($f['id']) ?>" class="accordion-collapse collapse">
                                    <div class="ud-faq-body">
                                        <?php _esc($f['content']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- ====== FAQ End ====== -->
<?php } ?>
<?php print_adsense_code('home_page_5'); ?>
<?php if ($config['testimonials_enable'] && $config['show_testimonials_home']) { ?>
    <!-- ====== Testimonials Start ====== -->
    <section id="testimonials" class="ud-testimonials">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ud-section-title mx-auto text-center wow fadeInUp" data-wow-delay=".1s">
                        <span><?php _e("Testimonials") ?></span>
                        <h2><?php _e("What our Customers Says") ?></h2>
                        <p><?php _e("Thousands of marketers, agencies, and entrepreneurs choose QuickAI to automate and simplify their content marketing.") ?></p>
                    </div>
                </div>
            </div>

            <!-- Categories Carousel -->
            <div class="fullwidth-carousel-container margin-top-20">
                <div class="testimonial-carousel testimonials">
                    <!-- Item -->
                    <?php
                    foreach ($testimonials as $testimonial) {
                        ?>
                        <div class="fw-carousel-review">
                            <div class="testimonial-box">
                                <div class="testimonial-avatar">
                                    <img src="<?php _esc($config['site_url']); ?>storage/testimonials/<?php _esc($testimonial['image']) ?>"
                                         alt="<?php _esc($testimonial['name']) ?>">
                                </div>
                                <div class="testimonial-author">
                                    <h4><?php _esc($testimonial['name']) ?></h4>
                                    <span><?php _esc($testimonial['designation']) ?></span>
                                </div>
                                <div class="testimonial"><?php _esc($testimonial['content']) ?></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- Categories Carousel / End -->
        </div>
    </section>
    <!-- ====== Testimonials End ====== -->
<?php }

if ($config['show_partner_logo_home']) { ?>
    <!-- ====== Team Start ====== -->
    <section class="ud-partners">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <!-- Carousel -->
                    <div class="col-md-12">
                        <div class="logo-carousel wow fadeInUp" data-wow-delay=".1s">
                            <?php
                            $dir = ROOTPATH . '/storage/partner/';
                            $i = 0;
                            foreach (glob($dir . '*') as $path) {
                                ?>
                                <div class="logo-carousel-item">
                                    <img src="<?php _esc($config['site_url']); ?>storage/partner/<?php _esc(basename($path)) ?>">
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <!-- Carousel / End -->
                </div>
            </div>
        </div>
    </section>
    <!-- ====== Team End ====== -->
<?php }
print_adsense_code('home_page_6');
if ($config['blog_enable'] && $config['show_blog_home']) { ?>
    <section id="blog" class="ud-blog">
        <div class="container">
            <div class="section-headline margin-top-0 margin-bottom-45 wow fadeInUp" data-wow-delay=".1s">
                <h3><?php _e("Recent Blog") ?></h3>
                <a href="<?php url("BLOG") ?>" class="headline-link"><?php _e('View Blog') ?></a>
            </div>

            <div class="row">
                <!-- Blog Post Item -->
                <?php
                $delay = 0.1;
                foreach ($recent_blog as $blog) {
                    ?>
                    <div class="col-xl-4">
                        <a href="<?php _esc($blog['link']) ?>" class="blog-compact-item-container wow fadeInUp"
                           data-wow-delay="<?php _esc($delay) ?>s">
                            <div class="blog-compact-item">
                                <img src="<?php _esc($config['site_url']); ?>storage/blog/<?php _esc($blog['image']) ?>"
                                     alt="<?php _esc($blog['title']) ?>">
                                <span class="blog-item-tag"><?php _esc($blog['author']) ?></span>
                                <div class="blog-compact-item-content">
                                    <ul class="blog-post-tags">
                                        <li><?php _esc($blog['created_at']) ?></li>
                                    </ul>
                                    <h3><?php _esc($blog['title']) ?></h3>
                                    <p><?php _esc($blog['description']) ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                    $delay += 0.05;
                } ?>
                <!-- Blog post Item / End -->
            </div>
        </div>
    </section>
<?php }

if(get_option('show_newsletter_form_home')) { ?>
    <section class="footer-newsletter">
        <div class="shape">
            <img src="http://localhost/quickai/templates/classic-theme/images/shape.svg" alt="shape">
        </div>
        <div class="container">
            <div class="inner-content">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-5 col-12">
                        <div class="title">
                            <h3><?php _e('Subscribe to our newsletter'); ?></h3>
                            <p><?php _e('The latest news, articles, and resources, sent to your inbox weekly.'); ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-7 col-12">
                        <div class="form">
                            <form id="newsletter-form" action="#" method="post" class="newsletter-form">
                                <input name="email" class="with-border newsletter-email" placeholder="<?php _e('Your email address'); ?>" type="email" title="<?php _e('Your email address'); ?>" required>
                                <div class="button">
                                    <button class="btn" type="submit"><?php _e('Subscribe'); ?><span class="dir-part"></span></button>
                                </div>
                                <small id="newsletter-form-status"></small>
                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php } ?>

    <script src="<?php _esc(TEMPLATE_URL); ?>/js/landing/bootstrap.bundle.min.js"></script>
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/landing/wow.min.js"></script>
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/landing/main.js"></script>
    <link href="<?php _esc(TEMPLATE_URL); ?>/css/lightbox/lightgallery.min.css" rel="stylesheet">
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/lightgallery.min.js"></script>
    <script>
        // ==== for menu scroll
        const pageLink = document.querySelectorAll(".ud-menu-scroll");

        pageLink.forEach((elem) => {
            elem.addEventListener("click", (e) => {
                e.preventDefault();
                document.querySelector(elem.getAttribute("href")).scrollIntoView({
                    behavior: "smooth",
                    offsetTop: 1 - 60,
                });
            });
        });

        // section menu active
        function onScroll(event) {
            const sections = document.querySelectorAll(".ud-menu-scroll");
            const scrollPos =
                window.pageYOffset ||
                document.documentElement.scrollTop ||
                document.body.scrollTop;

            for (let i = 0; i < sections.length; i++) {
                const currLink = sections[i];
                const val = currLink.getAttribute("href");
                const refElement = document.querySelector(val);
                const scrollTopMinus = scrollPos + 73;
                if (
                    refElement.offsetTop <= scrollTopMinus &&
                    refElement.offsetTop + refElement.offsetHeight > scrollTopMinus
                ) {
                    document
                        .querySelector(".ud-menu-scroll")
                        .classList.remove("active");
                    currLink.classList.add("active");
                } else {
                    currLink.classList.remove("active");
                }
            }
        }

        window.document.addEventListener("scroll", onScroll);

        $(".image-lightbox").each(function () {
            lightGallery($(this).get(0), {
                selector: '.ai-lightbox-image',
                download: false,
            });
        });
    </script>
<?php overall_footer(); ?>

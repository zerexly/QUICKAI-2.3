<?php
overall_header();
global $config;
?>
<?php print_adsense_code('header_bottom'); ?>
    <div class="hero-section">
        <div class="container">

            <!-- Intro Headline -->
            <div class="row">
                <div class="col-md-12">
                    <div class="text-center hero-content">
                        <img class="lazy-load" width="180"
                             src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                             data-original="<?php _esc(TEMPLATE_URL . '/images/home-hero-icon.png'); ?>">

                        <h1 class="margin-bottom-10 text_gradient_animation">
                            <strong><?php _e("Best AI Content Writer"); ?></strong>
                        </h1>
                        <p><?php _e("Create SEO-optimized and unique content for your blogs, ads, emails, and website 10X faster & save hours of work."); ?></p>
                        <a href="<?php url('SIGNUP') ?>"
                           class="button ripple-effect button-sliding-icon"><?php _e("Get Started For Free"); ?><i
                                    class="icon-feather-arrow-right"></i></a>
                        <div class="margin-top-2 gray"><small><?php _e("No credit card required."); ?></small></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Content
    ================================================== -->
    <!-- Section How it Work Start-->
    <div class="section gray padding-top-65 padding-bottom-65">
        <div class="container">
            <div class="row">

                <div class="col-xl-12">
                    <!-- Section Headline -->
                    <div class="section-headline centered margin-top-0 margin-bottom-5">
                        <h3><?php _e("How It Works?"); ?></h3>
                    </div>
                </div>

                <div class="col-xl-4 col-md-4">
                    <!-- Icon Box -->
                    <div class="icon-box with-line">
                        <!-- Icon -->
                        <div class="icon-box-circle">
                            <div class="icon-box-circle-inner">
                                <i class="icon-feather-layers"></i>
                                <div class="icon-box-check"><i class="icon-material-outline-check"></i></div>
                            </div>
                        </div>
                        <h3><?php _e("Select a template"); ?></h3>
                        <p><?php _e("Choose a content creation template. Multiple templates are available for your all needs."); ?></p>
                    </div>
                </div>

                <div class="col-xl-4 col-md-4">
                    <!-- Icon Box -->
                    <div class="icon-box with-line">
                        <!-- Icon -->
                        <div class="icon-box-circle">
                            <div class="icon-box-circle-inner">
                                <i class="icon-feather-folder"></i>
                                <div class="icon-box-check"><i class="icon-material-outline-check"></i></div>
                            </div>
                        </div>
                        <h3><?php _e("Fill the form"); ?></h3>
                        <p><?php _e("Enter a detailed description of your content. Tell the AI what do you want."); ?></p>
                    </div>
                </div>

                <div class="col-xl-4 col-md-4">
                    <!-- Icon Box -->
                    <div class="icon-box">
                        <!-- Icon -->
                        <div class="icon-box-circle">
                            <div class="icon-box-circle-inner">
                                <i class="icon-feather-file-text"></i>
                                <div class="icon-box-check"><i class="icon-material-outline-check"></i></div>
                            </div>
                        </div>
                        <h3><?php _e("Get your content"); ?></h3>
                        <p><?php _e("Get a unique high quality content. The content is plagiarism free and you can use it anywhere."); ?></p>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php print_adsense_code('home_page_1'); ?>

    <div class="section padding-top-65 padding-bottom-65">
        <div class="container margin-bottom-20">
            <div class="section-headline centered margin-top-0 margin-bottom-30">
                <h3 class="margin-bottom-5"><?php _e("Templates"); ?></h3>
                <p><?php _e("Generate your required content with over 60+ content creation templates"); ?></p>
            </div>
            <div class="template-categories home-templates">
                <ul>
                    <li class="active"><a href="javascript:void();" class="ai-templates-category"
                                          data-category="all"><?php _e("All templates") ?></a></li>
                    <?php
                    foreach ($ai_templates as $category) { ?>
                        <li><a href="javascript:void();" class="ai-templates-category"
                               data-category="<?php _esc($category['id']) ?>"><?php _esc($category['title']) ?></a></li>
                    <?php }
                    ?>
                </ul>
            </div>
            <div class="row ai-template-blocks">
                <?php
                foreach ($ai_templates as $category) { ?>
                    <div class="col-md-12 ai-templates-category-title">
                        <h4><?php _esc($category['title']) ?></h4>
                    </div>
                    <?php
                    foreach ($category['templates'] as $template) { ?>
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
                                            <?php _esc($template['title']) ?>
                                            <?php if (!is_null($plan_templates) && !in_array($template['slug'], $plan_templates)) { ?>
                                                <span class="dashboard-status-button yellow"><i
                                                            class="fa fa-gift"></i> <?php _e("Pro") ?></span>
                                            <?php } ?>
                                        </h4>
                                        <p class="margin-bottom-0"><?php _esc($template['description']) ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php }
                }
                ?>
            </div>
        </div>
    </div>

<?php print_adsense_code('home_page_2'); ?>

<?php if (get_option('show_ai_images_home')) {
    if (!empty($ai_images->get_results())) {
        ?>
        <div class="section gray padding-top-65 padding-bottom-65">
            <div class="container margin-bottom-20">
                <div class="section-headline centered margin-top-0 margin-bottom-30">
                    <h3 class="margin-bottom-5"><?php _e("AI Images"); ?></h3>
                    <p><?php _e("Here's our latest generated AI images."); ?></p>
                </div>
                <div class="row">
                    <?php foreach ($ai_images as $ai_image) { ?>
                        <div class="col-sm-4 col-md-2 col-6">
                            <div class="margin-bottom-30">
                                <a href="<?php echo _esc($config['site_url'], 0) . 'storage/ai_images/' . $ai_image['image']; ?>"
                                   target="_blank">
                                    <img class="lazy-load rounded"
                                         src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                         data-original="<?php echo _esc($config['site_url'], 0) . 'storage/ai_images/small_' . $ai_image['image']; ?>"
                                         alt="<?php _esc($ai_image['description']) ?>" data-tippy-placement="top"
                                         title="<?php _esc($ai_image['description']) ?>">
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php }
} ?>

<?php print_adsense_code('home_page_3'); ?>
<?php if ($config['show_membershipplan_home']) { ?>
    <!-- Membership Plans  -->
    <div class="section border-top padding-top-60 padding-bottom-75">
        <div class="container">
            <div class="row">

                <div class="col-xl-12">
                    <!-- Section Headline -->
                    <div class="section-headline centered margin-top-0 margin-bottom-75">
                        <h3><?php _e("Membership Plan") ?></h3>
                    </div>
                </div>

                <div class="col-xl-12">
                    <form name="form1" method="post" action="<?php url("MEMBERSHIP") ?>">
                        <div class="billing-cycle-radios margin-bottom-70">
                            <?php if ($total_monthly) { ?>
                                <div class="radio billed-monthly-radio">
                                    <input id="radio-monthly" name="billed-type" type="radio" value="monthly"
                                           checked="">
                                    <label for="radio-monthly"><span class="radio-label"></span> <?php _e("Monthly") ?>
                                    </label>
                                </div>
                                <?php
                            }
                            if ($total_annual) {
                                ?>
                                <div class="radio billed-yearly-radio">
                                    <input id="radio-yearly" name="billed-type" type="radio" value="yearly">
                                    <label for="radio-yearly"><span class="radio-label"></span> <?php _e("Yearly") ?>
                                    </label>
                                </div>
                                <?php
                            }
                            if ($total_lifetime) {
                                ?>
                                <div class="radio billed-lifetime-radio">
                                    <input id="radio-lifetime" name="billed-type" type="radio" value="lifetime">
                                    <label for="radio-lifetime"><span
                                                class="radio-label"></span> <?php _e("Lifetime") ?></label>
                                </div>
                            <?php } ?>
                        </div>
                        <!-- Pricing Plans Container -->
                        <div class="pricing-plans-container">
                            <?php
                            foreach ($sub_types as $plan) {
                                ?>
                                <!-- Plan -->
                                <div class='pricing-plan <?php if (isset($plan['recommended']) && $plan['recommended'] == "yes") {
                                    echo 'recommended';
                                } ?>'>

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
                                        <strong><?php _e("Features of") ?>&nbsp;<?php _esc($plan['title']) ?></strong>
                                        <ul>
                                            <?php if (!get_option('single_model_for_plans')) { ?>
                                                <li>
                                                    <strong>
                                                        <?php
                                                        $models = get_opeai_models();
                                                        _esc($models[$plan['ai_model']])
                                                        ?>
                                                    </strong>&nbsp;<br><em><small><?php _e("Open AI Model") ?></small></em>
                                                </li>
                                            <?php } ?>
                                            <li>
                                                <strong><?php _esc(count($plan['ai_templates'])) ?></strong> <?php _e("AI Document Templates") ?>
                                            </li>
                                            <li>
                                                <strong><?php _esc(is_string($plan['ai_words_limit']) ? $plan['ai_words_limit'] : number_format($plan['ai_words_limit'])) ?></strong> <?php _e("Words per month") ?>
                                            </li>
                                            <?php if ($config['enable_ai_images']) { ?>
                                                <li>
                                                    <strong><?php _esc(is_string($plan['ai_images_limit']) ? $plan['ai_images_limit'] : number_format($plan['ai_images_limit'])) ?></strong> <?php _e("Images per month") ?>
                                                </li>
                                            <?php }
                                            if ($config['enable_speech_to_text']) { ?>
                                                <li>
                                                    <strong><?php _esc(is_string($plan['ai_speech_to_text_limit']) ? $plan['ai_speech_to_text_limit'] : number_format($plan['ai_speech_to_text_limit'])) ?></strong> <?php _e("Speech to Text per month") ?>
                                                    <i class="icon-feather-help-circle margin-left-2"
                                                       data-tippy-placement="top"
                                                       title="<?php _e("Create audio transcription") ?>"></i></li>
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
                                        echo '<button type="submit" class="button full-width margin-top-20 ripple-effect" name="upgrade" value="' . _esc($plan['id'], false) . '">' . __("Upgrade") . '</button>';
                                    }
                                    if ($plan['Selected'] == 1) {
                                        echo '<a href="javascript:void(0);" class="button full-width margin-top-20 ripple-effect">' . __("Current Plan") . '</a>';
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Membership Plans / End-->
<?php } ?>

<?php print_adsense_code('home_page_4'); ?>
<?php if ($config['testimonials_enable'] && $config['show_testimonials_home']) { ?>
    <div class="section gray padding-top-65 padding-bottom-55">

        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <!-- Section Headline -->
                    <div class="section-headline centered margin-top-0 margin-bottom-5">
                        <h3><?php _e("Testimonials") ?></h3>
                    </div>
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
<?php } ?>
    <!-- Testimonials / End -->
<?php print_adsense_code('home_page_5'); ?>
    <!-- Recent Blog Posts -->
<?php if ($config['blog_enable'] && $config['show_blog_home']) { ?>
    <div class="section border-top padding-top-65 padding-bottom-50">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">

                    <!-- Section Headline -->
                    <div class="section-headline margin-top-0 margin-bottom-45">
                        <h3><?php _e("Recent Blog") ?></h3>
                        <a href="<?php url("BLOG") ?>" class="headline-link"><?php _e('View Blog') ?></a>
                    </div>

                    <div class="row">
                        <!-- Blog Post Item -->
                        <?php
                        foreach ($recent_blog as $blog) {
                            ?>
                            <div class="col-xl-4">
                                <a href="<?php _esc($blog['link']) ?>" class="blog-compact-item-container">
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
                        <?php } ?>
                        <!-- Blog post Item / End -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
    <!-- Recent Blog Posts / End -->
<?php print_adsense_code('home_page_6'); ?>
<?php if ($config['show_partner_logo_home']) { ?>
    <div class="section gray border-top padding-top-45 padding-bottom-45">
        <!-- Logo Carousel -->
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <!-- Carousel -->
                    <div class="col-md-12">
                        <div class="logo-carousel">
                            <?php
                            $dir = ROOTPATH . '/storage/partner/';
                            $i = 0;
                            foreach (glob($dir . '*') as $path) {
                                ?>
                                <div class="carousel-item">
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
    </div>
<?php } ?>
<?php
overall_footer();
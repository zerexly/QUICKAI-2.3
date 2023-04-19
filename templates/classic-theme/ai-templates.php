<?php
overall_header(__("Templates"));
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
                        <?php _e("Templates") ?>
                        <div class="word-used-wrapper margin-left-10">
                            <i class="icon-feather-bar-chart-2"></i>
                            <?php echo '<i id="quick-words-left">' .
                                _esc(number_format((float) $total_words_used), 0) . '</i> / ' .
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
                            <li><?php _e("Templates") ?></li>
                        </ul>
                    </nav>
                </div>
                <div>
                    <input id="template-search" placeholder="<?php _e('Search...'); ?>" type="text" class="with-border border-pilled">
                </div>
                <div class="template-categories">
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
                <div>
                    <div class="row ai-template-blocks">
                        <?php
                        foreach ($ai_templates as $key => $category) { ?>
                            <div class="col-md-12 ai-templates-category-title">
                                <h4><?php _esc($category['title']) ?></h4>
                            </div>
                            <?php
                            foreach ($category['templates'] as $template) { ?>
                                <div class="col-md-4 col-sm-6 category-<?php _esc($category['id']) ?>">
                                    <a href="<?php echo url('AI_TEMPLATES', false) . '/' . $template['slug'] ?>"
                                        <?php if (!in_array($template['slug'], $plan_templates)) { ?>
                                    title="<?php _e("Upgrade your plan to use this template") ?>" data-tippy-placement="top"
                                <?php } ?>>
                                        <div class="dashboard-box ai-templates <?php echo (!in_array($template['slug'], $plan_templates)) ? 'ai-templates-pro' : ''; ?>">
                                            <div class="content">
                                                <div class="ai-templates-icon">
                                                    <i class="<?php _esc($template['icon']) ?>"></i>
                                                </div>
                                                <h4>
                                                    <?php _esc($template['title']) ?>
                                                    <?php if (!in_array($template['slug'], $plan_templates)) { ?>
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
                <?php print_adsense_code('footer_top'); ?>
                <!-- Footer -->
                <div class="dashboard-footer-spacer"></div>
                <div class="small-footer margin-top-15">
                    <div class="footer-copyright">
                        <?php _esc($config['copyright_text']); ?>
                    </div>
                    <ul class="footer-social-links">
                        <?php
                        if($config['facebook_link'] != "")
                            echo '<li><a href="'._esc($config['facebook_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-facebook"></i></a></li>';
                        if($config['twitter_link'] != "")
                            echo '<li><a href="'._esc($config['twitter_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-twitter"></i></a></li>';
                        if($config['instagram_link'] != "")
                            echo '<li><a href="'._esc($config['instagram_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-instagram"></i></a></li>';
                        if($config['linkedin_link'] != "")
                            echo '<li><a href="'._esc($config['linkedin_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-linkedin"></i></a></li>';
                        if($config['pinterest_link'] != "")
                            echo '<li><a href="'._esc($config['pinterest_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-pinterest"></i></a></li>';
                        if($config['youtube_link'] != "")
                            echo '<li><a href="'._esc($config['youtube_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-youtube"></i></a></li>';
                        ?>
                    </ul>
                    <div class="clearfix"></div>
                </div>

            </div>
        </div>
    </div>
<?php ob_start() ?>
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';
<?php

overall_header(__('AI Code'));


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
                        <?php _e('AI Code') ?>
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
                            <li><?php _e('AI Code') ?></li>
                        </ul>
                    </nav>
                </div>

                <div class="row">
                    <!-- Dashboard Box -->
                    <div class="col-md-4">
                        <form id="ai_code" name="ai_code" method="post" action="#">
                            <div class="dashboard-box margin-top-0 margin-bottom-30">
                                <!-- Headline -->
                                <div class="headline">
                                    <h3>
                                        <i class="icon-feather-code"></i><?php _e('AI Code') ?>
                                    </h3>
                                </div>
                                <div class="content with-padding">
                                    <div class="notification small-notification notice"><?php _e('Use this code generator to create code in any programming language.') ?></div>
                                    <div>
                                        <div class="submit-field margin-bottom-20">
                                            <h6><?php _e("Title") ?></h6>
                                            <input name="title" type="text" class="with-border small-input quick-text-counter" data-maxlength="100" value="<?php _e('New Code'); ?>">
                                        </div>
                                        <div class="submit-field margin-bottom-20">
                                            <h6><?php _e("Description") ?><span class="form-required">*</span></h6>
                                            <textarea rows="4" name="description" class="with-border small-input" placeholder="<?php _e('Write a javascript function to generate a random string'); ?>" required></textarea>
                                        </div>
                                        <small class="form-error"></small>
                                        <button type="submit" name="submit"
                                                class="button ripple-effect full-width"><?php _e("Generate") ?>
                                            <i class="icon-feather-arrow-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <div class="dashboard-box margin-top-0 margin-bottom-30">
                            <!-- Headline -->
                            <div class="headline">
                                <h3><i class="fa fa-align-left"></i><?php _e("Generated Result") ?></h3>
                                <div class="margin-left-auto line-height-1">
                                    <a href="#" class="button ripple-effect btn-sm" id="export_to_word"
                                       data-tippy-placement="top"
                                       title="<?php _e("Export as Word Document") ?>"><i class="fa fa-file-word-o"></i></a>
                                    <a href="#" class="button ripple-effect btn-sm" id="export_to_txt"
                                       title="<?php _e("Export as Text File") ?>"
                                       data-tippy-placement="top"><i class="fa fa-file-text-o"></i></a>
                                    <a href="#" class="button ripple-effect btn-sm" id="copy_text"
                                       title="<?php _e("Copy Text") ?>"
                                       data-tippy-placement="top"><i class="fa fa-copy"></i></a>
                                </div>
                            </div>
                            <div class="content with-padding">
                                <div id="content-focus"></div>
                                <textarea name="content" class="tiny-editor"></textarea>
                            </div>
                        </div>
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
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/jquery-simple-txt-counter.min.js"></script>
    <script src="<?php _esc($config['site_url'] . $config['admin_folder']); ?>/assets/plugins/tinymce/tinymce.min.js"></script>
    <script>
        // text counter
        $('.quick-text-counter').each(function () {
            var $this = $(this);

            $this.simpleTxtCounter({
                maxLength: $this.data('maxlength'),
                countElem: '<div class="form-text"></div>',
                lineBreak: false,
            });
        });

        // tinymce
        tinymce.init({
            selector: '.tiny-editor',
            min_height: 500,
            resize: true,
            plugins: 'advlist lists table autolink link wordcount fullscreen autoresize',
            toolbar: [
                "blocks | bold italic underline strikethrough | alignleft aligncenter alignright  | link blockquote",
                "undo redo | removeformat | table | bullist numlist | outdent indent"
            ],
            menubar: "",
            // link
            relative_urls: false,
            link_assume_external_targets: true,
            content_style: 'body { font-size:14px }'
        });
    </script>
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';
<?php

overall_header(__('View Document'));


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
                    <h3><?php _e('View Document') ?></h3>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><a href="<?php url("ALL_DOCUMENTS") ?>"><?php _e("All Documents") ?></a></li>
                            <li><?php _e('View Document') ?></li>
                        </ul>
                    </nav>
                </div>

                <div class="dashboard-box margin-top-0 margin-bottom-30">
                    <!-- Headline -->
                    <div class="headline">
                        <h3><i class="fa fa-align-left"></i><?php _e("View Document") ?></h3>
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
                        <form id="ai_document_form" name="ai_document_form" method="post" action="#">
                            <input type="hidden" name="id" id="post_id" value="<?php _esc($id) ?>">
                            <input type="hidden" name="ai_template" value="<?php _esc($template_slug); ?>">
                            <div class="d-flex margin-bottom-10">
                                <input name="title" type="text" class="with-border small-input"
                                       placeholder="<?php _e("Untitled Document") ?>" value="<?php _esc($title) ?>"
                                       required>
                                <button class="button btn-sm margin-left-5 ripple-effect"
                                        name="submit"
                                        type="submit"
                                        title="<?php _e("Save Document") ?>"
                                        data-tippy-placement="top"><i class="icon-feather-save"></i></button>
                            </div>
                            <small class="form-error margin-bottom-10"></small>
                            <textarea name="content" class="tiny-editor"><?php _esc($content) ?></textarea>
                        </form>
                    </div>
                    <div id="content-focus"></div>
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
    <script src="<?php _esc($config['site_url'].$config['admin_folder']); ?>/assets/plugins/tinymce/tinymce.min.js"></script>
    <script>

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
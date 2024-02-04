<?php

overall_header(__("All Speeches"));
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
                        <?php _e("All Speeches") ?>
                        <div class="word-used-wrapper margin-left-10">
                            <i class="icon-feather-bar-chart-2"></i>
                            <?php echo '<i id="quick-images-left">' .
                                _esc(number_format((float)$total_character_used), 0) . '</i> / ' .
                                ($characters_limit == -1
                                    ? __('Unlimited')
                                    : _esc(number_format($characters_limit + get_user_option($_SESSION['user']['id'], 'total_text_to_speech_available', 0)), 0)); ?>
                            <strong><?php _e('Characters Used'); ?></strong>
                        </div>
                    </h3>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><?php _e("All Speeches") ?></li>
                        </ul>
                    </nav>
                </div>

                <div class="dashboard-box margin-top-0 margin-bottom-30">
                    <!-- Headline -->
                    <div class="headline">
                        <h3><i class="icon-feather-volume-2"></i><?php _e("All Speeches") ?></h3>
                    </div>
                    <div class="content with-padding">
                        <table class="basic-table">
                            <thead>
                            <tr>
                                <th><?php _e("Text") ?></th>
                                <th><?php _e("Audio") ?></th>
                                <th><?php _e("Voice") ?></th>
                                <th class="small-width"><?php _e("Date") ?></th>
                                <th data-priority="2" class="small-width"><?php _e("Action") ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(empty($speeches)){ ?>
                                <tr class="no-order-found">
                                    <td colspan="5" class="text-center"><?php _e("No audios found.") ?></td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($speeches as $speech) { ?>
                                <tr>
                                    <td data-label="<?php _e("Text") ?>">
                                        <div><strong><?php _esc($speech['title']) ?></strong></div>
                                        <small data-tippy-placement="top" title="<?php _esc(escape($speech['text'])) ?>"><?php _esc($speech['text_short']) ?></small>
                                    </td>
                                    <td data-label="<?php _e("Audio") ?>">
                                        <audio controls="" preload="none"><source src="<?php _esc($speech['file_url']) ?>" type="audio/mpeg"></audio>
                                    </td>
                                    <td data-label="<?php _e("Voice") ?>">
                                        <span><?php _esc($speech['voice']['voice']) ?></span>, <small><?php _esc($speech['voice']['gender']) ?><?php if($speech['voice']['voice_type'] == 'neural') _esc(', Neural'); ?></small>
                                        <br>
                                        <small><strong><?php _esc($speech['language']['language']) ?></strong></small>
                                    </td>
                                    <td data-label="<?php _e("Date") ?>">
                                        <small><?php echo _esc($speech['date'], 0) . ' <br><strong>' . _esc($speech['time'], 0) . '</strong>' ?></small>
                                    </td>
                                    <td data-label="<?php _e("Action") ?>">
                                        <a href="<?php _esc($speech['file_url']); ?>" class="button ripple-effect btn-sm"
                                           data-tippy-placement="top"
                                           title="<?php _e("Download") ?>" download><i class="fa fa-download"></i>
                                        </a>
                                        <a href="#" class="button red ripple-effect btn-sm quick-delete"
                                           data-id="<?php _esc($speech['id']) ?>"
                                           data-action="delete_speech"
                                           data-tippy-placement="top"
                                           title="<?php _e("Delete") ?>"><i class="fa fa-trash-o"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($show_paging) { ?>
                            <!-- Pagination -->
                            <div class="pagination-container margin-top-20">
                                <nav class="pagination">
                                    <ul>
                                        <?php
                                        foreach ($pagging as $page) {
                                            if ($page['current'] == 0) {
                                                ?>
                                                <li>
                                                    <a href="<?php _esc($page['link']) ?>"><?php _esc($page['title']) ?></a>
                                                </li>
                                            <?php } else {
                                                ?>
                                                <li><a href="#" class="current-page"><?php _esc($page['title']) ?></a>
                                                </li>
                                            <?php }
                                        }
                                        ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php } ?>
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
    <link href="<?php _esc(TEMPLATE_URL); ?>/css/lightbox/lightgallery.min.css" rel="stylesheet">
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/lightgallery.min.js"></script>
    <script>
        $( ".image-lightbox" ).each(function() {
            lightGallery($(this).get(0),{
                selector: '.ai-lightbox-image',
                download: true,
            });
        });
    </script>
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';

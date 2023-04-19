<?php

overall_header(__("All Images"));
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
                        <?php _e("All Images") ?>
                        <div class="word-used-wrapper margin-left-10">
                            <i class="icon-feather-bar-chart-2"></i>
                            <?php echo '<i id="quick-images-left">' .
                                _esc(number_format((float)$total_images_used), 0) . '</i> / ' .
                                ($images_limit == -1
                                    ? __('Unlimited')
                                    : _esc(number_format($images_limit), 0)); ?>
                            <strong><?php _e('Images Used'); ?></strong>
                        </div>
                    </h3>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><?php _e("All Images") ?></li>
                        </ul>
                    </nav>
                </div>

                <div class="dashboard-box margin-top-0 margin-bottom-30">
                    <!-- Headline -->
                    <div class="headline">
                        <h3><i class="icon-feather-file"></i><?php _e("All Images") ?></h3>
                    </div>
                    <div class="content with-padding">
                        <table class="basic-table">
                            <thead>
                            <tr>
                                <th data-priority="1"><?php _e("Image") ?></th>
                                <th><?php _e("Resolution") ?></th>
                                <th class="small-width"><?php _e("Date") ?></th>
                                <th data-priority="2" class="small-width"><?php _e("Action") ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(empty($images)){ ?>
                                <tr class="no-order-found">
                                    <td colspan="4" class="text-center"><?php _e("No images found.") ?></td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($images as $image) { ?>
                                <tr>
                                    <td data-label="<?php _e("Image") ?>">
                                        <div class="d-flex align-items-center">
                                            <a href="<?php echo _esc($config['site_url'],0).'storage/ai_images/'.$image['image']; ?>" target="_blank" data-tippy-placement="top" title="<?php _e("View") ?>">
                                                <img src="<?php echo _esc($config['site_url'],0).'storage/ai_images/small_'.$image['image']; ?>" class="rounded" alt="<?php _esc($image['title']) ?>" width="60">
                                            </a>
                                            <div class="margin-left-10">
                                                <div><strong><?php _esc($image['title']) ?></strong></div>
                                                <small><?php _esc($image['description']) ?></small>
                                            </div>

                                        </div>
                                    </td>
                                    <td data-label="<?php _e("Resolution") ?>"><?php _esc($image['resolution']) ?></td>
                                    <td data-label="<?php _e("Date") ?>">
                                        <small><?php echo _esc($image['date'], 0) . ' <br><strong>' . _esc($image['time'], 0) . '</strong>' ?></small>
                                    </td>
                                    <td data-label="<?php _e("Action") ?>">
                                        <a href="<?php echo _esc($config['site_url'],0).'storage/ai_images/'.$image['image']; ?>" class="button ripple-effect btn-sm"
                                                data-tippy-placement="top"
                                                title="<?php _e("Download") ?>" download><i class="fa fa-download"></i>
                                        </a>
                                        <a href="#" class="button red ripple-effect btn-sm quick-delete"
                                                data-id="<?php _esc($image['id']) ?>"
                                                data-action="delete_image"
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
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';
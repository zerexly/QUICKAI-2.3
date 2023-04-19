<?php
overall_header(__("Affiliate Program"));
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
                    <h3><?php _e("Affiliate Program") ?></h3>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><?php _e("Affiliate Program") ?></li>
                        </ul>
                    </nav>
                </div>

                <!-- Fun Facts Container -->
                <div class="fun-facts-container">
                    <div class="fun-fact" data-fun-fact-color="#b81b7f">
                        <div class="fun-fact-text">
                            <span><?php _e("Wallet"); ?></span>
                            <h4>
                                <?php _esc(($wallet)); ?>
                            </h4>
                        </div>
                        <div class="fun-fact-icon"><i class="icon-feather-pocket"></i></div>
                    </div>
                    <div class="fun-fact" data-fun-fact-color="#36bd78">
                        <div class="fun-fact-text">
                            <span><?php _e("Total Referred"); ?></span>
                            <h4>
                                <?php _esc(($total_referred)); ?>
                            </h4>
                        </div>
                        <div class="fun-fact-icon"><i class="icon-feather-user-plus"></i></div>
                    </div>
                    <div class="fun-fact" data-fun-fact-color="#efa80f">
                        <div class="fun-fact-text">
                            <span><?php _e("Total Earning"); ?></span>
                            <h4>
                                <?php _esc(($total_earning)); ?>
                            </h4>
                        </div>
                        <div class="fun-fact-icon"><i class="fa fa-money"></i></div>
                    </div>
                </div>

                <!-- Dashboard Box -->
                <div class="dashboard-box main-box-in-row">
                    <div class="headline">
                        <h3>
                            <i class="icon-feather-share-2"></i> <?php _e("Start earning with the affiliate program"); ?>
                        </h3>
                    </div>
                    <div class="content with-padding">
                        <div class="notification notice">
                            <?php _e('Invite new customers to our site using your affiliate link and when they purchase any membership plan, you will get a commission.') ?>
                        </div>
                        <div class="margin-top-30">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="bidding-widget margin-bottom-30">
                                        <!-- Headline -->
                                        <span class="bidding-detail"><?php _e('Current <strong>Commission Rate</strong>') ?></span>

                                        <div class="bidding-value"><?php _esc(get_option('affiliate_commission_rate', 30)) ?>%</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bidding-widget margin-bottom-30">
                                        <!-- Headline -->
                                        <span class="bidding-detail"><?php _e('Affiliate <strong>Rule</strong>') ?></span>
                                        <div class="bidding-value">
                                            <?php if (get_option('affiliate_rule') == 'all') { ?>
                                                <?php _e('Each Subscriptions') ?>
                                                <i class="icon-feather-help-circle margin-left-2"
                                                   data-tippy-placement="top"
                                                   title="<?php _e("You will get a commission on each successful subscription payments.") ?>"></i>
                                            <?php } else { ?>
                                                <?php _e('First Subscription') ?>
                                                <i class="icon-feather-help-circle margin-left-2"
                                                   data-tippy-placement="top"
                                                   title="<?php _e("You will get a commission on the first successful subscription payment.") ?>"></i>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="margin-bottom-10"><?php _e('Affiliate URL') ?></h4>
                                    <div class="d-flex">
                                        <input type="text" class="with-border margin-bottom-0 affiliate-url"
                                               onfocus="this.select()"
                                               value="<?php _esc($affiliate_url) ?>"
                                               readonly>
                                        <button class="button ripple-effect margin-left-5 copy-url"
                                                data-tippy-placement="top"
                                                title="<?php _e('Copy URL') ?>"><i class="fa fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Dashboard Box / End -->

                <div class="dashboard-box main-box-in-row">
                    <div class="headline">
                        <h3>
                            <i class="fa fa-money"></i> <?php _e("Commissions"); ?>
                        </h3>
                    </div>
                    <div class="content with-padding">
                        <table id="datatable">
                            <thead>
                            <tr>
                                <th class="small-width"></th>
                                <th class="small-width"><?php _e("Commission") ?></th>
                                <th class="small-width"><?php _e("Total Amount") ?></th>
                                <th class="small-width"><?php _e("Commission Rate") ?></th>
                                <th><?php _e("Transaction ID") ?></th>
                                <th><?php _e("Date") ?></th>
                            </tr>
                            </thead>
                            <?php if(count($affiliates) == "0"){ ?>
                                <tbody>
                                <tr>
                                    <td colspan="6" class="text-center"><?php _e("No result found.") ?></td>
                                </tr>
                                </tbody>
                                <?php
                            }else{ ?>
                                <tbody>
                                <?php foreach($affiliates as $affiliate){ ?>
                                    <tr>
                                        <td></td>
                                        <td class="padding-left-20"><?php _esc(price_format($affiliate['commission'])) ?></td>
                                        <td class="padding-left-20"><?php _esc(price_format($affiliate['payment'])) ?></td>
                                        <td class="padding-left-20"><?php _esc($affiliate['rate'].'%') ?></td>
                                        <td class="padding-left-20"><?php _esc($affiliate['transaction_id']) ?></td>
                                        <td class="padding-left-20"><?php _esc(date('d M Y h:i A', strtotime($affiliate['date']))) ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            <?php } ?>
                        </table>
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
    <link rel="stylesheet" href="<?php _esc(TEMPLATE_URL);?>/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="<?php _esc(TEMPLATE_URL);?>/css/responsive.dataTables.min.css">
    <script src="<?php _esc(TEMPLATE_URL);?>/js/jquery.dataTables.min.js"></script>
    <script src="<?php _esc(TEMPLATE_URL);?>/js/dataTables.responsive.min.js"></script>
    <script>

        $(document).ready(function () {
            $('#datatable').DataTable({
                responsive: {
                    details: {
                        type: 'column'
                    }
                },
                "language": {
                    "paginate": {
                        "previous": "<?php _e("Previous") ?>",
                        "next": "<?php _e("Next") ?>"
                    },
                    "search": "<?php _e("Search") ?>",
                    "lengthMenu": "<?php _e("Display") ?> _MENU_",
                    "zeroRecords": "<?php _e("No result found.") ?>",
                    "info": "<?php _e("Page") ?> _PAGE_ - _PAGES_",
                    "infoEmpty": "<?php _e("No result found.") ?>",
                    "infoFiltered": "( <?php _e("Total Results") ?> _MAX_)"
                },
                columnDefs: [{
                    className: 'control',
                    orderable: false,
                    targets: 0
                }]
            });
        });

    </script>
    <script>
        $('.copy-url').on('click', function (e) {
            e.preventDefault();
            $('.affiliate-url').select();
            document.execCommand("copy");

            Snackbar.show({
                text: "<?php _e("Copied successfully.") ?>",
                pos: 'bottom-center',
                showAction: false,
                actionText: "Dismiss",
                duration: 2000,
                textColor: '#fff',
                backgroundColor: '#383838'
            });
        });
    </script>
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';
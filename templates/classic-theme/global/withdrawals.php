<?php
overall_header(__("Withdrawals"));
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
                    <h3><?php _e("Withdrawals") ?></h3>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><?php _e("Withdrawals") ?></li>
                        </ul>
                    </nav>
                </div>


                <div class="js-accordion">
                    <div class="dashboard-box js-accordion-item">
                        <div class="headline d-block js-accordion-header">
                            <h3>
                                <i class="fa fa-bell-o"></i> <?php _e("Request withdrawal"); ?>
                            </h3>
                        </div>
                        <div class="content with-padding js-accordion-body">
                            <div class="notification notice">
                                <?php _e('The requested amount will be deducted from your wallet and the amount will be blocked until it get approved or rejected by the administrator. Once its approved, the requested amount will be manually pay to you.') ?>
                            </div>

                            <form name="form1" method="post" action="" id="send">
                                <?php
                                if (!empty($error)) {
                                    echo '<div class="notification error closeable"><p><i class="icon-info-sign"></i> ' . _esc($error, false) . '</p></div>';
                                }
                                ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="submit-field">
                                            <h5><?php _e("Withdrawal Amount") ?>
                                                (<?php _esc($config['currency_sign']) ?>)</h5>
                                            <div class="input-with-icon">
                                                <input class="with-border" type="number"
                                                       placeholder="<?php _e("Amount") ?>"
                                                       name="amount"
                                                       value="<?php _esc(get_option('affiliate_minimum_payout')) ?>"
                                                       min="<?php _esc(get_option('affiliate_minimum_payout')) ?>"
                                                       required>
                                                <i class="fa fa-money"></i>
                                            </div>
                                            <small><i class="fa fa-info-circle"></i> <?php _e("Minimum withdraw amount") ?> : <?php _esc(price_format(get_option('affiliate_minimum_payout'))) ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="submit-field">
                                    <h5><?php _e("Payment Method") ?></h5>
                                    <?php foreach ($payment_methods as $payment) { ?>
                                        <div>
                                            <div class="radio">
                                                <input id="<?php _esc($payment['payment_id']) ?>" name="payment_id"
                                                       type="radio"
                                                       value="<?php _esc($payment['payment_id']) ?>" checked>
                                                <label for="<?php _esc($payment['payment_id']) ?>"><span
                                                            class="radio-label"></span> <?php _esc($payment['payment_title']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="submit-field">
                                            <h5><?php _e("Account Details") ?></h5>
                                            <textarea name="account_details" class="with-border" placeholder="<?php _e("Write Payment Details...") ?>" required></textarea>
                                            <small><?php _e("Write here your payment id or payment details of selected payment gateways.") ?></small>
                                        </div>
                                    </div>
                                </div>
                                <button name="submit" class="button" type="submit"><?php _e("Withdraw") ?></button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="dashboard-box">
                    <div class="headline">
                        <h3>
                            <i class="fa fa-list-alt"></i> <?php _e("Withdrawal Requests"); ?>
                        </h3>
                    </div>
                    <div class="content with-padding">
                        <table id="datatable">
                            <thead>
                            <tr>
                                <th class="small-width"></th>
                                <th><?php _e("Requested On") ?></th>
                                <th class="small-width"><?php _e("Amount") ?></th>
                                <th><?php _e("Payment Method") ?></th>
                                <th class="small-width"><?php _e("Status") ?></th>
                            </tr>
                            </thead>
                            <?php if (count($withdrawals) == "0") { ?>
                                <tbody>
                                <tr>
                                    <td colspan="5" class="text-center"><?php _e("No result found.") ?></td>
                                </tr>
                                </tbody>
                                <?php
                            } else { ?>
                                <tbody>
                                <?php foreach ($withdrawals as $withdrawal) { ?>
                                    <tr>
                                        <td></td>
                                        <td class="padding-left-20"><?php _esc(date('d M Y h:i A', strtotime($withdrawal['created_at']))) ?></td>
                                        <td class="padding-left-20"><?php _esc(price_format($withdrawal['amount'])) ?></td>
                                        <td class="padding-left-20">
                                            <?php _esc($withdrawal['payment_title']) ?>
                                            <i class="fa fa-info-circle" title="<?php _esc(nl2br(escape($withdrawal['account_details']))) ?>" data-tippy-placement="top"></i>
                                        </td>
                                        <td class="padding-left-20">
                                            <?php
                                            if ($withdrawal['status'] == "success") {
                                                $status = '<span class="dashboard-status-button green">'.__("Paid").'</span>';
                                            } elseif ($withdrawal['status'] == "pending") {
                                                $status = '<span class="dashboard-status-button yellow">'.__("Pending").'</span>';
                                            } else{
                                                $status = '<span class="dashboard-status-button red">'.__("Reject").'</span>';
                                                $status = ' <i class="fa fa-info-circle" title="'. nl2br(escape($withdrawal['reject_reason'])).'" data-tippy-placement="top"></i>';
                                            }
                                            _esc($status);
                                            ?>
                                        </td>
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
    <link rel="stylesheet" href="<?php _esc(TEMPLATE_URL); ?>/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="<?php _esc(TEMPLATE_URL); ?>/css/responsive.dataTables.min.css">
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/jquery.dataTables.min.js"></script>
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/dataTables.responsive.min.js"></script>
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
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';
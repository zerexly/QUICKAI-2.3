<?php
overall_header(__("Membership"));
?>
<!-- Dashboard Container -->
<div class="dashboard-container">

    <?php include_once TEMPLATE_PATH.'/dashboard_sidebar.php'; ?>

    <!-- Dashboard Content
    ================================================== -->
    <div class="dashboard-content-container" data-simplebar>
        <div class="dashboard-content-inner" >
            <?php print_adsense_code('header_bottom'); ?>
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <h3><?php _e("Current Plan") ?></h3>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><?php _e("Current Plan") ?></li>
                    </ul>
                </nav>
            </div>

            <!-- Row -->
            <div class="row">
                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box">
                        <!-- Headline -->
                        <div class="headline">
                            <h3><i class="icon-feather-gift"></i> <?php _e("Current Plan") ?></h3>
                        </div>
                        <div class="content with-padding">
                            <div class="table-responsive">
                                <table id="js-table-list" class="basic-table dashboard-box-list">
                                    <tr>
                                        <th><?php _e("Membership") ?></th>
                                        <th><?php _e("Payment Mode") ?></th>
                                        <th><?php _e("Start Date") ?></th>
                                        <th><?php _e("Expiry Date") ?></th>
                                        <?php if($show_cancel_button == "1"){ ?> <th><?php _e("Cancel") ?></th><?php } ?>
                                    </tr>
                                    <tr>
                                        <td><?php _esc($upgrades_title) ?></td>
                                        <td>
                                            <?php
                                            if($payment_mode == "one_time")
                                                _e("One Time");
                                            else
                                                _e("Recurring");
                                            ?>
                                        </td>
                                        <td><?php _esc($upgrades_start_date) ?></td>
                                        <td><?php _esc($upgrades_expiry_date) ?></td>
                                        <?php if($show_cancel_button == "1"){ ?>
                                            <td><a href="<?php url("MEMBERSHIP") ?>/?action=cancel_auto_renew"><i class="fa fa-remove"></i> <?php _e("Cancel") ?></a></td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td align="right" colspan="7"><button type="button" class="button" onClick="window.location.href='<?php url("MEMBERSHIP") ?>/changeplan'"><?php _e("Change Plan") ?></button></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row / End -->
            <?php include_once TEMPLATE_PATH.'/overall_footer_dashboard.php'; ?>

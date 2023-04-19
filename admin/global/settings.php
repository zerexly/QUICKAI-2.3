<?php
include '../includes.php';
$page_title = __('Settings');
include '../header.php'; ?>

    <!-- Page Body Start-->
    <div class="page-body-wrapper">
        <?php include '../sidebar.php'; ?>

        <!-- Page Sidebar Ends-->
        <div class="page-body">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-lg-6 main-header">
                            <h2><?php _esc($page_title) ?></h2>
                            <h6 class="mb-0"><?php _e('admin panel') ?></h6>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Container-fluid starts-->
            <div class="container-fluid">
                <div class="tab-content">
                    <div class="tab-pane active" id="quick_settings_general">
                        <form method="post" class="ajax_submit_form" data-action="SaveSettings"
                              data-ajax-sidepanel="true">
                            <div class="quick-card card">
                                <div class="card-header">
                                    <h5><?php _e('General') ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="site_url">
                                                    <?php _e('Site URL') ?>
                                                    <i class="icon-feather-help-circle"
                                                       title="<?php _e('The site url is the url where you installed Script.') ?>"
                                                       data-tippy-placement="top"></i>
                                                </label>
                                                <input id="site_url" class="form-control" type="text" name="site_url"
                                                       value="<?php _esc(get_option("site_url")); ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="site_title">
                                                    <?php _e('Site Title') ?>
                                                    <i class="icon-feather-help-circle"
                                                       title="<?php _e('The site title is what you would like your website to be known as, this will be used in emails and in the title of your webpages.') ?>"
                                                       data-tippy-placement="top"></i>
                                                </label>
                                                <input name="site_title" class="form-control" type="text"
                                                       id="site_title"
                                                       value="<?php echo $config['site_title']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php quick_switch(__('Disable Landing Page'), 'disable_landing_page', (get_option("disable_landing_page") == '1')); ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php quick_switch(__('Enable Maintenance Mode'), 'enable_maintenance_mode', (get_option("enable_maintenance_mode") == '1')); ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="default_user_plan"><?php _e('Default Membership Plan for New Users') ?></label>
                                                <select name="default_user_plan" id="default_user_plan"
                                                        class="form-control">
                                                    <option value="free" <?php if (get_option("default_user_plan") == 'free') {
                                                        echo "selected";
                                                    } ?>><?php _e('Free') ?></option>
                                                    <option value="trial" <?php if (get_option("default_user_plan") == 'trial') {
                                                        echo "selected";
                                                    } ?>><?php _e('Trial') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="cron_exec_time"><?php _e('Cron job run time (In seconds)') ?>
                                                    <i class="icon-feather-help-circle"
                                                       title="<?php _e('Please enter time in seconds for example: 60 = 1 minutes<br>3600 = 1 Hour.') ?>"
                                                       data-tippy-placement="top"></i>
                                                </label>
                                                <input name="cron_exec_time" class="form-control" type="text"
                                                       id="cron_exec_time"
                                                       value="<?php echo $config['cron_exec_time']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="site_title"><?php _e('Show/hide Verify Email Message to Non-active Users') ?></label>
                                                <select name="non_active_msg" id="non_active_msg" class="form-control">
                                                    <option value="1" <?php if (get_option("non_active_msg") == '1') {
                                                        echo "selected";
                                                    } ?>>Show
                                                    </option>
                                                    <option value="0" <?php if (get_option("non_active_msg") == '0') {
                                                        echo "selected";
                                                    } ?>>Hide
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="site_title"><?php _e('Allow Non-active users to use AI') ?>
                                                    <i class="icon-feather-help-circle"
                                                       title="<?php _e('When disallow, an error message will be shown to non-active users to verify their email address.') ?>"
                                                       data-tippy-placement="top"></i>
                                                </label>
                                                <select name="non_active_allow" id="non_active_allow"
                                                        class="form-control">
                                                    <option value="1" <?php if (get_option("non_active_allow") == '1') {
                                                        echo "selected";
                                                    } ?>><?php _e('Allow') ?></option>
                                                    <option value="0" <?php if (get_option("non_active_allow") == '0') {
                                                        echo "selected";
                                                    } ?>><?php _e('Disallow') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="inputPassword4"><?php _e('Allow User Language Selection') ?></label>
                                                <select name="userlangsel" class="form-control" id="userlangsel">
                                                    <option value="1" <?php if ($config['userlangsel'] == 1) {
                                                        echo "selected";
                                                    } ?>><?php _e('Yes') ?></option>
                                                    <option value="0" <?php if ($config['userlangsel'] == 0) {
                                                        echo "selected";
                                                    } ?>><?php _e('No') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="transfer_filter"><?php _e('Transfer Filter') ?>
                                                    <i class="icon-feather-help-circle"
                                                       title="<?php _e('Whether you should be shown a transfer screen between saving admin pages or not.') ?>"
                                                       data-tippy-placement="top"></i>
                                                </label>
                                                <select name="transfer_filter" class="form-control"
                                                        id="transfer_filter">
                                                    <option value="1" <?php if ($config['transfer_filter'] == 1) {
                                                        echo "selected";
                                                    } ?>><?php _e('Yes') ?></option>
                                                    <option value="0" <?php if ($config['transfer_filter'] == 0) {
                                                        echo "selected";
                                                    } ?>><?php _e('No') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">

                                            <div class="form-group">
                                                <label><?php _e('Term & Condition Page Link') ?></label>
                                                <div>
                                                    <input name="termcondition_link" type="url" class="form-control"
                                                           value="<?php echo get_option("termcondition_link"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php _e('Privacy Page Link') ?></label>
                                                <div>
                                                    <input name="privacy_link" type="url" class="form-control"
                                                           value="<?php echo get_option("privacy_link"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php _e('Cookie Policy Page Link') ?></label>
                                                <div>
                                                    <input name="cookie_link" type="url" class="form-control"
                                                           value="<?php echo get_option("cookie_link"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php _e('After login redirect page link') ?>
                                                    <i class="icon-feather-help-circle"
                                                       title="<?php _e('User will be redirected to this url after login. By default dashboard page link will be used.') ?>"
                                                       data-tippy-placement="top"></i>
                                                </label>
                                                <div>
                                                    <input name="after_login_link" type="url" class="form-control"
                                                           value="<?php echo get_option("after_login_link"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="cookie_consent"><?php _e('Show/hide Cookie Consent Box') ?></label>
                                                <select name="cookie_consent" class="form-control" id="userthemesel">
                                                    <option value="1" <?php if (get_option("cookie_consent") == 1) {
                                                        echo "selected";
                                                    } ?>><?php _e('Show') ?></option>
                                                    <option value="0" <?php if (get_option("cookie_consent") == 0) {
                                                        echo "selected";
                                                    } ?>><?php _e('Hide') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group <?php if (empty($config['purchase_key'])) {
                                                echo "d-none";
                                            } ?>">
                                                <label for="developer_credit"><?php _e('Show Developer Credit') ?></label>
                                                <select name="developer_credit" id="developer_credit"
                                                        class="form-control">
                                                    <option value="1" <?php if ($config['developer_credit'] == 1) {
                                                        echo "selected";
                                                    } ?>><?php _e('Yes') ?></option>
                                                    <option value="0" <?php if ($config['developer_credit'] == 0) {
                                                        echo "selected";
                                                    } ?>><?php _e('No') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group <?php if ($config['quickad_debug'] == 0) {
                                                echo "d-none";
                                            } ?> ">
                                                <label for="quickad_debug"><?php _e('Enable Developement Mode') ?></label>
                                                <select name="quickad_debug" id="quickad_debug" class="form-control">
                                                    <option value="1" <?php if ($config['quickad_debug'] == 1) {
                                                        echo "selected";
                                                    } ?>><?php _e('Yes') ?></option>
                                                    <option value="0" <?php if ($config['quickad_debug'] == 0) {
                                                        echo "selected";
                                                    } ?>><?php _e('No') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <input type="hidden" name="general_setting" value="1">
                                    <button name="submit" type="submit"
                                            class="btn btn-primary"><?php _e('Save') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="quick_logo_watermark">
                        <form method="post" class="ajax_submit_form" data-action="SaveSettings"
                              data-ajax-sidepanel="true">
                            <div class="quick-card card">
                                <div class="card-header">
                                    <h5><?php _e('Logo') ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Favicon upload-->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label"><?php _e('Favicon') ?>
                                                    <code>*</code></label>
                                                <div class="screenshot">
                                                    <img class="redux-option-image" id="favicon_uploader"
                                                         src="<?php _esc($config['site_url']); ?>/storage/logo/<?php echo $config['site_favicon'] ?>"
                                                         alt="" target="_blank" rel="external">
                                                </div>
                                                <input class="form-control input-sm" type="file" name="favicon"
                                                       onchange="readURL(this,'favicon_uploader')">
                                                <span class="help-block"><?php _e('Ideal Size 16x16 PX') ?></span>
                                            </div>
                                        </div>

                                        <!-- Site Logo upload-->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label"><?php _e('Logo') ?><code>*</code></label>
                                                <div class="screenshot"><img class="redux-option-image"
                                                                             id="image_logo_uploader"
                                                                             src="<?php _esc($config['site_url']); ?>/storage/logo/<?php echo $config['site_logo'] ?>"
                                                                             alt="" target="_blank" rel="external">
                                                </div>
                                                <input class="form-control input-sm" type="file" name="file"
                                                       onchange="readURL(this,'image_logo_uploader')">
                                                <span class="help-block"><?php _e('Ideal Size 170x60 PX') ?></span>
                                            </div>
                                        </div>

                                        <!-- Site Logo upload-->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label"><?php _e('Footer Logo') ?>
                                                    <code>*</code></label>
                                                <div class="screenshot"><img class="redux-option-image"
                                                                             id="image_flogo_uploader"
                                                                             src="<?php _esc($config['site_url']); ?>/storage/logo/<?php echo $config['site_logo_footer'] ?>"
                                                                             alt="" target="_blank" rel="external">
                                                </div>
                                                <input class="form-control input-sm" type="file" name="footer_logo"
                                                       onchange="readURL(this,'image_flogo_uploader')">
                                                <span class="help-block"><?php _e('Display in the footer') ?></span>
                                            </div>
                                        </div>

                                        <!-- Admin Logo upload-->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label"><?php _e('Admin Logo') ?></label>
                                                <div class="screenshot"><img class="redux-option-image" id="adminlogo"
                                                                             src="<?php _esc($config['site_url']); ?>/storage/logo/<?php echo $config['site_admin_logo'] ?>"
                                                                             alt="" target="_blank" rel="external">
                                                </div>
                                                <input class="form-control input-sm" type="file" name="adminlogo"
                                                       onchange="readURL(this,'adminlogo')">
                                                <span class="help-block"><?php _e('Ideal Size 235x62 PX') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <input type="hidden" name="logo_watermark" value="1">
                                    <button name="submit" type="submit"
                                            class="btn btn-primary"><?php _e('Save') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="quick_map">
                        <form method="post" class="ajax_submit_form" data-action="SaveSettings"
                              data-ajax-sidepanel="true">
                            <div class="quick-card card">
                                <div class="card-header">
                                    <h5><?php _e('Map') ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="map_type"><?php _e('Map Type (Google/Openstreet)') ?></label>
                                                <select name="map_type" id="map_type" class="form-control">
                                                    <option value="google" <?php if (get_option('map_type') == 'google') {
                                                        echo "selected";
                                                    } ?>><?php _e('Google Map') ?></option>
                                                    <option value="openstreet" <?php if (get_option('map_type') == 'openstreet') {
                                                        echo "selected";
                                                    } ?>><?php _e('Openstreet Map') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Google Map Color:') ?></label>
                                                <div>
                                                    <input name="map_color" type="color" class="form-control"
                                                           value="<?php echo get_option('map_color'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="gmap_api_key"><?php _e('OpenStreet Access Token') ?></label>
                                                <p class="help-block"><a
                                                            href="https://account.mapbox.com/access-tokens/"
                                                            target="_blank"><?php _e('Get MapBox Access Token For OpenStreet Map') ?></a>
                                                </p>
                                                <input name="openstreet_access_token" class="form-control" type="text"
                                                       id="openstreet_access_token"
                                                       value="<?php echo get_option('openstreet_access_token'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="gmap_api_key"><?php _e('Google Map API Key') ?></label>
                                                <p class="help-block"><a
                                                            href="https://developers.google.com/maps/documentation/javascript/get-api-key"
                                                            target="_blank"><?php _e('Get API Key') ?></a></p>
                                                <input name="gmap_api_key" class="form-control" type="text"
                                                       id="gmap_api_key"
                                                       value="<?php echo get_option('gmap_api_key'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Default Map Latitude:') ?></label>
                                                <div>
                                                    <input name="home_map_latitude" type="text" class="form-control"
                                                           value="<?php echo get_option('home_map_latitude'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Default Map Longitude:') ?></label>
                                                <div>
                                                    <input name="home_map_longitude" type="text" class="form-control"
                                                           value="<?php echo get_option('home_map_longitude'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Contact Map Latitude:') ?></label>
                                                <div>
                                                    <input name="contact_latitude" type="text" class="form-control"
                                                           value="<?php echo get_option('contact_latitude'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Contact Map Longitude:') ?></label>
                                                <div>
                                                    <input name="contact_longitude" type="text" class="form-control"
                                                           value="<?php echo get_option('contact_longitude'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <input type="hidden" name="quick_map" value="1">
                                    <button name="submit" type="submit"
                                            class="btn btn-primary"><?php _e('Save') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="quick_international">
                        <form method="post" class="ajax_submit_form" data-action="SaveSettings"
                              data-ajax-sidepanel="true">
                            <div class="quick-card card">
                                <div class="card-header">
                                    <h5><?php _e('International') ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="specific_country"><?php _e('Default Country') ?>
                                                    <i class="icon-feather-help-circle"
                                                       title="<?php _e('When user first time visit your website. Then the site run for that choosen default country.') ?>"
                                                       data-tippy-placement="top"></i>
                                                </label>
                                                <div>
                                                    <select class="js-select2 w-100 form-control"
                                                            name="specific_country"
                                                            id="specific_country">
                                                        <?php

                                                        $country = get_country_list(get_option("specific_country"));
                                                        foreach ($country as $value) {
                                                            echo '<option value="' . $value['code'] . '" ' . $value['selected'] . '>' . $value['asciiname'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="timezone"><?php _e('Timezone') ?>
                                                    <i class="icon-feather-help-circle"
                                                       title="<?php _e('Set your website timezone.') ?>"
                                                       data-tippy-placement="top"></i>
                                                </label>
                                                <div>
                                                    <select name="timezone" id="timezone"
                                                            class="js-select2 w-100 form-control">
                                                        <?php
                                                        $timezone = get_timezone_list(get_option("timezone"));

                                                        foreach ($timezone as $value) {
                                                            $id = $value['id'];
                                                            $country_code = $value['country_code'];
                                                            $time_zone_id = $value['time_zone_id'];
                                                            $selected = $value['selected'];
                                                            echo '<option value="' . $time_zone_id . '" ' . $selected . ' data-tokens="' . $time_zone_id . '">' . $time_zone_id . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="currency"><?php _e('Currency') ?>
                                                    <i class="icon-feather-help-circle"
                                                       title="<?php _e('This is default currecny which used for payment method.') ?>"
                                                       data-tippy-placement="top"></i>
                                                </label>
                                                <div>
                                                    <select name="currency" id="currency"
                                                            class="js-select2 w-100 form-control">
                                                        <?php
                                                        $currency = get_currency_list(get_option("currency_code"));

                                                        foreach ($currency as $value) {
                                                            $id = $value['id'];
                                                            $code = $value['code'];
                                                            $name = $value['name'];
                                                            $html_code = $value['html_entity'];
                                                            $selected = $value['selected'];

                                                            echo '<option value="' . $id . '" ' . $selected . ' data-tokens="' . $name . '">' . $name . ' (' . $html_code . ')</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="inputEmail3"><?php _e('Language') ?></label>
                                                <select name="lang" id="lang" class="js-select2 w-100 form-control">
                                                    <?php
                                                    $lang_list = get_language_list();

                                                    foreach ($lang_list as $l) {
                                                        $lang_name = $l['name'];
                                                        $lang_file_name = $l['file_name'];
                                                        $path_of_file = ROOTPATH . '/includes/lang/lang_' . $lang_file_name . '.php';
                                                        $selected = "";
                                                        if (get_option("lang") == $lang_file_name) {
                                                            $selected = "selected";
                                                        }

                                                        if (file_exists($path_of_file))
                                                            echo '<option value="' . $lang_file_name . '" ' . $selected . '>' . $lang_name . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <input type="hidden" name="international" value="1">
                                    <button name="submit" type="submit"
                                            class="btn btn-primary"><?php _e('Save') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="quick_email">
                        <form method="post" class="ajax_submit_form" data-action="SaveSettings"
                              data-ajax-sidepanel="true">
                            <div class="quick-card card">
                                <div class="card-header">
                                    <h5><?php _e('Email Setting') ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="admin_email">
                                                    <?php _e('Admin Email') ?>
                                                    <i class="icon-feather-help-circle"
                                                       title="<?php _e('This is the email address that the contact and report emails will be sent to, aswell as being the from address in signup and notification emails.') ?>"
                                                       data-tippy-placement="top"></i>
                                                </label>
                                                <div>
                                                    <input name="admin_email" class="form-control" type="text"
                                                           id="admin_email"
                                                           value="<?php echo get_option("admin_email"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="email_type">
                                                    <?php _e('E-Mail Sending Method') ?>
                                                    <i class="icon-feather-help-circle"
                                                       title="<?php _e('E-Mail connection and sending method. SMTP is a commonly used method. But if you have trouble with SMTP connections, you can choose different method.') ?>"
                                                       data-tippy-placement="top"></i>
                                                </label>
                                                <p>
                                                    <strong><?php _e('IMPORTANT:') ?></strong> <?php _e('If you use foreign SMTP accounts on your server you may get SMTP connection errors, if your hosting service provider block foreign e-mail account connections.') ?>
                                                </p>
                                                <div>
                                                    <select name="email_type" id="email_type" class="form-control">
                                                        <option value="smtp" <?php if (get_option("email_type") == 'smtp') {
                                                            echo "selected";
                                                        } ?>>SMTP
                                                        </option>
                                                        <option value="mail" <?php if (get_option("email_type") == 'mail') {
                                                            echo "selected";
                                                        } ?>>PHPMail
                                                        </option>
                                                        <option value="aws" <?php if (get_option("email_type") == 'aws') {
                                                            echo "selected";
                                                        } ?>>Amazon SES
                                                        </option>
                                                        <option value="mandrill" <?php if (get_option("email_type") == 'mandrill') {
                                                            echo "selected";
                                                        } ?>>Mandrill
                                                        </option>
                                                        <option value="sendgrid" <?php if (get_option("email_type") == 'sendgrid') {
                                                            echo "selected";
                                                        } ?>>SendGrid
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="mt-5">
                                                <div class="mailMethod-smtp mailMethods" <?php if ($config['email_type'] != 'smtp') {
                                                    echo 'style="display: none;"';
                                                } ?>>
                                                    <h4 class="text-warning"><?php _e('SMTP') ?></h4>
                                                    <hr>
                                                    <div class="form-group">
                                                        <label for="smtp_host">
                                                            <?php _e('SMTP Host') ?>
                                                            <i class="icon-feather-help-circle"
                                                               title="<?php _e('This is the host address for your smtp server, this is only needed if you are using SMTP as the Email Send Type.') ?>"
                                                               data-tippy-placement="top"></i>
                                                        </label>
                                                        <div>
                                                            <input name="smtp_host" type="text" class="form-control"
                                                                   id="smtp_host"
                                                                   value="<?php echo get_option("smtp_host"); ?>">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="smtp_host">
                                                            <?php _e('SMTP Port') ?>
                                                        </label>
                                                        <input name="smtp_port" type="text" class="form-control"
                                                               id="smtp_port"
                                                               value="<?php echo get_option("smtp_port"); ?>">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="smtp_username">
                                                            <?php _e('SMTP Username') ?>
                                                            <i class="icon-feather-help-circle"
                                                               title="<?php _e('This is the username for your smtp server, this is only needed if you are using SMTP as the Email Send Type.') ?>"
                                                               data-tippy-placement="top"></i>
                                                        </label>
                                                        <input name="smtp_username" class="form-control" type="text"
                                                               id="smtp_username"
                                                               value="<?php echo get_option("smtp_username"); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="smtp_password">
                                                            <?php _e('SMTP Password') ?>
                                                            <i class="icon-feather-help-circle"
                                                               title="<?php _e('This is the password for your smtp server, this is only needed if you are using SMTP as the Email Send Type.') ?>"
                                                               data-tippy-placement="top"></i>
                                                        </label>
                                                        <input name="smtp_password" type="password" class="form-control"
                                                               id="smtp_password"
                                                               value="<?php echo get_option("smtp_password"); ?>">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="smtp_secure">
                                                            <?php _e('SMTP Encryption') ?>
                                                            <i class="icon-feather-help-circle"
                                                               title="<?php _e('If your e-mail service provider supported secure connections, you can choose security method on list.') ?>"
                                                               data-tippy-placement="top"></i>
                                                        </label>
                                                        <select name="smtp_secure" id="smtp_secure"
                                                                class="form-control">
                                                            <option value="0" <?php if (get_option("smtp_secure") == '0') {
                                                                echo "selected";
                                                            } ?>><?php _e('Off') ?></option>
                                                            <option value="1" <?php if (get_option("smtp_secure") == '1') {
                                                                echo "selected";
                                                            } ?>><?php _e('SSL') ?></option>
                                                            <option value="2" <?php if (get_option("smtp_secure") == '2') {
                                                                echo "selected";
                                                            } ?>><?php _e('TLS') ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="smtp_auth">
                                                            <?php _e('SMTP Auth') ?>
                                                            <i class="icon-feather-help-circle"
                                                               title="<?php _e('SMTP Authentication, often abbreviated SMTP AUTH, is an extension of the Simple Mail Transfer Protocol whereby an SMTP client may log in using an authentication mechanism chosen among those supported by the SMTP server.') ?>"
                                                               data-tippy-placement="top"></i>
                                                        </label>
                                                        <select name="smtp_auth" id="smtp_auth" class="form-control">
                                                            <option value="true" <?php if (get_option("smtp_auth") == 'true') {
                                                                echo "selected";
                                                            } ?>><?php _e('On') ?></option>
                                                            <option value="false" <?php if (get_option("smtp_auth") == 'false') {
                                                                echo "selected";
                                                            } ?>><?php _e('Off') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mailMethod-aws mailMethods" <?php if ($config['email_type'] != 'aws') {
                                                    echo 'style="display: none;"';
                                                } ?>>
                                                    <h4 class="text-warning"><?php _e('Amazon SES') ?></h4>
                                                    <hr>
                                                    <div class="form-group">
                                                        <label for="aws_host"><?php _e('AWS Region') ?></label>
                                                        <input name="aws_host" type="text" class="form-control"
                                                               id="aws_host"
                                                               value="<?php echo get_option("aws_host"); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="aws_access_key">
                                                            <?php _e('AWS SMTP Username') ?>
                                                            <i class="icon-feather-help-circle"
                                                               title="<?php _e('Note: Your SMTP user name and password are not the same as your AWS access key ID and secret access key. Do not attempt to use your AWS credentials to authenticate yourself against the SMTP endpoint.') ?>"
                                                               data-tippy-placement="top"></i>
                                                        </label>
                                                        <p class="help-block">
                                                            <?php _e('For more information about credential types') ?>,
                                                            <a
                                                                    href="https://docs.aws.amazon.com/console/ses/using-credentials"
                                                                    target="_blank"><?php _e('click here') ?>.</a></p>
                                                        <input name="aws_access_key" class="form-control" type="text"
                                                               id="aws_access_key"
                                                               value="<?php echo get_option("aws_access_key"); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="aws_secret_key"><?php _e('AWS SMTP Password') ?></label>
                                                        <input name="aws_secret_key" type="password"
                                                               class="form-control"
                                                               id="aws_secret_key"
                                                               value="<?php echo get_option("aws_secret_key"); ?>">
                                                    </div>

                                                </div>
                                                <div class="mailMethod-mandrill mailMethods" <?php if ($config['email_type'] != 'mandrill') {
                                                    echo 'style="display: none;"';
                                                } ?>>
                                                    <h4 class="text-warning"><?php _e('Mandrill') ?></h4>
                                                    <hr>
                                                    <div class="form-group">
                                                        <label for="mandrill_user"><?php _e('Mandrill Username') ?></label>
                                                        <input name="mandrill_user" class="form-control" type="text"
                                                               id="mandrill_user"
                                                               value="<?php echo get_option("mandrill_user"); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="mandrill_key"><?php _e('Mandrill API Key') ?></label>
                                                        <input name="mandrill_key" type="text" class="form-control"
                                                               id="mandrill_key"
                                                               value="<?php echo get_option("mandrill_key"); ?>">
                                                    </div>
                                                </div>
                                                <div class="mailMethod-sendgrid mailMethods" <?php if ($config['email_type'] != 'sendgrid') {
                                                    echo 'style="display: none;"';
                                                } ?>>
                                                    <h4 class="text-warning"><?php _e('SendGrid') ?></h4>
                                                    <hr>
                                                    <div class="form-group">
                                                        <label for="sendgrid_user"><?php _e('SendGrid Username') ?></label>
                                                        <input name="sendgrid_user" class="form-control" type="text"
                                                               id="sendgrid_user"
                                                               value="<?php echo get_option("sendgrid_user"); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sendgrid_pass"><?php _e('SendGrid Password') ?></label>
                                                        <input name="sendgrid_pass" type="password" class="form-control"
                                                               id="sendgrid_pass"
                                                               value="<?php echo get_option("sendgrid_pass"); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <input type="hidden" name="email_setting" value="1">
                                    <button name="submit" type="submit"
                                            class="btn btn-primary"><?php _e('Save') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="quick_theme_setting">
                        <form method="post" class="ajax_submit_form" data-action="SaveSettings"
                              data-ajax-sidepanel="true">
                            <div class="quick-card card">
                                <div class="card-header">
                                    <h5><?php _e('Theme Setting') ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <?php quick_switch(__('Show membership plan on home page'), 'show_membershipplan_home', (get_option("show_membershipplan_home") == '1')); ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php quick_switch(__('Show partners slider on home page'), 'show_partner_logo_home', (get_option("show_partner_logo_home") == '1')); ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Theme Color:') ?></label>
                                                <div>
                                                    <input name="theme_color" type="color" class="form-control"
                                                           value="<?php echo get_option("theme_color"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Meta Keywords:') ?></label>
                                                <div>
                                                    <input name="meta_keywords" type="text" class="form-control"
                                                           value="<?php echo get_option("meta_keywords"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Meta Description:') ?></label>
                                                <div>
                                                    <input name="meta_description" type="text" class="form-control"
                                                           value="<?php echo get_option("meta_description"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Contact Address:') ?></label>
                                                <div>
                                                    <input name="contact_address" type="text" class="form-control"
                                                           value="<?php echo get_option("contact_address"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Contact Email:') ?></label>
                                                <div>
                                                    <input name="contact_email" type="text" class="form-control"
                                                           value="<?php echo get_option("contact_email"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Contact Phone:') ?></label>
                                                <div>
                                                    <input name="contact_phone" type="text" class="form-control"
                                                           value="<?php echo get_option("contact_phone"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php _e('Footer Facebook Page Link:') ?></label>
                                                <div>
                                                    <input name="facebook_link" type="text" class="form-control"
                                                           value="<?php echo get_option("facebook_link"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php _e('Footer Twitter Page Link:') ?></label>
                                                <div>
                                                    <input name="twitter_link" type="text" class="form-control"
                                                           value="<?php echo get_option("twitter_link"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php _e('Footer Instagram Page Link:') ?></label>
                                                <div>
                                                    <input name="instagram_link" type="text" class="form-control"
                                                           value="<?php echo get_option("instagram_link"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php _e('Footer LinkedIn Page Link:') ?></label>
                                                <div>
                                                    <input name="linkedin_link" type="text" class="form-control"
                                                           value="<?php echo get_option("linkedin_link"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php _e('Footer Pinterest Page Link:') ?></label>
                                                <div>
                                                    <input name="pinterest_link" type="text" class="form-control"
                                                           value="<?php echo get_option("pinterest_link"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php _e('Footer Youtube Page/Video Link:') ?></label>
                                                <div>
                                                    <input name="youtube_link" type="text" class="form-control"
                                                           value="<?php echo get_option("youtube_link"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Copyright text:') ?></label>
                                                <div>
                                                    <input name="copyright_text" type="text" class="form-control"
                                                           value="<?php echo get_option("copyright_text"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class=""><?php _e('Footer text:') ?></label>
                                                <div>
                                                <textarea name="footer_text"
                                                          class="form-control"><?php echo get_option("footer_text"); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php _e('External Javascript or Css In header:') ?> <i
                                                            class="icon-feather-help-circle"
                                                            title="<?php _e("You can add Any javascript code and style css. Like Google Analytics code. This code will paste on head part."); ?>"
                                                            data-tippy-placement="top"></i></label>
                                                <p class="help-block"></p>
                                                <div>
                                                <textarea name="external_code" type="text" class="form-control"
                                                          rows="5"><?php echo get_option("external_code"); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <input type="hidden" name="theme_setting" value="1">
                                    <button name="submit" type="submit"
                                            class="btn btn-primary"><?php _e('Save') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="quick_ai_setting">
                        <form method="post" class="ajax_submit_form" data-action="SaveSettings"
                              data-ajax-sidepanel="true">
                            <div class="quick-card card">
                                <div class="card-header">
                                    <h5><?php _e('AI Settings') ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="bad_words"><?php _e('Bad Words') ?></label>
                                        <textarea name="bad_words" id="bad_words" type="text" class="form-control"
                                                          rows="2"><?php echo get_option("bad_words"); ?></textarea>
                                        <span class="form-text text-mute"><?php _e('You can enter bad words seperated by commas to filter every request.'); ?></span>
                                    </div>
                                    <div class="mt-4">
                                        <h4 class="text-primary"><?php _e('OpenAI') ?></h4>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?php quick_switch(__('Use single OpenAI model for all plans'), 'single_model_for_plans', (get_option("single_model_for_plans") == '1')); ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group open_ai_model">
                                                    <?php $selected_model = get_option('open_ai_model', 'gpt-3.5-turbo'); ?>
                                                    <label for="open_ai_model"><?php _e('OpenAI Model') ?></label>
                                                    <select id="open_ai_model" class="form-control"
                                                            name="open_ai_model">
                                                        <?php foreach (get_opeai_models() as $key => $model){ ?>
                                                            <option value="<?php _esc($key) ?>" <?php echo $key == $selected_model ? 'selected' : '' ?>><?php _esc($model) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="form-text text-muted"><?php _e('Select the AI model for all users.') ?> <a href="https://platform.openai.com/docs/models/gpt-3" target="_blank"><?php _e('Read more here.') ?></a></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="open_ai_api_key"><?php _e('API Key') ?></label>
                                                    <div>
                                                        <?php
                                                        $api_keys = ORM::for_table($config['db']['pre'] . 'api_keys')
                                                            ->where('active', '1')
                                                            ->where('type', 'openai')
                                                            ->find_array();
                                                        $default_key = get_option('open_ai_api_key');
                                                        ?>
                                                        <select name="open_ai_api_key" id="open_ai_api_key"
                                                                class="form-control" required>
                                                            <option value="random" <?php if ('random' == $default_key) echo 'selected'; ?>><?php _e('Use all randomly') ?></option>
                                                            <?php foreach ($api_keys as $key) { ?>
                                                                <option value="<?php _esc($key['id']) ?>" <?php if ($key['id'] == $default_key) echo 'selected'; ?>><?php _esc($key['title']) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <span class="form-text text-mute"><a
                                                                    href="<?php echo ADMINURL ?>app/api-keys.php"
                                                                    target="_blank"><?php _e('Setup your API keys here'); ?></a></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="ai_languages"><?php _e('Languages') ?></label>
                                                    <textarea id="ai_languages" name="ai_languages" type="text"
                                                              class="form-control"><?php echo get_option("ai_languages"); ?></textarea>
                                                    <span class="form-text text-mute"><?php _e('Insert languages seperated by commas (in english only).'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ai_default_lang"><?php _e('Default Language') ?></label>
                                                    <input id="ai_default_lang" name="ai_default_lang" type="text"
                                                           class="form-control"
                                                           value="<?php echo get_option("ai_default_lang"); ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ai_default_quality_type"><?php _e('Default Quality type') ?></label>
                                                    <select name="ai_default_quality_type" id="ai_default_quality_type"
                                                            class="form-control" required>
                                                        <option value="0.25" <?php if ('0.25' == get_option('ai_default_quality_type')) echo 'selected'; ?>><?php _e('Economy') ?></option>
                                                        <option value="0.5" <?php if ('0.5' == get_option('ai_default_quality_type')) echo 'selected'; ?>><?php _e('Average') ?></option>
                                                        <option value="0.75" <?php if ('0.75' == get_option('ai_default_quality_type')) echo 'selected'; ?>><?php _e('Good') ?></option>
                                                        <option value="1" <?php if ('1' == get_option('ai_default_quality_type')) echo 'selected'; ?>><?php _e('Premium') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ai_default_tone_voice"><?php _e('Default Tone of Voice') ?></label>
                                                    <select name="ai_default_tone_voice" id="ai_default_tone_voice"
                                                            class="form-control" required>
                                                        <option value="funny" <?php if ('funny' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Funny') ?></option>
                                                        <option value="casual" <?php if ('casual' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Casual') ?></option>
                                                        <option value="excited" <?php if ('excited' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Excited') ?></option>
                                                        <option value="professional" <?php if ('professional' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Professional') ?></option>
                                                        <option value="witty" <?php if ('witty' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Witty') ?></option>
                                                        <option value="sarcastic" <?php if ('witty' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Sarcastic') ?></option>
                                                        <option value="feminine" <?php if ('feminine' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Feminine') ?></option>
                                                        <option value="masculine" <?php if ('masculine' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Masculine') ?></option>
                                                        <option value="bold" <?php if ('bold' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Bold') ?></option>
                                                        <option value="dramatic" <?php if ('dramatic' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Dramatic') ?></option>
                                                        <option value="gumpy" <?php if ('gumpy' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Gumpy') ?></option>
                                                        <option value="secretive" <?php if ('secretive' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Secretive') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ai_default_max_langth"><?php _e('Default Max Results Length') ?></label>
                                                    <input id="ai_default_max_langth" name="ai_default_max_langth"
                                                           type="number" class="form-control"
                                                           value="<?php echo get_option("ai_default_max_langth"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <h4 class="text-primary"><?php _e('AI Image') ?></h4>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <?php quick_switch(__('Enable AI Image'), 'enable_ai_images', (get_option("enable_ai_images") == '1')); ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ai_image_api"><?php _e('AI API') ?></label>
                                                    <select name="ai_image_api" id="ai_image_api"
                                                            class="form-control api-type" required>
                                                        <option value="any" <?php if ('any' == get_option('ai_image_api')) echo 'selected'; ?>><?php _e('Any') ?></option>
                                                        <option value="openai" <?php if ('openai' == get_option('ai_image_api')) echo 'selected'; ?>><?php _e('OpenAI') ?></option>
                                                        <option value="stable-diffusion" <?php if ('stable-diffusion' == get_option('ai_image_api')) echo 'selected'; ?>><?php _e('Stable Diffusion') ?></option>
                                                    </select>
                                                    <span class="form-text text-mute"><?php _e('Select the AI to use for generating the images.'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ai_image_api_key"><?php _e('API Key') ?></label>
                                                    <?php
                                                    $api_keys = ORM::for_table($config['db']['pre'] . 'api_keys')
                                                        ->where('active', '1')
                                                        ->find_array();
                                                    $default_key = get_option('ai_image_api_key');
                                                    ?>
                                                    <select name="ai_image_api_key" id="ai_image_api_key"
                                                            class="form-control ai-image-api-key" required>
                                                        <option value="random" <?php if ('random' == $default_key) echo 'selected'; ?>><?php _e('Use all randomly') ?></option>
                                                        <?php foreach ($api_keys as $key) { ?>
                                                            <option value="<?php _esc($key['id']) ?>"
                                                                    data-type="<?php _esc($key['type']) ?>" <?php if ($key['id'] == $default_key) echo 'selected'; ?>><?php _esc($key['title']) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="form-text text-mute"><a
                                                                href="<?php echo ADMINURL ?>app/api-keys.php"
                                                                target="_blank"><?php _e('Setup your API keys here'); ?></a></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <?php quick_switch(__('Show AI Images on Home Page'), 'show_ai_images_home', (get_option("show_ai_images_home") == '1')); ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ai_images_home_limit"><?php _e('Number of Images on Home page') ?></label>
                                                    <input id="ai_images_home_limit" name="ai_images_home_limit"
                                                           type="number" class="form-control"
                                                           value="<?php echo get_option("ai_images_home_limit"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <h4 class="text-primary"><?php _e('Speech to Text') ?></h4>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <?php quick_switch(__('Enable Speech to Text'), 'enable_speech_to_text', (get_option("enable_speech_to_text") == '1')); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <h4 class="text-primary"><?php _e('AI Code') ?></h4>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?php quick_switch(__('Enable AI Code'), 'enable_ai_code', (get_option("enable_ai_code") == '1')); ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ai_code_max_token"><?php _e('Max Results Length (Words)') ?></label>
                                                    <input id="ai_code_max_token" name="ai_code_max_token" type="number"
                                                           class="form-control"
                                                           value="<?php echo get_option("ai_code_max_token", '-1'); ?>">
                                                    <span class="form-text text-mute"><?php _e('Set -1 for no limit.'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <h4 class="text-primary"><?php _e('AI Chat') ?></h4>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?php quick_switch(__('Enable AI Chat'), 'enable_ai_chat', (get_option("enable_ai_chat") == '1')); ?>
                                                <span class="form-text text-warning mt-n2 form-group"><?php _e('<strong>ChatGPT</strong> OpenAI model is required for this feature.') ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ai_chat_max_token"><?php _e('Max Results Length (Words)') ?></label>
                                                    <input id="ai_chat_max_token" name="ai_chat_max_token" type="number"
                                                           class="form-control"
                                                           value="<?php echo get_option("ai_chat_max_token", '-1'); ?>">
                                                    <span class="form-text text-mute"><?php _e('Set -1 for no limit.'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ai_chat_bot_name"><?php _e('Chat Bot Name') ?></label>
                                                    <input id="ai_chat_bot_name" name="ai_chat_bot_name" type="text"
                                                           class="form-control"
                                                           value="<?php echo get_option("ai_chat_bot_name"); ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label><?php _e('Chat Bot Avatar') ?></label>
                                                    <input class="form-control input-sm" type="file"
                                                           name="chat_bot_avatar"
                                                           onchange="readURL(this,'chat_bot_uploader')">
                                                    <span class="help-block"><?php _e('Ideal Size 90x90 PX') ?></span>
                                                    <div class="screenshot">
                                                        <img class="redux-option-image" id="chat_bot_uploader"
                                                             src="<?php _esc($config['site_url']); ?>/storage/profile/<?php echo get_option('chat_bot_avatar') ?>"
                                                             alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="mt-4">
                                        <h4 class="text-primary"><?php _e('Proxies') ?></h4>
                                        <hr>
                                        <p><?php _e('You can setup the proxies here for the API requests to hide your identity. You can enter multiple values separated by commas.'); ?></p>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                <textarea id="ai_proxies" title="" name="ai_proxies"
                                                          class="form-control"
                                                          placeholder="http://username:password@ip:port"><?php echo get_option("ai_proxies"); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <input type="hidden" name="ai_settings" value="1">
                                    <button name="submit" type="submit"
                                            class="btn btn-primary"><?php _e('Save') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="quick_affiliate_settings">
                        <form method="post" class="ajax_submit_form" data-action="SaveSettings"
                              data-ajax-sidepanel="true">
                            <div class="quick-card card">
                                <div class="card-header">
                                    <h5><?php _e('Affiliate Program') ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <?php quick_switch(__('Enable'), 'enable_affiliate_program', (get_option("enable_affiliate_program") == '1')); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="affiliate_rule"><?php _e('Affiliate Rule') ?></label>
                                                <div>
                                                    <select name="affiliate_rule" id="affiliate_rule"
                                                            class="form-control" required>
                                                        <option value="first" <?php if ('first' == get_option('affiliate_rule')) echo 'selected'; ?>><?php _e('Only The First Purchase') ?></option>
                                                        <option value="all" <?php if ('all' == get_option('affiliate_rule')) echo 'selected'; ?>><?php _e('All Purchases') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                        <div class="form-group">
                                            <label class=""><?php _e('Commission Rate (%)') ?></label>
                                            <div>
                                                <input name="affiliate_commission_rate" type="number"
                                                       class="form-control"
                                                       value="<?php echo get_option("affiliate_commission_rate", 30); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class=""><?php _e('Minimum Payout Amount') ?>
                                                (<?php _esc($config['currency_code']) ?>)</label>
                                            <div>
                                                <input name="affiliate_minimum_payout" type="number"
                                                       class="form-control"
                                                       value="<?php echo get_option("affiliate_minimum_payout", 50); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <input type="hidden" name="affiliate_program_settings" value="1">
                                <button name="submit" type="submit" class="btn btn-primary"><?php _e('Save') ?></button>
                            </div>
                    </div>
                    </form>
                </div>
                    <div class="tab-pane" id="quick_live_chat">
                        <form method="post" class="ajax_submit_form" data-action="SaveSettings"
                              data-ajax-sidepanel="true">
                            <div class="quick-card card">
                                <div class="card-header">
                                    <h5><?php _e('Live Chat') ?> (Tawk.to)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php quick_switch(__('Enable'), 'enable_live_chat', (get_option("enable_live_chat") == '1')); ?>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="tawkto_chat_link"><?php _e('Direct Chat Link') ?></label>
                                                <input id="tawkto_chat_link" name="tawkto_chat_link" type="url"
                                                       class="form-control"
                                                       value="<?php echo get_option("tawkto_chat_link"); ?>">
                                                <span class="form-text text-muted"><a href="https://help.tawk.to/article/direct-chat-link" target="_blank"><?php _e('You can find here.'); ?></a></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <?php quick_switch(__('Membership Based'), 'tawkto_membership', (get_option("tawkto_membership") == '1'), __('Disable this to allow live chat for all users. Otherwise you need to specify in the Membership plan.')); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <input type="hidden" name="live_chat_settings" value="1">
                                    <button name="submit" type="submit" class="btn btn-primary"><?php _e('Save') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                <div class="tab-pane" id="quick_billing_details">
                    <form method="post" class="ajax_submit_form" data-action="SaveSettings" data-ajax-sidepanel="true">
                        <div class="quick-card card">
                            <div class="card-header">
                                <h5><?php _e('Billing Details') ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <p><?php _e('These details will be used for the invoice.') ?></p>
                                        <div class="form-group">
                                            <label class=""><?php _e('Invoice Number Prefix') ?></label>
                                            <div>
                                                <input name="invoice_nr_prefix" type="text" class="form-control"
                                                       value="<?php echo get_option("invoice_nr_prefix"); ?>"
                                                       placeholder="Ex: INV-">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class=""><?php _e('Name') ?></label>
                                            <div>
                                                <input name="invoice_admin_name" type="text" class="form-control"
                                                       value="<?php echo get_option("invoice_admin_name"); ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class=""><?php _e('Email') ?></label>
                                                    <div>
                                                        <input name="invoice_admin_email" type="text"
                                                               class="form-control"
                                                               value="<?php echo get_option("invoice_admin_email"); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class=""><?php _e('Phone') ?></label>
                                                    <div>
                                                        <input name="invoice_admin_phone" type="text"
                                                               class="form-control"
                                                               value="<?php echo get_option("invoice_admin_phone"); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class=""><?php _e('Address') ?></label>
                                            <div>
                                                <input name="invoice_admin_address" type="text" class="form-control"
                                                       value="<?php echo get_option("invoice_admin_address"); ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class=""><?php _e('City') ?></label>
                                                    <div>
                                                        <input name="invoice_admin_city" type="text"
                                                               class="form-control"
                                                               value="<?php echo get_option("invoice_admin_city"); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class=""><?php _e('State/Province') ?></label>
                                                    <div>
                                                        <input name="invoice_admin_state" type="text"
                                                               class="form-control"
                                                               value="<?php echo get_option("invoice_admin_state"); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class=""><?php _e('ZIP Code') ?></label>
                                                    <div>
                                                        <input name="invoice_admin_zipcode" type="text"
                                                               class="form-control"
                                                               value="<?php echo get_option("invoice_admin_zipcode"); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class=""><?php _e('Country') ?></label>
                                            <div>
                                                <select class="form-control" name="invoice_admin_country">
                                                    <?php
                                                    $country = get_country_list();
                                                    foreach ($country as $value) {
                                                        echo '<option value="' . $value['code'] . '" ' . (($value['code'] == get_option('invoice_admin_country')) ? 'selected' : '') . '>' . $value['asciiname'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class=""><?php _e('Tax Type') ?></label>
                                                    <div>
                                                        <input name="invoice_admin_tax_type" type="text"
                                                               class="form-control"
                                                               value="<?php echo get_option("invoice_admin_tax_type"); ?>"
                                                               placeholder="Ex: VAT">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class=""><?php _e('Tax ID') ?></label>
                                                    <div>
                                                        <input name="invoice_admin_tax_id" type="text"
                                                               class="form-control"
                                                               value="<?php echo get_option("invoice_admin_tax_id"); ?>">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <input type="hidden" name="billing_details" value="1">
                                <button name="submit" type="submit" class="btn btn-primary"><?php _e('Save') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="quick_social_login_setting">
                    <form method="post" class="ajax_submit_form" data-action="SaveSettings" data-ajax-sidepanel="true">
                        <div class="quick-card card">
                            <div class="card-header">
                                <h5><?php _e('Social Login Setting') ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="quick-accordion quick-payment-sortable ui-sortable" id="accordion">
                                    <!-- Favebook Login -->
                                    <div class="card quick-card">
                                        <div class="card-header">
                                            <h5 class="mb-0 d-flex align-items-center">
                                                <button class="btn btn-link pl-0 ml-1" data-toggle="collapse"
                                                        data-target="#facebook" aria-expanded="false"
                                                        aria-controls="facebook"
                                                        type="button"><?php _e('Facebook Login') ?></button>
                                            </h5>
                                        </div>
                                        <div class="collapse" id="facebook" aria-labelledby="facebook"
                                             data-parent="#accordion">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label><?php _e('Facebook app id:') ?></label>
                                                            <div>
                                                                <input name="facebook_app_id" type="text"
                                                                       class="form-control"
                                                                       value="<?php echo get_option("facebook_app_id"); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label><?php _e('Facebook app secret:') ?></label>
                                                            <div>
                                                                <input name="facebook_app_secret" type="text"
                                                                       class="form-control"
                                                                       value="<?php echo get_option("facebook_app_secret"); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label><?php _e('Facebook callback url:') ?></label>
                                                            <p class="help-block"><?php _e('Use this redirect url in facebook app.') ?></p>
                                                            <div>
                                                                <input type="text" class="form-control" disabled
                                                                       value="<?php echo $config['site_url']; ?>includes/social_login/facebook/index.php">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Google Login -->
                                    <div class="card quick-card">
                                        <div class="card-header">
                                            <h5 class="mb-0 d-flex align-items-center">
                                                <button class="btn btn-link pl-0 ml-1" data-toggle="collapse"
                                                        data-target="#google" aria-expanded="false"
                                                        aria-controls="google" type="button"><?php _e('Google Login') ?>
                                                </button>
                                            </h5>
                                        </div>
                                        <div class="collapse" id="google" aria-labelledby="google"
                                             data-parent="#accordion">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label><?php _e('Google app id:') ?></label>
                                                            <div>
                                                                <input name="google_app_id" type="text"
                                                                       class="form-control"
                                                                       value="<?php echo get_option("google_app_id"); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label><?php _e('Google app secret:') ?></label>
                                                            <div>
                                                                <input name="google_app_secret" type="text"
                                                                       class="form-control"
                                                                       value="<?php echo get_option("google_app_secret"); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label><?php _e('Google callback url:') ?></label>
                                                            <div>
                                                                <input type="text" class="form-control" disabled
                                                                       value="<?php echo $config['site_url']; ?>includes/social_login/google/index.php">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <input type="hidden" name="social_login_setting" value="1">
                                <button name="submit" type="submit" class="btn btn-primary"><?php _e('Save') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="quick_recaptcha">
                    <form method="post" class="ajax_submit_form" data-action="SaveSettings" data-ajax-sidepanel="true">
                        <div class="quick-card card">
                            <div class="card-header">
                                <h5><?php _e('Google reCAPTCHA') ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <h5 class="quick-bold"><?php _e('Instructions') ?></h5>
                                    <p><?php _e('To find your Site Key and Secret Key, follow the below steps:') ?></p>
                                    <ol>
                                        <li><?php _e('Go to the <a href="https://www.google.com/recaptcha/admin/create" target="_blank">Google reCAPTCHA</a> and register a new site.') ?></li>
                                        <li><?php _e("Enter label and select <strong>reCAPTCHA v2</strong> -> <strong>\"I'm not a robot\" Checkbox</strong> in <strong>reCAPTCHA type</strong> field.") ?></li>
                                        <li><?php _e('Enter your domain url.') ?></li>
                                        <li><?php _e('Accept Terms of Service and click on the <strong>Submit</strong> button.') ?></li>
                                        <li><?php _e('Look for the <strong>Site Key</strong> and <strong>Secret Key</strong>. Use them in the form below on this page.') ?></li>
                                        <li><?php _e('Enable Google reCAPTCHA and click on the <strong>Save</strong> button.') ?></li>
                                    </ol>
                                </div>
                                <?php quick_switch(__('Google reCAPTCHA'), 'recaptcha_mode', (get_option("recaptcha_mode") == '1')); ?>

                                <div class="form-group">
                                    <label><?php _e('Public Key:') ?></label>
                                    <div>
                                        <input name="recaptcha_public_key" type="text" class="form-control"
                                               value="<?php echo get_option("recaptcha_public_key"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?php _e('Private Key:') ?></label>
                                    <div>
                                        <input name="recaptcha_private_key" type="text" class="form-control"
                                               value="<?php echo get_option("recaptcha_private_key"); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <input type="hidden" name="recaptcha_setting" value="1">
                                <button name="submit" type="submit" class="btn btn-primary"><?php _e('Save') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="quick_blog">
                    <form method="post" class="ajax_submit_form" data-action="SaveSettings" data-ajax-sidepanel="true">
                        <div class="quick-card card">
                            <div class="card-header">
                                <h5><?php _e('Blog Setting') ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php quick_switch(__('Blog'), 'blog_enable', (get_option("blog_enable") == '1')); ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?php quick_switch(__('Show Blog On Home Page'), 'show_blog_home', (get_option("show_blog_home") == '1')); ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?php quick_switch(__('Blog Commenting'), 'blog_comment_enable', (get_option("blog_comment_enable") == '1')); ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="blog_page_limit"><?php _e('Number of Blogs on blog page:') ?></label>
                                            <input name="blog_page_limit" id="blog_page_limit" type="text" class="form-control"
                                            value="<?php _esc(get_option('blog_page_limit', 8)) ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php _e('Blog Banner Image:') ?></label>
                                            <div>
                                                <select name="blog_banner" id="blog_banner" class="form-control">
                                                    <option <?php if (get_option("blog_banner") == '1') {
                                                        echo "selected";
                                                    } ?> value="1"><?php _e('Show') ?></option>
                                                    <option <?php if (get_option("blog_banner") == '0') {
                                                        echo "selected";
                                                    } ?> value="0"><?php _e('Hide') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php _e('Comment Approval:') ?></label>
                                            <div>
                                                <select name="blog_comment_approval" id="blog_comment_approval"
                                                        class="form-control">
                                                    <option <?php if (get_option("blog_comment_approval") == '1') {
                                                        echo "selected";
                                                    } ?> value="1"><?php _e('Disable Auto Approve Comments') ?></option>
                                                    <option <?php if (get_option("blog_comment_approval") == '2') {
                                                        echo "selected";
                                                    } ?> value="2"><?php _e('Auto Approve Login Users Comments') ?></option>
                                                    <option <?php if (get_option("blog_comment_approval") == '3') {
                                                        echo "selected";
                                                    } ?> value="3"><?php _e('Auto Approve All Comments') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>
                                                <?php _e('Who Can Comment') ?>
                                                <i class="icon-feather-help-circle"
                                                   title="<?php _e('Non-login users have to enter their name and email address.') ?>"
                                                   data-tippy-placement="top"></i>
                                            </label>
                                            <div>
                                                <select name="blog_comment_user" id="blog_comment_user"
                                                        class="form-control">
                                                    <option <?php if (get_option("blog_comment_user") == '1') {
                                                        echo "selected";
                                                    } ?> value="1"><?php _e('Everyone') ?></option>
                                                    <option <?php if (get_option("blog_comment_user") == '0') {
                                                        echo "selected";
                                                    } ?> value="0"><?php _e('Only Login Users') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <input type="hidden" name="blog_setting" value="1">
                                <button name="submit" type="submit" class="btn btn-primary"><?php _e('Save') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="quick_testimonials">
                    <form method="post" class="ajax_submit_form" data-action="SaveSettings" data-ajax-sidepanel="true">
                        <div class="quick-card card">
                            <div class="card-header">
                                <h5><?php _e('Testimonials Setting') ?></h5>
                            </div>
                            <div class="card-body">
                                <?php quick_switch(__('Testimonials'), 'testimonials_enable', (get_option("testimonials_enable") == '1')); ?>
                                <?php quick_switch(__('Show On Blog Page'), 'show_testimonials_blog', (get_option("show_testimonials_blog") == '1')); ?>
                                <?php quick_switch(__('Show On Home Page'), 'show_testimonials_home', (get_option("show_testimonials_home") == '1')); ?>

                            </div>
                            <div class="card-footer">
                                <input type="hidden" name="testimonials_setting" value="1">
                                <button name="submit" type="submit" class="btn btn-primary"><?php _e('Save') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="quick_purchase_code">
                    <form method="post" class="ajax_submit_form" data-action="SaveSettings" data-ajax-sidepanel="true">
                        <div class="quick-card card">
                            <div class="card-header">
                                <h5><?php _e('Purchase Code') ?></h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($config['purchase_key']) && $config['purchase_key'] != "") { ?>
                                    <div class="alert alert-info"><?php _e('Your purchase code is already verified.') ?></div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="quick_purchase_code"><?php _e('Purchase Code') ?></label>
                                    <p>Enter any value!</p>
                                    <input id="quick_purchase_code" class="form-control" type="text"
                                           name="purchase_key">
                                </div>
                                <div class="form-group">
                                    <label for="buyer_email"><?php _e('Email') ?></label>
                                    <input id="buyer_email" class="form-control" type="text" name="buyer_email">
                                </div>
                            </div>
                            <div class="card-footer">
                                <input type="hidden" name="valid_purchase_setting" value="1">
                                <button type="submit" class="btn btn-primary"><?php _e('Save') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
    <script id="quick-sidebar-menu-js-extra">
        var QuickMenu = {"page": "settings"};
    </script>
<?php ob_start() ?>
    <script>
        $(function () {
            $('.api-type').on('change', function () {
                if ($(this).val() == 'any') {
                    $('.ai-image-api-key option').show();
                } else {
                    $('.ai-image-api-key option').hide();
                    $('.ai-image-api-key option[data-type="' + $(this).val() + '"]').show();
                    // display random field always
                    $('.ai-image-api-key option:first-child').show();
                }
            }).trigger('change');

            $('#single_model_for_plans').on('change', function (){
                if($(this).is(':checked'))
                    $('.open_ai_model').fadeIn();
                else
                    $('.open_ai_model').fadeOut();
            }).trigger('change');

            var hash = window.location.hash;
            hash && $('ul.nav a[href="' + hash + '"]').click();

            $('.nav a').on('click', function (e) {
                var scrollmem = $('body').scrollTop();
                window.location.hash = this.hash;
                $('html,body').scrollTop(scrollmem);
            });
        });
    </script>
    <link rel="stylesheet" href="../assets/css/datatables.css"/>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
<?php
$footer_content = ob_get_clean();

include '../footer.php';
<?php
overall_header(__("Account Setting"));
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
                    <h3><?php _e("Account Setting") ?></h3>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><?php _e("Account Setting") ?></li>
                        </ul>
                    </nav>
                </div>

                <!-- Row -->
                <div class="row">
                    <!-- Dashboard Box -->
                    <div class="col-xl-12">
                        <div class="dashboard-box margin-top-0">
                            <!-- Headline -->
                            <div class="headline">
                                <h3><i class="icon-feather-settings"></i> <?php _e("Account Setting") ?></h3>
                            </div>
                            <div class="content with-padding">
                                <form method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-12">
                                        <div class="submit-field">
                                            <h5><?php _e('Avatar'); ?></h5>
                                            <div class="uploadButton">
                                                <input class="uploadButton-input" type="file" accept="images/*" id="avatar"
                                                       name="avatar"/>
                                                <label class="uploadButton-button ripple-effect"
                                                       for="avatar"><?php _e('Upload Avatar') ?></label>
                                                <span class="uploadButton-file-name"><?php _e('Use 150x150px for better use') ?></span>
                                            </div>
                                            <?php if(!empty($avatar_error)){ _esc($avatar_error) ; }?>
                                        </div>
                                        </div>
                                        <div class="col-xl-6 col-md-12">
                                            <div class="submit-field">
                                                <h5><?php _e("Username") ?> *</h5>
                                                <div class="input-with-icon-left">
                                                    <i class="la la-user"></i>
                                                    <input type="text" class="with-border" id="username" name="username" value="<?php _esc($username)?>" onBlur="checkAvailabilityUsername()">
                                                </div>
                                                <span id="user-availability-status"><?php if($username_error != ""){ _esc($username_error) ; }?></span>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-12">
                                            <div class="submit-field">
                                                <h5><?php _e("Email Address") ?> *</h5>
                                                <div class="input-with-icon-left">
                                                    <i class="la la-envelope"></i>
                                                    <input type="text" class="with-border" id="email" name="email" value="<?php _esc($email_field)?>" onBlur="checkAvailabilityEmail()">
                                                </div>
                                                <span id="email-availability-status"><?php if($email_error != ""){ _esc($email_error) ; }?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="submit-field">
                                                <h5><?php _e("New Password") ?></h5>
                                                <input type="password" id="password" name="password" class="with-border" onkeyup="checkAvailabilityPassword()">
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <div class="submit-field">
                                                <h5><?php _e("Confirm Password") ?></h5>
                                                <input type="password" id="re_password" name="re_password" class="with-border" onkeyup="checkRePassword()">
                                            </div>
                                        </div>
                                    </div>
                                    <span id="password-availability-status"><?php if($password_error != ""){ _esc($password_error) ; }?></span>
                                    <button type="submit" name="submit" class="button ripple-effect"><?php _e("Save Changes") ?></button>
                                </form>
                            </div>
                        </div>
                        <div class="dashboard-box">
                            <div class="headline">
                                <h3><i class="icon-material-outline-description"></i> <?php _e("Billing Details") ?></h3>
                            </div>
                            <div class="content">
                                <div class="content with-padding">
                                    <div class="notification notice"><?php _e("These details will be used in invoice and payments.") ?></div>
                                    <?php if($billing_error == "1"){ ?>
                                        <div class="notification error"><?php _e("All fields are required.") ?></div>
                                    <?php } ?>
                                    <form method="post" accept-charset="UTF-8">
                                        <div class="submit-field">
                                            <h5><?php _e("Type") ?></h5>
                                            <select name="billing_details_type" id="billing_details_type"  class="with-border selectpicker" required>
                                                <option value="personal" <?php if($billing_details_type == "personal"){ echo 'selected';} ?> ><?php _e("Personal") ?></option>
                                                <option value="business" <?php if($billing_details_type == "business"){ echo 'selected';} ?> ><?php _e("Business") ?></option>
                                            </select>
                                        </div>
                                        <div class="submit-field billing-tax-id">
                                            <h5>
                                               <?php
                                               if($config['invoice_admin_tax_type'] != ""){
                                                   _esc($config['invoice_admin_tax_type']);
                                               }else{
                                                   _e("Tax ID");
                                               }
                                               ?>
                                            </h5>
                                            <input type="text" id="billing_tax_id" name="billing_tax_id" class="with-border" value="<?php _esc($billing_tax_id)?>">
                                        </div>
                                        <div class="submit-field">
                                            <h5><?php _e("Name") ?> *</h5>
                                            <input type="text" id="billing_name" name="billing_name" class="with-border" value="<?php _esc($billing_name)?>" required>
                                        </div>
                                        <div class="submit-field">
                                            <h5><?php _e("Address") ?> *</h5>
                                            <input type="text" id="billing_address" name="billing_address" class="with-border" value="<?php _esc($billing_address)?>" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="submit-field">
                                                    <h5><?php _e("City") ?> *</h5>
                                                    <input type="text" id="billing_city" name="billing_city" class="with-border" value="<?php _esc($billing_city)?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="submit-field">
                                                    <h5><?php _e("State") ?> *</h5>
                                                    <input type="text" id="billing_state" name="billing_state" class="with-border" value="<?php _esc($billing_state)?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="submit-field">
                                                    <h5><?php _e("Zip code") ?> *</h5>
                                                    <input type="text" id="billing_zipcode" name="billing_zipcode" class="with-border" value="<?php _esc($billing_zipcode)?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="submit-field">
                                            <h5><?php _e("Country") ?> *</h5>
                                            <select name="billing_country" id="billing_country" class="with-border selectpicker" data-live-search="true" required>
                                                <?php
                                                foreach($countries as $country){
                                                    ?>
                                                    <option value="<?php _esc($country['code']) ?>" <?php _esc($country['selected']) ?>><?php _esc($country['asciiname']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <button type="submit" name="billing-submit" class="button ripple-effect"><?php _e("Save Changes") ?></button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row / End -->
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
    <script>
        var error = "";
        function checkAvailabilityUsername() {
            jQuery.ajax({
                url: "<?php _esc($config['app_url']) ?>global/check_availability.php",
                data: 'username=' + $("#username").val(),
                type: "POST",
                success: function (data) {
                    if (data != "success") {
                        error = 1;
                        $("#user-availability-status").html(data);
                    }
                    else {
                        error = 0;
                        $("#user-availability-status").html("");
                    }
                },
                error: function () {
                }
            });
        }
        function checkAvailabilityEmail() {
            jQuery.ajax({
                url: "<?php _esc($config['app_url']) ?>global/check_availability.php",
                data: 'email=' + $("#email").val(),
                type: "POST",
                success: function (data) {
                    if (data != "success") {
                        error = 1;
                        $("#email-availability-status").html(data);
                    }
                    else {
                        error = 0;
                        $("#email-availability-status").html("");
                    }
                    $("#loaderIcon").hide();
                },
                error: function () {
                }
            });
        }
        function checkAvailabilityPassword() {
            var length = $('#password').val().length;
            if (length != 0) {
                var PASSLENG = "<?php _e('Password must be between 4 and 20 characters long') ?>";
                if (length < 5 || length > 21) {
                    $("#password-availability-status").html("<span class='status-not-available'>" + PASSLENG + "</span>");
                }
                else {
                    $("#password-availability-status").html("");
                }
            }
        }
        function checkRePassword(){
            if($('#password').val() != $('#re_password').val()){
                var PASS = "<?php _e('The passwords you entered did not match') ?>";
                $("#password-availability-status").html("<span class='status-not-available'>" + PASS + "</span>");
            }else{
                $("#password-availability-status").html("");
            }
        }
        jQuery(window).on('load',function () {
            jQuery('#password').val("");
        });

        $('#billing_details_type').on('change', function () {
            if($(this).val() == 'business')
                $('.billing-tax-id').slideDown();
            else
                $('.billing-tax-id').slideUp();
        }).trigger('change');
    </script>
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH.'/overall_footer_dashboard.php';
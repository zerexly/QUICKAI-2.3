<?php
overall_header(__("Register"));
?>
<?php print_adsense_code('header_bottom'); ?>
<!-- Titlebar
================================================== -->
<div id="titlebar" class="gradient">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php _e("Register") ?></h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><?php _e("Register") ?></li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xl-5 offset-xl-3">
            <div class="login-register-page">
                <!-- Welcome Text -->
                <div class="welcome-text">
                    <h3><?php _e("Let's create your account!") ?></h3>
                    <span><?php _e("Already have an account?") ?> <a href="<?php url("LOGIN") ?>"><?php _e("Log In!") ?></a></span>
                </div>
                <?php if($config['facebook_app_id'] != "" && $config['google_app_id'] != ""){ ?>
                    <div class="social-login-buttons">
                        <?php if($config['facebook_app_id'] != ""){ ?>
                        <button class="facebook-login ripple-effect" onclick="fblogin()"><i class="fa fa-facebook"></i> <?php _e("Log In via Facebook") ?>
                        </button>
                        <?php } ?>

                        <?php if($config['google_app_id'] != ""){ ?>
                        <button class="google-login ripple-effect" onclick="gmlogin()"><i class="fa fa-google"></i> <?php _e("Log In via Google") ?>
                        </button>
                        <?php } ?>
                    </div>
                    <div class="social-login-separator"><span><?php _e("or") ?></span></div>
                <?php } ?>
                <form method="post" id="register-account-form" action="#" accept-charset="UTF-8" onsubmit="document.getElementById('submit-btn').disabled = true;">
                    <div class="form-group">
                        <div class="input-with-icon-left">
                            <i class="la la-user"></i>
                            <input type="text" class="input-text with-border" placeholder="<?php _e("Full Name") ?>" value="<?php _esc($name_field)?>" id="name" name="name" onBlur="checkAvailabilityName()" required/>
                        </div>
                        <span id="name-availability-status"><?php if($name_error != ""){ _esc($name_error) ; }?></span>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon-left">
                            <i class="la la-user"></i>
                            <input type="text" class="input-text with-border" placeholder="<?php _e("Username") ?>" value="<?php _esc($username_field)?>" id="Rusername" name="username" onBlur="checkAvailabilityUsername()" required/>
                        </div>
                        <span id="user-availability-status"><?php if($username_error != ""){ _esc($username_error) ; }?></span>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon-left">
                            <i class="la la-envelope"></i>
                            <input type="text" class="input-text with-border" placeholder="<?php _e("Email Address") ?>" value="<?php _esc($email_field)?>" name="email" id="email" onBlur="checkAvailabilityEmail()" required/>
                        </div>
                        <span id="email-availability-status"><?php if($email_error != ""){ _esc($email_error) ; }?></span>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon-left">
                            <i class="la la-unlock"></i>
                            <input type="password" class="input-text with-border" placeholder="<?php _e("Password") ?>" id="Rpassword" name="password" onBlur="checkAvailabilityPassword()" required/>
                        </div>
                        <span id="password-availability-status"><?php if($password_error != ""){ _esc($password_error) ; }?></span>
                    </div>
                    <div class="form-group margin-bottom-15">
                        <div class="text-center">
                            <?php
                            if($config['recaptcha_mode'] == '1'){
                                echo '<div class="g-recaptcha" data-sitekey="'._esc($config['recaptcha_public_key'],false).'"></div>';
                            }
                            ?>
                        </div>
                        <span><?php if($recaptcha_error != ""){ _esc($recaptcha_error) ; }?></span>
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" id="agree_for_term" name="agree_for_term" value="1" required>
                        <label for="agree_for_term"><span class="checkbox-icon"></span> <?php _e("By clicking on Register button you are agree to our") ?> <?php _e("Terms & Condition") ?></label>
                    </div>
                    <input type="hidden" name="submit" value="submit">
                    <button id="submit-btn" class="button full-width button-sliding-icon ripple-effect margin-top-10" type="submit"><?php _e("Register") ?> <i class="icon-feather-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="margin-top-70"></div>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script>

    var error = "";

    function checkAvailabilityName() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "<?php _esc($config['app_url'])?>global/check_availability.php",
            data: 'name=' + $("#name").val(),
            type: "POST",
            success: function (data) {
                if (data != "success") {
                    error = 1;
                    $("#name").removeClass('has-success');
                    $("#name-availability-status").html(data);
                    $("#name").addClass('has-error mar-zero');
                }
                else {
                    error = 0;
                    $("#name").removeClass('has-error mar-zero');
                    $("#name-availability-status").html("");
                    $("#name").addClass('has-success');
                }
                $("#loaderIcon").hide();
            },
            error: function () {
            }
        });
    }
    function checkAvailabilityUsername() {
        var $item = $("#Rusername").closest('.form-group');
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "<?php _esc($config['app_url'])?>global/check_availability.php",
            data: 'username=' + $("#Rusername").val(),
            type: "POST",
            success: function (data) {
                if (data != "success") {
                    error = 1;
                    $item.removeClass('has-success');
                    $("#user-availability-status").html(data);
                    $item.addClass('has-error');
                }
                else {
                    error = 0;
                    $item.removeClass('has-error');
                    $("#user-availability-status").html("");
                    $item.addClass('has-success');
                }
                $("#loaderIcon").hide();
            },
            error: function () {
            }
        });
    }
    function checkAvailabilityEmail() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "<?php _esc($config['app_url'])?>global/check_availability.php",
            data: 'email=' + $("#email").val(),
            type: "POST",
            success: function (data) {
                if (data != "success") {
                    error = 1;
                    $("#email").removeClass('has-success');
                    $("#email-availability-status").html(data);
                    $("#email").addClass('has-error mar-zero');
                }
                else {
                    error = 0;
                    $("#email").removeClass('has-error mar-zero');
                    $("#email-availability-status").html("");
                    $("#email").addClass('has-success');
                }
                $("#loaderIcon").hide();
            },
            error: function () {
            }
        });
    }
    function checkAvailabilityPassword() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "<?php _esc($config['app_url'])?>global/check_availability.php",
            data: 'password=' + $("#Rpassword").val(),
            type: "POST",
            success: function (data) {
                if (data != "success") {
                    error = 1;
                    $("#Rpassword").removeClass('has-success');
                    $("#password-availability-status").html(data);
                    $("#Rpassword").addClass('has-error mar-zero');
                }
                else {
                    error = 0;
                    $("#Rpassword").removeClass('has-error mar-zero');
                    $("#password-availability-status").html("");
                    $("#Rpassword").addClass('has-success');
                }
                $("#loaderIcon").hide();
            },
            error: function () {
            }
        });
    }

</script>
<?php
overall_footer();
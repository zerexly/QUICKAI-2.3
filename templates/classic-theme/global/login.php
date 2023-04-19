<?php
overall_header(__("Login"));
?>
<?php print_adsense_code('header_bottom'); ?>
<!-- Titlebar
================================================== -->
<div id="titlebar" class="gradient">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php _e("Login") ?></h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><?php _e("Login") ?></li>
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
                    <h3><?php _e("Welcome Back!") ?></h3>
                    <span><?php _e("Don't have an account?") ?> <a href="<?php url("SIGNUP") ?>"><?php _e("Sign Up Now!") ?></a></span>
                </div>
                <?php if($config['facebook_app_id'] != "" || $config['google_app_id'] != ""){ ?>
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
                <!-- Form -->
                <?php
                if($error != ''){
                    echo '<span class="status-not-available">'.$error.'</span>';
                }
                ?>
                <form method="post">
                    <div class="input-with-icon-left">
                        <i class="la la-user"></i>
                        <input type="text" class="input-text with-border" name="username" id="username"
                        placeholder="<?php _e("Username") ?> / <?php _e("Email Address") ?>" required/>
                    </div>

                    <div class="input-with-icon-left">
                        <i class="la la-unlock"></i>
                        <input type="password" class="input-text with-border" name="password" id="password"
                        placeholder="<?php _e("Password") ?>" required/>
                    </div>
                    <a href="<?php url("LOGIN") ?>?fstart=1" class="forgot-password"><?php _e("Forgot Password?") ?></a>
                    <input type="hidden" name="ref" value="{REF}"/>
                    <button class="button full-width button-sliding-icon ripple-effect margin-top-10" name="submit" type="submit"><?php _e("Login") ?> <i class="icon-feather-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<div class="margin-top-70"></div>
<?php
overall_footer();
?>

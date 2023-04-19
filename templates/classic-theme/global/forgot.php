<?php
overall_header(__("Change Password"));
?>
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php _e("Change Password") ?></h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><?php _e("Change Password") ?></li>
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
                    <h3><?php _e("Change Password") ?></h3>
                </div>
                <?php
                if($forgot_error != ''){
                    echo '<span class="status-not-available">'._esc($forgot_error,false).'</span>';
                }
                ?>
                <form method="post">
                    <span class="status-available">
                        <strong><?php _e("Username") ?> : </strong> <?php _esc($username) ?>
                    </span>
                    <div class="input-with-icon-left">
                        <i class="la la-unlock"></i>
                        <input type="password" class="input-text with-border" name="password" id="password"
                        placeholder="<?php _e("Password") ?>" required/>
                    </div>
                    <div class="input-with-icon-left">
                        <i class="la la-unlock"></i>
                        <input type="password" class="input-text with-border" name="password2" id="password2"
                        placeholder="<?php _e("Confirm Password") ?>" required/>
                    </div>
                    <input type="hidden" name="forgot" id="forgot" value="<?php _esc($field_forgot) ?>">
                    <input type="hidden" name="r" id="r" value="<?php _esc($field_r) ?>">
                    <input type="hidden" name="e" id="e" value="<?php _esc($field_e) ?>">
                    <input type="hidden" name="t" id="t" value="<?php _esc($field_t) ?>">
                    <button class="button full-width button-sliding-icon ripple-effect margin-top-10" name="submit" type="submit"><?php _e("Change Password") ?> <i class="icon-feather-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="margin-top-70"></div>
<?php
overall_footer();
?>

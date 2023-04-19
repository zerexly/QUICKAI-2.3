<?php
overall_header(__("Report Violation"));
?>
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php _e("Report Violation") ?></h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><?php _e("Report Violation") ?></li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container margin-bottom-50">
    <div class="row"><!-- user-login -->
        <div class="col-xl-8 margin-0-auto">
            <div class="user-account clearfix">
                <form action="#" method="post">
                    <div class="submit-field">
                      <h5><?php _e("Your Name") ?></h5>
                      <input class="with-border" type="text" name="name" value="<?php _esc($name);?>">
                        <?php
                        if($name_error != ''){
                            echo '<span class="status-not-available">'.$name_error.'</span>';
                        }
                        ?>
                    </div>
                    <div class="submit-field">
                      <h5><?php _e("Your E-Mail") ?></h5>
                      <input class="with-border" type="email" name="email" value="<?php _esc($email);?>">
                        <?php
                        if($email_error != ''){
                            echo '<span class="status-not-available">'.$email_error.'</span>';
                        }
                        ?>
                    </div>
                    <div class="submit-field">
                      <h5><?php _e("Your Username") ?></h5>
                      <input class="with-border" type="text" name="username" value="<?php _esc($username);?>">
                    </div>
                    <div class="submit-field">
                        <h5><?php _e("Violation") ?> <?php _e("Type") ?></h5>
                        <select name="violation" class="selectpicker with-border">
                            <option><?php _e("Select") ?> <?php _e("Violation") ?> <?php _e("Type") ?></option>
                            <option value="<?php _e("Posting contact information") ?>"><?php _e("Posting contact information") ?></option>
                            <option value="<?php _e("Advertising another website") ?>"><?php _e("Advertising another website") ?></option>
                            <option value="<?php _e("Fake job posted") ?>"><?php _e("Fake job posted") ?></option>
                            <option value="<?php _e("Non-featured job posted requiring abnormal bidding") ?>"><?php _e("Non-featured job posted requiring abnormal bidding") ?></option>
                            <option value="<?php _e("Other") ?>"><?php _e("Other") ?></option>
                        </select>
                    </div>
                    <div class="submit-field">
                      <h5><?php _e("Username of other person") ?></h5>
                      <input class="with-border" type="text" name="username2" value="<?php _esc($username2);?>">
                    </div>
                    <div class="submit-field">
                      <h5><?php _e("URL of violation") ?></h5>
                      <input class="with-border" type="text" name="url" value="<?php _esc($redirect_url);?>">
                    </div>
                    <div class="submit-field">
                      <h5><?php _e("Violation Details") ?></h5>
                      <textarea class="with-border" name="details"><?php _esc($details);?></textarea>
                        <?php
                        if($viol_error != ''){
                            echo '<span class="status-not-available">'.$viol_error.'</span>';
                        }
                        ?>
                    </div>
                    <button type="submit" name="Submit" id="submit" class="button"><?php _e("Report Violation") ?></button>
                </form>
                <!-- checkbox -->
            </div>
        </div>
        <!-- user-login -->
    </div>
    <!-- row -->
</div>
<?php
overall_footer();
?>

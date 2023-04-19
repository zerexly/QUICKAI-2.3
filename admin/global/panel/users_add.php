<?php
require_once '../../includes.php';

?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php _e('Add User') ?></h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn-icon btn-primary" title="<?php _e('Save') ?>">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn-icon slidePanel-close" title="<?php _e('Close') ?>">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form method="post" data-ajax-action="addEditUser" id="sidePanel_form">
            <div class="form-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            <img src="<?php _esc(SITEURL) ?>/storage/profile/default_user.png" width="80" class="rounded" alt="">
                        </div>
                        <div class="col-md-10">
                            <label for="image"><?php _e("Profile Picture"); ?></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="image" name="image"
                                       accept="image/png, image/gif, image/jpeg">
                                <label class="custom-file-label" for="image"><?php _e("Choose file..."); ?></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_fullname"><?php _e("Full Name"); ?></label>
                    <input id="id_fullname" type="text" class="form-control" name="name" value="">
                </div>

                <div class="form-group">
                    <label><?php _e("Gender"); ?></label>
                    <select class="form-control" name="sex">
                        <option value="Male"><?php _e("Male"); ?></option>
                        <option value="Female"><?php _e("Female"); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php _e("Country"); ?></label>
                    <select class="form-control" name="country">
                        <?php
                        $country = get_country_list();
                        foreach ($country as $value){
                            echo '<option value="'.$value['asciiname'].'">'.$value['asciiname'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php _e("About Us"); ?></label>
                    <textarea name="about" rows="6" class="form-control tiny-editor" id="pageContent"></textarea>
                </div>
                <h5 class="text-primary m-t-35"><?php _e('Account Setting') ?></h5>
                <hr>
                <div class="form-group">
                    <label for="id_uname"><?php _e("Username"); ?></label>
                    <input id="id_uname" type="text" class="form-control" name="username" value="">
                </div>
                <div class="form-group">
                    <label for="id_email"><?php _e("Email address"); ?></label>
                    <input id="id_email" type="email" class="form-control" name="email" value="">
                </div>
                <div class="form-group">
                    <label for="id_pw2"><?php _e("New Password"); ?></label>
                    <input id="id_pw2" type="password" class="form-control" name="password">
                </div>
                <input type="hidden" name="submit">
            </div>
        </form>
    </div>
</div>
<script src="../assets/plugins/tinymce/tinymce.min.js"></script>
<script src="../assets/js/script.js"></script>
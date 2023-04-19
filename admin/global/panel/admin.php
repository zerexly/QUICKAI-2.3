<?php
require_once '../../includes.php';
$info = array(
    'id' => '',
    'name' => '',
    'username' => '',
    'email' => '',
    'image' => 'default_user.png'
);
if(isset($_GET['id']))
    $info = ORM::for_table($config['db']['pre'].'admins')->find_one($_GET['id']);
else
    $_GET['id'] = null;
?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Admin') : __('Add Admin'); ?></h2>
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
        <form method="post" id="sidePanel_form" data-ajax-action="addEditAdmin">
            <div class="form-body">
                <?php if(isset($_GET['id'])){ ?>
                    <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <?php } ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            <img src="<?php echo SITEURL.'storage/profile/'.$info['image']; ?>" width="80" class="rounded" alt="">
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
                    <label for="name"><?php _e("Full Name"); ?></label>
                    <input id="name" type="text" name="name" value="<?php echo $info['name']; ?>"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="username"><?php _e("Username"); ?></label>
                    <input id="username" type="text" name="username" value="<?php echo $info['username']; ?>"
                           class="form-control" autocomplete="new-username">
                </div>
                <div class="form-group">
                    <label for="email"><?php _e("Email"); ?></label>
                    <input id="email" type="text" name="email" value="<?php echo $info['email']; ?>"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="password"><?php _e("Password"); ?></label>
                    <input id="password" type="password" name="password" value="" class="form-control" autocomplete="new-password">
                </div>
                <input type="hidden" name="submit">
            </div>
        </form>
    </div>
</div>
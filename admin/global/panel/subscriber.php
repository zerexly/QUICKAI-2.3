<?php
require_once '../../includes.php';
$info = array(
    'id' => '',
    'email' => '',
);
if(isset($_GET['id']))
    $info = ORM::for_table($config['db']['pre'].'subscriber')->find_one($_GET['id']);
else
    $_GET['id'] = null;
?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Subscriber') : __('Add Subscriber'); ?></h2>
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
        <form method="post" data-ajax-action="addEditSubscriber" id="sidePanel_form">
            <?php if(isset($_GET['id'])){ ?>
                <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
            <?php } ?>
            <div class="form-body">
                <div class="form-group">
                    <label for="email"><?php _e('Email') ?></label>
                    <input id="email" type="text" name="email" value="<?php echo $info['email']; ?>"
                           class="form-control">
                </div>
                <input type="hidden" name="submit">
            </div>
        </form>
    </div>
</div>
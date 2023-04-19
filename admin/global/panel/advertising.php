<?php
if(empty($_GET['id'])){
    _e('Unexpected error, please try again.');
    die();
}

require_once '../../includes.php';

$info = array(
    'id' => '',
    'provider_name' => '',
    'large_track_code' => '',
    'tablet_track_code' => '',
    'phone_track_code' => '',
    'status' => '',
);
$info = ORM::for_table($config['db']['pre'].'adsense')->find_one(validate_input($_GET['id']));

?>

<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php _e('Edit Advertising'); ?></h2>
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
        <div id="post_error"></div>
        <form name="form2"  class="form form-horizontal" method="post" id="sidePanel_form"
              data-ajax-action="editAdvertise" >
            <div class="form-body">
                <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <div class="form-group">
                    <label for="provider_name"><?php _e('Title') ?></label>
                    <input name="provider_name" id="provider_name" type="text" class="form-control" value="<?php echo $info['provider_name']?>">
                </div>
                <div class="form-group">
                    <label for="large_track_code"><?php _e('Tracking Code (Large Format)') ?></label>
                    <textarea name="large_track_code" id="large_track_code" rows="2" type="text" class="form-control"><?php echo $info['large_track_code']?></textarea>
                </div>
                <div class="form-group">
                    <label for="tablet_track_code"><?php _e('Tracking Code (Tablet Format)') ?></label>
                    <textarea name="tablet_track_code" id="tablet_track_code" rows="2" type="text" class="form-control"><?php echo $info['tablet_track_code']?></textarea>
                </div>
                <div class="form-group">
                    <label for="phone_track_code"><?php _e('Tracking Code (Phone Format)') ?></label>
                    <textarea name="phone_track_code" id="phone_track_code" rows="2" type="text" class="form-control"><?php echo $info['phone_track_code']?></textarea>
                </div>
                <?php
                quick_switch(__('Status'),'status', $info['status']); ?>
            </div>
        </form>
    </div>
</div>
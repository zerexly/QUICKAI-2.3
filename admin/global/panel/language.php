<?php
require_once '../../includes.php';
$info = array(
    'id' => '',
    'code' => '',
    'direction' => '',
    'name' => '',
    'file_name' => '',
    'active' => ''
);
if(isset($_GET['id']))
    $info = get_language_by_id($_GET['id']);
else
    $_GET['id'] = null;
?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Language') : __('Add Language'); ?></h2>
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
        <form method="post" data-ajax-action="<?php echo isset($_GET['id']) ? "editLanguage" : "addLanguage"; ?>" id="sidePanel_form">
            <?php if(isset($_GET['id'])){ ?>
                <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
            <?php } ?>
            <div class="form-body">
                <div class="form-group">
                    <label><?php _e('Language name') ?> *</label>
                    <input type="text" name="name" value="<?php _esc($info['name']); ?>" class="form-control" required="">
                </div>
                <?php if(!isset($_GET['id'])){ ?>
                    <div class="form-group">
                        <label><?php _e('File name') ?> * <i class="icon-feather-help-circle" title="<?php _e("In english characters only"); ?>" data-tippy-placement="top"></i></label>
                        <input type="text" name="file_name" value="<?php _esc($info['file_name']); ?>" class="form-control" required="">
                    </div>
                <?php } ?>
                <div class="form-group">
                    <label><?php _e('Code (ISO 639-1)') ?> *</label>
                    <input type="text" name="code" value="<?php _esc($info['code']); ?>" class="form-control" required="">
                    <p class="help-block"><a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes</a> </p>
                </div>

                <div class="form-group">
                    <label><?php _e('Direction') ?> *</label>
                    <select name="direction" class="form-control">
                        <option value="ltr" <?php echo ($info['direction'] == "ltr")? "selected" : ""?>>ltr</option>
                        <option value="rtl" <?php echo ($info['direction'] == "rtl")? "selected" : ""?>>rtl</option>
                    </select>
                </div>

                <?php quick_switch(__('Activate'), 'active', $info['active'] == '1'); ?>

                <?php if(!isset($_GET['id'])){
                    quick_switch(__('Auto Google Translate'), 'auto_tran', false,__('Auto translate process take 2-3 minutes.'));
                } ?>

                <input type="hidden" name="submit">
            </div>
        </form>
    </div>
</div>
<script>
    $('.quick-select2').select2({tags: true});
</script>

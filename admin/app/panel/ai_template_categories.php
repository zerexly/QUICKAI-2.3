<?php
require_once '../../includes.php';

$info = array(
    'id' => '',
    'title' => '',
    'active' => '1',
);
if(!empty($_GET['id'])) {
    $info = ORM::for_table($config['db']['pre'] . 'ai_template_categories')->find_one(validate_input($_GET['id']));
}

?>

<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Category') : __('Add Category'); ?></h2>
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
              data-ajax-action="editAITplCategory" >
            <div class="form-body">
                <?php if(isset($_GET['id'])){ ?>
                    <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <?php } ?>
                <div class="form-group">
                    <label for="title"><?php _e('Title') ?> *</label>
                    <input name="title" id="title" type="text" class="form-control" value="<?php echo $info['title']?>" required>
                </div>
                <?php
                quick_switch(__('Active'),'active', $info['active']); ?>
            </div>
        </form>
    </div>
</div>
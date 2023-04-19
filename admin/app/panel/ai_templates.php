<?php
if(empty($_GET['id'])){
    _e('Unexpected error, please try again.');
    die();
}

require_once '../../includes.php';

$info = array(
    'id' => '',
    'title' => '',
    'category_id' => '',
    'description' => '',
    'icon' => '',
    'active' => '',
);
$info = ORM::for_table($config['db']['pre'].'ai_templates')->find_one(validate_input($_GET['id']));

?>

<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php _e('Edit AI Template'); ?></h2>
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
              data-ajax-action="editAITemplate" >
            <div class="form-body">
                <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <div class="form-group">
                    <label for="title"><?php _e('Title') ?></label>
                    <input name="title" id="title" type="text" class="form-control" value="<?php echo $info['title']?>">
                </div>
                <div class="form-group">
                    <label for="icon">
                        <?php _e('Icon') ?>
                        <i class="icon-feather-help-circle" title="<?php _e('You can use FontAwesome icons') ?>" data-tippy-placement="top"></i>
                    </label>
                    <input name="icon" id="icon" type="text" class="form-control" value="<?php echo $info['icon']?>">
                </div>
                <div class="form-group">
                    <label for="category"><?php _e('Category') ?></label>
                    <select id="category" name="category" class="form-control">
                        <?php
                        $categories = ORM::for_table($config['db']['pre'] . 'ai_template_categories')
                            ->where('active', '1')
                            ->order_by_asc('position')
                            ->find_array();
                        foreach ($categories as $category) {
                        ?>
                        <option value="<?php _esc($category['id']) ?>" <?php if($category['id'] == $info['category_id']) echo 'selected'; ?>><?php _esc($category['title']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description"><?php _e('Description') ?></label>
                    <textarea name="description" id="description" rows="4" type="text" class="form-control"><?php echo $info['description']?></textarea>
                </div>
                <?php
                quick_switch(__('Active'),'active', $info['active']); ?>
            </div>
        </form>
    </div>
</div>
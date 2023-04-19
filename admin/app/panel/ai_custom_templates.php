<?php
require_once '../../includes.php';

$info = array(
    'id' => '',
    'title' => '',
    'slug' => '',
    'category_id' => '',
    'description' => '',
    'prompt' => '',
    'icon' => 'fa fa-check-square',
    'active' => '1',
    'parameters' => '[{"title":"Text","type":"text","placeholder":"","options":""}]',
);
if(!empty($_GET['id'])) {
    $info = ORM::for_table($config['db']['pre'] . 'ai_custom_templates')->find_one(validate_input($_GET['id']));
}

?>

<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Custom Template') : __('Add Custom Template'); ?></h2>
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
              data-ajax-action="editAICustomTemplate" >
            <div class="form-body">
                <?php if(isset($_GET['id'])){ ?>
                    <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <?php } ?>
                <div class="form-group">
                    <label for="title"><?php _e('Title') ?> *</label>
                    <input name="title" id="title" type="text" class="form-control" value="<?php echo $info['title']?>" required>
                </div>
                <div class="form-group">
                    <label for="slug"><?php _e('Slug') ?></label>
                    <input name="slug" id="slug" type="text" class="form-control" value="<?php echo $info['slug']?>">
                    <small class="form-text text-muted"><?php _e('Use only alphanumeric value without space. (Hyphen(-) allow).'); ?></small>
                    <small class="form-text text-muted"><?php _e('Slug will be used for the template url.'); ?></small>
                </div>
                <div class="form-group">
                    <label for="icon">
                        <?php _e('Icon') ?>
                        <i class="icon-feather-help-circle" title="<?php _e('You can use FontAwesome icons') ?>" data-tippy-placement="top"></i>
                    </label>
                    <input name="icon" id="icon" type="text" class="form-control" value="<?php echo $info['icon']?>">
                </div>
                <div class="form-group">
                    <label for="category"><?php _e('Category') ?> *</label>
                    <select id="category" name="category" class="form-control" required>
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
                    <label for="description"><?php _e('Description') ?> *</label>
                    <textarea name="description" id="description" rows="3" type="text" class="form-control" required><?php echo $info['description']?></textarea>
                </div>
                <div class="form-group">
                    <label for="prompt"><?php _e('Prompt') ?> *</label>
                    <textarea name="prompt" id="prompt" rows="4" type="text" class="form-control" required><?php echo $info['prompt']?></textarea>
                    <small><?php _e('Use {{input title}} shortcode in the prompt for the custom input value') ?></small>
                </div>
                <div class="form-group">
                    <label><?php _e('Custom Inputs') ?> *</label>
                    <div class="custom-inputs">
                        <?php
                        if(!empty($info['parameters'])) {
                        $parameters = json_decode($info['parameters'], true);
                        foreach ($parameters as $parameter) {
                        ?>
                            <div class="custom-input-wrapper mb-3">
                                <div class="d-flex align-items-center mb-1">
                                    <input class="form-control mr-1" title="<?php _e('Title') ?>"
                                           name="parameter_title[]" type="text" placeholder="<?php _e('Title') ?>"
                                           value="<?php _esc($parameter['title']) ?>">
                                    <select class="form-control mr-2 field-type"
                                            title="<?php _e('Select Field Type') ?>" name="parameter_type[]">
                                        <option value="text" <?php echo 'text' == $parameter['type'] ? 'selected' : '' ?>><?php _e('Text Field') ?></option>
                                        <option value="textarea" <?php echo 'textarea' == $parameter['type'] ? 'selected' : '' ?>><?php _e('Textarea Field') ?></option>
                                        <option value="select" <?php echo 'select' == $parameter['type'] ? 'selected' : '' ?>><?php _e('Select List Field') ?></option>
                                    </select>
                                    <a href="#" class="text-danger delete-parameter" title="<?php _e('Delete') ?>"
                                       data-tippy-placement="top"><i class="icon-feather-trash-2"></i></a>
                                </div>
                                <input class="form-control mr-1 placeholder-field" title="<?php _e('Placeholder') ?>"
                                       name="parameter_placeholder[]" type="text"
                                       placeholder="<?php _e('Placeholder') ?>"
                                       value="<?php _esc($parameter['placeholder']) ?>" <?php echo $parameter['type'] == 'select' ? 'style="display: none"' : ''; ?>>
                                <div class="options-field" <?php echo $parameter['type'] != 'select' ? 'style="display: none"' : ''; ?>>
                                    <input class="form-control mr-1" title="<?php _e('options') ?>"
                                           name="parameter_options[]" type="text" placeholder="<?php _e('Options') ?>"
                                           value="<?php _esc($parameter['options']) ?>">
                                    <small class="text-muted"><?php _e('Enter comma separated values for the select list.') ?></small>
                                </div>
                            </div>
                        <?php }
                        } ?>
                    </div>
                    <button class="btn btn-primary btn-sm" type="button" id="add-parameter"><i class="icon-feather-plus"></i> <?php _e('Add Field'); ?></button>
                </div>
                <?php
                quick_switch(__('Active'),'active', $info['active']); ?>
            </div>
        </form>
    </div>
</div>
<script>
    $('#add-parameter').off('click').on('click', function (e){
        e.preventDefault();

        $('.custom-inputs').append(
            $('<div class="custom-input-wrapper mb-3">' +
                '<div class="d-flex align-items-center mb-1">' +
                '<input class="form-control mr-1" title="<?php _e('Title') ?>" name="parameter_title[]" type="text" placeholder="<?php _e('Title') ?>">' +
                '<select class="form-control field-type mr-2" title="<?php _e('Select Field Type') ?>" name="parameter_type[]">' +
                '<option value="text"><?php _e('Text Field') ?></option>' +
                '<option value="textarea"><?php _e('Textarea Field') ?></option>' +
                '<option value="select"><?php _e('Select List Field') ?></option></select>' +
                '<a href="#" class="text-danger delete-parameter" title="<?php _e('Delete') ?>" data-tippy-placement="top"><i class="icon-feather-trash-2"></i></a>' +
                '</div>' +
                `<input class="form-control mr-1 placeholder-field"
                title="<?php _e('Placeholder') ?>"
                name="parameter_placeholder[]" type="text"
                placeholder="<?php _e('Placeholder') ?>"
                value="">
                <div class="options-field" style="display: none"'>
                <input class="form-control mr-1" title="<?php _e('options') ?>" name="parameter_options[]" type="text" placeholder="<?php _e('Options') ?>" value="">` +
                '<small class="text-muted"><?php _e('Enter comma separated values for the select list.') ?></small>' +
                '</div>' +
                '</div>')
        );
    });

    $('.custom-inputs').on('click', '.delete-parameter', function (e){
        e.preventDefault();
        $(this).parents('.custom-input-wrapper').remove();
    })

    .on('change', '.field-type', function (e){
        if($(this).val() == 'select') {
            $(this).parents('.custom-input-wrapper').find('.placeholder-field').hide();
            $(this).parents('.custom-input-wrapper').find('.options-field').show();
        } else {
            $(this).parents('.custom-input-wrapper').find('.placeholder-field').show();
            $(this).parents('.custom-input-wrapper').find('.options-field').hide();
        }
    });
</script>
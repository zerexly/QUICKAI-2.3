<?php
require_once '../../includes.php';

$info = array(
    'id' => '',
    'title' => '',
    'chat_bots' => '',
    'description' => '',
    'prompt' => '',
    'active' => '1',
);
if (!empty($_GET['id'])) {
    $info = ORM::for_table($config['db']['pre'] . 'ai_chat_prompts')->find_one(validate_input($_GET['id']));
    $info['translations'] = json_decode((string)$info['translations'], true);
}

$chat_bots = ORM::for_table($config['db']['pre'] . 'ai_chat_bots')
    ->where('active', 1)
    ->order_by_asc('position')
    ->find_array();

$languages = get_language_list('', 'selected', true);
?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Chat Prompts') : __('Add Chat Prompts'); ?></h2>
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
        <form method="post" data-ajax-action="editAIChatPrompts" id="sidePanel_form">
            <div class="form-body">
                <?php if (isset($_GET['id'])) { ?>
                    <input type="hidden" name="id" value="<?php _esc($_GET['id']) ?>">
                <?php } ?>

                <div class="form-group">
                    <label class="d-flex align-items-end" for="title">
                        <?php _e('Title') ?>
                        <div class="d-flex align-items-center translate-picker">
                            <i class="fa fa-language"></i>
                            <select class="custom-select custom-select-sm ml-1">
                                <option value="default"><?php _e('Default') ?></option>
                                <?php foreach ($languages as $l) { ?>
                                    <option value="<?php _esc($l['code']) ?>"><?php _esc($l['name']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </label>
                    <div class="translate-fields translate-fields-default">
                        <input id="title" type="text" class="form-control" name="title"
                               value="<?php _esc($info['title']) ?>">
                    </div>
                    <?php foreach ($languages as $l) { ?>
                        <div class="translate-fields translate-fields-<?php _esc($l['code']) ?>" style="display: none">
                            <input type="text" class="form-control"
                                   name="translations[<?php _esc($l['code']) ?>][title]"
                                   value="<?php echo !empty($info['translations'][$l['code']]['title']) ? $info['translations'][$l['code']]['title'] : $info['title'] ?>"
                                   required>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label class="d-flex align-items-end" for="description">
                        <?php _e('Description') ?>
                        <div class="d-flex align-items-center translate-picker">
                            <i class="fa fa-language"></i>
                            <select class="custom-select custom-select-sm ml-1">
                                <option value="default"><?php _e('Default') ?></option>
                                <?php foreach ($languages as $l) { ?>
                                    <option value="<?php _esc($l['code']) ?>"><?php _esc($l['name']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </label>
                    <div class="translate-fields translate-fields-default">
                        <textarea name="description" rows="2" class="form-control"
                                  id="description"><?php _esc($info['description']) ?></textarea>
                    </div>
                    <?php foreach ($languages as $l) { ?>
                        <div class="translate-fields translate-fields-<?php _esc($l['code']) ?>" style="display: none">
                            <textarea rows="2" class="form-control"
                                      name="translations[<?php _esc($l['code']) ?>][description]"><?php echo !empty($info['translations'][$l['code']]['description']) ? $info['translations'][$l['code']]['description'] : $info['description'] ?></textarea>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="chat_bots"><?php _e("Chat Bots"); ?></label>
                    <select id="chat_bots" class="form-control quick-select2" name="chat_bots[]" multiple>
                        <?php
                        $bots = explode(',', $info['chat_bots']);
                        if (get_option("enable_default_chat_bot", 1)) { ?>
                            <option value="default" <?php echo in_array('default', $bots) ? 'selected' : '' ?>><?php _e('Default Bot'); ?></option>
                        <?php } ?>
                        <?php
                        foreach ($chat_bots as $chat_bot) {
                            echo '<option value="' . $chat_bot['id'] . '" ' . (in_array($chat_bot['id'], $bots) ? 'selected' : '') . '>' . $chat_bot['name'] . '</option>';
                        }
                        ?>
                    </select>
                    <span class="form-text text-muted"><?php _e('Leave empty for all bots.') ?></span>
                </div>
                <div class="form-group">
                    <label for="prompt"><?php _e("Prompt"); ?></label>
                    <textarea name="prompt" rows="2" class="form-control"
                              id="prompt"><?php _esc($info['prompt']) ?></textarea>
                </div>
                <?php
                quick_switch(__('Active'), 'active', $info['active']); ?>
            </div>
        </form>
    </div>
</div>
<script>
    $('.quick-select2').select2();

    // translate picker
    $(document).off('change', ".translate-picker select").on('change', ".translate-picker select", function (e) {
        $('.translate-fields').hide();
        $('.translate-fields-' + $(this).val()).show();
        $('.translate-picker select').val($(this).val());
    });
</script>

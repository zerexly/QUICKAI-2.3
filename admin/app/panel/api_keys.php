<?php
require_once '../../includes.php';

$info = array(
    'id' => '',
    'title' => '',
    'api_key' => '',
    'type' => 'openai',
    'active' => '1',
);
if (!empty($_GET['id'])) {
    $info = ORM::for_table($config['db']['pre'] . 'api_keys')->find_one(validate_input($_GET['id']));
}

?>

<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php _e('Edit API Key'); ?></h2>
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
        <form name="form2" class="form form-horizontal" method="post" id="sidePanel_form"
              data-ajax-action="editAPIKey">
            <div class="form-body">
                <?php if (isset($_GET['id'])) { ?>
                    <input type="hidden" name="id" value="<?php _esc($_GET['id']) ?>">
                <?php } ?>
                <div class="form-group">
                    <label for="title"><?php _e('Title') ?> *</label>
                    <input name="title" id="title" type="text" class="form-control"
                           value="<?php echo $info['title'] ?>">
                    <span class="form-text text-muted"><?php _e('Internal only to identify it later.') ?></span>
                </div>
                <div class="form-group">
                    <label for="type"><?php _e('Type') ?> *</label>
                    <select name="type" id="type" class="form-control api-type">
                        <option value="openai" <?php echo 'openai' == $info['type'] ? 'selected' : '' ?>><?php _e('OpenAI'); ?></option>
                        <option value="stable-diffusion" <?php echo 'stable-diffusion' == $info['type'] ? 'selected' : '' ?>><?php _e('Stable Diffusion'); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="api_key"><?php _e('API Key') ?> *</label>
                    <?php if (check_allow()) { ?>
                        <input name="api_key" id="api_key" type="text" class="form-control"
                               value="<?php echo $info['api_key'] ?>" required>
                    <?php } else { ?>
                        <input type="text" class="form-control" value="***********">
                    <?php } ?>
                    <span class="form-text text-muted api-hint hint-openai">
                        <a href="https://platform.openai.com/account/api-keys"
                                target="_blank"><?php _e('Get your OpenAI API key'); ?></a>
                    </span>
                    <span class="form-text text-muted api-hint hint-stable-diffusion">
                        <a href="https://platform.stability.ai/docs/getting-started/authentication"
                                target="_blank"><?php _e('Get your Stable Diffusion API key'); ?></a>
                    </span>
                </div>
                <?php
                quick_switch(__('Active'), 'active', $info['active']); ?>
            </div>
        </form>
    </div>
</div>
<script>
    $('.api-type').off('change').on('change', function () {
        $('.api-hint').hide();
        $('.hint-' + $(this).val()).show();
    }).trigger('change');
</script>
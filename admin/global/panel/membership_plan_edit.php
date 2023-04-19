<?php
include '../datatable-json/includes.php';

switch ($_GET['id']) {
    case 'free':
        $info = json_decode(get_option('free_membership_plan'), true);
        $settings = $info['settings'];
        break;
    case 'trial':
        $info = json_decode(get_option('trial_membership_plan'), true);
        $settings = $info['settings'];
        break;
    default:
        $info = ORM::for_table($config['db']['pre'] . 'plans')
            ->where('id', $_GET['id'])
            ->find_one();
        $settings = json_decode($info['settings'], true);
        break;
}

?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php _e('Edit Plan') ?></h2>
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
        <form method="post" id="sidePanel_form" data-ajax-action="editMembershipPlan">
            <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
            <div class="form-body">
                <?php quick_switch(__('Activate'), 'active', ($info['status'] == '1')); ?>
                <div class="form-group">
                    <label for="name"><?php _e('Plan Name') ?>*</label>
                    <input id="name" name="name" type="text" class="form-control" value="<?php _esc($info['name']); ?>">
                </div>
                <div class="form-group d-none">
                    <label for="badge"><?php _e('Plan Badge') ?></label>
                    <input id="badge" name="badge" type="text" class="form-control"
                           value="<?php _esc($info['badge']); ?>">
                    <span class="form-text text-muted"><?php _e('Paste Image Url, This badge will display in user profile after username.') ?></span>
                </div>
                <?php if ($_GET['id'] != 'free' && $_GET['id'] != 'trial') { ?>
                    <div class="form-group">
                        <label for="monthly_price"><?php _e('Monthly Price') ?>*</label>
                        <input name="monthly_price" type="number" class="form-control" id="monthly_price"
                               value="<?php _esc($info['monthly_price']); ?>">
                        <span class="form-text text-muted"><?php _e('Set 0 to disable it.') ?></span>
                    </div>
                    <div class="form-group">
                        <label for="annual_price"><?php _e('Annual Price') ?>*</label>
                        <input name="annual_price" type="number" class="form-control" id="annual_price"
                               value="<?php _esc($info['annual_price']); ?>">
                        <span class="form-text text-muted"><?php _e('Set 0 to disable it.') ?></span>
                    </div>
                    <div class="form-group">
                        <label for="lifetime_price"><?php _e('Lifetime Price') ?>*</label>
                        <input name="lifetime_price" type="number" class="form-control" id="lifetime_price"
                               value="<?php _esc($info['lifetime_price']); ?>">
                        <span class="form-text text-muted"><?php _e('Set 0 to disable it.') ?></span>
                    </div>
                    <?php
                    quick_switch(__('Recommended'), 'recommended', ($info['recommended'] == 'yes'));
                }
                if ($_GET['id'] == 'trial') { ?>
                    <div class="form-group">
                        <label for="days"><?php _e('Days') ?></label>
                        <input name="days" type="number" class="form-control" id="days"
                               value="<?php _esc($info['days']); ?>">
                        <span class="form-text text-muted"><?php _e('The number of days that the trial plan can be used.') ?></span>
                    </div>
                <?php } ?>
                <h5 class="m-t-35"><?php _e('Plan Settings') ?></h5>
                <hr>
                <?php if (!get_option('single_model_for_plans')) { ?>
                    <div class="form-group">
                        <label for="ai_model"><?php _e('OpenAI Model') ?></label>
                        <select id="ai_model" class="form-control" name="ai_model">
                            <?php
                            $selected_model = $settings['ai_model'];
                            foreach (get_opeai_models() as $key => $model) { ?>
                                <option value="<?php _esc($key) ?>" <?php echo $key == $selected_model ? 'selected' : '' ?>><?php _esc($model) ?></option>
                            <?php } ?>
                        </select>
                        <span class="form-text text-muted"><?php _e('Select the AI model.') ?> <a
                                    href="https://platform.openai.com/docs/models/gpt-3"
                                    target="_blank"><?php _e('Read more here.') ?></a></span>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <label for="ai_templates"><?php _e('AI Templates') ?></label>
                    <select class="form-control quick-multi-select" id="ai_templates" name="ai_templates[]" multiple>
                        <?php
                        $ai_templates = get_ai_templates();
                        foreach ($ai_templates as $category) {
                            echo "<optgroup label='{$category['title']}'>";
                            foreach ($category['templates'] as $template) {
                                echo '<option value="' . $template['slug'] . '" ' . (in_array($template['slug'], $settings['ai_templates']) ? 'selected' : '') . '>' . $template['title'] . '</option>';
                            }
                            echo '</optgroup>';
                        }
                        ?>
                    </select>

                    <span class="form-text text-muted"><?php _e('Select AI templates for this plan.') ?></span>
                </div>
                <div class="form-group">
                    <label for="ai_chat"><?php _e('AI Chat') ?></label>
                    <select id="ai_chat" class="form-control" name="ai_chat">
                        <option value="0" <?php echo '0' == $settings['ai_chat'] ? 'selected' : '' ?>><?php _e('Disallow') ?></option>
                        <option value="1" <?php echo '1' == $settings['ai_chat'] ? 'selected' : '' ?>><?php _e('Allow') ?></option>
                    </select>
                    <span class="form-text text-muted"><?php _e('Allow AI Chat for this plan\'s users.') ?></span>
                    <span class="form-text text-warning"><?php _e('<strong>ChatGPT</strong> OpenAI model is required for this feature.') ?></span>
                    <?php if (!get_option('enable_ai_chat')) { ?>
                        <small class="text-danger"><?php _e('AI chat is disabled, please enable it from the OpenAI settings to use it.'); ?></small>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="ai_code"><?php _e('AI Code') ?></label>
                    <select id="ai_code" class="form-control" name="ai_code">
                        <option value="0" <?php echo '0' == $settings['ai_code'] ? 'selected' : '' ?>><?php _e('Disallow') ?></option>
                        <option value="1" <?php echo '1' == $settings['ai_code'] ? 'selected' : '' ?>><?php _e('Allow') ?></option>
                    </select>
                    <span class="form-text text-muted"><?php _e('Allow AI Code for this plan\'s users.') ?></span>
                    <?php if (!get_option('enable_ai_code')) { ?>
                        <small class="text-danger"><?php _e('AI Code is disabled, please enable it from the OpenAI settings to use it.'); ?></small>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="ai_speech_to_text_limit"><?php _e('Speech to Text Per Month') ?></label>
                    <input name="ai_speech_to_text_limit" type="number" class="form-control"
                           id="ai_speech_to_text_limit" value="<?php _esc($settings['ai_speech_to_text_limit']) ?>">
                    <span class="form-text text-muted"><?php _e('Set -1 for unlimited.') ?></span>
                    <?php if (!get_option('enable_speech_to_text')) { ?>
                        <small class="text-danger"><?php _e('Speech to Text is disabled, please enable it from the OpenAI settings to use it.'); ?></small>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="ai_speech_to_text_file_limit"><?php _e('Speech to Text File Size Limit') ?></label>
                    <input name="ai_speech_to_text_file_limit" type="number" class="form-control"
                           id="ai_speech_to_text_file_limit"
                           value="<?php _esc($settings['ai_speech_to_text_file_limit']) ?>">
                    <span class="form-text text-muted"><?php _e('Set file size limit for the file in MB. Set -1 for unlimited.') ?></span>
                </div>
                <div class="form-group">
                    <label for="ai_words_limit"><?php _e('AI Words Per Month') ?></label>
                    <input name="ai_words_limit" type="number" class="form-control" id="ai_words_limit"
                           value="<?php _esc($settings['ai_words_limit']) ?>">
                    <span class="form-text text-muted"><?php _e('Set -1 for unlimited.') ?></span>
                </div>
                <div class="form-group">
                    <label for="ai_images_limit"><?php _e('AI Images Per Month') ?></label>
                    <input name="ai_images_limit" type="number" class="form-control" id="ai_images_limit"
                           value="<?php _esc($settings['ai_images_limit']) ?>">
                    <span class="form-text text-muted"><?php _e('Set -1 for unlimited.') ?></span>
                </div>
                <?php quick_switch(__('Show Ads'), 'show_ads', $settings['show_ads'], __('Show ads to this plan\'s users')); ?>
                <?php
                if (get_option('enable_live_chat') && get_option('tawkto_membership')) {
                    quick_switch(__('Live Chat Support'), 'live_chat', $settings['live_chat'], __('Enable live chat for this plan\'s users'));
                }
                ?>
                <?php
                $plan_custom = ORM::for_table($config['db']['pre'] . 'plan_options')
                    ->where('active', 1)
                    ->order_by_asc('position')
                    ->find_array();
                if (!empty($plan_custom)) { ?>
                    <h5 class="m-t-35"><?php _e('Custom Settings') ?></h5>
                    <hr>
                    <?php
                    foreach ($plan_custom as $custom) {
                        if (!empty($custom['title']) && trim($custom['title']) != '') {
                            quick_switch(_esc($custom['title'], false), 'custom_' . $custom['id'], (isset($settings['custom'][$custom['id']]) && $settings['custom'][$custom['id']] == '1'));
                            ?>
                            <?php
                        }
                    }
                }
                ?>
                <h5 class="m-t-35"><?php _e('Taxes') ?></h5>
                <hr>
                <div class="form-group">
                    <label><?php _e('Select Taxes') ?></label>
                    <select class="form-control quick-select2" name="taxes[]" multiple>
                        <?php
                        $plan_taxes = explode(',', (string)$info['taxes_ids']);
                        $taxes = ORM::for_table($config['db']['pre'] . 'taxes')
                            ->find_many();
                        foreach ($taxes as $tax) {
                            $value = ($tax['value_type'] == 'percentage' ? (float)$tax['value'] . '%' : price_format($tax['value']));
                            echo '<option value="' . $tax['id'] . '" ' . (in_array($tax['id'], $plan_taxes) ? 'selected' : '') . '>' . $tax['name'] . ' (' . $value . ')</option>';
                        }
                        ?>
                    </select>
                    <span class="form-text text-muted"><?php _e('Select taxes for this plan.') ?></span>
                </div>
                <input type="hidden" name="submit">
            </div>
        </form>
    </div>
</div>
<link rel="stylesheet" href="<?php _esc($config['site_url']) ?>admin/assets/css/jquery.multiselect.css"/>
<script src="<?php _esc($config['site_url']) ?>admin/assets/js/jquery.multiselect.js"></script>
<script>
    $('.quick-select2').select2();
    $('.quick-multi-select').multiselect({
        showCheckbox: false,  // display the checkbox to the user
        search: true, // include option search box
        selectAll: true, // add select all option
        minHeight: 20,
        maxPlaceholderOpts: 2
    });
</script>
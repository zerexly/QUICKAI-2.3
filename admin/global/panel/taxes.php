<?php
include '../datatable-json/includes.php';

$info = array(
    'id' => '',
    'internal_name' => '',
    'name' => '',
    'description' => '',
    'value' => '',
    'value_type' => '',
    'type' => '',
    'billing_type' => '',
    'countries' => ''
);
if(isset($_GET['id']))
    $info = ORM::for_table($config['db']['pre'].'taxes')->find_one($_GET['id']);
else
    $_GET['id'] = null;
?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Tax') : __('Add Tax'); ?></h2>
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
        <form method="post" data-ajax-action="addEditTax" id="sidePanel_form">
            <?php if(isset($_GET['id'])){ ?>
                <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
            <?php } ?>
            <div class="form-body">
                <div class="form-group">
                    <label for="internal_name"><?php _e('Internal Name') ?></label>
                    <input type="text" id="internal_name" name="internal_name" value="<?php _esc($info['internal_name']) ?>" class="form-control">
                    <span class="form-text text-muted"><?php _e('Only visible in the admin.') ?></span>
                </div>
                <div class="form-group">
                    <label for="name"><?php _e('Name') ?></label>
                    <input type="text" id="name" name="name" value="<?php _esc($info['name']) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="description"><?php _e('Description') ?></label>
                    <input type="text" id="description" name="description" value="<?php _esc($info['description']) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="value"><?php _e('Tax Value') ?></label>
                    <input type="number" id="value" name="value" value="<?php _esc($info['value']) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="value_type"><?php _e('Value Type') ?></label>
                    <select id="value_type" name="value_type" class="form-control">
                        <option value="percentage" <?php echo ($info['value_type'] == 'percentage') ? 'selected': ''; ?>><?php _e('Percentage') ?></option>
                        <option value="fixed" <?php echo ($info['value_type'] == 'fixed') ? 'selected': ''; ?>><?php _e('Fixed') ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type"><?php _e('Type') ?></label>
                    <select name="type" id="type" class="form-control">
                        <option value="inclusive" <?php echo ($info['type'] == 'inclusive')? 'selected': '';?>><?php _e('Inclusive') ?></option>
                        <option value="exclusive" <?php echo ($info['type'] == 'exclusive')? 'selected': '';?>><?php _e('Exclusive') ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="billing_type"><?php _e('Billing for') ?></label>
                    <select id="billing_type" name="billing_type" class="form-control">
                        <option value="personal" <?php echo ($info['billing_type'] == 'personal')? 'selected': ''; ?>><?php _e('Personal') ?></option>
                        <option value="business" <?php echo ($info['billing_type'] == 'business')? 'selected': ''; ?>><?php _e('Business') ?></option>
                        <option value="both" <?php echo ($info['billing_type'] == 'both')? 'selected': ''; ?>><?php _e('Both') ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="countries"><?php _e('Country') ?></label>
                    <select id="countries" class="form-control quick-select2" name="countries[]" multiple>
                        <?php
                        $tax_countries = explode(',', $info['countries']);
                        $country = get_country_list();
                        foreach ($country as $value){
                            echo '<option value="'.$value['code'].'" '. (in_array($value['code'], $tax_countries)? 'selected':'') .'>'.$value['asciiname'].'</option>';
                        }
                        ?>
                    </select>
                    <span class="form-text text-muted"><?php _e('Leave empty for all countries.') ?></span>
                </div>
                <input type="hidden" name="submit">
            </div>
        </form>
    </div>
</div>
<script>
    $('.quick-select2').select2({tags: true});
</script>
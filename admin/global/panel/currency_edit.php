<?php
include '../datatable-json/includes.php';

$code = $name = $html_entity = $font_arial = $font_code2000 = $unicode_decimal = $unicode_hex = $in_left =
    $decimal_places = $decimal_separator = $thousand_separator = '';
if(isset($_GET['id'])){
    $info = ORM::for_table($config['db']['pre'].'currencies')->find_one($_GET['id']);
    $code = $info['code'];
    $name = $info['name'];
    $html_entity = $info['html_entity'];
    $font_arial = $info['font_arial'];
    $font_code2000 = $info['font_code2000'];
    $unicode_decimal = $info['unicode_decimal'];
    $unicode_hex = $info['unicode_hex'];
    $in_left = $info['in_left'];
    $decimal_places = $info['decimal_places'];
    $decimal_separator = $info['decimal_separator'];
    $thousand_separator = $info['thousand_separator'];
}
?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Currency') : __('Add Currency'); ?></h2>
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
        <form method="post" data-ajax-action="addEditCurrency" id="sidePanel_form">
            <div class="form-body">
                <?php if(isset($_GET['id'])){ ?>
                    <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <?php } ?>
                <div class="form-group">
                    <label for="code"><?php _e('Currency Code') ?></label>
                    <input id="code" type="text" name="code" value="<?php _esc($code); ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="name"><?php _e('Name') ?></label>
                    <input id="name" type="text" name="name" value="<?php _esc($name); ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="html_entity"><?php _e('Html Entity') ?></label>
                    <input id="html_entity" type="text" name="html_entity" value="<?php _esc($html_entity); ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="font_arial"><?php _e('Font Arial') ?></label>
                    <input id="font_arial" type="text" name="font_arial" value="<?php _esc($font_arial); ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="font_code2000"><?php _e('Font Code2000') ?></label>
                    <input id="font_code2000" type="text" name="font_code2000" value="<?php _esc($font_code2000); ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="unicode_decimal"><?php _e('Unicode Decimal') ?></label>
                    <input id="unicode_decimal" type="text" name="unicode_decimal" value="<?php _esc($unicode_decimal); ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="unicode_hex"><?php _e('Unicode Hex') ?></label>
                    <input id="unicode_hex" type="text" name="unicode_hex" value="<?php _esc($unicode_hex); ?>" class="form-control">
                </div>
                <?php quick_switch(__('Symbol in left'), 'in_left', ($in_left == 1)); ?>
                <div class="form-group">
                    <label for="decimal_places"><?php _e('Decimal Places') ?></label>
                    <input id="decimal_places" type="text" name="decimal_places" value="<?php _esc($decimal_places); ?>" class="form-control">
                    <span class="form-text text-muted"><?php _e('Number after decimal. Ex: 2 => 150.00 [or] 3 => 150.000') ?></span>
                </div>
                <!-- text input -->
                <div class="form-group">
                    <label for="decimal_separator"><?php _e('Decimal Separator') ?></label>
                    <input id="decimal_separator" type="text" name="decimal_separator" value="<?php _esc($decimal_separator); ?>" maxlength="1" class="form-control">
                    <span class="form-text text-muted"><?php _e('Ex: "." => 100.00 [or] "," => 100,00') ?></span>
                </div>

                <!-- text input -->
                <div class="form-group">
                    <label for="thousand_separator"><?php _e('Thousand Separator') ?></label>
                    <input id="thousand_separator" type="text" name="thousand_separator" value="<?php _esc($thousand_separator); ?>" maxlength="1" class="form-control">
                    <span class="form-text text-muted"><?php _e('Ex: "," => 100,000.00 [or] whitespace => 100 000.000') ?></span>
                </div>
                <input type="hidden" name="submit">
            </div>
        </form>
    </div>
</div>
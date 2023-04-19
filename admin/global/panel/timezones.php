<?php
include '../datatable-json/includes.php';

$country_code = $time_zone_id = $gmt = $dst = $raw = '';
if(isset($_GET['id'])) {
    $info = ORM::for_table($config['db']['pre'] . 'time_zones')->find_one($_GET['id']);
    $country_code = $info['country_code'];
    $time_zone_id = $info['time_zone_id'];
    $gmt = $info['gmt'];
    $dst = $info['dst'];
    $raw = $info['raw'];
}
?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Timezone') : __('Add Timezone'); ?></h2>
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
        <form method="post" data-ajax-action="addEditTimezone" id="sidePanel_form">
            <div class="form-body">
                <?php if(isset($_GET['id'])){ ?>
                    <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <?php } ?>
                <div class="form-group">
                    <label for="country_code"><?php _e('Country') ?> *</label>
                    <select id="country_code" class="form-control quick-select2" name="country_code" required>
                        <?php $country = get_country_list($country_code,"selected",false);

                        foreach ($country as $value){
                            echo '<option value="'.$value['code'].'" '.$value['selected'].'>'.$value['asciiname'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="time_zone_id"><?php _e('Time Zone') ?> *</label>
                    <input type="text" id="time_zone_id" name="time_zone_id" value="<?php _esc($time_zone_id); ?>" class="form-control" required="">
                    <span class="form-text text-muted"><?php _e('Please check the TimeZone Id code format here:') ?> <a href="http://download.geonames.org/export/dump/timeZones.txt" target="_blank">http://download.geonames.org/export/dump/timeZones.txt</a></span>

                </div>
                <div class="form-group">
                    <label for="gmt"><?php _e('GMT') ?> *</label>
                    <input type="text" id="gmt" name="gmt" value="<?php _esc($gmt); ?>" class="form-control" required="">
                </div>
                <div class="form-group">
                    <label for="dst"><?php _e('DST') ?> *</label>
                    <input type="text" id="dst" name="dst" value="<?php _esc($dst); ?>" class="form-control" required="">
                </div>
                <div class="form-group">
                    <label for="raw"><?php _e('RAW') ?></label>
                    <input type="text" id="raw" name="raw" value="<?php _esc($raw); ?>" class="form-control">
                </div>
                <input type="hidden" name="submit">
            </div>

        </form>
    </div>
</div>
<script>
    $('.quick-select2').select2();
</script>
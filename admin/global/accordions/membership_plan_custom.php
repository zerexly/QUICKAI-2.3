<?php
include '../datatable-json/includes.php';

global $config;

$rows = ORM::for_table($config['db']['pre'].'plan_options')
    ->order_by_asc('position')
    ->find_array();

if(!empty($rows)){
    foreach ($rows as $row) {
        ?>
        <div class="card quick-card quick-accordion-card quick-reorder-element" data-id="<?php _esc($row['id']) ?>">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="icon-feather-menu quick-reorder-icon m-r-5"
                       title="Reorder"></i>
                    <button class="btn btn-link pl-0" data-toggle="collapse"
                            data-target="#custom_<?php _esc($row['id']) ?>"
                            aria-expanded="false" aria-controls="custom_<?php _esc($row['id']) ?>"
                            type="button">

                        <?php _esc($row['title']); ?>
                    </button>
                </h5>
                <div class="card-header-right">
                    <div class="checkbox">
                        <input type="checkbox" id="check_<?php _esc($row['id']) ?>" value="<?php _esc($row['id']) ?>" class="quick-check">
                        <label for="check_<?php _esc($row['id']) ?>"><span class="checkbox-icon"></span></label>
                    </div>
                </div>
            </div>
            <div class="collapse" id="custom_<?php _esc($row['id']) ?>"
                 aria-labelledby="custom_<?php _esc($row['id']) ?>" data-parent="#accordion">
                <div class="card-body">
                    <form method="get" id="<?php _esc($row['id']); ?>" class="ajax_submit_form" data-action="editPlanCustom">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="title_<?php _esc($row['id']); ?>"><?php _e("Title"); ?></label>
                                    <input name="title" value="<?php _esc($row['title']); ?>" id="title_<?php _esc($row['id']); ?>" class="form-control" type="text">
                                    <input name="id" value="<?php _esc($row['id']); ?>" type="hidden">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="status_<?php _esc($row['id']); ?>"><?php _e("Enable/Disable"); ?></label>
                                    <select name="status" id="status_<?php _esc($row['id']); ?>" class="form-control">
                                        <option value="1"><?php _e("Enable"); ?></option>
                                        <option value="0" <?php echo ($row['active'] == 0)? "selected" :  "" ?>><?php _e("Disable"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <button type="submit" class="btn btn-primary ripple-effect"><?php _e("Save"); ?></button>
                            </div>
                            <div class="col-md-6 col-sm-12 text-right">
                                <button type="button" class="btn btn-secondary quick-modal-trigger" data-action="langTranslation_PlanCustom" data-type-id="<?php _esc($row['id']); ?>"><i class="icon-feather-globe"></i> <?php _e("Edit Language"); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php  }
} else {
    include '../../no-data.php';
}
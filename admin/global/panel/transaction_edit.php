<?php
include '../datatable-json/includes.php';

$info = ORM::for_table($config['db']['pre'].'transaction')->find_one($_GET['id']);
$item_id = $info['id'];
$status = $info['status'];
?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php _e('Edit Transaction Status') ?></h2>
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
        <form method="post" data-ajax-action="transactionEdit" id="sidePanel_form">
            <div class="form-body">
                <input type="hidden" name="id" value="<?php _esc($item_id) ?>">
                <div class="form-group">
                    <label><?php _e("Transaction ID") ?></label>
                    <input type="text" class="form-control" value="<?php _esc($item_id) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="status"><?php _e("Transaction Status") ?></label>
                    <select name="status" id="status" class="form-control">
                        <option value="success" <?php if($status == 'success') echo "selected"; ?>><?php _e("Success") ?></option>
                        <option value="pending" <?php if($status == 'pending') echo "selected"; ?>><?php _e("Pending") ?></option>
                        <option value="cancel" <?php if($status == 'cancel') echo "selected"; ?>><?php _e("Cancelled") ?></option>
                        <option value="failed" <?php if($status == 'failed') echo "selected"; ?>><?php _e("Failed") ?></option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
if(empty($_GET['id'])){
    _e('Unexpected error, please try again.');
    die();
}

require_once '../../includes.php';

$info = array(
    'amount' => '',
);
$info = ORM::for_table($config['db']['pre'].'withdrawal')->find_one(validate_input($_GET['id']));

?>

<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php _e('Withdrawal'); ?></h2>
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
              data-ajax-action="editWithdrawal" >
            <div class="form-body">
                <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <div class="form-group">
                    <label for="title"><?php _e('Amount') ?> (<?php _esc($config['currency_sign']) ?>)</label>
                    <input id="title" type="text" class="form-control" value="<?php _esc($info['amount'])?>" readonly>
                </div>
                <div class="form-group">
                    <label for="status"><?php _e('Status') ?></label>
                    <select name="status" id="status" class="form-control withdrawal-status">
                        <option value="success" <?php echo 'success' == $info['status'] ? 'selected' : '' ?>><?php _e('Success'); ?></option>
                        <option value="pending" <?php echo 'pending' == $info['status'] ? 'selected' : '' ?>><?php _e('Pending'); ?></option>
                        <option value="reject" <?php echo 'reject' == $info['status'] ? 'selected' : '' ?>><?php _e('Reject'); ?></option>
                    </select>
                </div>
                <div class="form-group reject_reason" style="display: none">
                    <label for="reject_reason"><?php _e('Reject Reason') ?></label>
                    <textarea name="reject_reason" id="title" class="form-control"><?php _esc($info['reject_reason']) ?></textarea>
                    <span class="form-text text-muted"><?php _e('User will be notified with this reason.') ?></span>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('.withdrawal-status').off('change').on('change', function (e){
        if($(this).val() == 'reject'){
            $('.reject_reason').slideDown('fast');
        } else {
            $('.reject_reason').slideUp('fast');
        }
    })
</script>
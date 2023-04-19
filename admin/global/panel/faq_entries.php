<?php
require_once '../../includes.php';
$info = array(
    'id' => '',
    'active' => '1',
    'faq_title' => '',
    'faq_content' => ''
);
if(isset($_GET['id']))
    $info = ORM::for_table($config['db']['pre'].'faq_entries')
        ->use_id_column('faq_id')
        ->find_one(validate_input($_GET['id']));
else
    $_GET['id'] = null;
?>

<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit FAQ') : __('Add FAQ'); ?></h2>
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
              data-ajax-action="<?php echo isset($_GET['id']) ? "editFAQentry" : "addFAQentry"; ?>" >
            <div class="form-body">
                <?php if(isset($_GET['id'])){ ?>
                    <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <?php } ?>
                <?php quick_switch(__('Activate'), 'active', $info['active'] == '1'); ?>

                <div class="form-group">
                    <label><?php _e('Title') ?>:</label>
                    <input name="title" type="text" class="form-control" value="<?php echo $info['faq_title']?>">
                </div>

                <div class="form-group">
                    <label><?php _e('Content') ?>:</label>
                    <textarea name="content" id="pageContent" rows="14" type="text" class="form-control tiny-editor"><?php echo $info['faq_content']?></textarea>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="../assets/plugins/tinymce/tinymce.min.js"></script>
<script src="../assets/js/script.js"></script>


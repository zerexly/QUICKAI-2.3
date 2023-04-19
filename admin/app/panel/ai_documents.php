<?php
if(empty($_GET['id'])){
    _e('Unexpected error, please try again.');
    die();
}

require_once '../../includes.php';

$info = array(
    'id' => '',
    'title' => '',
    'content' => '',
);
$info = ORM::for_table($config['db']['pre'].'ai_documents')->find_one(validate_input($_GET['id']));

?>

<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php _e('Edit AI Document'); ?></h2>
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
              data-ajax-action="editAIDocument" >
            <div class="form-body">
                <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <div class="form-group">
                    <label for="title"><?php _e('Title') ?>:</label>
                    <input name="title" id="title" type="text" class="form-control" value="<?php echo $info['title']?>">
                </div>

                <div class="form-group">
                    <label for="content"><?php _e('Content') ?>:</label>
                    <textarea name="content" id="content" rows="14" type="text" class="form-control tiny-editor"><?php echo $info['content']?></textarea>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="../assets/plugins/tinymce/tinymce.min.js"></script>
<script src="../assets/js/script.js"></script>
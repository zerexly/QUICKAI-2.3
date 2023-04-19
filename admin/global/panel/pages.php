<?php
require_once '../../includes.php';
$info = array(
    'id' => '',
    'slug' => '',
    'name' => '',
    'title' => '',
    'type' => '',
    'active' => '1',
    'content' => ''
);
if(isset($_GET['id']))
    $info = ORM::for_table($config['db']['pre'].'pages')->find_one($_GET['id']);
else
    $_GET['id'] = null;
?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Page') : __('Add Page'); ?></h2>
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
              data-ajax-action="<?php echo isset($_GET['id']) ? "editStaticPage" : "addStaticPage"; ?>">
            <div class="form-body">
                <?php if(isset($_GET['id'])){ ?>
                    <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <?php } ?>
                <div class="form-group">
                    <label><?php _e('Slug') ?></label>
                    <input name="slug" type="text" class="form-control" value="<?php echo $info['slug']?>">
                </div>
                <div class="form-group">
                    <label><?php _e('Name') ?></label>
                    <input name="name" type="text" class="form-control" value="<?php echo $info['name']?>">
                </div>
                <div class="form-group">
                    <label><?php _e('Title') ?></label>
                    <input name="title" type="text" class="form-control" value="<?php echo $info['title']?>">
                </div>
                <div class="form-group">
                    <label><?php _e('Page Type') ?></label>
                    <select name="type" id="type" class="form-control">
                        <option value="0" <?php if($info['type'] == '0') echo "selected"; ?>><?php _e('Standard') ?></option>
                        <option value="1" <?php if($info['type'] == '1') echo "selected"; ?>><?php _e('Logged in only') ?></option>
                    </select>
                </div>
                <?php quick_switch(__('Activate'), 'active', ($info['active'] == '1')); ?>

                <div class="form-group">
                    <label for="pageContent"><?php _e('Content') ?></label>
                    <textarea name="content" rows="6" class="form-control tiny-editor" id="pageContent"><?php echo $info['content']?></textarea>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="../assets/plugins/tinymce/tinymce.min.js"></script>
<script src="../assets/js/script.js"></script>
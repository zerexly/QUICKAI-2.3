<?php
require_once '../../includes.php';
$info = array(
    'id' => '',
    'title' => '',
    'slug' => '',
    'active' => ''
);
if (isset($_GET['id']))
    $info = ORM::for_table($config['db']['pre'] . 'blog_categories')->find_one($_GET['id']);
else
    $_GET['id'] = null;

?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Blog Category') : __('Add Blog Category'); ?></h2>
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
        <form name="form2" class="form form-horizontal" method="post" id="sidePanel_form"
              data-ajax-action="<?php echo isset($_GET['id']) ? "editBlogCat" : "addBlogCat"; ?>">
            <div class="form-body">
                <?php if(isset($_GET['id'])){ ?>
                    <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <?php } ?>
                <div class="form-group">
                    <label for="title"><?php _e('Title') ?></label>
                    <input id="title" type="text" name="title" value="<?php echo $info['title']; ?>"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="slug"><?php _e('Slug') ?></label>
                    <input id="slug" type="text" name="slug" value="<?php echo $info['slug']; ?>"
                           class="form-control">
                </div>
                <?php quick_switch(__('Activate'), 'active', (bool) $info['active']); ?>
                <input type="hidden" name="submit">
            </div>
        </form>
    </div>
</div>
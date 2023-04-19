<?php
include '../datatable-json/includes.php';

$name = $designation = $image = $content = '';
if(isset($_GET['id'])) {
    $info = ORM::for_table($config['db']['pre'] . 'testimonials')->find_one($_GET['id']);
    $name = $info['name'];
    $designation = $info['designation'];
    $image = $info['image'];
    $content = $info['content'];
}
?>
<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo isset($_GET['id']) ? __('Edit Testimonial') : __('Add Testimonial'); ?></h2>
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
        <form method="post" data-ajax-action="addEditTestimonial" id="sidePanel_form">
            <?php if(isset($_GET['id'])){ ?>
                <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
            <?php } ?>
            <div class="form-body">
                <div class="form-group">
                    <label for="name"><?php _e('Name') ?></label>
                    <input id="name" name="name" type="text" class="form-control" required="" value="<?php _esc($name)?>">
                </div>
                <div class="form-group">
                    <label for="designation"><?php _e('Designation') ?></label>
                    <input id="designation" name="designation" type="text" class="form-control" required value="<?php _esc($designation)?>">
                </div>
                <div class="form-group">
                    <label for="image"><?php _e('User Image') ?></label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="image" name="image"
                               accept="image/png, image/gif, image/jpeg">
                        <label class="custom-file-label" for="image"><?php _e('Choose image...') ?></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="content"><?php _e('Content') ?></label>
                    <textarea name="content" id="content" rows="6" class="form-control" required><?php _esc($content)?></textarea>
                </div>
                <input type="hidden" name="submit">
            </div>
        </form>
    </div>
</div>
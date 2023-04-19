<?php
include '../includes.php';

global $config;

$id = $title = $slug = $image = $description = $tags = $status = null;
$categories = array();
if (!empty($_GET['id'])) {

    $info = ORM::for_table($config['db']['pre'].'blog')
        ->select_many_expr('b.*', 'GROUP_CONCAT(bc.category_id) categories')
        ->table_alias('b')
        ->left_outer_join($config['db']['pre'] . "blog_cat_relation", 'bc.blog_id = b.id', 'bc')
        ->where('b.id', $_GET['id'])
        ->find_one();
    if (!empty($info)) {
        $id = $info['id'];
        $title = $info['title'];
        $image = $info['image'];
        $description = $info['description'];
        $status = $info['status'];
        $tags = $info['tags'];
        $categories = explode(',', $info['categories']);
    } else {
        echo __('Page not found');
        exit();
    }
}

$page_title = !empty($_GET['id']) ? __('Edit Blog') : __('Add New Blog');
include '../header.php'; ?>

    <!-- Page Body Start-->
    <div class="page-body-wrapper">
<?php include '../sidebar.php'; ?>
    <!-- Page Sidebar Ends-->
    <div class="page-body">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-lg-6 main-header">
                        <h2><?php _esc($page_title); ?></h2>
                        <h6 class="mb-0"><?php _e('admin panel'); ?></h6>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <form id="ajax_submit_form" method="post" action="#" enctype="multipart/form-data" data-action="saveBlog">
                <input type="hidden" name="id" id="post_id" value="<?php _esc($id); ?>">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="quick-card card">
                            <div class="card-body p-20">
                                <div class="form-group">
                                    <label for="title"><?php _e('Title'); ?></label>
                                    <input name="title" class="form-control" type="text" id="title"
                                           value="<?php _esc($title); ?>"
                                           required="">
                                    <span class="form-text text-muted"><?php _e('No html allowed here.'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="post_image"><?php _e('Image'); ?></label>
                                    <div><img class="thumbnail" id="post_image"
                                              src="../../storage/blog/<?php _esc($image); ?>" alt="" width="400" <?php if (empty($image)){ echo 'style="display: none;"'; } ?>></div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="image" name="image"
                                               accept="image/png, image/gif, image/jpeg"
                                               onchange="readURL(this,'post_image')">
                                        <label class="custom-file-label" for="image"><?php _e('Choose file...'); ?></label>
                                    </div>
                                    <span class="form-text text-muted"><?php _e('Only jpg, jpeg & png allowed.'); ?></span>
                                </div>
                                <div class="form-group m-0">
                                    <label for="description"><?php _e('Description'); ?></label>
                                    <textarea id="description" name="description" rows="6"
                                              class="form-control tiny-editor"><?php _esc($description); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="quick-card card">
                            <div class="card-header p-20"><h5><?php _e('Publish'); ?></h5></div>
                            <div class="card-body p-20">
                                <div class="form-group">
                                    <label for="status"><?php _e('Status'); ?></label>
                                    <select name="status" id="status" class="form-control">
                                        <option <?php echo ($status == 'publish') ? 'selected' : ''; ?> value="publish">
                                            <?php _e('Publish'); ?>
                                        </option>
                                        <option <?php echo ($status == 'pending') ? 'selected' : ''; ?> value="pending">
                                            <?php _e('Pending'); ?>
                                        </option>
                                    </select>
                                    <span class="form-text text-muted"><?php _e("Select <strong>Pending</strong> if you want to hide this from the frontend"); ?></span>
                                </div>
                                <button id="submit_btn" name="submit" type="submit" class="btn btn-primary">
                                    <?php _e('Submit'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="quick-card card">
                            <div class="card-header p-20"><h5><?php _e('Categories'); ?></h5></div>
                            <div class="card-body p-20">
                                <?php
                                $rows = ORM::for_table($config['db']['pre'].'blog_categories')
                                    ->where('active','1')
                                    ->order_by_asc('position')
                                    ->find_array();

                                if (!empty($rows)) { ?>
                                    <select name="category[]" class="form-control quick-multi-select" multiple>
                                        <?php foreach ($rows as $row) { ?>
                                                <option value="<?php _esc($row['id']); ?>" <?php echo in_array($row['id'], $categories) ? 'selected' : ''; ?>><?php _esc($row['title']); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } else {
                                    echo __('No category available.');
                                } ?>
                            </div>
                        </div>
                        <div class="quick-card card">
                            <div class="card-header p-20"><h5><?php _e('Tags'); ?></h5></div>
                            <div class="card-body p-20">
                                <p class="help-block m-b-5"><?php _e('Enter tags separated by comma.'); ?></p>
                                <textarea name="tags" rows="2" class="form-control"
                                          id="tags"><?php _esc($tags); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Container-fluid Ends-->
    </div>
    <script>
        var QuickMenu = {"page": "blog", "subpage": "blog-post"};
    </script>

<?php ob_start() ?>
    <link rel="stylesheet" href="../assets/css/jquery.multiselect.css" />
    <script src="../assets/js/jquery.multiselect.js"></script>

    <script src="../assets/plugins/tinymce/tinymce.min.js"></script>
<?php
$footer_content = ob_get_clean();

include '../footer.php';
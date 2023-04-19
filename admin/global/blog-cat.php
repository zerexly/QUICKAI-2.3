<?php
include '../includes.php';

$page_title = __('Blog Categories');

$rows = ORM::for_table($config['db']['pre'] . 'blog_categories')
        ->order_by_asc('position')
        ->find_array();

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
                        <h2><?php _esc($page_title) ?></h2>
                        <h6 class="mb-0"><?php _e('admin panel') ?></h6>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="quick-card card">
                <div class="card-header d-flex align-items-center">
                    <h5>&nbsp;</h5>
                    <div class="card-header-right">
                        <button type="button" class="btn btn-primary ripple-effect quick-popover" data-form="custom-setting-form">
                            <i class="icon-feather-plus"></i> <?php _e('Add New'); ?>
                        </button>
                        <form class="popover-form" method="post" id="custom-setting-form" data-action="addBlogCat">
                            <div class="m-b-10">
                                <label for="new-type-name"><?php _e('Title'); ?></label>
                                <input class="form-control" id="new-type-name" type="text" name="name" required />
                            </div>
                            <button type="submit" class="btn btn-primary submit-form"><?php _e('Save'); ?></button>
                            <button type="button" class="btn btn-default cancel-popover"><?php _e('Cancel'); ?></button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dataTables_wrapper">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th><?php _e('Title') ?></th>
                                <th><?php _e('Slug') ?></th>
                                <th><?php _e('Active') ?></th>
                                <th width="20" class="no-sort"></th>
                                <th width="20" class="no-sort">
                                    <div class="checkbox">
                                        <input type="checkbox" id="quick-checkbox-all">
                                        <label for="quick-checkbox-all"><span class="checkbox-icon"></span></label>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="quick-reorder-body" data-action="blogCatPosition">
                            <?php
                            if(!empty($rows)) {
                            foreach ($rows as $row) {
                                $active = $row['active'];
                                if ($active == "1"){
                                    $active = '<span class="badge badge-primary">'.__("Active").'</span>';
                                }
                                else{
                                    $active = '<span class="badge badge-secondary">'.__("Not Active").'</span>';
                                }
                                ?>
                            <tr class="quick-reorder-element" id="table_row_<?php _esc($row['id']) ?>" data-id="<?php _esc($row['id']) ?>">
                                <td><i class="icon-feather-menu quick-reorder-icon"
                                       title="Reorder"></i> <?php _esc($row['title']) ?></td>
                                <td><?php _esc($row['slug']) ?></td>
                                <td><?php _esc($active) ?></td>
                                <td width="20" class="no-sort">
                                    <div class="btn-group">
                                        <a href="#" data-url="panel/blog-cat.php?id=<?php _esc($row['id']) ?>" data-toggle="slidePanel" title="<?php _e("Edit") ?>" class="btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                                    </div>
                                </td>
                                <td width="20" class="no-sort">
                                    <div class="checkbox">
                                        <input type="checkbox" id="check_<?php _esc($row['id']) ?>" value="<?php _esc($row['id']) ?>" class="quick-check">
                                        <label for="check_<?php _esc($row['id']) ?>"><span class="checkbox-icon"></span></label>
                                    </div>
                                </td>
                            </tr>
                            <?php }
                            } else {
                                ?>
                                <tr>
                                    <td class="text-center" colspan="4"><?php _e('No data available.') ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
    <!-- Site Action -->
    <div class="site-action">
        <div class="site-action-buttons">
            <button type="button" id="quick-delete-button" data-action="delBlogCat"
                    class="btn btn-danger btn-floating animation-slide-bottom">
                <i class="icon icon-feather-trash-2" aria-hidden="true"></i>
            </button>
        </div>
        <button type="button" class="back-icon btn btn-primary btn-floating">
            <i class="icon-feather-x animation-scale-up" aria-hidden="true"></i>
        </button>
    </div>
    <script>
        var QuickMenu = {"page":"blog", "subpage":"blog-cat"};
    </script>

<?php ob_start() ?>
<?php
$footer_content = ob_get_clean();

include '../footer.php'; ?>
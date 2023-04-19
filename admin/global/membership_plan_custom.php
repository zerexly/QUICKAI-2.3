<?php
include '../includes.php';

$page_title = __('Custom Settings');
include '../header.php'; ?>

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
                        <form class="popover-form" method="post" id="custom-setting-form" data-action="addPlanCustom">
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
                    <div class="quick-accordion popover-reload quick-reorder-body" id="accordion" data-url="membership_plan_custom.php" data-action="quickad_update_plan_custom_position">
                        <!-- Dynamic data -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
    <div class="site-action">
        <div class="site-action-buttons">
            <button type="button" id="quick-delete-button" data-action="delPlanCustom"
                    class="btn btn-danger btn-floating animation-slide-bottom">
                <i class="icon icon-feather-trash-2" aria-hidden="true"></i>
            </button>
        </div>
        <button type="button" class="back-icon btn btn-primary btn-floating">
            <i class="icon-feather-x animation-scale-up" aria-hidden="true"></i>
        </button>
    </div>

<!-- Dynamic language modal -->
    <div id="quick-dynamic-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" class="ajax_submit_form" data-action="edit_langTranslation_PlanCustom">
                    <div class="modal-header">
                        <h4 class="modal-title"><?php _e('Edit Language Translation'); ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class=" icon-feather-x"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="loader text-center">
                            <img src="<?php echo SITEURL; ?>includes/assets/images/loading.gif" alt=""/>
                        </div>
                        <div class="form-horizontal" id="displayData">
                            <!--Dynamic form fields-->
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel'); ?></button>
                        <button type="submit" class="btn btn-primary" id="saveEditLanguage"><?php _e('Save'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        var QuickMenu = {"page": "membership", "subpage": "membership-custom"};
    </script>

<?php ob_start() ?>
<?php
$footer_content = ob_get_clean();

include '../footer.php';
<?php
include '../includes.php';

$page_title = __('Membership Plan');
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
                <div class="card-body">
                    <div class="dataTables_wrapper">
                        <table class="table table-striped" id="ajax_datatable" data-jsonfile="membership_plan.php">
                            <thead>
                            <tr>
                                <th><?php _e('Plan Name') ?></th>
                                <th><?php _e('Monthly Price') ?></th>
                                <th><?php _e('Annual Price') ?></th>
                                <th><?php _e('Lifetime Price') ?></th>
                                <th width="20" class="no-sort" data-priority="1"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
    <div class="site-action">
        <div class="site-action-buttons">
        </div>
        <button type="button" class="front-icon btn btn-primary btn-floating"
                data-url="panel/membership_plan_add.php" data-toggle="slidePanel">
            <i class="icon-feather-plus animation-scale-up" aria-hidden="true"></i>
        </button>
        <button type="button" class="back-icon btn btn-primary btn-floating">
            <i class="icon-feather-x animation-scale-up" aria-hidden="true"></i>
        </button>
    </div>
    <script>
        var QuickMenu = {"page": "membership", "subpage": "membership-plans"};
    </script>

<?php ob_start() ?>
<?php
$footer_content = ob_get_clean();

include '../footer.php';
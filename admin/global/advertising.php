<?php
include '../includes.php';

$page_title = __('Advertisements');
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
                <div class="card-body">
                    <div class="dataTables_wrapper">
                        <table class="table table-striped" id="ajax_datatable" data-jsonfile="advertising.php" data-order-dir="asc">
                            <thead>
                            <tr>
                                <th><?php _e('ID') ?></th>
                                <th><?php _e('Key') ?></th>
                                <th><?php _e('Title') ?></th>
                                <th><?php _e('Status') ?></th>
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
    <script>
        var QuickMenu = {"page":"advertising"};
    </script>

<?php ob_start() ?>
<?php
$footer_content = ob_get_clean();
include '../footer.php';
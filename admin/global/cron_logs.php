<?php
include '../includes.php';

if(isset($_GET['clear']))
{
    ORM::raw_execute("TRUNCATE TABLE `".$config['db']['pre']."logs`");
}

$page_title = __('Cron Logs');
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
                        <a href="?clear=1" class="btn btn-primary ripple-effect">
                            <i class="icon-feather-trash-2"></i> <?php _e('Clear All'); ?>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dataTables_wrapper">
                        <table class="table table-striped" id="ajax_datatable" data-jsonfile="cron_logs.php">
                            <thead>
                            <tr>
                                <th class="no-sort"><?php _e('Summary') ?></th>
                                <th><?php _e('Date') ?></th>
                                <th class="no-sort"><?php _e('Details') ?></th>
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
        var QuickMenu = {"page": "membership", "subpage": "cron-logs"};
    </script>

<?php ob_start() ?>
<?php
$footer_content = ob_get_clean();

include '../footer.php';
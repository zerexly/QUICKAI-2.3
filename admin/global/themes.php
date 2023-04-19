<?php
include '../includes.php';
$template_path = ROOTPATH.'/templates/';
if(isset($_POST['tpl_name']))
{
    if(!check_allow()){
        ?>
        <script>
            $(document).ready(function(){
                $('#sa-title').trigger('click');
            });
        </script>
        <?php

    }
    else {
        update_option("tpl_name",$_POST['tpl_name']);

        transfer($_SERVER['REQUEST_URI'], __('Theme Changed'));
        exit;
    }
}
$page_title = __('Themes');
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
                    <div class="row">
                        <?php
                        if ($handle = opendir($template_path))
                        {
                            while (false !== ($folder = readdir($handle)))
                            {
                                if ($folder != "." && $folder != "..")
                                {
                                    $filepath = $template_path . $folder . "/theme-info.txt";
                                    if(file_exists($filepath)){
                                        $themefile = fopen($filepath,"r");

                                        $themeinfo = array();
                                        while(! feof($themefile)) {
                                            $lineRead = fgets($themefile);
                                            if (strpos($lineRead, ':') !== false) {
                                                $line = explode(':',$lineRead);
                                                $key = trim($line[0]);
                                                $value = trim($line[1]);
                                                $themeinfo[$key] = $value;
                                            }
                                        }
                                        ?>
                                        <div class="col-sm-6 col-md-4 col-lg-4">
                                            <div class="cnt-item">
                                                <img src="<?php _esc($config['site_url']) ?>templates/<?php echo $folder ?>/screenshot.png">
                                                <div class="cnt-item-details">
                                                    <h6><?php _esc($themeinfo['Theme Name'])  ?></h6>
                                                    <div class="cnt-item-meta d-flex flex-row">
                                                        <?php _e('Author')  ?>: <?php _esc($themeinfo['Author']) ?>
                                                        <span class="cnt-item-price">
                                                            <?php _e('Price')  ?> <span>(<?php _esc($themeinfo['Price']) ?>)</span>
                                                        </span>
                                                    </div>
                                                    <hr>
                                                    <div class="cnt-item-author-actions clearfix">
                                                        <div class="cnt-item-actions float-left">
                                                            <form action="themes.php" method="post" name="f1" id="f1">
                                                                <input type="hidden" value="<?php _esc($folder) ?>" name="tpl_name">
                                                                <?php

                                                                if($folder == $config['tpl_name'])
                                                                {
                                                                    $txt = __("Change Theme");
                                                                    echo '<button class="btn btn-default btn-sm" type="button">'.__("Change Theme").'</button>';
                                                                }
                                                                else{
                                                                    $txt = __("Change Theme");
                                                                    echo '<button class="btn btn-success btn-sm" type="submit">'.__("Activate Me").'</button>';
                                                                }
                                                                ?>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        fclose($themefile);
                                    }
                                }
                            }
                            closedir($handle);
                        }

                        ?>

                        <?php
                        if ($handle = opendir($template_path))
                        {
                            while (false !== ($file = readdir($handle)))
                            {
                                if ($file != '.' && $file != '..')
                                {
                                    ?>

                                    <?php
                                }
                            }
                            closedir($handle);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>

    <script>
        var QuickMenu = {"page": "themes"};
    </script>

<?php ob_start() ?>
<?php
$footer_content = ob_get_clean();

include '../footer.php';
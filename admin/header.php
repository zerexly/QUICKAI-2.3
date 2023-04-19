<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo isset($page_title) ?$page_title.' - ':''; ?> <?php _e('Admin Panel') ?></title>
    <meta name="description" content="<?php echo isset($page_title) ?$page_title.' - ':''; ?> <?php _e('Admin Panel') ?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $config['site_url'];?>storage/logo/<?php echo $config['site_favicon']?>">

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="author" content="Sahil" />
    <meta name="robots" content="noindex, nofollow" />
    <link rel="stylesheet" href="<?php echo SITEURL; ?>includes/assets/css/icons.css">
    <link rel="stylesheet" href="<?php echo ADMINURL; ?>assets/css/bootstrap.css?ver=<?php echo $config['version'];?>">
    <link rel="stylesheet" href="<?php echo ADMINURL; ?>assets/css/datatables.css"/>
    <link rel="stylesheet" href="<?php echo ADMINURL; ?>assets/css/slidePanel.min.css"/>
    <link rel="stylesheet" href="<?php echo ADMINURL; ?>assets/css/select2.min.css"/>
    <link rel="stylesheet" href="<?php echo ADMINURL; ?>assets/css/sweetalert.css">
    <link rel="stylesheet" href="<?php echo ADMINURL; ?>assets/css/style.css?ver=<?php echo $config['version'];?>">
    <link rel="stylesheet" href="<?php echo ADMINURL; ?>assets/css/custom.css?ver=<?php echo $config['version'];?>">
    <link rel="stylesheet" href="<?php echo ADMINURL; ?>assets/css/responsive.css?ver=<?php echo $config['version'];?>">

    <script>
        var ajaxurl = '<?php echo ADMINURL."admin_ajax.php" ?>';
        var sidepanel_ajaxurl = '<?php echo ADMINURL."ajax_sidepanel.php"; ?>';
    </script>
    <?php
    if(!empty($config['quickad_secret_file'])){
        ?>
        <script>
            var ajaxurl = '<?php echo ADMINURL.$config['quickad_secret_file'].'.php'; ?>';
        </script>
        <?php
    }
    ?>
</head>
<body>
<div class="quick-page-wrapper">
    <!-- page-wrapper Start-->
    <div class="page-wrapper">

<!-- Page Header Start-->
<div class="page-main-header">
    <div class="main-header-right">
        <div class="main-header-left">
            <div class="logo-wrapper"><a href="<?php _esc(ADMINURL) ?>"><img src="<?php echo $config['site_url'].'storage/logo/'. $config['site_admin_logo']?>" alt="" height="30"></a></div>
        </div>
        <div class="mobile-sidebar">
            <div class="media-body text-right switch-sm">
                <label class="switch ml-3"><i class="font-primary icon-feather-align-center" id="sidebar-toggle"></i></label>
            </div>
        </div>
        <div class="nav-right col pull-right right-menu">
            <ul class="nav-menus">
                <li><a class="text-dark" href="<?php echo $config['site_url'] ?>" title="<?php _e('Frontend') ?>" data-tippy-placement="top" target="_blank"><i class="icon-feather-external-link"></i></a></li>
                <li><a class="text-dark" href="#" onclick="toggleFullScreen()" title="<?php _e('Full Screen') ?>" data-tippy-placement="top"><i class="icon-feather-maximize"></i></a></li>
                <li><a class="text-dark" href="<?php echo ADMINURL; ?>global/settings.php" title="<?php _e('Settings') ?>" data-tippy-placement="top"><i class="icon-feather-settings"></i></a></li>
                <li>
                    <form method="get">
                    <select name="lang" class="custom-select custom-select-sm" title="<?php _e('Language') ?>" data-tippy-placement="top" onchange="this.form.submit()">
                        <?php
                        $lang_list = get_language_list('','selected',true);

                        foreach ($lang_list as $l)
                        {
                            $lang_name = $l['name'];
                            $lang_file_name = $l['file_name'];
                            $path_of_file = ROOTPATH.'/includes/lang/lang_'.$lang_file_name.'.php';
                            $selected = "";
                            if($config['lang'] == $lang_file_name) {
                                $selected = "selected";
                            }

                            if(file_exists($path_of_file))
                                echo '<option value="'.$l['code'].'" '.$selected.'>'.$lang_name.'</option>';
                        }
                        ?>
                    </select>
                    </form>
                </li>
                <li><button class="btn btn-default" type="button" disabled><?php _e('Version') ?> <?php echo $config['version'] ?></button></li>
            </ul>
        </div>
    </div>
</div>
<!-- Page Header Ends -->
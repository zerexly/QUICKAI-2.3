<?php
include '../includes.php';

require_once ROOTPATH . '/includes/lib/GoogleTranslate.php';
$filePath = '';
if(isset($_GET['file'])){
    $info = ORM::for_table($config['db']['pre'].'languages')
        ->select('code')
        ->where('file_name', $_GET['file'])
        ->find_one();
    $lang_code = $info['code'];

    $filePath = ROOTPATH.'/includes/lang/lang_'.$_GET['file'].'.php';
}else{
    $filePath = ROOTPATH.'/includes/lang/lang_english.php';
}
$new_lang = array();
if(file_exists($filePath)){
    /*$lang = array();
    include($filePath);
    $new_lang = $lang;*/
    $new_lang = getLanguageFileVariable($filePath);
}
else{
    echo '<script>window.location="404.php"</script>';
    exit;
}

function change_config_file_settings($filePath, $newSettings, $lang)
{
    // Update $fileSettings with any new values
    $fileSettings = array_merge($lang, $newSettings);
    //ksort($fileSettings);
    // Build the new file as a string
    $newFileStr = "<?php\n";
    foreach ($fileSettings as $name => $val) {
        // Using var_export() allows you to set complex values such as arrays and also
        // ensures types will be correct
        $newFileStr .= '$lang['. var_export($name, true) .'] = ' . var_export($val, true) . ";\n";
    }
    // Closing tag intentionally omitted, you can add one if you want

    // Write it back to the file
    file_put_contents($filePath, $newFileStr);

}


if(isset($_POST['refresh'])) {
    if (!check_allow()) {
        transfer($_SERVER['REQUEST_URI'],__('Refreshed Successfully'));
        exit;
    } else {

        $english_lang = array();
        $lang = array();

        $english_lang = getLanguageFileVariable(ROOTPATH.'/includes/lang/lang_english.php');
        $array_diff=array_diff_key($english_lang,$new_lang);

        $source = 'en';
        $target = $lang_code;

        $trans = new GoogleTranslate();
        $newLangArray = array();
        foreach ($array_diff as $key => $value)
        {
            $result = $trans->translate($source, $target, $value);
            $newLangArray[$key] = !empty($result) ? $result : $value;
        }

        fopen($filePath, "w");
        change_config_file_settings($filePath, $newLangArray,$new_lang);

        transfer($_SERVER['REQUEST_URI'],__('Refreshed Successfully'));
        exit;
    }
}

$page_title = __('Edit Language File');
include '../header.php';
?>

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
                    <div class="col-lg-6">
                        <div class="text-right">
                            <form method="post">
                                <a href="languages.php" class="btn btn-success waves-effect waves-light m-r-10"><i class="icon-feather-arrow-left mr-1"></i> <?php _e('Back') ?></a>

                                <button type="submit" name="refresh" id="refresh_list" class="btn btn-warning waves-effect waves-light"><i class="icon-feather-refresh-cw mr-1"></i> <?php _e('Refresh File') ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="icon-feather-x"></i></button>
                <strong><?php _e('Important!') ?></strong> <?php _e('Do not edit or delete any language if they are already used. You can simply deactivate if not want to use.') ?>
            </div>
            <div class="alert alert-warning">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="icon-feather-x"></i></button>
                <strong><?php _e('Important!') ?></strong> <?php _e('Click on the "Refresh" button if you did not find any text.') ?>
            </div>
            <div class="quick-card card">
                <div class="card-body">
                    <div class="dataTables_wrapper">
                        <div class="table-responsive" id="js-table-list">

                            <table id="basic_datatable" class="table table-vcenter table-hover font-14" data-tablesaw-mode="stack" data-plugin="animateList" data-animate="fade" data-child="tr">
                                <thead>
                                <tr>
                                    <th><?php _e('No.') ?></th>
                                    <th><?php _e('Key (English)') ?></th>
                                    <th><?php _e('Value') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $count = 1;

                                foreach ($new_lang as $key => $value)
                                {
                                    $id = $count;
                                    ?>
                                    <tr>

                                        <td><?php _esc($id); ?></td>
                                        <td><?php _esc($key); ?></td>
                                        <td>
                                            <form method="post" name="f1" id="f1">
                                                <span class="langtitle_<?php _esc($id); ?>"><?php _esc($value); ?></span>
                                                <br>
                                                <div style="display: none" data-id="<?php _esc($id); ?>">
                                                    <input name="newlang_key" type="hidden" value="<?php echo escape($key); ?>">
                                                    <input name="langfile_name" type="hidden" value="<?php _esc($_GET['file']); ?>">
                                                    <input name="newlang_value" type="text" value="<?php echo escape($value); ?>" id="<?php _esc($id); ?>" class="form-control">

                                                    <button type="button" class="btn btn-xs btn-success mt-1 savebutton"><?php _e('Save') ?></button>
                                                    <a href="javascript:void(0)" class="btn btn-xs btn-warning mt-1 cancelbutton"><?php _e('Cancel') ?></a>

                                                </div>

                                                <button type="button" class="btn btn-xs btn-primary editbutton"><?php _e('Edit this content') ?></button>

                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                    $count++;
                                }?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>

    <script>
        var QuickMenu = {"page": "languages"};
    </script>

<?php ob_start() ?>
<?php
$footer_content = ob_get_clean();

include '../footer.php';

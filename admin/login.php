<?php
define("ROOTPATH", dirname(__DIR__));
define("APPPATH", ROOTPATH."/php/");
define("ADMINPATH", __DIR__);

require_once ROOTPATH . '/includes/autoload.php';
require_once ROOTPATH . '/includes/lang/lang_'.$config['lang'].'.php';

$admin_url = $config['site_url']."admin/";
define("SITEURL", $config['site_url'].'/');
define("ADMINURL", $admin_url);

admin_session_start();

if (checkloggedadmin()) {
    headerRedirect('index.php');
}

if(isset($_POST['username']))
{
    $recaptcha_error = __('Error: reCAPTCHA error');
    if(get_option('recaptcha_mode') == 1){
        if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
            //your site secret key
            $secret = $config['recaptcha_private_key'];
            //get verify response data
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
            $responseData = json_decode($verifyResponse);
            if ($responseData->success) {
                $recaptcha_responce = true;
            }else{
                $recaptcha_responce = false;
                $recaptcha_error = __("reCAPTCHA verification failed, please try again.");
            }
        }else{
            $recaptcha_responce = false;
            $recaptcha_error = __("Please click on the reCAPTCHA box.");
        }
    }else{
        $recaptcha_responce = true;
    }

    if($recaptcha_responce){
        if(adminlogin($_POST['username'],$_POST['password']))
        {
            if(isset($_GET['redirect_to']) && (strpos($_GET['redirect_to'],SITEURL) !== false)){
                headerRedirect($_GET['redirect_to']);
            }else{
                headerRedirect('index.php');
            }
        }
        else
        {
            $error = __("Error: Username & Password do not match");
        }
    }else{
        $error = $recaptcha_error;
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php _esc($config['site_title']) ?> <?php _e('- Admin Login') ?></title>
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $config['site_url'];?>storage/logo/<?php echo $config['site_favicon']; ?>">

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="author" content="<?php _esc($config['site_title']) ?>" />
    <meta name="robots" content="noindex, nofollow" />

    <link rel="stylesheet" href="assets/icons/css/feather-icon.css?ver=<?php _esc($config['version']);?>">
    <link rel="stylesheet" href="assets/css/bootstrap.css?ver=<?php _esc($config['version']);?>">
    <link rel="stylesheet" href="assets/css/style.css?ver=<?php _esc($config['version']);?>">
    <link rel="stylesheet" href="assets/css/custom.css?ver=<?php _esc($config['version']);?>">
    <link rel="stylesheet" href="assets/css/responsive.css?ver=<?php _esc($config['version']);?>">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6 offset-md-3">
                <div class="quick-card card m-t-50">
                    <div class="card-body">
                        <div class="text-center m-b-20"><img class="img-responsive w-100" src="../storage/logo/<?php echo $config['site_admin_logo']?>" alt="Sahil" /></div>
                        <?php if(!empty($error)){ ?>
                        <div class="alert alert-danger m-b-20">
                            <?php echo $error; ?>
                        </div>
                        <?php } ?>
                        <form action="#" method="post">
                            <div class="form-group">
                                <label for="username"><?php _e('Email or Username') ?></label>
                                <input type="text" name="username" class="form-control" id="username"  />
                            </div>
                            <div class="form-group">
                                <label for="login_password"><?php _e('Password') ?></label>
                                <input type="password" name="password" class="form-control" id="login_password"  />
                            </div>
                            <?php
                            if(get_option('recaptcha_mode') == 1){
                                ?>
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <div class="g-recaptcha" data-sitekey="<?php echo $config['recaptcha_public_key'] ?>"></div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <button type="submit" class="btn btn-primary w-100" name="login"><?php _e('Login') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    // load google font
    var quick_load_css = function (id, url) {
        if (!document.getElementById('')) {
            var l = document.getElementsByTagName("head")[0], o = document.createElement("link");
            o.id = id, o.rel = "stylesheet", o.type = "text/css", o.href = url, l.appendChild(o)
        }
    };
    quick_load_css("quick-google-font","//fonts.googleapis.com/css?family=Nunito:400,400i,600,600i,700,700i,800,800i&display=swap");

    // load google recapctha
    var quick_load_js = function (id, url) {
        if (!document.getElementById('')) {
            var l = document.getElementsByTagName("head")[0], o = document.createElement("script");
            o.id = id, o.src = url, l.appendChild(o)
        }
    };
    quick_load_js("quick-google-recaptcha","https://www.google.com/recaptcha/api.js");
</script>

</body>
</html>
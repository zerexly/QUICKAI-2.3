<?php
require_once("includes/lib/curl/curl.php");
require_once("includes/lib/curl/CurlResponse.php");

if(!isset($_GET['i']))
{
    error(__("Invalid Payment Processor"), __LINE__, __FILE__, 1);
    exit();
}

$_GET['i'] = str_replace('.','',$_GET['i']);
$_GET['i'] = str_replace('/','',$_GET['i']);
$_GET['i'] = strip_tags($_GET['i']);

if(preg_match('[^A-Za-z0-9_]',$_GET['i']))
{
    error(__("Invalid Payment Processor"), __LINE__, __FILE__, 1);
    exit();
}

if(trim($_GET['i']) == '')
{
    error(__("Invalid Payment Processor"), __LINE__, __FILE__, 1);
    exit();
}

if(isset($_GET['i']) && isset($_GET['access_token']))
{
    $access_token = $_GET['access_token'];
    if(isset($_SESSION['quickad'][$access_token])){
        $payment_settings = ORM::for_table($config['db']['pre'].'payments')
            ->select('payment_folder')
            ->where('payment_folder', $_GET['i'])
            ->find_one();

        if(!isset($payment_settings['payment_folder']))
        {
            error(__("Invalid Payment Processor"), __LINE__, __FILE__, 1);
            exit();
        }
        require_once('includes/payments/'.$payment_settings['payment_folder'].'/ipn.php');
    }
}
?>

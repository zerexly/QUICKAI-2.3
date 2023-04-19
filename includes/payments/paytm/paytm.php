<?php
/**
 ***** Paytm Functions *****
 **/

function getChecksumFromArray($arrayList, $key, $sort = 1)
{
    if ($sort != 0) {
        ksort($arrayList);
    }
    $str = getArray2Str($arrayList);
    $salt = generateSalt_e(4);
    $finalString = $str . "|" . $salt;
    $hash = hash("sha256", $finalString);
    $hashString = $hash . $salt;
    $checksum = encrypt_e($hashString, $key);
    return $checksum;
}

function getArray2Str($arrayList)
{
    $findme = 'REFUND';
    $findmepipe = '|';
    $paramStr = "";
    $flag = 1;
    foreach ($arrayList as $key => $value) {
        $pos = strpos($value, $findme);
        $pospipe = strpos($value, $findmepipe);
        if ($pos !== false || $pospipe !== false) {
            continue;
        }

        if ($flag) {
            $paramStr .= checkString_e($value);
            $flag = 0;
        } else {
            $paramStr .= "|" . checkString_e($value);
        }
    }
    return $paramStr;
}

function checkString_e($value)
{
    if ($value == 'null' || $value == 'NULL')
        $value = '';
    return $value;
}

function generateSalt_e($length)
{
    $random = "";
    srand((double)microtime() * 1000000);

    $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
    $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
    $data .= "0FGH45OP89";

    for ($i = 0; $i < $length; $i++) {
        $random .= substr($data, (rand() % (strlen($data))), 1);
    }

    return $random;
}

function encrypt_e($input, $ky)
{
    $key = html_entity_decode($ky);
    $iv = "@@@@&&&&####$$$$";
    $data = openssl_encrypt($input, "AES-128-CBC", $key, 0, $iv);
    return $data;
}

function decrypt_e($crypt, $ky)
{
    $key = html_entity_decode($ky);
    $iv = "@@@@&&&&####$$$$";
    $data = openssl_decrypt($crypt, "AES-128-CBC", $key, 0, $iv);
    return $data;
}

function verifychecksum_e($arrayList, $key, $checksumvalue)
{
    $arrayList = removeCheckSumParam($arrayList);
    ksort($arrayList);
    $str = getArray2StrForVerify($arrayList);
    $paytm_hash = decrypt_e($checksumvalue, $key);
    $salt = substr($paytm_hash, -4);

    $finalString = $str . "|" . $salt;

    $website_hash = hash("sha256", $finalString);
    $website_hash .= $salt;

    $validFlag = "FALSE";
    if ($website_hash == $paytm_hash) {
        $validFlag = "TRUE";
    } else {
        $validFlag = "FALSE";
    }
    return $validFlag;
}

function removeCheckSumParam($arrayList)
{
    if (isset($arrayList["CHECKSUMHASH"])) {
        unset($arrayList["CHECKSUMHASH"]);
    }
    return $arrayList;
}

function getArray2StrForVerify($arrayList)
{
    $paramStr = "";
    $flag = 1;
    foreach ($arrayList as $key => $value) {
        if ($flag) {
            $paramStr .= checkString_e($value);
            $flag = 0;
        } else {
            $paramStr .= "|" . checkString_e($value);
        }
    }
    return $paramStr;
}

function callNewAPI($apiURL, $requestParamList) {
    $jsonResponse = "";
    $responseParamList = array();
    $postData = 'JsonData='.json_encode($requestParamList, JSON_UNESCAPED_SLASHES);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, $apiURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $jsonResponse = curl_exec($ch);
    $responseParamList = json_decode($jsonResponse,true);
    return $responseParamList;
}

/**
 * Execute purchase product after successful payment
 */
function paytmReturn()
{
    global $config;
    $error = '';
    $access_token = $_GET["access_token"];

    if ($_POST["RESPCODE"] == 01 && isset($_GET["access_token"])) {

        $paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; //Sent by Paytm pg

        //URL
        $PAYTM_STATUS_QUERY_NEW_URL_SANDBOX = 'https://securegw-stage.paytm.in/merchant-status/getTxnStatus';
        $PAYTM_STATUS_QUERY_NEW_URL = 'https://securegw.paytm.in/merchant-status/getTxnStatus';

        $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
        if($payment_type == "order") {
            $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];

            $PAYTM_MERCHANT_KEY = get_restaurant_option($restaurant_id,'restaurant_paytm_merchant_key');
            $PAYTM_MERCHANT_MID = get_restaurant_option($restaurant_id,'restaurant_paytm_merchant_mid');
            $PAYTM_SANDBOX = get_restaurant_option($restaurant_id,'restaurant_paytm_sandbox_mode');
        }else{
            $PAYTM_MERCHANT_KEY = get_option('PAYTM_MERCHANT_KEY');
            $PAYTM_MERCHANT_MID = get_option('PAYTM_MERCHANT_MID');
            $PAYTM_SANDBOX = get_option('PAYTM_ENVIRONMENT');
        }

        //Verify all parameters received from Paytm pg to your application. Like MID received from paytm pg is same as your application's MID, TXN_AMOUNT and ORDER_ID are same as what was sent by you to Paytm PG for initiating transaction etc.
        $isValidChecksum = verifychecksum_e($_POST, $PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.

        if ($isValidChecksum == "TRUE") {

            $requestParamList = array("MID" => $PAYTM_MERCHANT_MID, "ORDERID" => $_GET["access_token"]);
            $StatusCheckSum = getChecksumFromArray($requestParamList, $PAYTM_MERCHANT_KEY);
            $requestParamList['CHECKSUMHASH'] = urlencode($StatusCheckSum);

            $url = ($PAYTM_SANDBOX == 'TEST') ? $PAYTM_STATUS_QUERY_NEW_URL_SANDBOX : $PAYTM_STATUS_QUERY_NEW_URL;

            $responseParamList = callNewAPI($url, $requestParamList);
            if ($responseParamList['STATUS'] == 'TXN_SUCCESS') {
                //echo "Transaction was successful";
                payment_success_save_detail($access_token);
                exit();
            } else {

                // the transaction was not successful, do not deliver value'
                // print_r($result);  //uncomment this line to inspect the result, to check why it failed.

                payment_fail_save_detail($access_token);
                mail($config['admin_email'],'Paytm error in '.$config['site_title'],'Paytm error in '.$config['site_title'].', status from Paytm');

                $error_msg = "Transaction was not successful: Last gateway response was: ".$_POST["RESPMSG"];
                payment_error("error",$error_msg,$access_token);
                exit();
            }

        } else {
            // the transaction was not successful, do not deliver value'
            // print_r($result);  //uncomment this line to inspect the result, to check why it failed.

            payment_fail_save_detail($access_token);
            mail($config['admin_email'],'Paytm error in '.$config['site_title'],'Paytm error in '.$config['site_title'].', status from Paytm');

            $error_msg = "Transaction was not successful: Last gateway response was: ".$_POST["RESPMSG"];
            payment_error("error",$error_msg,$access_token);
            exit();
        }
    } else {
        // the transaction was not successful, do not deliver value'
        // print_r($result);  //uncomment this line to inspect the result, to check why it failed.

        payment_fail_save_detail($access_token);
        mail($config['admin_email'],'Paytm error in '.$config['site_title'],'Paytm error in '.$config['site_title'].', status from Paytm');

        $error_msg = "Transaction was not successful: Last gateway response was: ".$_POST["RESPMSG"];
        payment_error("error",$error_msg,$access_token);
        exit();
    }
}
<?php
load_all_option_in_template($config);
$timezone = get_option("timezone");
date_default_timezone_set($timezone);
$date = new DateTime("now", new DateTimeZone($timezone));
$timenow = date('Y-m-d H:i:s');

if (isset($config['quickad_debug']) && $config['quickad_debug'] == 1) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    ini_set('log_errors', TRUE); // Error/Exception file logging engine.
    ini_set('error_log', ROOTPATH . '/errors.log'); // Logging file path
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}

/**
 * Check script is installed
 */
function checkinstall()
{
    global $config;
    if (!isset($config['installed'])) {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
        $site_url = $protocol . $_SERVER['HTTP_HOST'] . str_replace("index.php", "", $_SERVER['PHP_SELF']);
        header("Location: " . $site_url . "install/");
        exit;
    }
}

/**
 * Get db connection
 *
 * @return mysqli|void
 */
function db_connect()
{
    global $config;
    checkinstall();
    // Create connection in MYsqli
    $db_connection = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);
    $db_connection->set_charset('utf8mb4');
    // Check connection in MYsqli
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    return $db_connection;
}

/**
 * Get user location by ip address
 *
 * @return string[]
 */
function getLocationInfoByIp()
{
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = @$_SERVER['REMOTE_ADDR'];
    $result = array('country' => '', 'city' => '');
    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }
    if ($ip != "::1") {
        try {
            require_once ROOTPATH . '/includes/database/geoip/autoload.php';
            // Country DB
            $reader = new \MaxMind\Db\Reader(ROOTPATH . '/includes/database/geoip/geo_country.mmdb');
            $data = $reader->get($ip);
            $result['countryCode'] = @strtoupper(trim($data['country']['iso_code']));
            $result['country'] = trim($data['country']['names']['en']);
        } catch (Exception $e) {
            error_log($e->getMessage());

            $result['countryCode'] = "";
            $result['country'] = "";
        }
    } else {
        $result['countryCode'] = "";
        $result['country'] = "";
    }

    return $result;
}


/**
 * Create template header
 *
 * @param string $page_title
 * @param string $meta_desc
 * @param string $meta_image
 * @param bool $meta_article
 * @return false|string
 */
function overall_header($page_title = '', $meta_desc = '', $meta_image = '', $meta_article = false)
{
    global $config;
    checkinstall();
    $fullname = '';
    $balance = '';
    if (isset($_SESSION['user']['id'])) {
        $userdata = get_user_data(null, $_SESSION['user']['id']);
        $fullname = $userdata['name'];
        $balance = $userdata['balance'];
    }
    $config['lang'] = check_user_lang();

    if (!isset($config['location_track_icon'])) {
        update_option("location_track_icon", '1');
        update_option("auto_detect_location", 'no');
        update_option("live_location_api", 'ip_api');
    }

    $page_title = ($page_title != '') ? $page_title . ' - ' . $config['site_title'] : $config['site_title'];
    $page_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $meta_desc = ($meta_desc == '') ? $config['meta_description'] : $meta_desc;

    if (empty($meta_image)) {
        if(get_option('site_metaimage')){
            $meta_image = $config['site_url'] . 'storage/logo/' . get_option('site_metaimage');
        } else {
            $meta_image = $config['site_url'] . 'storage/logo/' . $config['site_logo'];
        }

    }

    if ($meta_article) {
        $meta_content = 'article';
    } else {
        $meta_content = 'website';
    }

    $html_pages = ORM::for_table($config['db']['pre'] . 'pages')
        ->where('translation_lang', $config['lang_code'])
        ->where('type', '1')
        ->find_many();

    //Print Template 'overall_header'
    HtmlTemplate::display('overall_header', array(
        'page_title' => $page_title,
        'page_link' => $page_link,
        'meta_desc' => $meta_desc,
        'meta_content' => $meta_content,
        'meta_image' => $meta_image,
        'tpl_url' => $config['site_url'] . 'templates/' . $config['tpl_name'],
        'lang_direction' => get_current_lang_direction(),
        'fullname' => $fullname,
        'balance' => $balance,
        'languages' => get_language_list('', 'selected', true),
        'html_pages' => $html_pages,
    ));

}


/**
 * Create template footer
 *
 * @return false|string
 */
function overall_footer()
{
    HtmlTemplate::display('overall_footer', array(
        'htmlpages' => get_html_pages(),
        'ref_url' => $_SERVER['REQUEST_URI']
    ));
}

/**
 * Get real value of url
 *
 * @param string $link
 * @return string
 */
function get_the_value($link)
{
    //If it's not empty
    if (!empty($link)) {
        //If it begins with https...
        if (preg_match('/^https/', $link)) {
            //...then we'll set the $url_prefix variable to https://
            $url_prefix = 'https://';
        } else {
            //If it does not begin with https we'll use http
            $url_prefix = 'http://';
        }
        //Get rid of the http:// or https://
        $link = str_replace(array('http://', 'https://'), '', $link);
        return check_www_in_url($link);
    }
    return $link;
}

/**
 * Remove www from url
 *
 * @param string $link
 * @return string
 */
function check_www_in_url($link)
{
    $params = array();
    //If it's not empty
    if (!empty($link)) {
        $params = explode('.', $link);

        if ($params[0] == 'www') {
            // www exists
        } else {
            // non www
        }
        //Get rid of the www.
        return $link = str_replace("www.", '', $link);
    }
    return $link;
}

/**
 * Get site url with valid protocols
 *
 * @param string $site_url
 * @return string
 */
function get_site_url($site_url)
{
    //If it's not empty
    if (!empty($site_url)) {
        // Check if SSL enabled
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']))
            $protocol = $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https" ? "https://" : "http://";
        else
            $protocol = !empty($_SERVER['HTTPS']) && $_SERVER["HTTPS"] != "off" ? "https://" : "http://";

        $link = get_the_value($site_url);

        $params = explode('.', $_SERVER["HTTP_HOST"]);

        if ($params[0] == 'www') {
            // www exists
            $link = "www." . $link;
        } else {
            // non www
        }

        return $site_url = $protocol . $link;
    }
    return $site_url;
}

/**
 * Load all options in config
 *
 * @param array $config
 */
function load_all_option_in_template(&$config)
{

    $info = ORM::for_table($config['db']['pre'] . 'options')
        ->find_many();

    foreach ($info as $data) {

        $key = $data['option_name'];
        $value = $data['option_value'];
        if ($key == 'lang')
            $config['default_lang'] = $value;

        if ($key == 'site_url') {
            $value = get_site_url($value);
        }
        if ($key == 'app_url') {
            $site_url = get_site_url($value);
            $value = $site_url . "php/";
        }
        $config[$key] = stripslashes((string)$value);
    }
}

/**
 * Add post option
 *
 * @param int $post_id
 * @param string $option
 * @param mixed $value
 * @return array|false|mixed
 * @throws Exception
 */
function add_post_option($post_id, $option, $value = null)
{

    global $config;
    $option = trim($option);
    if (empty($option))
        return false;

    $option_id = ORM::for_table($config['db']['pre'] . 'post_options')->create();
    $option_id->post_id = $post_id;
    $option_id->option_name = $option;
    $option_id->option_value = $value;
    $option_id->save();

    return $option_id->id();
}

/**
 * Get post option
 *
 * @param int $post_id
 * @param string $option
 * @param null $default
 * @return array|mixed|null
 */
function get_post_option($post_id, $option, $default = null)
{

    global $config;
    $option = trim($option);
    if (empty($option))
        return $default;

    $result = ORM::for_table($config['db']['pre'] . 'post_options')
        ->where('option_name', $option)
        ->where('post_id', $post_id)
        ->find_one();
    if (isset($result['option_value']))
        return $result['option_value'];
    else
        return $default;
}

/**
 * Check post option exist
 *
 * @param int $post_id
 * @param string $option
 * @return bool
 */
function check_post_option_exist($post_id, $option)
{

    global $config;
    $option = trim($option);
    if (empty($option))
        return false;

    $num_rows = ORM::for_table($config['db']['pre'] . 'post_options')
        ->where('option_name', $option)
        ->where('post_id', $post_id)
        ->count();
    if ($num_rows != 0)
        return true;
    else
        return false;
}

/**
 * Update post option
 *
 * @param int $post_id
 * @param string $option
 * @param mixed $value
 * @return bool
 * @throws Exception
 */
function update_post_option($post_id, $option, $value)
{

    global $config;
    $option = trim($option);
    if (empty($option))
        return false;

    if (check_post_option_exist($post_id, $option)) {
        $pdo = ORM::get_db();
        $data = [
            'post_id' => $post_id,
            'option_value' => $value,
            'option_name' => $option
        ];
        $sql = "UPDATE " . $config['db']['pre'] . "post_options SET option_value=:option_value WHERE option_name=:option_name AND post_id=:post_id";
        $query_result = $pdo->prepare($sql)->execute($data);

        if (!$query_result)
            return false;
        else
            return true;
    } else {
        add_post_option($post_id, $option, $value);
        return true;
    }
}

/**
 * Delete post option
 *
 * @param int $post_id
 * @param string $option
 * @return bool
 */
function delete_post_option($post_id, $option)
{

    global $config;
    $option = trim($option);
    if (empty($option))
        return false;

    $result = ORM::for_table($config['db']['pre'] . 'post_options')
        ->where('option_name', $option)
        ->where('post_id', $post_id)
        ->delete_many();

    if (!$result)
        return false;
    else
        return true;
}

/**
 * add option
 *
 * @param string $option
 * @param mixed $value
 * @return array|false|mixed
 * @throws Exception
 */
function add_option($option, $value = '')
{
    global $config;
    $option = trim($option);
    if (empty($option))
        return false;

    $option_id = ORM::for_table($config['db']['pre'] . 'options')->create();
    $option_id->option_name = $option;
    $option_id->option_value = $value;
    $option_id->save();

    return $option_id->id();
}

/**
 * get option
 *
 * @param string $option
 * @param mixed $default
 * @return mixed|null
 */
function get_option($option, $default = null)
{

    global $config;

    $option = trim($option);
    if (isset($config[$option])) {
        return $config[$option];
    } else {
        load_all_option_in_template($config);
        if (!isset($config[$option])) {
            return $default;
        }
        return $config[$option];
    }
}

/**
 * check option exist
 *
 * @param string $option
 * @return bool
 */
function check_option_exist($option)
{

    global $config;
    $option = trim($option);
    if (empty($option))
        return false;

    $num_rows = ORM::for_table($config['db']['pre'] . 'options')
        ->where('option_name', $option)
        ->count();
    if ($num_rows == 1)
        return true;
    else
        return false;
}

/**
 * update option
 *
 * @param string $option
 * @param mixed $value
 * @return bool
 * @throws Exception
 */
function update_option($option, $value)
{

    global $config;
    $option = trim($option);
    if (empty($option))
        return false;

    if (check_option_exist($option)) {
        $pdo = ORM::get_db();
        $data = [
            'option_value' => $value,
            'option_name' => $option
        ];
        $sql = "UPDATE " . $config['db']['pre'] . "options SET option_value=:option_value WHERE option_name=:option_name";
        $query_result = $pdo->prepare($sql)->execute($data);

        if (!$query_result)
            return false;
        else
            return true;
    } else {
        add_option($option, $value);
        return true;
    }
}

/**
 * delete option
 *
 * @param string $option
 * @return bool
 */
function delete_option($option)
{

    global $config;
    $option = trim($option);
    if (empty($option))
        return false;

    $result = ORM::for_table($config['db']['pre'] . 'options')
        ->where_equal('option_name', $option)
        ->delete_many();

    if (!$result)
        return false;
    else
        return true;
}

/**
 * Get list of available languages
 *
 * @return array
 */
function get_lang_list()
{

    global $config;
    $langs = array();

    if ($handle = opendir('includes/lang/')) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $langv = str_replace('.php', '', $file);
                $langv = str_replace('lang_', '', $langv);

                $langs[]['value'] = $langv;
            }
        }
        closedir($handle);
    }

    sort($langs);

    foreach ($langs as $key => $value) {
        if ($config['lang'] == $value['value']) {
            $langs[$key]['name'] = ucwords($value['value']);
            $langs[$key]['selected'] = 'selected';
        } else {
            $langs[$key]['name'] = ucwords($value['value']);
            $langs[$key]['selected'] = '';
        }
    }

    return $langs;
}

/**
 * Get file extension
 *
 * @param $str
 * @return false|string
 */
function getExtension($str)
{
    $file_ext = explode(".", $str);
    return end($file_ext);
}

/**
 * ---------- SIMPLE UPLOAD ----------
 * we create an instance of the class, giving as argument the PHP object
 * corresponding to the file field from the form
 * All the uploads are accessible from the PHP object $_FILES
 * set variables
 * @param $field_name
 * @param $target_dir
 * @return array
 */
function quick_file_upload($field_name, $target_dir, $allowed_type = null)
{
    $result = array();
    if (isset($_FILES[$field_name])) {
        $handle = new Verot\Upload\Upload($_FILES[$field_name]);
        //allowed array of allowed mime-types or file extensions (or one string). wildcard accepted, as in image/* (default: check init())
        //i.e. : $handle->allowed = array('application/pdf','application/msword', 'image/*');
        //OR
        //i.e. : $handle->allowed = array('png','pdf','jpg');
        if ($allowed_type == null) {
            $handle->allowed = 'image/*';
        } else {
            $handle->allowed = $allowed_type;
        }
        $handle->file_new_name_body = rand();
        // then we check if the file has been uploaded properly
        // in its *temporary* location in the server (often, it is /tmp)
        if ($handle->uploaded) {
            // yes, the file is on the server
            // now, we start the upload 'process'. That is, to copy the uploaded file
            // from its temporary location to the wanted location
            // It could be something like $handle->process('/home/www/storage/');
            $handle->process($target_dir);

            if ($handle->processed) {
                $result['success'] = true;
                $result['file_name'] = $handle->file_dst_name;
            } else {
                $result['success'] = false;
                $result['error'] = $handle->error;
            }
            // we delete the temporary files
            $handle->clean();
            return $result;
        } else {
            // if we're here, the upload file failed for some reasons
            // i.e. the server didn't receive the file
            $result['success'] = false;
            $result['error'] = $handle->error;
            return $result;
        }
    }

    $result['success'] = false;
    $result['error'] = "File not submitted";
    return $result;
}

/**
 * File upload
 *
 * @param string $path
 * @param array $files
 * @param string $type_file
 * @param string $title
 * @param int $reqwid
 * @param int $reqhei
 * @param bool $Anysize
 * @param null $unlink
 * @return int|string
 */
function fileUpload($path, $files, $type_file, $title, $reqwid, $reqhei, $Anysize = false, $unlink = null)
{

    $target_dir = $path;
    $target_file = $target_dir . basename($files["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

    $random1 = rand(9999, 100000);
    $random2 = rand(9999, 200000);
    $image_title = $title . '_' . $random1 . $random2 . '.' . $imageFileType;

    $newname = $target_dir . $image_title;

    $error = "";
    if ($type_file == "image") {
        list($width, $height) = getimagesize($files["tmp_name"]);
        if ($Anysize) {
            $uploadedfile = $files["tmp_name"];

            if ($imageFileType == "jpg" || $imageFileType == "jpeg") {
                $src = imagecreatefromjpeg($uploadedfile);
            } else if ($imageFileType == "png") {
                $src = imagecreatefrompng($uploadedfile);
            } else {
                $src = imagecreatefromgif($uploadedfile);
            }

            $thumb_width = $reqwid;
            $thumb_height = $reqhei;

            $width = imagesx($src);
            $height = imagesy($src);

            $original_aspect = $width / $height;
            $thumb_aspect = $thumb_width / $thumb_height;

            if ($original_aspect >= $thumb_aspect) {
                // If image is wider than thumbnail (in aspect ratio sense)
                $new_height = $thumb_height;
                $new_width = $width / ($height / $thumb_height);
            } else {
                // If the thumbnail is wider than the image
                $new_width = $thumb_width;
                $new_height = $height / ($width / $thumb_width);
            }

            $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

            // Resize and crop
            imagecopyresampled($thumb,
                $src,
                0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                0, 0,
                $new_width, $new_height,
                $width, $height);

            $image_name = $image_title;

            $filename = $target_dir . $image_name;

            imagejpeg($thumb, $filename, 80);

            imagedestroy($src);
            imagedestroy($thumb);

            //Moving file to uploads folder
            if ($filename) {
                if ($unlink != null) {
                    $filename = $target_dir . $unlink;
                    if (file_exists($filename)) {
                        unlink($filename);
                    }
                }
                move_uploaded_file($files["tmp_name"], $newname);
                $success = "The file " . basename($files["name"]) . " has been uploaded.";
                return $image_title;
            } else {
                $error = "Sorry, there was an error uploading your file.";
                return "";
            }

        } else {
            //Check width height
            if ($reqwid != $width && $reqhei != $height) {
                $error = "Sorry, only dimension" . $width . "x" . $height . "files are allowed.";
                $uploadOk = 0;
            }
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $error = "Sorry, only JPG, JPEG & PNG files are allowed.";
            $uploadOk = 0;
        }
    } elseif ($type_file == "zip") {
        // Allow certain file formats
        if ($imageFileType != "zip") {
            $error = "Sorry, only Zip file are allowed.";
            $uploadOk = 0;
        }
    } else {
        //Any type accepted
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $error = "Sorry, your file was not uploaded.";
        return 0;
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($files["tmp_name"], $newname)) {
            if ($unlink != null) {
                $filename = $target_dir . $unlink;
                unlink($filename);
            }
            $success = "The file " . basename($files["name"]) . " has been uploaded.";
            return $image_title;
        } else {
            $error = "Sorry, there was an error uploading your file.";
            return "";
        }
    }
}

/**
 * Resize and crop image by center
 *
 * @param int $max_width
 * @param int $max_height
 * @param string $dst_dir
 * @param string $source_file
 * @param int $quality
 * @return bool
 */
function resize_crop_image($max_width, $max_height, $dst_dir, $source_file, $quality = 80)
{
    $imgsize = getimagesize($source_file);
    $width = $imgsize[0];
    $height = $imgsize[1];
    $mime = $imgsize['mime'];

    switch ($mime) {
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image = "imagegif";
            break;

        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image = "imagepng";
            $quality = 7;
            break;

        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image = "imagejpeg";
            $quality = 80;
            break;

        default:
            return false;
            break;
    }

    $dst_img = imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($source_file);

    $width_new = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
    if ($width_new > $width) {
        //cut point by height
        $h_point = (($height - $height_new) / 2);
        //copy image
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    } else {
        //cut point by width
        $w_point = (($width - $width_new) / 2);
        imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }

    $image($dst_img, $dst_dir, $quality);

    if ($dst_img) imagedestroy($dst_img);
    if ($src_img) imagedestroy($src_img);
    return true;
}

/**
 * Resize image
 *
 * @param int $newwidth
 * @param string $filename
 * @param string $uploadedfile
 * @return bool
 */
function resizeImage($newwidth, $filename, $uploadedfile)
{
    $info = getimagesize($uploadedfile);
    $ext = $info['mime'];

    list($width, $height) = getimagesize($uploadedfile);

    $newheight = ($height / $width) * $newwidth;
    $tmp = imagecreatetruecolor($newwidth, $newheight);

    switch ($ext) {
        case 'image/jpeg':
            $src = imagecreatefromjpeg($uploadedfile);
            @imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagejpeg($tmp, $filename, 100);
            @imagedestroy($src);
            break;

        case 'image/png':
            $src = imagecreatefrompng($uploadedfile);
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagepng($tmp, $filename, 5);
            @imagedestroy($src);
            break;
    }
    @imagedestroy($tmp);
    return true;
}

/**
 * Get human-readable time difference (Ex. 2 days ago)
 *
 * @param string $timestamp
 * @return string
 */
function timeAgo($timestamp)
{
    global $lang;
    //$time_now = mktime(date('h')+0,date('i')+30,date('s'));
    $datetime1 = new DateTime("now");
    $datetime2 = date_create($timestamp);
    $diff = date_diff($datetime1, $datetime2);
    $timemsg = '';
    if ($diff->y > 0) {
        $timemsg = $diff->y . ' ' . ($diff->y > 1 ? __("years") : __("year"));
    } else if ($diff->m > 0) {
        $timemsg = $diff->m . ' ' . ($diff->m > 1 ? __("months") : __("month"));
    } else if ($diff->d > 0) {
        $timemsg = $diff->d . ' ' . ($diff->d > 1 ? __("days") : __("day"));
    } else if ($diff->h > 0) {
        $timemsg = $diff->h . ' ' . ($diff->h > 1 ? __("hours") : __("hour"));
    } else if ($diff->i > 0) {
        $timemsg = $diff->i . ' ' . ($diff->i > 1 ? __("minutes") : __("minute"));
    } else if ($diff->s > 0) {
        $timemsg = $diff->s . ' ' . ($diff->s > 1 ? __("seconds") : __("seconds"));
    }
    if ($timemsg == "")
        $timemsg = __("Just now");
    else
        $timemsg = $timemsg . ' ' . __("ago");

    return $timemsg;
}

/**
 * Get data for pagination
 *
 * @param int $total
 * @param int $page
 * @param int $perpage
 * @param string $url
 * @param int $posts
 * @param bool $seo_url
 * @return array
 */
function pagenav($total, $page, $perpage, $url, $posts = 0, $seo_url = false)
{
    $page_arr = array();
    $arr_count = 0;

    if ($posts) {
        $symb = '&';
    } else {
        $symb = '?';
    }
    $total_pages = ceil($total / $perpage);
    $llimit = 1;
    $rlimit = $total_pages;
    $window = 5;
    $html = '';
    if ($page < 1 || !$page) {
        $page = 1;
    }

    if (($page - floor($window / 2)) <= 0) {
        $llimit = 1;
        if ($window > $total_pages) {
            $rlimit = $total_pages;
        } else {
            $rlimit = $window;
        }
    } else {
        if (($page + floor($window / 2)) > $total_pages) {
            if ($total_pages - $window < 0) {
                $llimit = 1;
            } else {
                $llimit = $total_pages - $window + 1;
            }
            $rlimit = $total_pages;
        } else {
            $llimit = $page - floor($window / 2);
            $rlimit = $page + floor($window / 2);
        }
    }
    if ($page > 1) {
        $page_arr[$arr_count]['title'] = '<i class="fa fa-angle-left"></i>';
        if ($seo_url)
            $page_arr[$arr_count]['link'] = $url . '/' . ($page - 1);
        else
            $page_arr[$arr_count]['link'] = $url . $symb . 'page=' . ($page - 1);

        $page_arr[$arr_count]['current'] = 0;

        $arr_count++;
    }

    for ($x = $llimit; $x <= $rlimit; $x++) {
        if ($x <> $page) {
            $page_arr[$arr_count]['title'] = $x;
            if ($seo_url)
                $page_arr[$arr_count]['link'] = $url . '/' . ($x);
            else
                $page_arr[$arr_count]['link'] = $url . $symb . 'page=' . ($x);


            $page_arr[$arr_count]['current'] = 0;
        } else {
            $page_arr[$arr_count]['title'] = $x;
            if ($seo_url)
                $page_arr[$arr_count]['link'] = $url . '/' . ($x);
            else
                $page_arr[$arr_count]['link'] = $url . $symb . 'page=' . ($x);
            $page_arr[$arr_count]['current'] = 1;
        }

        $arr_count++;
    }

    if ($page < $total_pages) {
        $page_arr[$arr_count]['title'] = '<i class="fa fa-angle-right"></i>';
        if ($seo_url)
            $page_arr[$arr_count]['link'] = $url . '/' . ($page + 1);
        else
            $page_arr[$arr_count]['link'] = $url . $symb . 'page=' . ($page + 1);
        $page_arr[$arr_count]['current'] = 0;

        $arr_count++;
    }

    return $page_arr;
}

/**
 * Print error message
 *
 * @param string $msg
 * @param string $line
 * @param string $file
 * @param int $formatted
 */
function error($message, $line = '', $file = '', $formatted = 0)
{
    if ($formatted == 0) {
        echo "Low Level Error: " . $message . " " . $file . " " . $line;
    } else {
        $content = "";
        HtmlTemplate::display('global/error', compact('message', 'content'));
    }
    exit;
}

/**
 * Print error content
 *
 * @param string $message
 * @param string $content
 */
function error_content($message, $content = "")
{
    //Print Template
    HtmlTemplate::display('global/error', array(
        'message' => $message,
        'content' => $content
    ));
    exit;
}

/**
 * Send email
 *
 * @param string $email_to
 * @param string $email_to_name
 * @param string $email_subject
 * @param string $email_body
 * @param array $bcc
 * @param null $email_reply_to
 * @param null $email_reply_to_name
 */
function email($email_to, $email_to_name, $email_subject, $email_body, $bcc = array(), $email_reply_to = null, $email_reply_to_name = null)
{

    global $config;
    if ($config['email_template']) {
        $email_subject = stripcslashes(nl2br($email_subject));
    }

    return include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "../lib/phpmailer/init.engine.php");

}

/**
 * Show message
 *
 * @param string $heading
 * @param string $message
 * @param string $forward
 * @param bool $back
 */
function message($heading, $message, $forward = '', $back = true)
{
    if ($forward == '') {
        if ($back) {
            //Print Template 'Message alert Page'
            HtmlTemplate::display('global/message', compact('heading', 'message'));
        } else {
            HtmlTemplate::display('global/message_noback', compact('heading', 'message'));
        }
    } else {
        HtmlTemplate::display('global/message_forward', compact('heading', 'message', 'forward'));
    }
    exit;
}

/**
 * Redirect with message
 *
 * @param string $url
 * @param string $msg
 * @param string $page_title
 */
function transfer($url, $msg, $page_title = '')
{

    global $config;
    if (!$config['transfer_filter']) {
        header("Location: " . $url);
        exit;
    }
    ob_start();
    echo "<html>\n";
    echo "<head>\n";
    echo "<title>\n";
    echo $page_title;
    echo "</title>\n";
    echo "<STYLE>\n";
    echo "<!--\n";
    echo "TABLE, TR, TD                { font-family:Verdana, Tahoma, Arial;font-size: 7.5pt; color:#000000}\n";
    echo "a:link, a:visited, a:active  { text-decoration:underline; color:#000000 }\n";
    echo "a:hover                      { color:#465584 }\n";
    echo "#alt1   { font-size: 16px; }\n";
    echo "body {\n";
    echo "	background-color: #e8ebf1\n";
    echo "	z-index: 99999\n";
    echo "}\n";
    echo "-->\n";
    echo "</STYLE>\n";
    echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
    echo "function changeurl(){\n";
    echo "window.location='" . $url . "';\n";
    echo "}\n";
    echo "</script>\n";
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"></head>\n";
    echo "<body onload=\"window.setTimeout('changeurl();',2000);\">\n";
    echo "<table width='85%' height='85%' style='margin: 100px'>\n";
    echo "<tr>\n";
    echo "<td valign='middle'>\n";
    echo "<table align='center' border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"#fff\">\n";
    echo "<tr>\n";
    echo "<td id='mainbg'>";
    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"12\">\n";
    echo "<tr>\n";
    echo "<td width=\"100%\" align=\"center\" id=alt1>\n";
    echo $msg . "<br><br>\n";
    echo "<div><img src=\"" . $config['site_url'] . "includes/assets/images/loading.gif\"/></div><br><br>\n";
    echo "(<a href='" . $url . "'>".__('Or click here if you do not wish to wait')."</a>)</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</body></html>\n";
    ob_end_flush();
}

/**
 * Get domain from email
 *
 * @param string $email
 * @return string
 */
function get_domain($email)
{

    $domain = implode('.', array_slice(preg_split("/(\.|@)/", $email), -2));

    return strtolower($domain);
}

/**
 * Get valid IP address
 *
 * @param $server
 * @param $env
 * @return string
 */
function encode_ip($server = '', $env = '')
{

    if (getenv('HTTP_X_FORWARDED_FOR') != '') {
        $client_ip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : '');

        $entries = explode(',', getenv('HTTP_X_FORWARDED_FOR'));
        reset($entries);
        foreach ($entries as $entry) {
            $entry = trim($entry);
            if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)) {
                $private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/', '/^10\..*/', '/^224\..*/', '/^240\..*/');
                $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

                if ($client_ip != $found_ip) {
                    $client_ip = $found_ip;
                    break;
                }
            }
        }
    } else {
        $client_ip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : '');
    }

    return $client_ip;
}

/**
 * @return array|false|string
 * @deprecated
 *
 */
function get_client_ip()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

/**
 * Validate request's data
 *
 * @param string|array $input
 * @param bool $allow_html
 * @param bool $allow_iframe
 * @return string|array
 */
function validate_input($input, $allow_html = false, $allow_iframe = false)
{
    if ($input == null) {
        return $input;
    }
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = validate_input($value, $allow_html);
        }
        return $input;
    } else {

        if ($allow_html) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.Nofollow', true);
            $config->set('HTML.TargetBlank', true);
            $config->set('Attr.EnableID', true);
            $config->set('CSS.AllowTricky', true);

            if($allow_iframe) {
                $config->set('HTML.SafeIframe', true);
                $config->set('URI.SafeIframeRegexp', '%^(https?:)?//%'); //allow Safe Url
            }
            $config->set('HTML.Allowed', 'p,b,i,em,br,strong,div,a,span,ul,ol,li,img,table,thead,th,tr,td,iframe');
            $config->set('HTML.AllowedAttributes', 'a.href,a.target,a.rel,a.id,src, height, width, alt, style, class');

            $def = $config->getHTMLDefinition(true);
            $def->addAttribute('iframe', 'allowfullscreen', 'Bool');

            $purifier = new HTMLPurifier($config);
            $input = $purifier->purify($input);
        } else {
            $input = strip_tags($input);
        }

        return $input;
    }
}

/**
 * Limit string with length
 *
 * @param string $str
 * @param int $limit
 * @return string
 */
function strlimiter($str, $limit)
{

    if (mb_strlen($str) > $limit)
        $string = mb_substr($str, 0, $limit) . '...';
    else
        $string = $str;

    return $string;
}

/**
 * Redirect parent window by js
 *
 * @param string $url
 * @param false $close
 */
function redirect_parent($url, $close = false)
{

    echo "<script type='text/javascript'>";
    if ($close) {
        echo "window.close(); ";
        echo "window.opener.location.href='$url'";
    } else {
        echo "window.location.href='$url'";
    }
    echo "</script>";

}

// Todo: Improve this function
/**
 * Convert currencies based on market rates
 *
 * @param string $from_Currency
 * @param string $to_Currency
 * @param float|int $amount
 * @return float|int
 */
function currencyConverter($from_Currency, $to_Currency, $amount)
{
    $from_Currency = urlencode($from_Currency);
    $to_Currency = urlencode($to_Currency);
    $get = file_get_contents("https://finance.google.com/finance/converter?a=1&from=$from_Currency&to=$to_Currency");
    $get = explode("<span class=bld>", $get);
    $get = explode("</span>", $get[1]);
    $exchange_rate = preg_replace("/[^0-9\.]/", null, $get[0]);
    $converted_currency = $exchange_rate * $amount;
    return $converted_currency;

    // change amount according to your needs
    //$amount = 100;
    // change From Currency according to your needs
    //$from_Curr = "USD";
    // change To Currency according to your needs
    //$to_Curr = "INR";

    //$converted_currency = currencyConverter($from_Curr, $to_Curr, $amount);
    // Print outout
    //echo $converted_currency;
}

/**
 * Get valid number
 *
 * @param $number
 * @return int|float|string
 */
function rawFormat($number)
{
    if (is_numeric($number)) {
        return $number;
    }
    $number = trim($number);
    $number = strtr($number, array(' ' => ''));
    $number = preg_replace('/ +/', '', $number);
    $number = str_replace(',', '.', $number);
    $number = preg_replace('/[^0-9\.]/', '', $number);

    return $number;
}

/**
 * Get random string
 *
 * @param int $length
 * @return string
 */
function get_random_string($length = 10)
{
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}

/**
 * PHP redirect
 *
 * @param string $url
 */
function headerRedirect($url)
{
    header("Location: $url");
}

/**
 * Log actions
 *
 * @param string $summary
 * @param string $details
 */
function log_adm_action($summary, $details)
{
    global $config;
    $now = time();
    $logs = ORM::for_table($config['db']['pre'] . 'logs')->create();
    $logs->log_date = $now;
    $logs->log_summary = $summary;
    $logs->log_details = $details;
    $logs->save();
}

/**
 * Get name from email
 *
 * @param string $text
 * @return string
 */
function parse_name_from_email($text)
{
    list($text) = explode('@', $text);
    $text = preg_replace('/[^a-z0-9]/i', '', $text);
    return $text;
}

/**
 * Remove email and phone from string
 *
 * @param string $string
 * @return string
 */
function removeEmailAndPhoneFromString($string)
{
    // remove email
    $string = preg_replace('/([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/', '', $string);

    // remove phone
    $string = preg_replace('/([0-9]+[\- ]?[0-9]+)/', '', $string);

    return $string;
}

/**
 * Get human-readable currency format (Ex. 10K, 10M)
 *
 * @param float $num
 * @return string
 */
function thousandsCurrencyFormat($num)
{
    if ($num > 1000) {
        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('K', 'M', 'B', 'T');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int)$x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;
    }
    return $num;
}

/**
 * Base64 url encode
 *
 * @param string $input
 * @return string
 */
function base64_url_encode($input)
{
    return strtr(base64_encode($input), '+/=', '._-');
}

/**
 * Base64 url decode
 *
 * @param string $input
 * @return string
 */
function base64_url_decode($input)
{
    return base64_decode(strtr($input, '._-', '+/='));
}

/**
 * Sanitize
 *
 * @param string $text
 * @return string
 */
function sanitize($text)
{
    $text = htmlspecialchars($text, ENT_QUOTES);
    $text = str_replace("\n\r", "\n", $text);
    $text = str_replace("\r\n", "\n", $text);
    $text = str_replace("\n", "<br>", $text);
    return $text;
}

/**
 * De-sanitize
 *
 * @param string $text
 * @return string
 */
function de_sanitize($text)
{
    $text = str_replace("<br>", "\n", $text);
    return $text;
}

/**
 * Escape string
 *
 * @param string $text
 * @param bool $htmlspecialchars
 * @return string
 */
function escape($text)
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize string
 *
 * @param string $text
 * @return mixed
 */
function sanitize_string($text)
{
    return filter_var($text, FILTER_SANITIZE_STRING);
}

/**
 * Get random id
 *
 * @return string
 */
function get_random_id()
{
    $random = '';
    for ($i = 1; $i <= 8; $i++) {
        $random .= mt_rand(0, 9);
    }
    return $random;
}

/**
 * Get html pages
 *
 * @return array
 */
function get_html_pages()
{

    global $config;
    $htmlPages = array();
    $result = ORM::for_table($config['db']['pre'] . 'pages')
        ->where('translation_lang', $config['lang_code'])
        ->find_many();

    foreach ($result as $info) {
        $htmlPages[$info['id']]['id'] = $info['id'];
        $htmlPages[$info['id']]['title'] = $info['title'];

        $htmlPages[$info['id']]['link'] = $config['site_url'] . 'page/' . $info['slug'];

    }
    return $htmlPages;
}

/**
 * Get country name by code
 *
 * @param string $code
 * @return string
 */
function get_countryName_by_code($code)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'countries')
        ->select('asciiname')
        ->where('code', $code)
        ->find_one();
    return (!empty($info['asciiname'])) ? $info['asciiname'] : '';
}

/**
 * Get state name by code
 *
 * @param string $code
 * @return string
 */
function get_stateName_by_code($code)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'subadmin1')
        ->select('asciiname')
        ->where('code', $code)
        ->find_one();

    return (!empty($info['asciiname'])) ? $info['asciiname'] : '';
}

/**
 * Get district by code
 *
 * @param string $code
 * @return string
 */
function get_district_by_code($code)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'subadmin2')
        ->select('asciiname')
        ->where('code', $code)
        ->find_one();
    return (!empty($info['asciiname'])) ? $info['asciiname'] : '';
}

/**
 * get currency by country code
 *
 * @param string $code
 * @return string
 */
function get_countryCurrecny_by_code($code)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'countries')
        ->select('currency_code')
        ->where('code', $code)
        ->find_one();
    return (!empty($info['currency_code'])) ? $info['currency_code'] : '';
}

/**
 * Get currency by id
 *
 * @param int $id
 * @return false|ORM
 */
function get_currency_by_id($id)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'currencies')
        ->where('id', $id)
        ->find_one();
    return $info;
}

/**
 * Get currency by code
 *
 * @param string $code
 * @return false|ORM
 */
function get_currency_by_code($code)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'currencies')
        ->where('code', $code)
        ->find_one();
    return $info;
}

/**
 * Format price based on country
 *
 * @param float|int $number
 * @param string|null $code (country_code or currency_code)
 * @return float|int|string
 */
function price_format($number, $code = null)
{
    global $config;
    if (empty($number) or $number == '0' or $number < 1)
        return $number;

    if (empty($code)) {
        $code = $config['specific_country'];
        $currency = set_user_currency($code);
    } elseif (strlen($code) == 2) {
        $currency = set_user_currency($code);
    } else {
        $currency = ORM::for_table($config['db']['pre'] . 'currencies')
            ->where('code', $code)
            ->find_one();
    }
    // Convert string to numeric
    $number = rawFormat($number);

    // Currency format - Ex: USD 100,234.56 | EUR 100 234,56
    $number = number_format($number, (int)$currency['decimal_places'], $currency['decimal_separator'], $currency['thousand_separator']);

    //$tmp = explode($currency['decimal_places'], $number);

    if ($currency['in_left'] == 1) {
        $number = $currency['html_entity'] . $number;
    } else {
        $number = $number . ' ' . $currency['html_entity'];
    }

    // Remove decimal value if it's null
    $defaultDecimal = str_pad('', (int)$currency['decimal_places'], '0');
    $number = str_replace($currency['decimal_separator'] . $defaultDecimal, '', $number);

    return $number;
}

/**
 * Get currency list
 *
 * @param string $selected
 * @param string $selected_text
 * @return array
 */
function get_currency_list($selected = "", $selected_text = 'selected')
{

    global $config;
    $currencies = array();
    $count = 0;
    $result = ORM::for_table($config['db']['pre'] . 'currencies')
        ->order_by_asc('name')
        ->find_many();
    foreach ($result as $info) {
        $currencies[$count]['id'] = $info['id'];
        $currencies[$count]['code'] = $info['code'];
        $currencies[$count]['name'] = $info['name'];
        $currencies[$count]['html_entity'] = $info['html_entity'];
        $currencies[$count]['in_left'] = $info['in_left'];
        if ($selected != "") {
            if ($selected == $info['id'] or $selected == $info['code']) {
                $currencies[$count]['selected'] = $selected_text;
            } else {
                $currencies[$count]['selected'] = "";
            }
        }
        $count++;
    }

    return $currencies;
}

/**
 * Get timezone list
 *
 * @param string $selected
 * @param string $selected_text
 * @return array
 */
function get_timezone_list($selected = "", $selected_text = 'selected')
{

    global $config;
    $timezones = array();
    $count = 0;
    $result = ORM::for_table($config['db']['pre'] . 'time_zones')
        ->order_by_asc('time_zone_id')
        ->find_many();
    foreach ($result as $info) {
        $timezones[$count]['id'] = $info['id'];
        $timezones[$count]['country_code'] = $info['country_code'];
        $timezones[$count]['time_zone_id'] = $info['time_zone_id'];
        $timezones[$count]['gmt'] = $info['gmt'];
        $timezones[$count]['dst'] = $info['dst'];
        $timezones[$count]['raw'] = $info['raw'];
        if ($selected != "") {
            if ($selected == $info['id'] or $selected == $info['time_zone_id']) {
                $timezones[$count]['selected'] = $selected_text;
            } else {
                $timezones[$count]['selected'] = "";
            }
        }
        $count++;
    }

    return $timezones;
}

/**
 * Get language list
 *
 * @param string $selected
 * @param string $selected_text
 * @param bool $active
 * @return array
 */
function get_language_list($selected = "", $selected_text = 'selected', $active = false)
{

    global $config;
    $language = array();
    $count = 0;
    $where = "";
    if ($active) {
        $result = ORM::for_table($config['db']['pre'] . 'languages')
            ->where('active', 1)
            ->order_by_asc('name')
            ->find_many();
    } else {
        $result = ORM::for_table($config['db']['pre'] . 'languages')
            ->order_by_asc('id')
            ->find_many();
    }
    foreach ($result as $info) {
        $language[$count]['id'] = $info['id'];
        $language[$count]['code'] = $info['code'];
        $language[$count]['direction'] = $info['direction'];
        $language[$count]['name'] = $info['name'];
        $language[$count]['file_name'] = $info['file_name'];
        $language[$count]['active'] = $info['active'];
        $language[$count]['default'] = $info['default'];
        if ($selected != "") {
            if ($selected == $info['id'] or $selected == $info['code']) {
                $language[$count]['selected'] = $selected_text;
            } else {
                $language[$count]['selected'] = "";
            }
        }
        $count++;
    }

    return $language;
}

/**
 * Get language by id
 *
 * @param int $id
 * @return false|ORM
 */
function get_language_by_id($id)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'languages')
        ->where('id', $id)
        ->find_one();

    return $info;
}

/**
 * Get language by code
 *
 * @param string $code
 * @param bool $active
 * @return false|ORM
 */
function get_language_by_code($code, $active = false)
{

    global $config;
    $where = "";

    if ($active) {
        $info = ORM::for_table($config['db']['pre'] . 'languages')
            ->where(array(
                'active' => 1,
                'code' => $code
            ))
            ->find_one();
    } else {
        $info = ORM::for_table($config['db']['pre'] . 'languages')
            ->where('code', $code)
            ->find_one();
    }

    if ($info)
        return $info;
    else
        return false;
}

/**
 * Get lang code by filename
 *
 * @param string $lang
 * @return string
 */
function get_lang_code_by_filename($lang)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'languages')
        ->select('code')
        ->where('file_name', $lang)
        ->find_one();

    return (!empty($info['code'])) ? $info['code'] : '';

}

/**
 * Get current lang direction
 *
 * @return string
 */
function get_current_lang_direction()
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'languages')
        ->select('direction')
        ->where('file_name', $config['lang'])
        ->find_one();

    return (!empty($info['direction'])) ? $info['direction'] : '';
}

/**
 * Get country ID by state id
 *
 * @param string $code
 * @return false|string
 */
function get_countryID_by_state_id($code)
{
    return substr($code, 0, 2);
}

/**
 * Get state name by id
 *
 * @param int $id
 * @return string
 */
function get_stateName_by_id($id)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'subadmin1')
        ->select('asciiname')
        ->where('code', $id)
        ->find_one();
    return (!empty($info['asciiname'])) ? $info['asciiname'] : '';
}

/**
 * Get city name by id
 *
 * @param int $id
 * @return string
 */
function get_cityName_by_id($id)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'cities')
        ->select('asciiname')
        ->where('id', $id)
        ->find_one();
    return (!empty($info['asciiname'])) ? $info['asciiname'] : '';
}

/**
 * Get city detail by id
 *
 * @param int $cityid
 * @return false|ORM
 */
function get_cityDetail_by_id($cityid)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'cities')
        ->where('id', $cityid)
        ->find_one();
    return $info;
}

/**
 * Check country activated
 *
 * @param string $country_code
 * @return bool
 */
function check_country_activated($country_code)
{
    global $config;
    $num_rows = ORM::for_table($config['db']['pre'] . 'countries')
        ->where(array(
            'code' => $country_code,
            'active' => 1
        ))
        ->count();

    if ($num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get country data by id
 *
 * @param int $id
 * @return false|ORM
 */
function get_countryData_by_id($id)
{
    global $config;
    $info = ORM::for_table($config['db']['pre'] . 'countries')
        ->where('code', $id)
        ->find_one();
    return $info;
}

/**
 * Fet lat long of country
 *
 * @param int $country_code
 * @return array|false
 */
function get_lat_long_of_country($country_code)
{
    global $config;
    if (get_option("country_type") == "multi") {
        $country = get_countryData_by_id($country_code);
        $country_name = $country['asciiname'];
        $country_lat = $country['latitude'];
        $country_long = $country['longitude'];

        if ($country_lat != NULL && $country_long != NULL) {
            $latLng = array();
            $latLng["lat"] = $country_lat;
            $latLng["lng"] = $country_long;
            return $latLng;
        } else {
            $google_map_key = get_option("gmap_api_key");

            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $country_name . '&key=' . $google_map_key . '&sensor=false');
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_handle, CURLOPT_USERAGENT, 'app');
            $geocode_stats = curl_exec($curl_handle);
            curl_close($curl_handle);

            $output_deals = json_decode($geocode_stats);

            if (isset($output_deals->results[0])) {
                $latLng = $output_deals->results[0]->geometry->location;
                $lat = $latLng->lat;
                $lng = $latLng->lng;

                $pdo = ORM::get_db();
                $query = "UPDATE " . $config['db']['pre'] . "countries SET latitude = '" . validate_input($lat) . "', longitude = '" . validate_input($lng) . "' WHERE code='" . $country_code . "' LIMIT 1";
                $pdo->query($query);

                return $array = (array)$latLng;
            } else {
                $latLng = array();
                $latLng["lat"] = get_option("home_map_latitude");
                $latLng["lng"] = get_option("home_map_longitude");
                return $latLng;
            }
        }

    } else {
        return false;
    }
}

/**
 * Get country list
 *
 * @param string $selected
 * @param string $selected_text
 * @param int $installed
 * @return array
 */
function get_country_list($selected = "", $selected_text = 'selected', $installed = 1)
{
    global $config;
    $countries = array();
    $count = 0;
    if ($installed) {
        $result = ORM::for_table($config['db']['pre'] . 'countries')
            ->select_many('id', 'code', 'name', 'asciiname', 'languages')
            ->where('active', 1)
            ->order_by_asc('asciiname')
            ->find_many();
    } else {
        $result = ORM::for_table($config['db']['pre'] . 'countries')
            ->select_many('id', 'code', 'name', 'asciiname', 'languages')
            ->order_by_asc('asciiname')
            ->find_many();
    }
    foreach ($result as $info) {
        $countries[$count]['id'] = $info['id'];
        $countries[$count]['code'] = $info['code'];
        $countries[$count]['lowercase_code'] = strtolower($info['code']);
        $countries[$count]['name'] = $info['name'];
        $countries[$count]['asciiname'] = $info['asciiname'];
        $countries[$count]['lang'] = getLangFromCountry($info['languages']);
        if ($selected != "") {
            if (is_array($selected)) {
                foreach ($selected as $select) {

                    $select = strtoupper(str_replace('"', '', $select));
                    if ($select == $info['id']) {
                        $countries[$count]['selected'] = $selected_text;
                    }
                }
            } else {
                if ($selected == $info['id'] or $selected == $info['code'] or $selected == $info['asciiname']) {
                    $countries[$count]['selected'] = $selected_text;
                } else {
                    $countries[$count]['selected'] = "";
                }
            }
        } else {
            $countries[$count]['selected'] = "";
        }
        $count++;
    }

    return $countries;
}

/**
 * Check if string start with $needles
 *
 * @param string $haystack
 * @param string $needles
 * @return bool
 */
function startsWith($haystack, $needles)
{
    foreach ((array)$needles as $needle) {
        if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string)$needle) {
            return true;
        }
    }

    return false;
}

/**
 * Get lang from country
 *
 * @param $languages
 * @return mixed|string
 */
function getLangFromCountry($languages)
{
    global $config;
    // Get language code
    $langCode = $hrefLang = '';
    if (trim($languages) != '') {
        // Get the country's languages codes
        $countryLanguageCodes = explode(',', $languages);

        // Get all languages
        $availableLanguages = get_language_list();

        /*$availableLanguages = Cache::remember('languages.all', self::$cacheExpiration, function () {
            $availableLanguages = LanguageModel::all();
            return $availableLanguages;
        });*/

        if (!empty($availableLanguages)) {
            $found = false;
            foreach ($countryLanguageCodes as $isoLang) {
                foreach ($availableLanguages as $language) {
                    if (startsWith(strtolower($isoLang), strtolower($language['code']))) {
                        $langCode = $language['code'];
                        $hrefLang = $isoLang;
                        $found = true;
                        break;
                    }
                }
                if ($found) {
                    break;
                }
            }
        }
    }

    // Get language info
    if ($langCode != '') {
        return $langCode;
    } else {
        $lang = get_lang_code_by_filename($config['default_lang']);
    }

    return $lang;
}

/**
 * create slug
 *
 * @param string $string
 * @return string
 */
function create_slug($string)
{
    return URLify::filter($string);
}

/**
 * Create blog cat slug
 *
 * @param string $title
 * @return string
 */
function create_blog_cat_slug($title)
{
    global $config;
    $slug = create_slug($title);
    $numHits = ORM::for_table($config['db']['pre'] . 'blog_categories')
        ->where_like('slug', '' . $slug . '%')
        ->count();

    return ($numHits > 0) ? ($slug . '-' . $numHits) : $slug;
}

/**
 * Create blog cat slug
 *
 * @param string $title
 * @return string
 */
function create_custom_template_slug($title)
{
    global $config;
    $slug = create_slug($title);
    $numHits = ORM::for_table($config['db']['pre'] . 'ai_custom_templates')
        ->where_like('slug', '' . $slug . '%')
        ->count();

    return ($numHits > 0) ? ($slug . '-' . $numHits) : $slug;
}

/**
 * Check required values for subscription
 */
function check_validation_for_subscribePlan()
{
    global $lang;

    $userdata = get_user_data($_SESSION['user']['username']);
    $email = $userdata['email'];
    $username = $userdata['username'];
    $name = $userdata['name'];
    $phone = $userdata['phone'];
    $address = $userdata['address'];

    if ($email == null or $name == null or $phone == null or $address == null) {
        message(__("Information"), __("All user data(Name, Email, Phone, Address) required to make any transaction."), '', false);
    }
}

/**
 * Check user upgrades
 *
 * @param int $user_id
 * @return float|int
 */
function check_user_upgrades($user_id)
{
    global $config;
    $check_upgrade = ORM::for_table($config['db']['pre'] . 'upgrades')
        ->where('user_id', $user_id)
        ->count();

    return $check_upgrade;
}

/**
 * Save details after payment success
 *
 * @param string $access_token
 */
function payment_success_save_detail($access_token)
{
    global $config, $lang, $link;
    $pdo = ORM::get_db();
    $title = $_SESSION['quickad'][$access_token]['name'];
    $amount = $_SESSION['quickad'][$access_token]['amount'];
    $folder = $_SESSION['quickad'][$access_token]['folder'];
    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];

    $ip = encode_ip($_SERVER, $_ENV);
    $user_id = $_SESSION['user']['id'];
    $now = time();

    $person = ORM::for_table($config['db']['pre'] . 'user')->find_one($user_id);

    $taxes_ids = isset($_SESSION['quickad'][$access_token]['taxes_ids']) ? $_SESSION['quickad'][$access_token]['taxes_ids'] : null;

    $billing = array(
        'type' => get_user_option($_SESSION['user']['id'], 'billing_details_type'),
        'tax_id' => get_user_option($_SESSION['user']['id'], 'billing_tax_id'),
        'name' => get_user_option($_SESSION['user']['id'], 'billing_name', $_SESSION['user']['username']),
        'address' => get_user_option($_SESSION['user']['id'], 'billing_address'),
        'city' => get_user_option($_SESSION['user']['id'], 'billing_city'),
        'state' => get_user_option($_SESSION['user']['id'], 'billing_state'),
        'zipcode' => get_user_option($_SESSION['user']['id'], 'billing_zipcode'),
        'country' => get_user_option($_SESSION['user']['id'], 'billing_country')
    );

    if ($payment_type == "subscr") {
        $trans_desc = $title;
        $base_amount = $_SESSION['quickad'][$access_token]['base_amount'];
        $plan_interval = $_SESSION['quickad'][$access_token]['plan_interval'];
        $subcription_id = $_SESSION['quickad'][$access_token]['sub_id'];

        // Check that the payment is valid
        $subsc_details = ORM::for_table($config['db']['pre'] . 'plans')
            ->where('id', $subcription_id)
            ->find_one();
        if (!empty($subsc_details)) {
            // output data of each row

            $term = 0;
            if ($plan_interval == 'MONTHLY') {
                $term = 2678400;
            } elseif ($plan_interval == 'YEARLY') {
                $term = 31536000;
            } elseif ($plan_interval == 'LIFETIME') {
                $term = 3153600000;
            }

            $sub_group_id = $subsc_details['id'];

            $subsc_check = ORM::for_table($config['db']['pre'] . 'upgrades')
                ->where('user_id', $user_id)
                ->count();
            if ($subsc_check == 1) {
                $txn_type = 'subscr_update';
            } else {
                $txn_type = 'subscr_signup';
            }

            // Add time to their subscription
            $expires = (time() + $term);

            if ($txn_type == 'subscr_update') {

                $query = "UPDATE `" . $config['db']['pre'] . "upgrades` SET `sub_id` = '" . validate_input($subcription_id) . "',`upgrade_expires` = '" . validate_input($expires) . "' WHERE `user_id` = '" . validate_input($user_id) . "' LIMIT 1 ";
                $pdo->query($query);


                $person->group_id = $sub_group_id;
                $person->save();

            } elseif ($txn_type == 'subscr_signup') {
                $unique_subscription_id = uniqid();
                $subscription_status = "Active";

                $subscription_stripe_customer_id = isset($_SESSION['quickad'][$access_token]['customer_id']) ? $_SESSION['quickad'][$access_token]['customer_id'] : null;
                $subscription_stripe_subscription_id = isset($_SESSION['quickad'][$access_token]['subscription_id']) ? $_SESSION['quickad'][$access_token]['subscription_id'] : null;
                $subscription_billing_day = isset($_SESSION['quickad'][$access_token]['billing_day']) ? $_SESSION['quickad'][$access_token]['billing_day'] : null;
                $subscription_length = 0;
                $subscription_interval = isset($_SESSION['quickad'][$access_token]['interval']) ? $_SESSION['quickad'][$access_token]['interval'] : null;
                $subscription_trial_days = isset($_SESSION['quickad'][$access_token]['trial_days']) ? $_SESSION['quickad'][$access_token]['trial_days'] : null;
                $subscription_date_trial_ends = isset($_SESSION['quickad'][$access_token]['date_trial_ends']) ? $_SESSION['quickad'][$access_token]['date_trial_ends'] : null;

                $upgrades_insert = ORM::for_table($config['db']['pre'] . 'upgrades')->create();
                $upgrades_insert->sub_id = $subcription_id;
                $upgrades_insert->user_id = $user_id;
                $upgrades_insert->upgrade_lasttime = $now;
                $upgrades_insert->upgrade_expires = $expires;
                $upgrades_insert->unique_id = $unique_subscription_id;
                $upgrades_insert->stripe_customer_id = $subscription_stripe_customer_id;
                $upgrades_insert->stripe_subscription_id = $subscription_stripe_subscription_id;
                $upgrades_insert->billing_day = $subscription_billing_day;
                $upgrades_insert->length = $subscription_length;
                $upgrades_insert->interval = $subscription_interval;
                $upgrades_insert->trial_days = $subscription_trial_days;
                $upgrades_insert->status = $subscription_status;
                $upgrades_insert->date_trial_ends = $subscription_date_trial_ends;
                $upgrades_insert->save();

                $person->group_id = $sub_group_id;
                $person->save();
            }

            //Update Amount in balance table
            $balance = ORM::for_table($config['db']['pre'] . 'balance')->find_one(1);
            $current_amount = $balance['current_balance'];
            $total_earning = $balance['total_earning'];

            $updated_amount = ($amount + $current_amount);
            $total_earning = ($amount + $total_earning);

            $balance->current_balance = $updated_amount;
            $balance->total_earning = $total_earning;
            $balance->save();

            $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
            $trans_insert->product_name = $title;
            $trans_insert->product_id = $subcription_id;
            $trans_insert->seller_id = $user_id;
            $trans_insert->status = 'success';
            $trans_insert->base_amount = $base_amount;
            $trans_insert->amount = $amount;
            //$trans_insert->currency_code = $config['currency_code'];
            $trans_insert->transaction_gatway = $folder;
            $trans_insert->transaction_ip = $ip;
            $trans_insert->transaction_time = $now;
            $trans_insert->transaction_description = $trans_desc;
            $trans_insert->transaction_method = 'Subscription';
            $trans_insert->frequency = $plan_interval;
            $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
            $trans_insert->taxes_ids = $taxes_ids;
            $trans_insert->save();

            // check for affiliate payment
            $person = ORM::for_table($config['db']['pre'] . 'user')->find_one($user_id);
            check_affiliate_payment($person, $txn_type, $amount, $folder, $trans_insert->id);

            // reset user's data
            update_user_option($user_id, 'total_words_used', 0);
            update_user_option($user_id, 'total_images_used', 0);
            update_user_option($user_id, 'total_speech_used', 0);
            update_user_option($user_id, 'total_text_to_speech_used', 0);

            update_user_option($user_id, 'last_reset_time', time());

            unset($_SESSION['quickad'][$access_token]);
            message(__("Success"), __("Payment Successful"), $link['TRANSACTION']);
            exit();
        } else {
            unset($_SESSION['quickad'][$access_token]);
            error(__("Invalid Transaction"), __LINE__, __FILE__, 1);
            exit();
        }
    } elseif ($payment_type == "prepaid_plan") {
        $trans_desc = $title;
        $base_amount = $_SESSION['quickad'][$access_token]['base_amount'];
        $subcription_id = $_SESSION['quickad'][$access_token]['sub_id'];

        // Check that the payment is valid
        $subsc_details = ORM::for_table($config['db']['pre'] . 'prepaid_plans')
            ->where('id', $subcription_id)
            ->find_one();
        if (!empty($subsc_details)) {
//Update Amount in balance table
            $balance = ORM::for_table($config['db']['pre'] . 'balance')->find_one(1);
            $current_amount = $balance['current_balance'];
            $total_earning = $balance['total_earning'];

            $updated_amount = ($amount + $current_amount);
            $total_earning = ($amount + $total_earning);

            $balance->current_balance = $updated_amount;
            $balance->total_earning = $total_earning;
            $balance->save();

            $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
            $trans_insert->product_name = $title;
            $trans_insert->product_id = $subcription_id;
            $trans_insert->seller_id = $user_id;
            $trans_insert->status = 'success';
            $trans_insert->base_amount = $base_amount;
            $trans_insert->amount = $amount;
            //$trans_insert->currency_code = $config['currency_code'];
            $trans_insert->transaction_gatway = $folder;
            $trans_insert->transaction_ip = $ip;
            $trans_insert->transaction_time = $now;
            $trans_insert->transaction_description = $trans_desc;
            $trans_insert->transaction_method = 'prepaid_plan';
            $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
            $trans_insert->taxes_ids = $taxes_ids;
            $trans_insert->save();

            // update user's data
            $settings = json_decode($subsc_details['settings'], true);

            $total_words_available = get_user_option($user_id, 'total_words_available', 0);
            update_user_option($user_id, 'total_words_available', $total_words_available + $settings['ai_words_limit']);

            $total_images_available = get_user_option($user_id, 'total_images_available', 0);
            update_user_option($user_id, 'total_images_available', $total_images_available + $settings['ai_images_limit']);

            $total_speech_available = get_user_option($user_id, 'total_speech_available', 0);
            update_user_option($user_id, 'total_speech_available', $total_speech_available + $settings['ai_speech_to_text_limit']);

            $total_text_to_speech_available = get_user_option($user_id, 'total_text_to_speech_available', 0);
            update_user_option($user_id, 'total_text_to_speech_available', $total_text_to_speech_available + $settings['ai_text_to_speech_limit']);

            unset($_SESSION['quickad'][$access_token]);
            message(__("Success"), __("Payment Successful"), $link['TRANSACTION']);
            exit();
        }else {
            unset($_SESSION['quickad'][$access_token]);
            error(__("Invalid Transaction"), __LINE__, __FILE__, 1);
            exit();
        }
    } elseif ($payment_type == "banner_advertise") {
        $pdo = ORM::get_db();
        $txn_id = isset($_SESSION['quickad'][$access_token]['txn_id']) ? $_SESSION['quickad'][$access_token]['product_id'] : '';
        $payment_status = "Completed";
        $item_number = $_SESSION['quickad'][$access_token]['product_id'];
        $userdata = get_user_data($_SESSION['user']['username']);
        $payer_id = $userdata['id'];
        $payer_email = $userdata['email'];
        $payer_name = $userdata['name'];
        $transaction_type = $folder;
        $gross_total = $amount;
        $mc_currency = $config['currency_code'];

        $query = "SELECT t1.*, t2.title AS type_title FROM `" . $config['db']['pre'] . "qbm_banners` t1 LEFT JOIN `" . $config['db']['pre'] . "qbm_types` t2 ON t1.type_id = t2.id WHERE t1.id = '" . $item_number . "'";
        $banner_details = ORM::for_table($config['db']['pre'] . 'qbm_banners')->raw_query($query)->find_one();

        if (!empty($banner_details)) {
            $type_title = $banner_details["type_title"];
            $banner_title = $banner_details["title"];
        } else {
            $payment_status = "Unrecognized";
        }

        $sql = "INSERT INTO `" . $config['db']['pre'] . "qbm_transactions` (
			banner_id, payer_name, payer_email, gross, currency, payment_status, transaction_type, txn_id, created) VALUES (
			'" . $item_number . "',
			'" . $payer_name . "',
			'" . $payer_email . "',
			'" . floatval($gross_total) . "',
			'" . $mc_currency . "',
			'" . $payment_status . "',
			'" . $transaction_type . "',
			'" . $txn_id . "',
			'" . time() . "'
		)";

        $pdo->query($sql);
        $status_active = '1';
        $status_pending = '7';
        if ($payment_status == "Completed") {
            $registered = time();
            $banner_approval = get_option('qbm_enable_approval');

            if ($banner_approval) {
                $pdo->query("UPDATE `" . $config['db']['pre'] . "qbm_banners` SET status = '" . $status_pending . "', registered = '" . $registered . "', blocked = '" . $registered . "' WHERE id = '" . $item_number . "'");
            } else {
                $pdo->query("UPDATE `" . $config['db']['pre'] . "qbm_banners` SET status = '" . $status_active . "', registered = '" . $registered . "', blocked = '0' WHERE id = '" . $item_number . "'");
            }

            $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
            $trans_insert->product_name = $title;
            $trans_insert->product_id = $_SESSION['quickad'][$access_token]['product_id'];
            $trans_insert->user_id = $user_id;
            $trans_insert->status = 'success';
            $trans_insert->base_amount = $amount;
            $trans_insert->amount = $amount;
            $trans_insert->currency_code = $config['currency_code'];
            $trans_insert->transaction_gatway = $folder;
            $trans_insert->transaction_ip = $ip;
            $trans_insert->transaction_time = $now;
            $trans_insert->transaction_description = $_SESSION['quickad'][$access_token]['trans_desc'];
            $trans_insert->transaction_method = 'banner_advertise';
            $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
            $trans_insert->taxes_ids = $taxes_ids;
            $trans_insert->save();

            //Update Amount in balance table
            $balance = ORM::for_table($config['db']['pre'] . 'balance')->find_one(1);
            $current_amount = $balance['current_balance'];
            $total_earning = $balance['total_earning'];

            $updated_amount = ($amount + $current_amount);
            $total_earning = ($amount + $total_earning);
            $balance->current_balance = $updated_amount;
            $balance->total_earning = $total_earning;
            $balance->save();

            $tags = array("{payer_name}", "{payer_email}", "{amount}", "{currency}", "{type_title}", "{banner_title}", "{transaction_date}", $folder);
            $vals = array($payer_name, $payer_id, $gross_total, $mc_currency, $type_title, $banner_title, date("Y-m-d H:i:s") . " (server time)", $folder);


            unset($_SESSION['quickad'][$access_token]);

            $redirect_url = $config['site_url'] . 'plugins/banner-admanager/ajax.php?action=payment_success&tags=' . $tags . '&vals=' . $vals . '&payer_email=' . $payer_email;
            headerRedirect($redirect_url);
            message(__("Success"), __("Payment Successful"), $link['TRANSACTION']);
            exit();

        } else {
            $pdo->query("UPDATE `" . $config['db']['pre'] . "qbm_banners` SET status = '" . $status_pending . "', registered = '" . time() . "', blocked = '" . time() . "' WHERE id = '" . $item_number . "'");
            $tags = array("{payer_name}", "{payer_email}", "{amount}", "{currency}", "{type_title}", "{banner_title}", "{payment_status}", "{transaction_date}", $folder);
            $vals = array($payer_name, $payer_id, $gross_total, $mc_currency, $type_title, $banner_title, $payment_status, date("Y-m-d H:i:s") . " (server time)", $folder);
            $redirect_url = $config['site_url'] . 'plugins/banner-admanager/ajax.php?action=payment_failed&tags=' . $tags . '&vals=' . $vals . '&payer_email=' . $payer_email;

            unset($_SESSION['quickad'][$access_token]);
            headerRedirect($redirect_url);
            exit;
        }
        exit;
    } elseif ($payment_type == "order_service") {
        $item_pro_id = $_SESSION['quickad'][$access_token]['product_id'];
        $trans_desc = $_SESSION['quickad'][$access_token]['trans_desc'];
        $plan_id = $_SESSION['quickad'][$access_token]['plan_id'];

        $code = generate_purchase_code();
        $order_insert = ORM::for_table($config['db']['pre'] . 'orders')->create();
        $order_insert->user_id = $user_id;
        $order_insert->post_id = $item_pro_id;
        $order_insert->plan_id = $plan_id;
        $order_insert->amount = $amount;
        $order_insert->purchase_code = $code;
        $order_insert->save();
        $order_id = $order_insert->id();

        $post = ORM::for_table($config['db']['pre'] . 'post')
            ->select('user_id')
            ->find_one($item_pro_id);

        $SenderName = '';
        $SenderId = $user_id;
        $OwnerName = '';
        $OwnerId = $post['user_id'];
        $productId = $order_id;
        $productTitle = $title;
        $type = 'new_order';
        $message = $trans_desc;
        add_firebase_notification($SenderName, $SenderId, $OwnerName, $OwnerId, $productId, $productTitle, $type, $message);

        $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
        $trans_insert->product_name = validate_input($title);
        $trans_insert->product_id = $order_id;
        $trans_insert->user_id = $user_id;
        $trans_insert->status = 'success';
        $trans_insert->base_amount = $amount;
        $trans_insert->amount = $amount;
        $trans_insert->currency_code = $config['currency_code'];
        $trans_insert->transaction_gatway = $folder;
        $trans_insert->transaction_ip = encode_ip($_SERVER, $_ENV);
        $trans_insert->transaction_time = time();
        $trans_insert->transaction_description = validate_input($trans_desc);
        $trans_insert->transaction_method = 'order_service';
        $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
        $trans_insert->taxes_ids = $taxes_ids;
        $trans_insert->save();

        unset($_SESSION['quickad'][$access_token]);
        message(__("Success"), __("Payment Successful"), $link['TRANSACTION']);
        exit();
    } elseif ($payment_type == "deposit") {
        $item_pro_id = $_SESSION['quickad'][$access_token]['product_id'];
        $trans_desc = $_SESSION['quickad'][$access_token]['trans_desc'];

        $user = ORM::for_table($config['db']['pre'] . 'user')
            ->select('balance')
            ->find_one($user_id);
        $balance = $user['balance'];
        $add_balance = $balance + $amount;

        $user = ORM::for_table($config['db']['pre'] . 'user')->find_one($user_id);
        $user->set('balance', $add_balance);
        $user->save();

        $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
        $trans_insert->product_name = validate_input($title);
        $trans_insert->product_id = $item_pro_id;
        $trans_insert->user_id = $user_id;
        $trans_insert->status = 'success';
        $trans_insert->base_amount = $amount;
        $trans_insert->amount = $amount;
        $trans_insert->currency_code = $config['currency_code'];
        $trans_insert->transaction_gatway = $folder;
        $trans_insert->transaction_ip = encode_ip($_SERVER, $_ENV);
        $trans_insert->transaction_time = time();
        $trans_insert->transaction_description = validate_input($trans_desc);
        $trans_insert->transaction_method = 'deposit';
        $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
        $trans_insert->taxes_ids = $taxes_ids;
        $trans_insert->save();

        unset($_SESSION['quickad'][$access_token]);
        message(__("Success"), __("Payment Successful"), $link['TRANSACTION']);
        exit();
    } else {
        $item_pro_id = $_SESSION['quickad'][$access_token]['product_id'];
        $item_featured = ($_SESSION['quickad'][$access_token]['featured'] == "1") ? "1" : "0";
        $item_urgent = ($_SESSION['quickad'][$access_token]['urgent'] == "1") ? "1" : "0";
        $item_highlight = ($_SESSION['quickad'][$access_token]['highlight'] == "1") ? "1" : "0";
        $trans_desc = $_SESSION['quickad'][$access_token]['trans_desc'];

        if (check_valid_author($item_pro_id)) {

            $group_info = get_user_membership_detail($user_id);
            $featured_duration = $group_info['settings']['featured_duration'];
            $urgent_duration = $group_info['settings']['urgent_duration'];
            $highlight_duration = $group_info['settings']['highlight_duration'];

            $f_duration_timestamp = $featured_duration * 86400;
            $featured_exp_date = (time() + $f_duration_timestamp);
            $featured_exp_date = date('Y-m-d H:i:s', $featured_exp_date);

            $u_duration_timestamp = $urgent_duration * 86400;
            $urgent_exp_date = (time() + $u_duration_timestamp);
            $urgent_exp_date = date('Y-m-d H:i:s', $urgent_exp_date);

            $h_duration_timestamp = $highlight_duration * 86400;
            $highlight_exp_date = (time() + $h_duration_timestamp);
            $highlight_exp_date = date('Y-m-d H:i:s', $highlight_exp_date);
            if ($item_featured == '1') {
                $featured_insert = ORM::for_table($config['db']['pre'] . 'product')->find_one($item_pro_id);
                $featured_insert->featured = '1';
                $featured_insert->featured_exp_date = $featured_exp_date;
                $featured_insert->save();
            }
            if ($item_urgent == '1') {

                $urgent_insert = ORM::for_table($config['db']['pre'] . 'product')->find_one($item_pro_id);
                $urgent_insert->urgent = '1';
                $urgent_insert->urgent_exp_date = $urgent_exp_date;
                $urgent_insert->save();
            }
            if ($item_highlight == '1') {

                $highlight_insert = ORM::for_table($config['db']['pre'] . 'product')->find_one($item_pro_id);
                $highlight_insert->highlight = '1';
                $highlight_insert->highlight_exp_date = $highlight_exp_date;
                $highlight_insert->save();
            }

            if (check_valid_resubmission($item_pro_id)) {
                if ($item_featured == '1') {
                    $query = "UPDATE " . $config['db']['pre'] . "product_resubmit set featured = '1',featured_exp_date='$featured_exp_date' where product_id='" . $item_pro_id . "' LIMIT 1";
                    $pdo->query($query);
                }
                if ($item_urgent == '1') {
                    $query = "UPDATE " . $config['db']['pre'] . "product_resubmit set urgent = '1',urgent_exp_date='$urgent_exp_date' where product_id='" . $item_pro_id . "' LIMIT 1";
                    $pdo->query($query);
                }
                if ($item_highlight == '1') {
                    $query = "UPDATE " . $config['db']['pre'] . "product_resubmit set highlight = '1',highlight_exp_date='$highlight_exp_date' where product_id='" . $item_pro_id . "' LIMIT 1";
                    $pdo->query($query);
                }
            }
            //Update Amount in balance table
            $balance = ORM::for_table($config['db']['pre'] . 'balance')->find_one(1);
            $current_amount = $balance['current_balance'];
            $total_earning = $balance['total_earning'];

            $updated_amount = ($amount + $current_amount);
            $total_earning = ($amount + $total_earning);
            $balance->current_balance = $updated_amount;
            $balance->total_earning = $total_earning;
            $balance->save();

            $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
            $trans_insert->product_name = $title;
            $trans_insert->product_id = $item_pro_id;
            $trans_insert->user_id = $user_id;
            $trans_insert->status = 'success';
            $trans_insert->base_amount = $amount;
            $trans_insert->amount = $amount;
            $trans_insert->currency_code = $config['currency_code'];
            if ($item_featured)
                $trans_insert->featured = (string)$item_featured;
            if ($item_urgent)
                $trans_insert->urgent = (string)$item_urgent;
            if ($item_highlight)
                $trans_insert->highlight = (string)$item_highlight;
            $trans_insert->transaction_gatway = $folder;
            $trans_insert->transaction_ip = $ip;
            $trans_insert->transaction_time = $now;
            $trans_insert->transaction_description = $trans_desc;
            $trans_insert->transaction_method = 'premium_badge';
            $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
            $trans_insert->taxes_ids = $taxes_ids;
            $trans_insert->save();

            unset($_SESSION['quickad'][$access_token]);
            message(__("Success"), __("Payment Successful"), $link['TRANSACTION']);
            exit();
        } else {
            unset($_SESSION['quickad'][$access_token]);
            error(__("Invalid Transaction"), __LINE__, __FILE__, 1);
            exit();
        }
    }
}

/**
 * Save details after payment fail
 *
 * @param string $access_token
 */
function payment_fail_save_detail($access_token)
{

    global $config, $link;
    $title = $_SESSION['quickad'][$access_token]['name'];
    $amount = $_SESSION['quickad'][$access_token]['amount'];
    $folder = $_SESSION['quickad'][$access_token]['folder'];
    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
    $user_id = $_SESSION['user']['id'];
    $now = time();
    $ip = encode_ip($_SERVER, $_ENV);

    if ($payment_type == "subscr") {
        $trans_desc = $title;
        $subcription_id = $_SESSION['quickad'][$access_token]['sub_id'];

        $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
        $trans_insert->product_name = $title;
        $trans_insert->product_id = $subcription_id;
        $trans_insert->seller_id = $user_id;
        $trans_insert->status = 'failed';
        $trans_insert->amount = $amount;
        //$trans_insert->currency_code = $config['currency_code'];
        $trans_insert->transaction_gatway = $folder;
        $trans_insert->transaction_ip = $ip;
        $trans_insert->transaction_time = $now;
        $trans_insert->transaction_description = $trans_desc;
        $trans_insert->transaction_method = 'Subscription';
        $trans_insert->save();
    } else if ($payment_type == "prepaid_plan") {
        $trans_desc = $title;
        $subcription_id = $_SESSION['quickad'][$access_token]['sub_id'];

        $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
        $trans_insert->product_name = $title;
        $trans_insert->product_id = $subcription_id;
        $trans_insert->seller_id = $user_id;
        $trans_insert->status = 'failed';
        $trans_insert->amount = $amount;
        //$trans_insert->currency_code = $config['currency_code'];
        $trans_insert->transaction_gatway = $folder;
        $trans_insert->transaction_ip = $ip;
        $trans_insert->transaction_time = $now;
        $trans_insert->transaction_description = $trans_desc;
        $trans_insert->transaction_method = 'prepaid_plan';
        $trans_insert->save();
    }else {
        $item_pro_id = $_SESSION['quickad'][$access_token]['product_id'];
        $item_featured = isset($_SESSION['quickad'][$access_token]['featured']) ? $_SESSION['quickad'][$access_token]['featured'] : '0';
        $item_urgent = isset($_SESSION['quickad'][$access_token]['urgent']) ? $_SESSION['quickad'][$access_token]['urgent'] : '0';
        $item_highlight = isset($_SESSION['quickad'][$access_token]['highlight']) ? $_SESSION['quickad'][$access_token]['highlight'] : '0';
        $trans_desc = $_SESSION['quickad'][$access_token]['trans_desc'];

        if ($payment_type == "order_service") {
            $transaction_method = "order_service";
            $plan_id = $_SESSION['quickad'][$access_token]['plan_id'];
        } else {
            $transaction_method = "premium_badge";
        }

        $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
        $trans_insert->product_name = $title;
        $trans_insert->product_id = $item_pro_id;
        $trans_insert->seller_id = $user_id;
        $trans_insert->status = 'failed';
        $trans_insert->amount = $amount;
        $trans_insert->currency_code = $config['currency_code'];
        if ($item_featured)
            $trans_insert->featured = (string)$item_featured;
        if ($item_urgent)
            $trans_insert->urgent = (string)$item_urgent;
        if ($item_highlight)
            $trans_insert->highlight = (string)$item_highlight;
        $trans_insert->transaction_gatway = $folder;
        $trans_insert->transaction_ip = $ip;
        $trans_insert->transaction_time = $now;
        $trans_insert->transaction_description = $trans_desc;
        $trans_insert->transaction_method = $transaction_method;
        $trans_insert->save();
    }

    unset($_SESSION['quickad'][$access_token]);
}

/**
 * Display payment error
 *
 * @param string $status
 * @param string $error_message
 * @param string $access_token
 */
function payment_error($status, $error_message, $access_token)
{

    global $config, $lang;
    $error_message = !isset($error_message) ? "" : $error_message;
    if (isset($_SESSION['quickad'][$access_token]['payment_type'])) {
        if (isset($_SESSION['quickad'][$access_token]['transaction_id'])) {
            $transaction_id = $_SESSION['quickad'][$access_token]['transaction_id'];
            unset($_SESSION['quickad'][$access_token]);

            if ($status == "cancel") {
                $trans_update = ORM::for_table($config['db']['pre'] . 'transaction')->find_one($transaction_id);
                $trans_update->status = 'cancel';
                $trans_update->save();

                error_content(__("Transaction Declined"), $error_message);
                exit();
            } elseif ($status == "error") {
                $trans_update = ORM::for_table($config['db']['pre'] . 'transaction')->find_one($transaction_id);
                $trans_update->status = 'failed';
                $trans_update->save();

                error_content(__("Transaction Failed"), $error_message);
                exit();
            } else {
                error_content(__("Transaction Failed"), $error_message);
                exit();
            }
        } else {
            unset($_SESSION['quickad'][$access_token]);
            error_content(__("Transaction Failed"), $error_message);
            exit();
        }

    } else {
        error_content(__("Invalid Payment Processor"), $error_message);
        exit();
    }
}

/**
 * After transaction status changed to success
 *
 * @param int $transaction_id
 * @return bool|void
 */
function transaction_success($transaction_id)
{

    global $config;
    $mysqli = db_connect();

    $result = $mysqli->query("SELECT * FROM `" . $config['db']['pre'] . "transaction` WHERE `id` = $transaction_id LIMIT 1");
    if (mysqli_num_rows($result) > 0) {
        // output data of each row
        $info = mysqli_fetch_assoc($result);

        $item_pro_id = $info['product_id'];
        $user_id = $info['seller_id'];
        $item_amount = $info['amount'];
        $trans_title = $info['product_name'];
        $trans_desc = $info['transaction_description'];

        if ($info['transaction_method'] == 'Subscription') {
            $subcription_id = $item_pro_id;
            $plan_interval = $info['frequency'];

            // Check that the payment is valid
            $subsc_details = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM " . $config['db']['pre'] . "plans WHERE id='" . validate_input($subcription_id) . "' LIMIT 1"));

            $term = 0;
            if ($plan_interval == 'MONTHLY') {
                $term = 2678400;
            } elseif ($plan_interval == 'YEARLY') {
                $term = 31536000;
            } elseif ($plan_interval == 'LIFETIME') {
                $term = 3153600000;
            }

            $sub_group_id = $subsc_details['id'];

            // Check valid user
            $user_check = mysqli_num_rows(mysqli_query($mysqli, "SELECT 1 FROM " . $config['db']['pre'] . "user WHERE id='" . validate_input($user_id) . "' LIMIT 1"));

            if (!$user_check) {
                exit('error, user does not exist');
            }

            $subsc_check = mysqli_num_rows(mysqli_query($mysqli, "select * from `" . $config['db']['pre'] . "upgrades` WHERE `user_id` = '" . validate_input($user_id) . "' LIMIT 1 ;"));

            if ($subsc_check == 1) {
                $txn_type = 'subscr_update';
            } else {
                $txn_type = 'subscr_signup';
            }

            // Add time to their subscription
            $expires = (time() + $term);

            if ($txn_type == 'subscr_update') {
                mysqli_query($mysqli, "UPDATE `" . $config['db']['pre'] . "upgrades` SET `sub_id` = '" . validate_input($subcription_id) . "',`upgrade_expires` = '" . validate_input($expires) . "' WHERE `user_id` = '" . validate_input($user_id) . "' LIMIT 1 ");

                mysqli_query($mysqli, "UPDATE `" . $config['db']['pre'] . "user` SET `group_id` = '" . validate_input($sub_group_id) . "' WHERE `id` = '" . validate_input($user_id) . "' LIMIT 1 ;");

            } elseif ($txn_type == 'subscr_signup') {
                mysqli_query($mysqli, "INSERT INTO `" . $config['db']['pre'] . "upgrades` (`sub_id` ,`user_id` ,`upgrade_lasttime` ,`upgrade_expires`) VALUES ('" . validate_input($subcription_id) . "', '" . validate_input($user_id) . "', '" . time() . "','" . validate_input($expires) . "')") or error(mysqli_error($mysqli));

                mysqli_query($mysqli, "UPDATE `" . $config['db']['pre'] . "user` SET `group_id` = '" . validate_input($sub_group_id) . "' WHERE `id` = '" . validate_input($user_id) . "' LIMIT 1 ;");
            }

            $person = ORM::for_table($config['db']['pre'] . 'user')
                ->where('id', $user_id)
                ->find_one();
            // check for affiliate payment
            check_affiliate_payment($person, $txn_type, $item_amount, 'wire_transfer', $transaction_id);

            // reset user's data
            update_user_option($user_id, 'total_words_used', 0);
            update_user_option($user_id, 'total_images_used', 0);
            update_user_option($user_id, 'total_speech_used', 0);
            update_user_option($user_id, 'total_text_to_speech_used', 0);

            update_user_option($user_id, 'last_reset_time', time());

        } elseif ($info['transaction_method'] == 'prepaid_plan') {

            $package = ORM::for_table($config['db']['pre'] . 'prepaid_plans')
                ->where('id', $item_pro_id)
                ->find_one();

            // check plan exists
            if (!isset($package['id'])) {
                exit('error, user does not exist');
            }

            // update user's data
            $settings = json_decode($package['settings'], true);

            $total_words_available = get_user_option($user_id, 'total_words_available', 0);
            update_user_option($user_id, 'total_words_available', $total_words_available + $settings['ai_words_limit']);

            $total_images_available = get_user_option($user_id, 'total_images_available', 0);
            update_user_option($user_id, 'total_images_available', $total_images_available + $settings['ai_images_limit']);

            $total_speech_available = get_user_option($user_id, 'total_speech_available', 0);
            update_user_option($user_id, 'total_speech_available', $total_speech_available + $settings['ai_speech_to_text_limit']);

            $total_text_to_speech_available = get_user_option($user_id, 'total_text_to_speech_available', 0);
            update_user_option($user_id, 'total_text_to_speech_available', $total_text_to_speech_available + $settings['ai_text_to_speech_limit']);

        } elseif ($info['transaction_method'] == 'order_service') {
            $trans_details = json_decode((string)$info['details'], true);

            $code = generate_purchase_code();
            $order_insert = ORM::for_table($config['db']['pre'] . 'orders')->create();
            $order_insert->user_id = $trans_details['user_id'];
            $order_insert->post_id = $trans_details['post_id'];
            $order_insert->plan_id = $trans_details['plan_id'];
            $order_insert->amount = $trans_details['amount'];
            $order_insert->purchase_code = $code;
            $order_insert->save();
            $order_id = $order_insert->id();

            $post = ORM::for_table($config['db']['pre'] . 'post')
                ->select('user_id')
                ->find_one($trans_details['post_id']);

            $SenderName = '';
            $SenderId = $user_id;
            $OwnerName = '';
            $OwnerId = $post['user_id'];
            $productId = $order_id;
            $productTitle = $trans_title;
            $type = 'new_order';
            $message = $trans_desc;
            add_firebase_notification($SenderName, $SenderId, $OwnerName, $OwnerId, $productId, $productTitle, $type, $message);
        } elseif ($info['transaction_method'] == 'deposit') {
            $user = ORM::for_table($config['db']['pre'] . 'user')
                ->select('balance')
                ->find_one($user_id);
            $balance = $user['balance'];
            $add_balance = $balance + $item_amount;

            $user = ORM::for_table($config['db']['pre'] . 'user')->find_one($user_id);
            $user->set('balance', $add_balance);
            $user->save();
        } else {
            $item_featured = $info['featured'];
            $item_urgent = $info['urgent'];
            $item_highlight = $info['highlight'];

            if ($item_featured == 1) {
                $mysqli->query("UPDATE " . $config['db']['pre'] . "product set featured = '$item_featured' where id='" . $item_pro_id . "' LIMIT 1");
            }
            if ($item_urgent == 1) {
                $mysqli->query("UPDATE " . $config['db']['pre'] . "product set urgent = '$item_urgent' where id='" . $item_pro_id . "' LIMIT 1");
            }
            if ($item_highlight == 1) {
                $mysqli->query("UPDATE " . $config['db']['pre'] . "product set highlight = '$item_highlight' where id='" . $item_pro_id . "' LIMIT 1");
            }

            $query = "SELECT 1 FROM " . $config['db']['pre'] . "product_resubmit WHERE product_id='" . $item_pro_id . "' and user_id='" . $user_id . "' LIMIT 1";
            $query_result = mysqli_query(db_connect(), $query);
            $num_rows = mysqli_num_rows($query_result);
            if ($num_rows == 1) {
                if ($item_featured == 1) {
                    $mysqli->query("UPDATE " . $config['db']['pre'] . "product_resubmit set featured = '$item_featured' where product_id='" . $item_pro_id . "' LIMIT 1");
                }
                if ($item_urgent == 1) {
                    $mysqli->query("UPDATE " . $config['db']['pre'] . "product_resubmit set urgent = '$item_urgent' where product_id='" . $item_pro_id . "' LIMIT 1");
                }
                if ($item_highlight == 1) {
                    $mysqli->query("UPDATE " . $config['db']['pre'] . "product_resubmit set highlight = '$item_highlight' where product_id='" . $item_pro_id . "' LIMIT 1");
                }
            }
        }

        //Transaction status Updating "Success"
        $mysqli->query("UPDATE " . $config['db']['pre'] . "transaction set status = 'success' where id='" . $transaction_id . "' LIMIT 1");

        //Add Amount in balance table
        $result2 = $mysqli->query("SELECT * FROM `" . $config['db']['pre'] . "balance` WHERE id = '1' LIMIT 1");
        if (mysqli_num_rows($result2) > 0) {
            $info2 = mysqli_fetch_assoc($result2);
            $current_amount = $info2['current_balance'];
            $total_earning = $info2['total_earning'];

            $updated_amount = ($item_amount + $current_amount);
            $total_earning = ($item_amount + $total_earning);

            $mysqli->query("UPDATE " . $config['db']['pre'] . "balance set current_balance = '" . $updated_amount . "', total_earning = '" . $total_earning . "' where id='1' LIMIT 1");
        }
        return true;
    } else {
        return false;
    }
}

/**
 * Save details after webhook payment success
 *
 * @param $payment_gateway
 * @param $metadata
 * @param $payment_id
 * @param $payment_subscription_id
 * @param $pay_mode
 * @param $payment_total
 * @throws Exception
 */
function payment_webhook_success($payment_gateway, $metadata, $payment_id, $payment_subscription_id, $pay_mode, $payment_total){
    global $config;

    $payment_type = $metadata->payment_type;

    if ($payment_type == "subscr") {
        $user_id = (int)$metadata->user_id;
        $package_id = (int)$metadata->package_id;
        $payment_frequency = $metadata->payment_frequency;
        $base_amount = $metadata->base_amount;
        $taxes_ids = $metadata->taxes_ids;

    }elseif ($payment_type == "prepaid_plan") {
        $user_id = (int)$metadata->user_id;
        $package_id = (int)$metadata->package_id;
        $base_amount = $metadata->base_amount;
        $taxes_ids = $metadata->taxes_ids;
    }

    $user = ORM::for_table($config['db']['pre'] . 'user')
        ->where('id', $user_id)
        ->find_one();

    // check user exists
    if (!isset($user['id'])) {
        http_response_code(400);
        die();
    }

    $ip = encode_ip($_SERVER, $_ENV);
    $billing = array(
        'type' => escape(get_user_option($user_id, 'billing_details_type')),
        'tax_id' => escape(get_user_option($user_id, 'billing_tax_id')),
        'name' => escape(get_user_option($user_id, 'billing_name', $user['username'])),
        'address' => escape(get_user_option($user_id, 'billing_address')),
        'city' => escape(get_user_option($user_id, 'billing_city')),
        'state' => escape(get_user_option($user_id, 'billing_state')),
        'zipcode' => escape(get_user_option($user_id, 'billing_zipcode')),
        'country' => escape(get_user_option($user_id, 'billing_country'))
    );

    if ($payment_type == "subscr") {
        $package = ORM::for_table($config['db']['pre'] . 'plans')
            ->where('id', $package_id)
            ->find_one();

        // check plan exists
        if (!isset($package['id'])) {
            http_response_code(400);
            die();
        }

        /* Make sure transaction is not already exist */
        if (ORM::for_table($config['db']['pre'] . 'transaction')
            ->where('payment_id', $payment_id)
            ->where('transaction_gatway', $payment_gateway)
            ->count()) {
            http_response_code(400);
            die();
        }

        $subsc_check = ORM::for_table($config['db']['pre'] . 'upgrades')
            ->where('user_id', $user_id)
            ->find_one();
        if (isset($subsc_check['user_id'])) {
            $txn_type = 'subscr_update';

            if (!empty($subsc_check['unique_id']) && ($subsc_check['unique_id'] != $payment_subscription_id)) {
                try {
                    cancel_recurring_payment($user_id);
                } catch (\Exception $e) {
                    error_log($e->getCode());
                    error_log($e->getMessage());
                }
            }
        } else {
            $txn_type = 'subscr_signup';
        }

        $term = 0;
        switch ($payment_frequency) {
            case 'MONTHLY':
                $term = 2678400;
                break;

            case 'YEARLY':
                $term = 31536000;
                break;

            case 'LIFETIME':
                $term = 3153600000;
                break;
        }

        // Add time to their subscription
        $expires = (time() + $term);
        $pdo = ORM::get_db();

        if ($txn_type == 'subscr_update') {
            $query = "UPDATE `" . $config['db']['pre'] . "upgrades` SET 
            `sub_id` = '" . validate_input($package_id) . "',
            `upgrade_expires` = '" . validate_input($expires) . "', 
            `pay_mode` = '$pay_mode', 
            `unique_id` = '" . ($payment_subscription_id) . "', 
            `upgrade_lasttime` = '" . time() . "' 
        WHERE `user_id` = '" . validate_input($user_id) . "' LIMIT 1";
            $pdo->query($query);

            // update user data
            $user->group_id = $package_id;
            $user->save();
        } elseif ($txn_type == 'subscr_signup') {
            $subscription_status = "Active";

            $upgrades_insert = ORM::for_table($config['db']['pre'] . 'upgrades')->create();
            $upgrades_insert->sub_id = $package_id;
            $upgrades_insert->user_id = $user_id;
            $upgrades_insert->upgrade_lasttime = time();
            $upgrades_insert->upgrade_expires = $expires;
            $upgrades_insert->pay_mode = $pay_mode;
            $upgrades_insert->unique_id = $payment_subscription_id;
            $upgrades_insert->status = $subscription_status;
            $upgrades_insert->save();

            $user->group_id = $package_id;
            $user->save();
        }

        $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
        $trans_insert->product_name = $package['name'];
        $trans_insert->product_id = $package_id;
        $trans_insert->seller_id = $user_id;
        $trans_insert->status = 'success';
        $trans_insert->base_amount = $base_amount;
        $trans_insert->amount = $payment_total;
        $trans_insert->transaction_gatway = $payment_gateway;
        $trans_insert->transaction_ip = $ip;
        $trans_insert->transaction_time = time();
        $trans_insert->transaction_description = $package['name'];
        $trans_insert->payment_id = $payment_id;
        $trans_insert->transaction_method = 'Subscription';
        $trans_insert->frequency = $payment_frequency;
        $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
        $trans_insert->taxes_ids = $taxes_ids;
        $trans_insert->save();

        //Update Amount in balance table
        $balance = ORM::for_table($config['db']['pre'] . 'balance')->find_one(1);
        $current_amount = $balance['current_balance'];
        $total_earning = $balance['total_earning'];

        $updated_amount = ($payment_total + $current_amount);
        $total_earning = ($payment_total + $total_earning);

        $balance->current_balance = $updated_amount;
        $balance->total_earning = $total_earning;
        $balance->save();

        // check for affiliate payment
        check_affiliate_payment($user, $txn_type, $payment_total, $payment_gateway, $trans_insert->id);

        // reset user's data
        update_user_option($user_id, 'total_words_used', 0);
        update_user_option($user_id, 'total_images_used', 0);
        update_user_option($user_id, 'total_speech_used', 0);
        update_user_option($user_id, 'total_text_to_speech_used', 0);

        update_user_option($user_id, 'last_reset_time', time());

    }elseif ($payment_type == "prepaid_plan") {

        $package = ORM::for_table($config['db']['pre'] . 'prepaid_plans')
            ->where('id', $package_id)
            ->find_one();

        // check plan exists
        if (!isset($package['id'])) {
            http_response_code(400);
            die();
        }

        /* Make sure transaction is not already exist */
        if (ORM::for_table($config['db']['pre'] . 'transaction')
            ->where('payment_id', $payment_id)
            ->where('transaction_gatway', $payment_gateway)
            ->count()) {
            http_response_code(400);
            die();
        }
//Update Amount in balance table
        $balance = ORM::for_table($config['db']['pre'] . 'balance')->find_one(1);
        $current_amount = $balance['current_balance'];
        $total_earning = $balance['total_earning'];

        $updated_amount = ($payment_total + $current_amount);
        $total_earning = ($payment_total + $total_earning);

        $balance->current_balance = $updated_amount;
        $balance->total_earning = $total_earning;
        $balance->save();

        $trans_insert = ORM::for_table($config['db']['pre'] . 'transaction')->create();
        $trans_insert->product_name = $package['name'];
        $trans_insert->product_id = $package_id;
        $trans_insert->seller_id = $user_id;
        $trans_insert->status = 'success';
        $trans_insert->base_amount = $base_amount;
        $trans_insert->amount = $payment_total;
        $trans_insert->transaction_gatway = $payment_gateway;
        $trans_insert->transaction_ip = $ip;
        $trans_insert->transaction_time = time();
        $trans_insert->transaction_description = $package['name'];
        $trans_insert->payment_id = $payment_id;
        $trans_insert->transaction_method = 'prepaid_plan';
        $trans_insert->billing = json_encode($billing, JSON_UNESCAPED_UNICODE);
        $trans_insert->taxes_ids = $taxes_ids;
        $trans_insert->save();

        // update user's data
        $settings = json_decode($package['settings'], true);

        $total_words_available = get_user_option($user_id, 'total_words_available', 0);
        update_user_option($user_id, 'total_words_available', $total_words_available + $settings['ai_words_limit']);

        $total_images_available = get_user_option($user_id, 'total_images_available', 0);
        update_user_option($user_id, 'total_images_available', $total_images_available + $settings['ai_images_limit']);

        $total_speech_available = get_user_option($user_id, 'total_speech_available', 0);
        update_user_option($user_id, 'total_speech_available', $total_speech_available + $settings['ai_speech_to_text_limit']);

        $total_text_to_speech_available = get_user_option($user_id, 'total_text_to_speech_available', 0);
        update_user_option($user_id, 'total_text_to_speech_available', $total_text_to_speech_available + $settings['ai_text_to_speech_limit']);
    }
}

/**
 * Cancel recurring payment
 *
 * @param bool $user_id
 * @throws \Stripe\Exception\ApiErrorException
 */
function cancel_recurring_payment($user_id = false)
{
    global $config, $lang;

    if ($user_id) {
        $subsc_check = ORM::for_table($config['db']['pre'] . 'upgrades')
            ->where('user_id', $user_id)
            ->use_id_column('upgrade_id')
            ->find_one();
    }

    if (empty($subsc_check['unique_id'])) {
        return;
    }

    $data = explode('###', $subsc_check['unique_id']);
    $type = strtolower($data[0]);
    $subscription_id = $data[1];

    switch ($type) {
        case 'stripe':
                include ROOTPATH . '/includes/payments/stripe/stripe-php/init.php';

                /* Initiate Stripe */
                \Stripe\Stripe::setApiKey(get_option('stripe_secret_key'));
                \Stripe\Stripe::setApiVersion('2020-08-27');

                /* Cancel the Stripe Subscription */
                $subscription = \Stripe\Subscription::retrieve($subscription_id);
                $subscription->cancel();

            break;

        case 'paypal':
            include ROOTPATH . '/includes/payments/paypal/paypal-sdk/autoload.php';

            /* Initiate paypal */
            $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential(get_option('paypal_api_client_id'), get_option('paypal_api_secret')));
            $paypal->setConfig(array(
                    'mode' => (get_option('paypal_sandbox_mode') == 'Yes') ?
                        'sandbox' :
                        'live')
            );

            /* Create an Agreement State Descriptor, explaining the reason to suspend. */
            $agreement_state_descriptior = new \PayPal\Api\AgreementStateDescriptor();
            $agreement_state_descriptior->setNote('Suspending the agreement');

            /* Get details about the executed agreement */
            $agreement = \PayPal\Api\Agreement::get($subscription_id, $paypal);

            /* Suspend */
            $agreement->suspend($agreement_state_descriptior, $paypal);


            break;
    }

    /* reset the data */
    $subsc_check->set('unique_id', '');
    $subsc_check->set('pay_mode','one_time');
    $subsc_check->save();
}

/**
 * Get current page url
 * @return string
 */
function get_current_page_url()
{
    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
        $url = 'https://';
    } else {
        $url = 'http://';
    }
    $url .= isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];

    return $url . $_SERVER['REQUEST_URI'];
}

/**
 * Echo URL
 *
 * @param string $key
 */
function url($key, $echo = true)
{
    global $link;
    if ($echo)
        echo isset($link[$key]) ? $link[$key] : '#';
    else
        return isset($link[$key]) ? $link[$key] : '#';
}

/**
 * Return language variable
 *
 * @param string $string
 * @return string
 */
function __($string)
{
    global $lang;
    return isset($lang[$string]) ? $lang[$string] : $string;
}

/**
 * Echo language variable
 *
 * @param string $string
 */
function _e($string)
{
    echo __($string);
}

/**
 * Echo variable
 *
 * @param mixed $variable
 */
function _esc($variable, $echo = true)
{
    if ($echo)
        echo $variable;
    else
        return $variable;
}

/**
 * Escape url
 *
 * @param string $url
 * @return string
 */
function esc_url($url)
{

    if ('' == $url) {
        return $url;
    }

    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string)$url;

    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }

    $url = str_replace(';//', '://', $url);

    $url = htmlentities($url);

    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

/**
 * Split Full name into first and last name
 *
 * @param $name
 * @return array
 */
function split_name($name)
{
    $name = trim($name);
    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
    $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));

    return array($first_name, $last_name);
}

/**
 * Print advertisement codes
 *
 * @param $slug
 */
function print_adsense_code($slug)
{
    global $config;

    $settings = get_user_membership_settings();

    if ($settings['show_ads']) {
        $adsense = ORM::for_table($config['db']['pre'] . 'adsense')
            ->where('slug', $slug)
            ->where('status', '1')
            ->find_one();

        if (!empty($adsense['id'])) {
            if (!empty($adsense['large_track_code'])) {
                ?>
                <div class="d-none d-lg-block">
                    <div class="adsense-banner adsense-banner-<?php _esc($slug) ?>">
                        <?php _esc($adsense['large_track_code']); ?>
                    </div>
                </div>
            <?php }
            if (!empty($adsense['tablet_track_code'])) {
                ?>
                <div class="d-none d-md-block d-lg-none">
                    <div class="adsense-banner adsense-banner-<?php _esc($slug) ?>">
                        <?php _esc($adsense['tablet_track_code']); ?>
                    </div>
                </div>
            <?php }
            if (!empty($adsense['phone_track_code'])) {
                ?>
                <div class="d-sm-block d-md-none">
                    <div class="adsense-banner adsense-banner-<?php _esc($slug) ?>">
                        <?php _esc($adsense['phone_track_code']); ?>
                    </div>
                </div>
            <?php }
        }
    }
}

function get_avatar_url_by_name($name, $length = 1)
{
    return 'https://ui-avatars.com/api/?color=random&background=random&name='.$name.'&length='.$length;
}

/**
 * Page not found
 */
function page_not_found()
{
    header("HTTP/1.0 404 Not Found");
    error(__("Page Not Found"), __LINE__, __FILE__, 1);
    exit();
}

function _image_fly($url, $width, $height, $echo = true)
{
    global $config;
    $fly_url = $config['site_url'] . "imagecontent?src=" . $url . "&w=" . $width . "&h=" . $height;
    if ($echo)
        echo $fly_url;
    else
        return $fly_url;
}

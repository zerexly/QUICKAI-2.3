<?php
require_once 'init.php';

if (Input::post("action") != "install") {
    jsonecho("Invalid action", 101);
}

// Check required keys
$required_fields = array(
    "key", "db_host", "db_name", "db_username", "user_fullname", "user_username", "user_email", "user_password", "user_timezone", "user_country"
);

foreach ($required_fields as $f) {
    if (!Input::post($f)) {
        jsonecho("Missing data: ".$f, 102);
    }
}

if (!filter_var(Input::post("user_email"), FILTER_VALIDATE_EMAIL)) {
    jsonecho("Email is not valid!", 103);
}

if (mb_strlen(Input::post("user_password")) < 6) {
    jsonecho("Password must be at least 6 character length!", 104);
}

// Check database connection
$dsn = 'mysql:host='
    . Input::post("db_host")
    . ';dbname=' . Input::post("db_name")
    . ';charset=utf8';
$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

try {
    $con = new PDO($dsn, Input::post("db_username"), Input::post("db_password"), $options);
} catch (\Exception $e) {
    jsonecho("Couldn't connect to the database!", 105);
}


$license_key = Input::post("key");

$output = json_decode(file_get_contents('../data.json'), true);

$output = array();
$output['success'] = true;

if(is_array($output)){
    if ($output['success']) {
        $db_host = Input::post("db_host");
        $db_name = Input::post("db_name");
        $db_user = Input::post("db_username");
        $db_pass = Input::post("db_password");
        $db_prefix = Input::post("db_table_prefix");

        $admin_fullname = Input::post("user_fullname");
        $admin_username = Input::post("user_username");
        $admin_email = Input::post("user_email");
        $admin_password = Input::post("user_password");
        $dafault_timezone = Input::post("user_timezone");
        $default_country = Input::post("user_country");

        importSchemaSql($con, $db_prefix);
        importDataSql($con, $db_prefix);

        $pass_hash = password_hash($admin_password, PASSWORD_DEFAULT, ['cost' => 13]);


        /*Insert Data in table*/
        $con->query("TRUNCATE TABLE `" . addslashes($db_prefix) . "admins`");
        $con->query("INSERT INTO `" . addslashes($db_prefix) . "admins` (`id`, `username`, `password_hash`, `name`, `email`, `image`, `permission`) VALUES
(1, '" . addslashes($admin_username) . "', '" . $pass_hash . "', 'Admin', '" . addslashes($admin_email) . "', 'default_user.png', '1');");


        $con->query("UPDATE `" . addslashes($db_prefix) . "countries` set active='1' WHERE `code` = '" . $default_country . "'");

        $con->query("INSERT INTO " . addslashes($db_prefix) . "options (`option_name`, `option_value`) VALUES ('specific_country', '" . $default_country . "')");

        $con->query("INSERT INTO " . addslashes($db_prefix) . "options (`option_name`, `option_value`) VALUES ('site_url', '" . APPURL . "')");

        // Content that will be written to the config file
        $content = "<?php\n";
        $content .= "\$config['db']['host'] = '" . addslashes($db_host) . "';\n";
        $content .= "\$config['db']['name'] = '" . addslashes($db_name) . "';\n";
        $content .= "\$config['db']['user'] = '" . addslashes($db_user) . "';\n";
        $content .= "\$config['db']['pass'] = '" . addslashes($db_pass) . "';\n";
        $content .= "\$config['db']['pre'] = '" . addslashes($db_prefix) . "';\n";
        $content .= "\n";
        $content .= "\$config['admin_folder'] = 'admin';\n";
        $content .= "\$config['version'] = '" . VERSION . "';\n";
        $content .= "\$config['installed'] = '1';\n";
        $content .= "?>";

        // Open the config.php for writting
        $handle = fopen('../includes/config.php', 'w');
        // Write the config file
        fwrite($handle, $content);
        // Close the file
        fclose($handle);

        jsonecho("Your license key verified", 1);
    }
    elseif($output['error']){
        jsonecho($output['error'], 107);
    }else {
        jsonecho("Couldn't validate your license key! Please try again later.", 109);
    }
}
else {
    jsonecho("Couldn't validate your license key! Please try again later.", 109);
}
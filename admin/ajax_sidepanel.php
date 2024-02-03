<?php
require_once('includes.php');

if (!checkloggedadmin()) {
    exit('Access Denied.');
}

//SidePanel Ajax Function
if(isset($_REQUEST['action'])){
    if(!check_allow()){
        $status = "error";
        $message = __("permission denied for demo.");
        echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
        die();
    }
    if ($_REQUEST['action'] == "addEditAdmin") { addEditAdmin(); }
    if ($_REQUEST['action'] == "addEditUser") { addEditUser(); }
    if ($_REQUEST['action'] == "addEditSubscriber") { addEditSubscriber(); }
    if ($_REQUEST['action'] == "addEditCurrency") { addEditCurrency(); }
    if ($_REQUEST['action'] == "addEditTimezone") { addEditTimezone(); }
    if ($_REQUEST['action'] == "addEditTestimonial") { addEditTestimonial(); }
    if ($_REQUEST['action'] == "addEditTax") { addEditTax(); }
    if ($_REQUEST['action'] == "addLanguage") { addLanguage(); }
    if ($_REQUEST['action'] == "editLanguage") { editLanguage(); }
    if ($_REQUEST['action'] == "addMembershipPlan") { addMembershipPlan(); }
    if ($_REQUEST['action'] == "editMembershipPlan") { editMembershipPlan(); }
    if ($_REQUEST['action'] == "editPrepaidPlan") { editPrepaidPlan(); }
    if ($_REQUEST['action'] == "addTax") { addTax(); }
    if ($_REQUEST['action'] == "editTax") { editTax(); }
    if ($_REQUEST['action'] == "addStaticPage") { addStaticPage(); }
    if ($_REQUEST['action'] == "editStaticPage") { editStaticPage(); }
    if ($_REQUEST['action'] == "addFAQentry") { addFAQentry(); }
    if ($_REQUEST['action'] == "editFAQentry") { editFAQentry(); }
    if ($_REQUEST['action'] == "transactionEdit") { transactionEdit(); }
    if ($_REQUEST['action'] == "paymentEdit") { paymentEdit(); }
    if ($_REQUEST['action'] == "addBlogCat") { addBlogCat(); }
    if ($_REQUEST['action'] == "editBlogCat") { editBlogCat(); }
    if ($_REQUEST['action'] == "saveEmailTemplate") { saveEmailTemplate(); }
    if ($_REQUEST['action'] == "testEmailTemplate") { testEmailTemplate(); }
    if ($_REQUEST['action'] == "editWithdrawal") { editWithdrawal(); }
    if ($_REQUEST['action'] == "editAdvertise") { editAdvertise(); }

    if ($_REQUEST['action'] == "editAIDocument") { editAIDocument(); }
    if ($_REQUEST['action'] == "editAITemplate") { editAITemplate(); }
    if ($_REQUEST['action'] == "editAICustomTemplate") { editAICustomTemplate(); }
    if ($_REQUEST['action'] == "editAITplCategory") { editAITplCategory(); }
    if ($_REQUEST['action'] == "editAPIKey") { editAPIKey(); }
    if ($_REQUEST['action'] == "editAIChatBot") { editAIChatBot(); }
    if ($_REQUEST['action'] == "editAIChatBotCategory") { editAIChatBotCategory(); }
    if ($_REQUEST['action'] == "editAIChatPrompts") { editAIChatPrompts(); }

    if ($_GET['action'] == "SaveSettings") { SaveSettings(); }
}

function change_config_file_settings($filePath, $newSettings,$lang)
{
    // Update $fileSettings with any new values
    $fileSettings = array_merge($lang, $newSettings);
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


function addEditAdmin() {
    global $config;
    $_POST = validate_input($_POST);

    $image = null;
    $error = array();
    $name_length = mb_strlen($_POST['name']);

    if(empty($_POST["name"])) {
        $error[] = __("Enter your full name.");
    }
    elseif( ($name_length < 2) OR ($name_length > 41) ) {
        $error[] = __("Name must be between 2 and 40 characters long.");
    }

    if(empty($_POST['username'])){
        $error[] = __('Username is required');
    }elseif(isset($_POST['id'])){
        $count = ORM::for_table($config['db']['pre'].'admins')
            ->where('username', $_POST['username'])
            ->where_not_equal('id', $_POST['id'])
            ->count();
        if($count) {
            $error[] = __('Username is already available');
        }
    }

    // Check if this is an Email availability
    $_POST["email"] = strtolower($_POST["email"]);

    if(empty($_POST["email"])) {
        $error[] = __("Please enter an email address");
    }
    elseif(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $error[] = __("This is not a valid email address");
    }
    elseif(isset($_POST['id'])){
        $count = ORM::for_table($config['db']['pre'].'admins')
            ->where('email', $_POST['email'])
            ->where_not_equal('id', $_POST['id'])
            ->count();
        if($count) {
            $error[] = __("An account already exists with that e-mail address");
        }
    }

    if(empty($error)) {
        if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
            $target_dir = ROOTPATH . "/storage/profile/";
            $result = quick_file_upload('image',$target_dir);
            if($result['success']) {
                $image = $result['file_name'];
                resizeImage(100, $target_dir . $image, $target_dir . $image);
                if (!empty($_POST['id'])){
                    // remove old image
                    $info = ORM::for_table($config['db']['pre'].'admins')
                        ->select('image')
                        ->find_one($_POST['id']);

                    if (!empty(trim($info['image'])) && $info['image'] != "default_user.png") {
                        if (file_exists($target_dir . $info['image'])) {
                            unlink($target_dir . $info['image']);
                        }
                    }
                }
            }else{
                $error[] = $result['error'];
            }
        }
    }

    if (empty($error)) {
        if (isset($_POST['id'])) {
            $admins = ORM::for_table($config['db']['pre'].'admins')->find_one($_POST['id']);
            $admins->name = validate_input($_POST['name']);
            $admins->username = validate_input($_POST['username']);
            $admins->email = validate_input($_POST['email']);
            if (!empty($_POST['password'])) {
                $pass_hash = password_hash($_POST['password'], PASSWORD_DEFAULT, ['cost' => 13]);
                $admins->password_hash = $pass_hash;
            }
            if (!empty($image)) {
                $admins->image = $image;
            }
            $admins->save();
        } else {
            $password = $_POST["password"];
            $pass_hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 13]);

            $admins = ORM::for_table($config['db']['pre'].'admins')->create();
            $admins->name = validate_input($_POST['name']);
            $admins->username = validate_input($_POST['username']);
            $admins->email = validate_input($_POST['email']);
            $admins->password_hash = $pass_hash;
            if (!empty($image)) {
                $admins->image = $image;
            }
            $admins->save();
        }

        if ($admins->id()) {
            $status = "success";
            $message = __("Saved Successfully");
        } else{
            $status = "error";
            $message = __("Error: Please try again.");
        }

    } else {
        $status = "error";
        $message = implode('<br>', $error);
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function addEditUser(){
    global $config;
    $error = array();
    $image = null;
    $now = date("Y-m-d H:i:s");
    if(!isset($_POST['user_type'])){
        $_POST['user_type'] = "user";
    }

    $name_length = mb_strlen(($_POST['name']));

    if(empty($_POST["name"])) {
        $error[] = __("Enter your full name.");
    }
    elseif( ($name_length < 2) OR ($name_length > 41) ) {
        $error[] = __("Name must be between 2 and 40 characters long.");
    }

    if(empty($_POST['username'])){
        $error[] = __('Username is required');
    }elseif(!isset($_POST['id'])){
        $count = check_username_exists($_POST["username"]);
        if($count) {
            $error[] = __('Username is already available');
        }
    }

    // Check if this is an Email availability
    $_POST["email"] = mb_strtolower($_POST["email"]);

    if(empty($_POST["email"])) {
        $error[] = __("Please enter an email address");
    }
    elseif(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $error[] = __("This is not a valid email address");
    }
    elseif(!isset($_POST['id'])){
        $count = check_account_exists($_POST["email"]);
        if($count) {
            $error[] = __("An account already exists with that e-mail address");
        }
    }

    if (empty($error)) {
        if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
            $target_dir = ROOTPATH . "/storage/profile/";
            $result = quick_file_upload('image', $target_dir);
            if ($result['success']) {
                $image = $result['file_name'];
                resizeImage(100, $target_dir . $image, $target_dir . $image);
                if (isset($_POST['id'])) {
                    // remove old image
                    $info = ORM::for_table($config['db']['pre'] . 'user')
                        ->select('image')
                        ->find_one($_POST['id']);

                    if (!empty(trim($info['image'])) && $info['image'] != "default_user.png") {
                        if (file_exists($target_dir . $info['image'])) {
                            unlink($target_dir . $info['image']);
                        }
                    }
                }
            } else {
                $error[] = $result['error'];
            }
        }
    }

    if(empty($error)){
        if (isset($_POST['id'])) {
            /* Update plan */
            $subsc_check = ORM::for_table($config['db']['pre'].'upgrades')
                ->where('user_id', $_POST['id'])
                ->count();
            if($_POST['current_plan'] != 'free'){
                $expires = strtotime($_POST['plan_expiration_date']);
                if($subsc_check == 1){
                    $upgrades = ORM::for_table($config['db']['pre'].'upgrades')
                        ->use_id_column('upgrade_id')
                        ->where('user_id', validate_input($_POST['id']))
                        ->find_one();
                    $upgrades->sub_id = validate_input($_POST['current_plan']);
                    $upgrades->upgrade_expires = validate_input($expires);
                    $upgrades->save();

                }else{
                    $upgrades_insert = ORM::for_table($config['db']['pre'].'upgrades')->create();
                    $upgrades_insert->sub_id = $_POST['current_plan'];
                    $upgrades_insert->user_id = $_POST['id'];
                    $upgrades_insert->upgrade_lasttime = time();
                    $upgrades_insert->upgrade_expires = $expires;
                    $upgrades_insert->status = "Active";
                    $upgrades_insert->save();
                }
            }else{
                ORM::for_table($config['db']['pre'].'upgrades')
                    ->where_equal('user_id', $_POST['id'])
                    ->delete_many();
            }

            // reset the plan uses if the plan is changed
            $user_data = get_user_data(null, $_POST['id']);
            if($user_data['group_id'] != $_POST['current_plan']){
                update_user_option($_POST['id'], 'total_words_used', 0);
                update_user_option($_POST['id'], 'total_images_used', 0);
                update_user_option($_POST['id'], 'total_speech_used', 0);
                update_user_option($_POST['id'], 'total_text_to_speech_used', 0);

                update_user_option($_POST['id'], 'last_reset_time', time());
            }

            update_user_option($_POST['id'], 'total_words_available', validate_input($_POST['total_words_available']));
            update_user_option($_POST['id'], 'total_images_available', validate_input($_POST['total_images_available']));
            update_user_option($_POST['id'], 'total_speech_available', validate_input($_POST['total_speech_available']));
            update_user_option($_POST['id'], 'total_text_to_speech_available', validate_input($_POST['total_text_to_speech_available']));

            $users = ORM::for_table($config['db']['pre'].'user')->find_one($_POST['id']);
            $users->group_id = validate_input($_POST['current_plan']);
            $users->status = validate_input($_POST['status']);
            $users->name = validate_input($_POST['name']);
            $users->username = validate_input($_POST['username']);
            $users->user_type = validate_input($_POST['user_type']);
            $users->email = validate_input($_POST['email']);
            $users->sex = validate_input($_POST['sex']);
            $users->description = validate_input($_POST['about'],true);
            $users->country = validate_input($_POST['country']);
            if (!empty($_POST['password'])) {
                $pass_hash = password_hash($_POST['password'], PASSWORD_DEFAULT, ['cost' => 13]);
                $users->password_hash = $pass_hash;
            }
            if (!empty($image)) {
                $users->image = $image;
            }
            $users->updated_at = $now;
            $users->save();

            /* Update trial done */
            update_user_option($_POST['id'], 'package_trial_done', (int) $_POST['plan_trial_done']);

        } else {
            $password = $_POST["password"];
            $pass_hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 13]);

            $confirm_id = get_random_id();

            $users = ORM::for_table($config['db']['pre'].'user')->create();
            $users->status = '0';
            $users->name = validate_input($_POST['name']);
            $users->username = validate_input($_POST['username']);
            $users->user_type = validate_input($_POST['user_type']);
            $users->email = validate_input($_POST['email']);
            $users->sex = validate_input($_POST['sex']);
            $users->description = validate_input($_POST['about'],true);
            $users->country = validate_input($_POST['country']);
            $users->confirm = $confirm_id;
            $users->referral_key = uniqid(get_random_string(5));
            $users->password_hash = $pass_hash;
            if (!empty($image)) {
                $users->image = $image;
            }
            $users->created_at = $now;
            $users->updated_at = $now;
            $users->save();
        }

        if ($users->id()) {
            $status = "success";
            $message = __("Saved Successfully");
        } else{
            $status = "error";
            $message = __("Error: Please try again.");
        }
    }else {
        $status = "error";
        $message = implode('<br>', $error);
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function addEditSubscriber() {
    global $config;
    $_POST = validate_input($_POST);

    if(isset($_POST['id']) && $_POST['id'] != ""){
        $subscriber_update = ORM::for_table($config['db']['pre'].'subscriber')->find_one($_POST['id']);
        $subscriber_update->set('email', $_POST['email']);
        $subscriber_update->set('joined', date('Y-m-d'));
        $subscriber_update->save();
    }else{
        /* Save email */
        $subscriber_insert = ORM::for_table($config['db']['pre'].'subscriber')->create();
        $subscriber_insert->email = $_POST['email'];
        $subscriber_insert->joined = date('Y-m-d');
        $subscriber_insert->save();
    }

    $result = array('status' => 'success', 'message' => __('Saved Successfully.'));

    echo json_encode($result);
}

function addEditCurrency(){
    global $config;
    $_POST = validate_input($_POST);
    $in_left = $_POST['in_left'];
    $decimal_places = ($_POST['decimal_places'] != "")? $_POST['decimal_places'] : 2;
    if(strlen($_POST['code']) > 3){
        $status = "error";
        $message = __('Currency code max length is 3.');
        echo '{"status" : "' . $status . '","message" : "' . $message . '"}';
        die();
    }

    if (isset($_POST['id'])) {
        $update_currency = ORM::for_table($config['db']['pre'].'currencies')->find_one($_POST['id']);
        $update_currency->set('name', $_POST['name']);
        $update_currency->set('code', $_POST['code']);
        $update_currency->set('html_entity', $_POST['html_entity']);
        $update_currency->set('font_arial', $_POST['font_arial']);
        $update_currency->set('font_code2000', $_POST['font_code2000']);
        $update_currency->set('unicode_decimal', $_POST['unicode_decimal']);
        $update_currency->set('unicode_hex', $_POST['unicode_hex']);
        $update_currency->set('decimal_places', $decimal_places);
        $update_currency->set('decimal_separator', $_POST['decimal_separator']);
        $update_currency->set('thousand_separator', $_POST['thousand_separator']);
        $update_currency->set('in_left', $in_left);
        $update_currency->save();
    } else {
        $insert_currency = ORM::for_table($config['db']['pre'].'currencies')->create();
        $insert_currency->name = $_POST['name'];
        $insert_currency->code = $_POST['code'];
        $insert_currency->html_entity = $_POST['html_entity'];
        $insert_currency->font_arial = $_POST['font_arial'];
        $insert_currency->font_code2000 = $_POST['font_code2000'];
        $insert_currency->unicode_decimal = $_POST['unicode_decimal'];
        $insert_currency->unicode_hex = $_POST['unicode_hex'];
        $insert_currency->decimal_places = $decimal_places;
        $insert_currency->decimal_separator = $_POST['decimal_separator'];
        $insert_currency->thousand_separator = $_POST['thousand_separator'];
        $insert_currency->in_left = $in_left;
        $insert_currency->save();
    }
    $status = "success";
    $message = __("Saved Successfully");

    echo '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function addEditTimezone(){
    global $config;

    $_POST = validate_input($_POST);
    if($_POST['time_zone_id'] == ""){
        $status = "error";
        $message = __('Please fill the required fields.');
        echo '{"status" : "' . $status . '","message" : "' . $message . '"}';
        die();
    }
    $gmt = ($_POST['gmt'] != "")? $_POST['gmt'] : 0;
    $dst = ($_POST['dst'] != "")? $_POST['dst'] : 0;
    $raw = ($_POST['raw'] != "")? $_POST['raw'] : 0;

    if (isset($_POST['id'])) {
        $timezones = ORM::for_table($config['db']['pre'].'time_zones')->find_one($_POST['id']);
        $timezones->set('country_code', $_POST['country_code']);
        $timezones->set('time_zone_id', $_POST['time_zone_id']);
        $timezones->set('gmt', $gmt);
        $timezones->set('dst', $dst);
        $timezones->set('raw', $raw);
        $timezones->save();

    } else {
        $timezones = ORM::for_table($config['db']['pre'].'time_zones')->create();
        $timezones->country_code = $_POST['country_code'];
        $timezones->time_zone_id = $_POST['time_zone_id'];
        $timezones->gmt = $gmt;
        $timezones->dst = $dst;
        $timezones->raw = $raw;
        $timezones->save();
    }

    if ($timezones->id()) {
        $status = "success";
        $message = __("Saved Successfully");
    } else{
        $status = "error";
        $message = __("Error: Please try again.");
    }

    echo '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function addEditTestimonial(){
    global $config;

    $_POST = validate_input($_POST);

    $title = $_POST['name'];
    $designation = $_POST['designation'];
    $description = $_POST['content'];

    $image = null;
    $error = array();

    if(empty($title)){
        $error[] = __("Name is required.");
    }
    if(empty($designation)){
        $error[] = __("Designation is required.");
    }
    if(empty($description)){
        $error[] = __("Content is required.");
    }

    if (empty($error)) {
        if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
            $target_dir = ROOTPATH . "/storage/testimonials/";
            $result = quick_file_upload('image', $target_dir);
            if ($result['success']) {
                $image = $result['file_name'];
                resizeImage(100, $target_dir . $image, $target_dir . $image);
                if (isset($_POST['id'])) {
                    // remove old image
                    $info = ORM::for_table($config['db']['pre'] . 'testimonials')
                        ->select('image')
                        ->find_one($_POST['id']);

                    if (!empty(trim($info['image'])) && $info['image'] != "default_user.png") {
                        if (file_exists($target_dir . $info['image'])) {
                            unlink($target_dir . $info['image']);
                        }
                    }
                }
            } else {
                $error[] = $result['error'];
            }
        }
    }

    if (empty($error)) {
        if(isset($_POST['id'])){
            $test = ORM::for_table($config['db']['pre'].'testimonials')->find_one($_POST['id']);
            $test->name = $title;
            $test->designation = $designation;
            $test->content = $description;
            if($image){
                $test->image = $image;
            }
            $test->save();
        } else {
            $test = ORM::for_table($config['db']['pre'].'testimonials')->create();
            $test->name = $title;
            $test->designation = $designation;
            $test->image = $image;
            $test->content = $description;
            $test->save();
        }

        $status = "success";
        $message = __("Saved Successfully");

        echo '{"status" : "' . $status . '","message" : "' . $message . '"}';
        die();
    } else {
        $status = "error";
        $message = implode('<br>', $error);
    }
    $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    echo $json;
    die();
}

function addEditTax(){
    global $config;

    $_POST = validate_input($_POST);

    if (isset($_POST['submit'])) {

        if ($_POST['internal_name'] == "") {
            echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
            die();
        }
        if ($_POST['name'] == "") {
            echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
            die();
        }
        if ($_POST['description'] == "") {
            echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
            die();
        }
        if ($_POST['value'] == "") {
            echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
            die();
        }

        if (isset($_POST['id'])) {
            $taxes = ORM::for_table($config['db']['pre'].'taxes')->find_one($_POST['id']);
            $taxes->internal_name = validate_input($_POST['internal_name']);
            $taxes->name = validate_input($_POST['name']);
            $taxes->description = validate_input($_POST['description']);
            $taxes->value = validate_input($_POST['value']);
            $taxes->value_type = validate_input($_POST['value_type']);
            $taxes->type = validate_input($_POST['type']);
            $taxes->billing_type = validate_input($_POST['billing_type']);
            $taxes->countries = isset($_POST['countries'])? validate_input(implode(',',$_POST['countries'])) : null;
            $taxes->save();
        } else {
            $taxes = ORM::for_table($config['db']['pre'].'taxes')->create();
            $taxes->internal_name = validate_input($_POST['internal_name']);
            $taxes->name = validate_input($_POST['name']);
            $taxes->description = validate_input($_POST['description']);
            $taxes->value = validate_input($_POST['value']);
            $taxes->value_type = validate_input($_POST['value_type']);
            $taxes->type = validate_input($_POST['type']);
            $taxes->billing_type = validate_input($_POST['billing_type']);
            $taxes->countries = isset($_POST['countries'])? validate_input(implode(',',$_POST['countries'])) : null;
            $taxes->datetime = date('Y-m-d H:i:s');
            $taxes->save();
        }

        if ($taxes->id()) {
            $status = "success";
            $message = __("Saved Successfully");
        } else{
            $status = "error";
            $message = __("Error: Please try again.");
        }

    } else {
        $status = "error";
        $message = __("Error: Please try again.");
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function addLanguage(){
    global $config,$lang;
    $_POST = validate_input($_POST);

    if (isset($_POST['submit'])) {
        if(isset($_POST['name']) && $_POST['name'] != ""){

            $post_langname = str_replace(' ', '', $_POST['name']);
            $post_filename = str_replace(' ', '', strtolower($_POST['file_name']));

            $filePath = '../includes/lang/lang_'.$post_filename.'.php';
            if (!file_exists($filePath)) {

                $source = 'en';
                $target = $_POST['code'];
                $trans = new GoogleTranslate();
                $newLangArray = array();
                foreach ($lang as $key => $value)
                {
                    if($_POST['auto_tran'] == 1){
                        $result = $trans->translate($source, $target, $value);
                        $result = !empty($result)?$result:$value;
                    }else{
                        $result = $value;
                    }

                    $newLangArray[$key] = $result;
                }
                fopen($filePath, "w");
                change_config_file_settings($filePath, $newLangArray,$lang);

                $insert_language = ORM::for_table($config['db']['pre'].'languages')->create();
                $insert_language->code = $_POST['code'];
                $insert_language->name = $post_langname;
                $insert_language->direction = $_POST['direction'];
                $insert_language->file_name = $post_filename;
                $insert_language->active = $_POST['active'];
                $insert_language->save();

                if ($insert_language->id()) {
                    $status = "success";
                    $message = __("Saved Successfully");
                } else{
                    $status = "error";
                    $message = __("Error: Please try again.");
                }


            } else {
                $message = __("Same language file exists. Change language file name.");
                echo $json = '{"status" : "error","message" : "' . $message . '"}';
                die();
            }
        }else{
            $status = "error";
            $message = __("Error: Please try again.");
        }

    } else {
        $status = "error";
        $message = __("Error: Please try again.");
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function editLanguage(){
    global $config;
    $_POST = validate_input($_POST);
    if (isset($_POST['id'])) {

        $update_language = ORM::for_table($config['db']['pre'].'languages')->find_one($_POST['id']);
        $update_language->set('code', $_POST['code']);
        $update_language->set('name', $_POST['name']);
        $update_language->set('direction', $_POST['direction']);
        $update_language->set('active', $_POST['active']);
        $update_language->save();

        if ($update_language) {
            $status = "success";
            $message = __("Saved Successfully");
        } else{
            $status = "error";
            $message = __("Error: Please try again.");
        }


    } else {
        $status = "error";
        $message = __("Error: Please try again.");
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function addMembershipPlan()
{
    global $config,$lang;

    if (isset($_POST['submit'])) {
        $_POST = validate_input($_POST);

        if ($_POST['name'] == "") {
            echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
            die();
        }
        if ($_POST['monthly_price'] == "") {
            echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
            die();
        }
        if ($_POST['annual_price'] == "") {
            echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
            die();
        }
        if ($_POST['lifetime_price'] == "") {
            echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
            die();
        }

        $recommended = isset($_POST['recommended']) ? "yes" : "no";
        $active = isset($_POST['active']) ? 1 : 0;

        $_POST['ai_model'] = !empty($_POST['ai_model']) ? $_POST['ai_model'] : get_option('open_ai_model');
        $_POST['ai_chat_model'] = !empty($_POST['ai_chat_model']) ? $_POST['ai_chat_model'] : get_option('open_ai_chat_model');

        $_POST['ai_templates'] = !empty($_POST['ai_templates']) ? $_POST['ai_templates'] : array();
        $_POST['ai_chatbots'] = !empty($_POST['ai_chatbots']) ? $_POST['ai_chatbots'] : array();

        $settings = array(
            'ai_model' => $_POST['ai_model'],
            'ai_templates' => $_POST['ai_templates'],
            'ai_words_limit' => (int) $_POST['ai_words_limit'],
            'ai_images_limit' => (int) $_POST['ai_images_limit'],
            'ai_chat' => (int) $_POST['ai_chat'],
            'ai_chatbots' => $_POST['ai_chatbots'],
            'ai_chat_model' => $_POST['ai_chat_model'],
            'ai_code' => (int) $_POST['ai_code'],
            'ai_text_to_speech_limit' => (int) $_POST['ai_text_to_speech_limit'],
            'ai_speech_to_text_limit' => (int) $_POST['ai_speech_to_text_limit'],
            'ai_speech_to_text_file_limit' => (int) $_POST['ai_speech_to_text_file_limit'],
            'show_ads' => (int) $_POST['show_ads'],
            'live_chat' => (int) $_POST['live_chat'],
            'custom' => array()
        );

        $plan_custom = ORM::for_table($config['db']['pre'].'plan_options')
            ->where('active', 1)
            ->order_by_asc('position')
            ->find_many();
        foreach ($plan_custom as $custom){
            if(!empty($custom['title']) && trim($custom['title']) != '' && !empty($_POST['custom_'.$custom['id']])) {
                $settings['custom'][$custom['id']] = 1;
            }
        }

        $insert_subscription = ORM::for_table($config['db']['pre'].'plans')->create();
        $insert_subscription->name = validate_input($_POST['name']);
        $insert_subscription->badge = $_POST['badge'];
        $insert_subscription->monthly_price = $_POST['monthly_price'];
        $insert_subscription->annual_price = $_POST['annual_price'];
        $insert_subscription->lifetime_price = $_POST['lifetime_price'];
        $insert_subscription->settings = json_encode($settings);
        $insert_subscription->taxes_ids = isset($_POST['taxes'])? validate_input(implode(',',$_POST['taxes'])) : null;
        $insert_subscription->status = $active;
        $insert_subscription->recommended = $recommended;
        $insert_subscription->date = date('Y-m-d H:i:s');
        $insert_subscription->save();

        if ($insert_subscription->id()) {
            $status = "success";
            $message = __("Saved Successfully");
        } else{
            $status = "error";
            $message = __("Error: Please try again.");
        }

    } else {
        $status = "error";
        $message = __("Error: Please try again.");
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function editMembershipPlan()
{
    global $config,$lang;

    if (isset($_POST['submit'])) {
        $_POST = validate_input($_POST);

        if ($_POST['name'] == "") {
            echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
            die();
        }

        $active = $_POST['active'] ? 1 : 0;

        $_POST['ai_model'] = !empty($_POST['ai_model']) ? $_POST['ai_model'] : get_option('open_ai_model');
        $_POST['ai_chat_model'] = !empty($_POST['ai_chat_model']) ? $_POST['ai_chat_model'] : get_option('open_ai_chat_model');

        $_POST['ai_templates'] = !empty($_POST['ai_templates']) ? $_POST['ai_templates'] : array();
        $_POST['ai_chatbots'] = !empty($_POST['ai_chatbots']) ? $_POST['ai_chatbots'] : array();

        $settings = array(
            'ai_model' => $_POST['ai_model'],
            'ai_templates' => $_POST['ai_templates'],
            'ai_words_limit' => (int) $_POST['ai_words_limit'],
            'ai_images_limit' => (int) $_POST['ai_images_limit'],
            'ai_chat' => (int) $_POST['ai_chat'],
            'ai_chatbots' => $_POST['ai_chatbots'],
            'ai_chat_model' => $_POST['ai_chat_model'],
            'ai_code' => (int) $_POST['ai_code'],
            'ai_text_to_speech_limit' => (int) $_POST['ai_text_to_speech_limit'],
            'ai_speech_to_text_limit' => (int) $_POST['ai_speech_to_text_limit'],
            'ai_speech_to_text_file_limit' => (int) $_POST['ai_speech_to_text_file_limit'],
            'show_ads' => (int) $_POST['show_ads'],
            'live_chat' => (int) $_POST['live_chat'],
            'custom' => array()
        );

        $plan_custom = ORM::for_table($config['db']['pre'].'plan_options')
            ->where('active', 1)
            ->order_by_asc('position')
            ->find_many();
        foreach ($plan_custom as $custom){
            if(!empty($custom['title']) && trim($custom['title']) != '' && !empty($_POST['custom_'.$custom['id']])) {
                $settings['custom'][$custom['id']] = 1;
            }
        }

        switch ($_POST['id']){
            case 'free':
                $plan = json_encode(array(
                    'id' => 'free',
                    'name' => validate_input($_POST['name']),
                    'badge' => $_POST['badge'],
                    'settings' => $settings,
                    'status' => $active
                ), JSON_UNESCAPED_UNICODE);
                update_option('free_membership_plan', $plan);
                break;
            case 'trial':
                $plan = json_encode(array(
                    'id' => 'trial',
                    'name' => validate_input($_POST['name']),
                    'badge' => $_POST['badge'],
                    'days' => (int) $_POST['days'],
                    'settings' => $settings,
                    'status' => $active
                ), JSON_UNESCAPED_UNICODE);
                update_option('trial_membership_plan', $plan);
                break;
            default:
                if ($_POST['monthly_price'] == "") {
                    echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
                    die();
                }
                if ($_POST['annual_price'] == "") {
                    echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
                    die();
                }
                if ($_POST['lifetime_price'] == "") {
                    echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
                    die();
                }

                $recommended = $_POST['recommended'] ? "yes" : "no";

                $insert_subscription = ORM::for_table($config['db']['pre'].'plans')->find_one($_POST['id']);
                $insert_subscription->name = validate_input($_POST['name']);
                $insert_subscription->badge = $_POST['badge'];
                $insert_subscription->monthly_price = $_POST['monthly_price'];
                $insert_subscription->annual_price = $_POST['annual_price'];
                $insert_subscription->lifetime_price = $_POST['lifetime_price'];
                $insert_subscription->settings = json_encode($settings);
                $insert_subscription->taxes_ids = isset($_POST['taxes'])? validate_input(implode(',',$_POST['taxes'])) : null;
                $insert_subscription->status = $active;
                $insert_subscription->recommended = $recommended;
                $insert_subscription->date = date('Y-m-d H:i:s');
                $insert_subscription->save();
                break;
        }

        $status = "success";
        $message = __("Saved Successfully");

    } else {
        $status = "error";
        $message = __("Error: Please try again.");
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function editPrepaidPlan() {
    global $config;

    if (isset($_POST['submit'])) {
        $_POST = validate_input($_POST);

        if ($_POST['name'] == "") {
            echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
            die();
        }

        $active = $_POST['active'] ? 1 : 0;

        $settings = array(
            'ai_words_limit' => (int) $_POST['ai_words_limit'],
            'ai_images_limit' => (int) $_POST['ai_images_limit'],
            'ai_text_to_speech_limit' => (int) $_POST['ai_text_to_speech_limit'],
            'ai_speech_to_text_limit' => (int) $_POST['ai_speech_to_text_limit'],
        );

        if ($_POST['price'] == "") {
            echo $json = '{"status" : "error","message" : "' . __('Please fill the required fields.') . '"}';
            die();
        }

        $recommended = $_POST['recommended'] ? "yes" : "no";

        if (isset($_POST['id'])) {
            $prepaid_plans = ORM::for_table($config['db']['pre'] . 'prepaid_plans')->find_one($_POST['id']);
        } else {
            $prepaid_plans = ORM::for_table($config['db']['pre'].'prepaid_plans')->create();
        }
        $prepaid_plans->name = validate_input($_POST['name']);
        $prepaid_plans->price = $_POST['price'];
        $prepaid_plans->settings = json_encode($settings);
        $prepaid_plans->taxes_ids = isset($_POST['taxes'])? validate_input(implode(',',$_POST['taxes'])) : null;
        $prepaid_plans->status = $active;
        $prepaid_plans->recommended = $recommended;
        $prepaid_plans->date = date('Y-m-d H:i:s');
        $prepaid_plans->save();

        $status = "success";
        $message = __("Saved Successfully");

    } else {
        $status = "error";
        $message = __("Error: Please try again.");
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function addStaticPage(){
    global $config;
    $error = array();

    if (empty($_POST['name'])) {
        $error[] = __('Name required');
    }
    if (empty($_POST['title'])) {
        $error[] = __('Title required.');
    }
    if (empty($_POST['content'])) {
        $error[] = __('Content required.');
    }
    if (empty($error)) {
        if (empty($_POST['slug']))
            $slug = create_slug($_POST['name']);
        else
            $slug = create_slug($_POST['slug']);

        $insert_page = ORM::for_table($config['db']['pre'].'pages')->create();
        $insert_page->translation_lang = 'en';
        $insert_page->name = validate_input($_POST['name']);
        $insert_page->title = validate_input($_POST['title']);
        $insert_page->content = validate_input($_POST['content'],true, true);
        $insert_page->slug = $slug;
        $insert_page->type = validate_input($_POST['type']);
        $insert_page->active = validate_input($_POST['active']);
        $insert_page->save();

        $id = $insert_page->id();

        $update_page = ORM::for_table($config['db']['pre'].'pages')->find_one($id);
        $update_page->set('translation_of', $id);
        $update_page->set('parent_id', $id);
        $update_page->save();

        $rows = ORM::for_table($config['db']['pre'].'languages')
            ->select_many('code','name')
            ->where('active', '1')
            ->where_not_equal('code', 'en')
            ->find_many();

        foreach ($rows as $fetch){
            $insert_page = ORM::for_table($config['db']['pre'].'pages')->create();
            $insert_page->translation_lang = $fetch['code'];
            $insert_page->translation_of = $id;
            $insert_page->parent_id = $id;
            $insert_page->name = validate_input($_POST['name']);
            $insert_page->title = validate_input($_POST['title']);
            $insert_page->content = validate_input($_POST['content'],true, true);
            $insert_page->slug = $slug;
            $insert_page->type = validate_input($_POST['type']);
            $insert_page->active = validate_input($_POST['active']);
            $insert_page->save();
        }
        $status = "success";
        $message = __('Saved Successfully');
    }else {
        $status = "error";
        $message = implode('<br>', $error);
    }
    $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    echo $json;
    die();
}

function editStaticPage(){
    global $config;
    $error = array();

    if (isset($_POST['id'])) {

        if (empty($_POST['name'])) {
            $error[] = __('Name required');
        }
        if (empty($_POST['title'])) {
            $error[] = __('Title required.');
        }
        if (empty($_POST['content'])) {
            $error[] = __('Content required.');
        }
        if (empty($error)) {
            if (empty($_POST['slug']))
                $slug = create_slug($_POST['name']);
            else
                $slug = create_slug($_POST['slug']);

            $update_page = ORM::for_table($config['db']['pre'].'pages')->find_one($_POST['id']);
            $update_page->set('name', validate_input($_POST['name']));
            $update_page->set('title', validate_input($_POST['title']));
            $update_page->set('content', validate_input($_POST['content'],true, true));
            $update_page->set('slug', $slug);
            $update_page->set('type', validate_input($_POST['type']));
            $update_page->set('active', validate_input($_POST['active']));
            $update_page->save();

            $status = "success";
            $message = __('Saved Successfully');

            echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
            die();
        }else {
            $status = "error";
            $message = __("Error: Please try again.");
        }
    } else {
        $status = "error";
        $message = implode('<br>', $error);
    }
    $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    echo $json;
    die();
}

function addFAQentry(){
    global $config;
    $error = array();

    if (empty($_POST['title'])) {
        $error[] = __('Title required.');
    }
    if (empty($_POST['content'])) {
        $error[] = __('Content required.');
    }
    if (empty($error)) {

        $insert_faq = ORM::for_table($config['db']['pre'].'faq_entries')->create();
        $insert_faq->translation_lang = 'en';
        $insert_faq->faq_title = validate_input($_POST['title']);
        $insert_faq->faq_content = validate_input($_POST['content'],true);
        $insert_faq->active = $_POST['active'];
        $insert_faq->save();

        $id = $insert_faq->id();

        $faqs = ORM::for_table($config['db']['pre'].'faq_entries')
            ->use_id_column('faq_id')
            ->find_one($id);
        $faqs->translation_of = $id;
        $faqs->parent_id = $id;
        $faqs->save();

        $rows = ORM::for_table($config['db']['pre'].'languages')
            ->select_many('code','name')
            ->where_not_equal('code', 'en')
            ->find_many();

        foreach ($rows as $fetch){
            $insert_faq = ORM::for_table($config['db']['pre'].'faq_entries')->create();
            $insert_faq->translation_lang = $fetch['code'];
            $insert_faq->translation_of = $id;
            $insert_faq->parent_id = $id;
            $insert_faq->faq_title = validate_input($_POST['title']);
            $insert_faq->faq_content = validate_input($_POST['content'],true);
            $insert_faq->active = $_POST['active'];
            $insert_faq->save();
        }
        $status = "success";
        $message = __("Saved Successfully");

    }else {
        $status = "error";
        $message = implode('<br>', $error);
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function editFAQentry(){
    global $config;
    $error = array();

    if (isset($_POST['id'])) {

        if (empty($_POST['title'])) {
            $error[] = __('Title required.');
        }
        if (empty($_POST['content'])) {
            $error[] = __('Content required.');
        }
        if (empty($error)) {

            $faqs = ORM::for_table($config['db']['pre'].'faq_entries')
                ->use_id_column('faq_id')
                ->find_one(validate_input($_POST['id']));
            $faqs->faq_title = validate_input($_POST['title']);
            $faqs->faq_content = validate_input($_POST['content'],true);
            $faqs->active = validate_input($_POST['active']);
            $faqs->save();

            if ($faqs->id()) {
                $status = "success";
                $message = __("Saved Successfully");
            } else{
                $status = "error";
                $message = __("Error: Please try again.");
            }
        }else {
            $status = "error";
            $message = __("Error: Please try again.");
        }
    } else {
        $status = "error";
        $message = implode('<br>', $error);
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function transactionEdit(){
    global $config;
    if (isset($_POST['id'])) {

        if (isset($_POST['status'])) {

            if($_POST['status'] == "success"){
                $transaction_id = $_POST['id'];
                transaction_success($transaction_id);
            }else{
                $transaction = ORM::for_table($config['db']['pre'].'transaction')->find_one($_POST['id']);
                $transaction->status = $_POST['status'];
                $transaction->save();
            }
            $status = "success";
            $message = __("Saved Successfully");


        }else {
            $status = "error";
            $message = __("Error: Please try again.");
        }
    } else {
        $status = "error";
        $message = __("Error: Please try again.");
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function paymentEdit()
{
    global $config;

    if (isset($_POST['id'])) {
        $payment = ORM::for_table($config['db']['pre'].'payments')
            ->use_id_column('payment_id')
            ->find_one($_POST['id']);
        $payment->set('payment_title', validate_input($_POST['title']));
        $payment->set('payment_install', validate_input($_POST['install']));
        $payment->save();

        if(isset($_POST['paypal_sandbox_mode'])){
            update_option("paypal_sandbox_mode",isset($_POST['paypal_sandbox_mode'])? $_POST['paypal_sandbox_mode'] : "");
            update_option("paypal_payment_mode",isset($_POST['paypal_payment_mode'])? $_POST['paypal_payment_mode'] : "");
            update_option("paypal_api_client_id",isset($_POST['paypal_api_client_id'])? $_POST['paypal_api_client_id'] : "");
            update_option("paypal_api_secret",isset($_POST['paypal_api_secret'])? $_POST['paypal_api_secret'] : "");
        }

        if(isset($_POST['stripe_secret_key'])){
            update_option("stripe_payment_mode",$_POST['stripe_payment_mode']);
            update_option("stripe_publishable_key",$_POST['stripe_publishable_key']);
            update_option("stripe_secret_key",$_POST['stripe_secret_key']);
            update_option("stripe_webhook_secret", $_POST['stripe_webhook_secret']);

        }

        if(isset($_POST['paystack_public_key'])){
            update_option("paystack_public_key",$_POST['paystack_public_key']);
            update_option("paystack_secret_key",$_POST['paystack_secret_key']);
        }

        if(isset($_POST['payumoney_merchant_key'])){
            update_option("payumoney_sandbox_mode",$_POST['payumoney_sandbox_mode']);
            update_option("payumoney_merchant_key",$_POST['payumoney_merchant_key']);
            update_option("payumoney_merchant_salt",$_POST['payumoney_merchant_salt']);
            update_option("payumoney_merchant_id",$_POST['payumoney_merchant_id']);
        }

        if(isset($_POST['checkout_account_number'])){
            update_option("2checkout_sandbox_mode",$_POST['2checkout_sandbox_mode']);
            update_option("checkout_account_number",$_POST['checkout_account_number']);
            update_option("checkout_public_key",$_POST['checkout_public_key']);
            update_option("checkout_private_key",$_POST['checkout_private_key']);
        }

        if(isset($_POST['company_bank_info'])){
            update_option("company_bank_info",$_POST['company_bank_info']);
            update_option("wire_transfer_payment_proof",$_POST['wire_transfer_payment_proof']);
        }

        if(isset($_POST['company_cheque_info'])){
            update_option("company_cheque_info",$_POST['company_cheque_info']);
            update_option("cheque_payable_to",$_POST['cheque_payable_to']);
        }

        if(isset($_POST['skrill_merchant_id'])){
            update_option("skrill_merchant_id",$_POST['skrill_merchant_id']);
        }

        if(isset($_POST['nochex_merchant_id'])){
            update_option("nochex_merchant_id",$_POST['nochex_merchant_id']);
        }

        if(isset($_POST['CCAVENUE_MERCHANT_KEY'])){
            update_option("CCAVENUE_MERCHANT_KEY",$_POST['CCAVENUE_MERCHANT_KEY']);
            update_option("CCAVENUE_ACCESS_CODE",$_POST['CCAVENUE_ACCESS_CODE']);
            update_option("CCAVENUE_WORKING_KEY",$_POST['CCAVENUE_WORKING_KEY']);
        }

        if(isset($_POST['PAYTM_ENVIRONMENT'])){
            update_option("PAYTM_ENVIRONMENT",$_POST['PAYTM_ENVIRONMENT']);
            update_option("PAYTM_MERCHANT_KEY",$_POST['PAYTM_MERCHANT_KEY']);
            update_option("PAYTM_MERCHANT_MID",$_POST['PAYTM_MERCHANT_MID']);
            update_option("PAYTM_MERCHANT_WEBSITE",$_POST['PAYTM_MERCHANT_WEBSITE']);
        }

        if(isset($_POST['mollie_api_key'])){
            update_option("mollie_api_key",$_POST['mollie_api_key']);
        }

        if(isset($_POST['iyzico_api_key'])){
            update_option("iyzico_sandbox_mode",$_POST['iyzico_sandbox_mode']);
            update_option("iyzico_api_key",$_POST['iyzico_api_key']);
            update_option("iyzico_secret_key",$_POST['iyzico_secret_key']);
        }

        if(isset($_POST['midtrans_client_key'])){
            update_option("midtrans_sandbox_mode",$_POST['midtrans_sandbox_mode']);
            update_option("midtrans_client_key",$_POST['midtrans_client_key']);
            update_option("midtrans_server_key",$_POST['midtrans_server_key']);
        }

        if(isset($_POST['paytabs_profile_id'])){
            update_option("paytabs_profile_id",$_POST['paytabs_profile_id']);
            update_option("paytabs_secret_key",$_POST['paytabs_secret_key']);
        }

        if(isset($_POST['telr_store_id'])){
            update_option("telr_sandbox_mode",$_POST['telr_sandbox_mode']);
            update_option("telr_store_id",$_POST['telr_store_id']);
            update_option("telr_authkey",$_POST['telr_authkey']);
        }

        if(isset($_POST['razorpay_api_key'])){
            update_option("razorpay_api_key",$_POST['razorpay_api_key']);
            update_option("razorpay_secret_key",$_POST['razorpay_secret_key']);
        }

        if(isset($_POST['flutterwave_api_key'])){
            update_option("flutterwave_api_key",$_POST['flutterwave_api_key']);
            update_option("flutterwave_secret_key",$_POST['flutterwave_secret_key']);
        }

        if(isset($_POST['yoomoney_shop_id'])){
            update_option("yoomoney_shop_id",$_POST['yoomoney_shop_id']);
            update_option("yoomoney_secret_key",$_POST['yoomoney_secret_key']);
        }

        if(isset($_POST['coinbase_api_key'])){
            update_option("coinbase_api_key",$_POST['coinbase_api_key']);
            update_option("coinbase_webhook_secret",$_POST['coinbase_webhook_secret']);
        }

        if(isset($_POST['mercadopago_access_token'])){
            update_option("mercadopago_access_token",$_POST['mercadopago_access_token']);
        }
        if(isset($_POST['paddle_sandbox_mode'])){
            update_option("paddle_sandbox_mode",$_POST['paddle_sandbox_mode']);
            update_option("paddle_vendor_id",$_POST['paddle_vendor_id']);
            update_option("paddle_api_key",$_POST['paddle_api_key']);
            update_option("paddle_public_key",$_POST['paddle_public_key']);
        }
        if(isset($_POST['sslcommerz_sandbox_mode'])){
            update_option("sslcommerz_sandbox_mode",$_POST['sslcommerz_sandbox_mode']);
            update_option("sslcommerz_store_id",$_POST['sslcommerz_store_id']);
            update_option("sslcommerz_store_pass",$_POST['sslcommerz_store_pass']);
        }
        $status = "success";
        $message = __("Saved Successfully");

    } else {
        $status = "error";
        $message = __("Error: Please try again.");
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function addBlogCat(){
    global $config;
    $_POST = validate_input($_POST);
    $name = $_POST['title'];
    $slug = $_POST['slug'];
    if (trim($name) != '' && is_string($name)) {
        if (empty($slug))
            $slug = create_blog_cat_slug($name);
        else
            $slug = create_blog_cat_slug($slug);

        if(check_allow()){
            $blog_cat = ORM::for_table($config['db']['pre'].'blog_categories')->create();
            $blog_cat->title = $name;
            $blog_cat->slug = $slug;
            $blog_cat->active = $_POST['active'];
            $blog_cat->save();

            $id = $blog_cat->id();
            if($id){
                $blog_pos = ORM::for_table($config['db']['pre'].'blog_categories')->find_one($id);
                $blog_pos->position = validate_input($id);
                $blog_pos->save();
            }
        }
        $status = "success";
        $message = __("Saved Successfully");
    } else{
        $status = "error";
        $message = __("Error: Please try again.");
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function editBlogCat(){
    global $config;
    $_POST = validate_input($_POST);
    $name = $_POST['title'];
    $slug = $_POST['slug'];
    $id = $_POST['id'];

    if (trim($name) != '' && is_string($name) && trim($id) != '') {
        if(check_allow()) {
            $blog_cat = ORM::for_table($config['db']['pre'] . 'blog_categories')->find_one($id);

            if($blog_cat['slug'] != $slug){
                if($slug == "")
                    $slug = create_blog_cat_slug($name);
                else
                    $slug = create_blog_cat_slug($slug);
            }

            $blog_cat->title = $name;
            $blog_cat->slug = $slug;
            $blog_cat->active = $_POST['active'];
            $blog_cat->save();
        }
        $status = "success";
        $message = __("Saved Successfully");
    } else{
        $status = "error";
        $message = __("Error: Please try again.");
    }

    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function saveEmailTemplate(){
    update_option("email_message_signup_details",$_POST['email_message_signup_details']);
    update_option("email_message_signup_confirm",$_POST['email_message_signup_confirm']);
    update_option("email_message_forgot_pass",$_POST['email_message_forgot_pass']);
    update_option("email_message_contact",$_POST['email_message_contact']);
    update_option("email_message_feedback",$_POST['email_message_feedback']);
    update_option("emailHTML_withdraw_accepted",$_POST['emailHTML_withdraw_accepted']);
    update_option("emailHTML_withdraw_rejected",$_POST['emailHTML_withdraw_rejected']);
    update_option("emailHTML_withdraw_request",$_POST['emailHTML_withdraw_request']);

    update_option("email_sub_signup_details",stripslashes(validate_input($_POST['email_sub_signup_details'])));
    update_option("email_sub_signup_confirm",stripslashes(validate_input($_POST['email_sub_signup_confirm'])));
    update_option("email_sub_forgot_pass",stripslashes(validate_input($_POST['email_sub_forgot_pass'])));
    update_option("email_sub_contact",stripslashes(validate_input($_POST['email_sub_contact'])));
    update_option("email_sub_feedback",stripslashes(validate_input($_POST['email_sub_feedback'])));
    update_option("email_sub_withdraw_accepted",stripslashes(validate_input($_POST['email_sub_withdraw_accepted'])));
    update_option("email_sub_withdraw_rejected",stripslashes(validate_input($_POST['email_sub_withdraw_rejected'])));
    update_option("email_sub_withdraw_request",stripslashes(validate_input($_POST['email_sub_withdraw_request'])));

    $status = "success";
    $message = __("Saved Successfully");

    echo '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

function testEmailTemplate(){

    global $config,$lang,$link;
    $_POST = validate_input($_POST);
    $errors = null;

    $test_to_email = $_POST['test_to_email'];
    $test_to_name = $_POST['test_to_name'];

    foreach ($_POST['templates'] as $template){
        switch ($template){
            case 'signup-details':
                $html = $config['email_sub_signup_details'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{USER_FULLNAME}', $test_to_name, $html);
                $email_subject = $html;

                $html = $config['email_message_signup_details'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{USERNAME}', "demo", $html);
                $html = str_replace ('{PASSWORD}', "demo", $html);
                $html = str_replace ('{USER_ID}', "1", $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{USER_FULLNAME}', $test_to_name, $html);
                $email_body = $html;

                $errors = email($test_to_email,$test_to_name,$email_subject,$email_body);
                break;

            case 'create-account':
                $html = $config['email_sub_signup_confirm'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{USER_FULLNAME}', $test_to_name, $html);
                $email_subject = $html;

                $confirmation_link = $link['SIGNUP']."?confirm=123456&user=1";

                $html = $config['email_message_signup_confirm'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{CONFIRMATION_LINK}', $confirmation_link, $html);
                $html = str_replace ('{USERNAME}', "demo", $html);
                $html = str_replace ('{USER_ID}', "1", $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{USER_FULLNAME}', $test_to_name, $html);
                $email_body = $html;

                $errors = email($test_to_email,$test_to_name,$email_subject,$email_body);
                break;

            case 'forgot-pass':
                $html = $config['email_sub_forgot_pass'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{USER_FULLNAME}', $test_to_name, $html);
                $email_subject = $html;

                $forget_password_link = $config['site_url']."login?forgot=sd1213f1x1&r=21d1d2d12&e=12&t=1213231";

                $html = $config['email_message_forgot_pass'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('FORGET_PASSWORD_LINK', $forget_password_link, $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{USER_FULLNAME}', $test_to_name, $html);
                $email_body = $html;

                $errors = email($test_to_email,$test_to_name,$email_subject,$email_body);
                break;

            case 'contact_us':
                $html = $config['email_sub_contact'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{CONTACT_SUBJECT}', "Contact Email", $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{NAME}', $test_to_name, $html);
                $email_subject = $html;


                $html = $config['email_message_contact'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{NAME}', $test_to_name, $html);
                $html = str_replace ('{CONTACT_SUBJECT}', "Contact Email", $html);
                $html = str_replace ('{MESSAGE}', "Test Message", $html);
                $email_body = $html;

                $errors = email($test_to_email,$test_to_name,$email_subject,$email_body);
                break;
            case 'feedback':
                $html = $config['email_sub_feedback'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{FEEDBACK_SUBJECT}', "Feedback Email", $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{NAME}', $test_to_name, $html);
                $email_subject = $html;


                $html = $config['email_message_feedback'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{NAME}', $test_to_name, $html);
                $html = str_replace ('{PHONE}', "1234567890", $html);
                $html = str_replace ('{FEEDBACK_SUBJECT}', "Feedback Email", $html);
                $html = str_replace ('{MESSAGE}', "Test Message", $html);
                $email_body = $html;

                $errors = email($test_to_email,$test_to_name,$email_subject,$email_body);
                break;

            case 'report':
                $html = $config['email_sub_report'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{NAME}', $test_to_name, $html);
                $html = str_replace ('{USERNAME}', $test_to_name, $html);
                $html = str_replace ('{VIOLATION}', __("Advertising another website"), $html);
                $email_subject = $html;


                $html = $config['email_message_report'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{EMAIL}', $test_to_email, $html);
                $html = str_replace ('{NAME}', $test_to_name, $html);
                $html = str_replace ('{USERNAME}', $test_to_name, $html);
                $html = str_replace ('{USERNAME2}', "Violator Username", $html);
                $html = str_replace ('{VIOLATION}', __("Advertising another website"), $html);
                $html = str_replace ('{URL}', "https://example.com", $html);
                $html = str_replace ('{DETAILS}', "Violator Message details here", $html);
                $email_body = $html;

                $errors = email($test_to_email,$test_to_name,$email_subject,$email_body);
                break;
        }
    }

    $result = [];
    $result['status'] = "success";
    $result['message'] = __("Email Sent Successfully");

    if(is_array($errors) && !empty($errors)){
        $result['status'] = "error";
        $result['message'] = implode('<br />', $errors);
    }

    echo json_encode($result);
    die();
}

function editWithdrawal() {
    global $config;

    if (isset($_POST['id'])) {

        if (isset($_POST['status'])) {
            $withdraw = ORM::for_table($config['db']['pre'].'withdrawal')->find_one(validate_input($_POST['id']));
            $user_id = $withdraw['user_id'];
            $amount = $withdraw['amount'];

            $userdata = ORM::for_table($config['db']['pre'] . 'user')->find_one($user_id);

            if($_POST['status'] == "reject"){
                $withdraw->reject_reason = validate_input($_POST['reject_reason']);

                $total = $userdata['balance'] + $amount;
                $userdata->balance = number_format($total, 2, '.', '');
                $userdata->save();


                /*User : Withdraw request rejected*/
                $html = $config['email_sub_withdraw_rejected'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{USER_ID}', $user_id, $html);
                $html = str_replace ('{USERNAME}', $userdata['username'], $html);
                $html = str_replace ('{EMAIL}', $userdata['email'], $html);
                $html = str_replace ('{USER_FULLNAME}', $userdata['name'], $html);
                $html = str_replace ('{AMOUNT}', $amount, $html);
                $email_subject = $html;

                $html = $config['emailHTML_withdraw_rejected'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{USER_ID}', $user_id, $html);
                $html = str_replace ('{USERNAME}', $userdata['username'], $html);
                $html = str_replace ('{EMAIL}', $userdata['email'], $html);
                $html = str_replace ('{USER_FULLNAME}', $userdata['name'], $html);
                $html = str_replace ('{AMOUNT}', $amount, $html);
                $html = str_replace ('{REJECT_REASON}', nl2br(validate_input($_POST['reject_reason'])), $html);
                $email_body = $html;

                email($userdata['email'],$userdata['name'],$email_subject,$email_body);
            }

            if($_POST['status'] == "success"){

                /*User : Withdraw request accepted*/
                $html = $config['email_sub_withdraw_accepted'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{USER_ID}', $user_id, $html);
                $html = str_replace ('{USERNAME}', $userdata['username'], $html);
                $html = str_replace ('{EMAIL}', $userdata['email'], $html);
                $html = str_replace ('{USER_FULLNAME}', $userdata['name'], $html);
                $html = str_replace ('{AMOUNT}', $amount, $html);
                $email_subject = $html;

                $html = $config['emailHTML_withdraw_accepted'];
                $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
                $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
                $html = str_replace ('{USER_ID}', $user_id, $html);
                $html = str_replace ('{USERNAME}', $userdata['username'], $html);
                $html = str_replace ('{EMAIL}', $userdata['email'], $html);
                $html = str_replace ('{USER_FULLNAME}', $userdata['name'], $html);
                $html = str_replace ('{AMOUNT}', $amount, $html);
                $email_body = $html;

                email($userdata['email'],$userdata['name'],$email_subject,$email_body);
            }

            $withdraw->status = validate_input($_POST['status']);
            $withdraw->save();

            $result['status'] = 'success';
            $result['message'] = __('Successfully Saved.');


        }else {
            $result['status'] = 'error';
            $result['message'] = __('Unexpected error, please try again.');
        }
    } else {
        $result['status'] = 'error';
        $result['message'] = __('Unexpected error, please try again.');
    }

    die(json_encode($result));
}

function editAdvertise() {
    global $config;

    if(!empty($_POST['id'])) {

        $adsense = ORM::for_table($config['db']['pre'] . 'adsense')->find_one(validate_input($_POST['id']));
        $adsense->provider_name = validate_input($_POST['provider_name']);
        $adsense->large_track_code = $_POST['large_track_code'];
        $adsense->tablet_track_code = $_POST['tablet_track_code'];
        $adsense->phone_track_code = $_POST['phone_track_code'];
        $adsense->status = validate_input($_POST['status']);
        $adsense->save();

        $result['status'] = 'success';
        $result['id'] = $adsense->id();
        $result['message'] = __('Successfully Saved.');
        die(json_encode($result));
    }
    $result['status'] = 'error';
    $result['message'] = __('Unexpected error, please try again.');
    die(json_encode($result));
}

function editAIDocument() {
    global $config;

    if(!empty($_POST['id'])) {
        $content = validate_input($_POST['content'], true);
        $_POST = validate_input($_POST);
        $_POST['content'] = $content;

        if(empty($_POST['title'])){
            $result['status'] = 'error';
            $result['message'] = __('Title is required.');
            die(json_encode($result));
        }

        $content = ORM::for_table($config['db']['pre'] . 'ai_documents')->find_one($_POST['id']);
        $content->title = $_POST['title'];
        $content->content = $_POST['content'];
        $content->save();

        $result['status'] = 'success';
        $result['id'] = $content->id();
        $result['message'] = __('Successfully Saved.');
        die(json_encode($result));
    }
    $result['status'] = 'error';
    $result['message'] = __('Unexpected error, please try again.');
    die(json_encode($result));
}

function editAITemplate() {
    global $config;

    if(!empty($_POST['id'])) {
        $_POST = validate_input($_POST);

        if(empty($_POST['title'])){
            $result['status'] = 'error';
            $result['message'] = __('Title is required.');
            die(json_encode($result));
        }
        if(empty($_POST['category'])){
            $result['status'] = 'error';
            $result['message'] = __('Category is required.');
            die(json_encode($result));
        }
        if(empty($_POST['description'])){
            $result['status'] = 'error';
            $result['message'] = __('Description is required.');
            die(json_encode($result));
        }

        $_POST['icon'] = empty($_POST['icon']) ? 'fa fa-check-square' : $_POST['icon'];

        $settings = [
            'language' => $_POST['language'],
            'quality_type' => $_POST['quality_type'],
            'tone_of_voice' => $_POST['tone_of_voice'],
        ];

        $template = ORM::for_table($config['db']['pre'] . 'ai_templates')->find_one($_POST['id']);
        $template->title = $_POST['title'];
        $template->icon = $_POST['icon'];
        $template->category_id = $_POST['category'];
        $template->description = $_POST['description'];
        $template->translations = json_encode($_POST['translations'], JSON_UNESCAPED_UNICODE);
        $template->settings = json_encode($settings);
        $template->active = $_POST['active'];
        $template->save();

        $result['status'] = 'success';
        $result['id'] = $template->id();
        $result['message'] = __('Successfully Saved.');
        die(json_encode($result));
    }
    $result['status'] = 'error';
    $result['message'] = __('Unexpected error, please try again.');
    die(json_encode($result));
}

function editAICustomTemplate() {
    global $config;

    $_POST = validate_input($_POST);

    if(empty($_POST['title'])){
        $result['status'] = 'error';
        $result['message'] = __('Title is required.');
        die(json_encode($result));
    }
    if(empty($_POST['category'])){
        $result['status'] = 'error';
        $result['message'] = __('Category is required.');
        die(json_encode($result));
    }
    if(empty($_POST['description'])){
        $result['status'] = 'error';
        $result['message'] = __('Description is required.');
        die(json_encode($result));
    }
    if(empty($_POST['prompt'])){
        $result['status'] = 'error';
        $result['message'] = __('Prompt is required.');
        die(json_encode($result));
    }

    // check slug
    if(!empty($_POST['slug'])){
        if (!preg_match('/^[a-z0-9]+(-?[a-z0-9]+)*$/i', $_POST['slug'])) {
            $result['status'] = 'error';
            $result['message'] = __('Slug is invalid.');
            die(json_encode($result));
        }

        if(ORM::for_table($config['db']['pre'].'ai_custom_templates')
            ->where('slug', $_POST['slug'])
            ->where_not_equal('id', !empty($_POST['id']) ? $_POST['id'] : 0)
            ->count()){

            $result['status'] = 'error';
            $result['message'] = __('Slug is already available.');
            die(json_encode($result));
        }
    }
    if(empty($_POST['slug'])){
        $_POST['slug'] = create_custom_template_slug($_POST['title']);
    }

    $_POST['icon'] = empty($_POST['icon']) ? 'fa fa-check-square' : $_POST['icon'];

    $parameter = array();
    if(!empty($_POST['parameter_title'])){
        foreach ($_POST['parameter_title'] as $key => $title) {
            $parameter[] = array(
                'title' => validate_input($title),
                'type' => $_POST['parameter_type'][$key],
                'placeholder' => escape($_POST['parameter_placeholder'][$key]),
                'options' => $_POST['parameter_type'][$key] == 'select'
                    ? escape($_POST['parameter_options'][$key])
                    : '',
                'translations' => $_POST['parameter_translations'][$key],
                'required' => isset($_POST['parameter_required'][$key]) ? 1 : 0
            );
        }
    }

    $settings = [
        'language' => $_POST['language'],
        'quality_type' => $_POST['quality_type'],
        'tone_of_voice' => $_POST['tone_of_voice'],
    ];

    if(!empty($_POST['id'])) {
        $template = ORM::for_table($config['db']['pre'] . 'ai_custom_templates')->find_one($_POST['id']);
    }else {
        $template = ORM::for_table($config['db']['pre'] . 'ai_custom_templates')->create();
    }
    $template->slug = $_POST['slug'];
    $template->title = $_POST['title'];
    $template->icon = $_POST['icon'];
    $template->category_id = $_POST['category'];
    $template->description = $_POST['description'];
    $template->prompt = $_POST['prompt'];
    $template->parameters = json_encode($parameter, JSON_UNESCAPED_UNICODE);
    $template->translations = json_encode($_POST['translations'], JSON_UNESCAPED_UNICODE);
    $template->settings = json_encode($settings);
    $template->active = $_POST['active'];
    $template->save();

    $result['status'] = 'success';
    $result['id'] = $template->id();
    $result['message'] = __('Successfully Saved.');
    die(json_encode($result));
}

function editAITplCategory() {
    global $config;

    $_POST = validate_input($_POST);

    if(empty($_POST['title'])){
        $result['status'] = 'error';
        $result['message'] = __('Title is required.');
        die(json_encode($result));
    }

    if(!empty($_POST['id'])) {
        $template = ORM::for_table($config['db']['pre'] . 'ai_template_categories')->find_one($_POST['id']);
    }else {
        $template = ORM::for_table($config['db']['pre'] . 'ai_template_categories')->create();
    }
    $template->title = $_POST['title'];
    $template->translations = json_encode($_POST['translations'], JSON_UNESCAPED_UNICODE);
    $template->active = $_POST['active'];
    $template->save();

    $result['status'] = 'success';
    $result['id'] = $template->id();
    $result['message'] = __('Successfully Saved.');
    die(json_encode($result));
}

function editAPIKey() {
    global $config;

    $_POST = validate_input($_POST);

    if(empty($_POST['title'])){
        $result['status'] = 'error';
        $result['message'] = __('Title is required.');
        die(json_encode($result));
    }
    if(empty($_POST['api_key'])){
        $result['status'] = 'error';
        $result['message'] = __('API key is required.');
        die(json_encode($result));
    }

    if(!empty($_POST['id'])) {
        $api_key = ORM::for_table($config['db']['pre'] . 'api_keys')->find_one($_POST['id']);
    }else {
        $api_key = ORM::for_table($config['db']['pre'] . 'api_keys')->create();
    }
    $api_key->title = $_POST['title'];
    $api_key->api_key = $_POST['api_key'];
    $api_key->type = $_POST['type'];
    $api_key->active = $_POST['active'];
    $api_key->save();

    $result['status'] = 'success';
    $result['id'] = $api_key->id();
    $result['message'] = __('Successfully Saved.');
    die(json_encode($result));
}

function editAIChatBot() {
    global $config;

    $welcome_message = $_POST['welcome_message'];
    $_POST = validate_input($_POST);
    $_POST['welcome_message'] = validate_input($welcome_message, true);

    $image = null;

    if(empty($_POST['name'])){
        $result['status'] = 'error';
        $result['message'] = __('Name is required.');
        die(json_encode($result));
    }
    if(empty($_POST['prompt'])){
        $result['status'] = 'error';
        $result['message'] = __('Prompt is required.');
        die(json_encode($result));
    }

    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $target_dir = ROOTPATH . "/storage/chat-bots/";
        $result = quick_file_upload('image', $target_dir);
        if ($result['success']) {
            $image = $result['file_name'];
            resizeImage(300, $target_dir . $image, $target_dir . $image);
            if (isset($_POST['id'])) {
                // remove old image
                $info = ORM::for_table($config['db']['pre'] . 'ai_chat_bots')
                    ->select('image')
                    ->find_one($_POST['id']);

                if (!empty(trim((string)$info['image'])) && $info['image'] != "default_user.png") {
                    if (file_exists($target_dir . $info['image'])) {
                        unlink($target_dir . $info['image']);
                    }
                }
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = $result['error'];
            die(json_encode($result));
        }
    }

    if (!empty($_POST['id'])) {
        $bot = ORM::for_table($config['db']['pre'] . 'ai_chat_bots')->find_one($_POST['id']);
    } else {
        $bot = ORM::for_table($config['db']['pre'] . 'ai_chat_bots')->create();
    }
    $bot->name = $_POST['name'];
    $bot->welcome_message = $_POST['welcome_message'];
    $bot->role = $_POST['role'];
    $bot->prompt = $_POST['prompt'];
    $bot->training_data = $_POST['training_data'];
    $bot->category_id = $_POST['category'];
    $bot->translations = json_encode($_POST['translations'], JSON_UNESCAPED_UNICODE);
    $bot->active = $_POST['active'];

    if($image)
        $bot->image = $image;

    $bot->save();

    $result['status'] = 'success';
    $result['id'] = $bot->id();
    $result['message'] = __('Successfully Saved.');
    die(json_encode($result));
}

function editAIChatBotCategory() {
    global $config;

    $_POST = validate_input($_POST);

    if(empty($_POST['title'])){
        $result['status'] = 'error';
        $result['message'] = __('Title is required.');
        die(json_encode($result));
    }

    if(!empty($_POST['id'])) {
        $template = ORM::for_table($config['db']['pre'] . 'ai_chat_bots_categories')->find_one($_POST['id']);
    }else {
        $template = ORM::for_table($config['db']['pre'] . 'ai_chat_bots_categories')->create();
    }
    $template->title = $_POST['title'];
    $template->translations = json_encode($_POST['translations'], JSON_UNESCAPED_UNICODE);
    $template->active = $_POST['active'];
    $template->save();

    $result['status'] = 'success';
    $result['id'] = $template->id();
    $result['message'] = __('Successfully Saved.');
    die(json_encode($result));
}

function editAIChatPrompts() {
    global $config;

    $_POST = validate_input($_POST);

    if(empty($_POST['title'])){
        $result['status'] = 'error';
        $result['message'] = __('Title is required.');
        die(json_encode($result));
    }
    if(empty($_POST['prompt'])){
        $result['status'] = 'error';
        $result['message'] = __('Prompt is required.');
        die(json_encode($result));
    }

    if (!empty($_POST['id'])) {
        $prompt = ORM::for_table($config['db']['pre'] . 'ai_chat_prompts')->find_one($_POST['id']);
    } else {
        $prompt = ORM::for_table($config['db']['pre'] . 'ai_chat_prompts')->create();
    }
    $prompt->title = $_POST['title'];
    $prompt->chat_bots = !empty($_POST['chat_bots'])? validate_input(implode(',',$_POST['chat_bots'])) : null;
    $prompt->description = $_POST['description'];
    $prompt->prompt = $_POST['prompt'];
    $prompt->translations = json_encode($_POST['translations'], JSON_UNESCAPED_UNICODE);
    $prompt->active = $_POST['active'];

    $prompt->save();

    $result['status'] = 'success';
    $result['id'] = $prompt->id();
    $result['message'] = __('Successfully Saved.');
    die(json_encode($result));
}

function SaveSettings(){

    global $config,$lang,$link;
    $status = "";
    if (isset($_POST['logo_watermark'])) {
        $valid_formats = array("jpg","jpeg","png"); // Valid image formats
        if (isset($_FILES['banner']) && $_FILES['banner']['tmp_name'] != "") {
            $filename = stripslashes($_FILES['banner']['name']);
            $ext = getExtension($filename);
            $ext = strtolower($ext);
            //File extension check
            if (in_array($ext, $valid_formats)) {
                $uploaddir = "../storage/banner/"; //Image upload directory
                $bannername = stripslashes($_FILES['banner']['name']);
                $size = filesize($_FILES['banner']['tmp_name']);
                //Convert extension into a lower case format

                $ext = getExtension($bannername);
                $ext = strtolower($ext);
                $banner_name = "bg" . '.' . $ext;
                $newBgname = $uploaddir . $config['home_banner'];
                //Moving file to uploads folder
                if(file_exists($newBgname)){
                    unlink($newBgname);
                }
                $result = quick_file_upload('banner', $uploaddir);
                //Moving file to uploads folder
                if ($result['success']) {
                    update_option("home_banner",$result['file_name']);
                    $status = "success";
                    $message = __("Banner updated Successfully");

                } else {
                    $status = "error";
                    $message = __("Error: Please try again.");
                }
            }
            else {
                $status = "error";
                $message = __("Only allowed jpg, jpeg png");
            }

        }

        if (isset($_FILES['favicon']) && $_FILES['favicon']['tmp_name'] != "") {
            $filename = stripslashes($_FILES['favicon']['name']);
            $ext = getExtension($filename);
            $ext = strtolower($ext);
            //File extension check
            if (in_array($ext, $valid_formats)) {
                $uploaddir = "../storage/logo/"; //Image upload directory
                $filename = stripslashes($_FILES['favicon']['name']);

                $ext = getExtension($filename);
                $ext = strtolower($ext);
                $image_name = "favicon" . '.' . $ext;
                $newLogo = $uploaddir . $config['site_favicon'];
                if(file_exists($newLogo) && !is_dir($newLogo)){
                    unlink($newLogo);
                }
                $result = quick_file_upload('favicon', $uploaddir);
                //Moving file to uploads folder
                if ($result['success']) {
                    update_option("site_favicon",$result['file_name']);
                    $status = "success";
                    $message = __("Site Favicon icon updated Successfully");
                } else {
                    $status = "error";
                    $message = __("Error: Please try again.");
                }
            }
            else {
                $status = "error";
                $message = __("Only allowed jpg, jpeg png");
            }

        }

        if (isset($_FILES['file']) && $_FILES['file']['tmp_name'] != "") {
            $filename = stripslashes($_FILES['file']['name']);
            $ext = getExtension($filename);
            $ext = strtolower($ext);
            //File extension check
            if (in_array($ext, $valid_formats)) {
                $uploaddir = "../storage/logo/"; //Image upload directory
                $filename = stripslashes($_FILES['file']['name']);

                $ext = getExtension($filename);
                $ext = strtolower($ext);
                $image_name = $config['tpl_name']."_logo" . '.' . $ext;
                $newLogo = $uploaddir . $config['site_logo'];
                if(file_exists($newLogo) && !is_dir($newLogo)){
                    unlink($newLogo);
                }
                $result = quick_file_upload('file', $uploaddir);
                //Moving file to uploads folder
                if ($result['success']) {
                    update_option("site_logo",$result['file_name']);
                    $status = "success";
                    $message = __("Site Logo updated Successfully");
                } else {
                    $status = "error";
                    $message = __("Error: Please try again.");
                }
            }
            else {
                $status = "error";
                $message = __("Only allowed jpg, jpeg png");
            }

        }

        if (isset($_FILES['footer_logo']) && $_FILES['footer_logo']['tmp_name'] != "") {
            $filename = stripslashes($_FILES['footer_logo']['name']);
            $ext = getExtension($filename);
            $ext = strtolower($ext);
            //File extension check
            if (in_array($ext, $valid_formats)) {
                $uploaddir = "../storage/logo/"; //Image upload directory
                $filename = stripslashes($_FILES['footer_logo']['name']);

                $ext = getExtension($filename);
                $ext = strtolower($ext);
                $image_name = $config['tpl_name']."_footer_logo" . '.' . $ext;
                $newLogo = $uploaddir . $config['site_logo_footer'];
                if(file_exists($newLogo) && !is_dir($newLogo)){
                    unlink($newLogo);
                }
                $result = quick_file_upload('footer_logo', $uploaddir);
                //Moving file to uploads folder
                if ($result['success']) {

                    update_option("site_logo_footer",$result['file_name']);
                    $status = "success";
                    $message = __("Site Logo updated Successfully");
                } else {
                    $status = "error";
                    $message = __("Error: Please try again.");
                }
            }
            else {
                $status = "error";
                $message = __("Only allowed jpg, jpeg png");
            }

        }

        if (isset($_FILES['adminlogo']) && $_FILES['adminlogo']['tmp_name'] != "") {
            $filename = stripslashes($_FILES['adminlogo']['name']);
            $ext = getExtension($filename);
            $ext = strtolower($ext);
            //File extension check
            if (in_array($ext, $valid_formats)) {
                $uploaddir = "../storage/logo/"; //Image upload directory
                $filename = stripslashes($_FILES['adminlogo']['name']);
                $size = filesize($_FILES['adminlogo']['tmp_name']);
                //Convert extension into a lower case format

                $ext = getExtension($filename);
                $ext = strtolower($ext);
                $adminlogo_name = "adminlogo" . '.' . $ext;
                $adminlogo = $uploaddir . $config['site_admin_logo'];
                if(file_exists($adminlogo) && !is_dir($adminlogo)){
                    unlink($adminlogo);
                }
                $result = quick_file_upload('adminlogo', $uploaddir);
                //Moving file to uploads folder
                if ($result['success']) {
                    update_option("site_admin_logo",$result['file_name']);
                    $status = "success";
                    $message = __("Admin Logo updated Successfully");
                } else {
                    $status = "error";
                    $message = __("Error: Please try again.");
                }
            }
            else {
                $status = "error";
                $message = __("Only allowed jpg, jpeg png");
            }

        }

        if (isset($_FILES['metaimage']) && $_FILES['metaimage']['tmp_name'] != "") {
            $filename = stripslashes($_FILES['metaimage']['name']);
            $ext = getExtension($filename);
            $ext = strtolower($ext);
            //File extension check
            if (in_array($ext, $valid_formats)) {
                $uploaddir = "../storage/logo/"; //Image upload directory

                //Convert extension into a lower case format
                $adminlogo = $uploaddir . get_option('site_metaimage');
                if(file_exists($adminlogo) && !is_dir($adminlogo)){
                    unlink($adminlogo);
                }
                $result = quick_file_upload('metaimage', $uploaddir);
                //Moving file to uploads folder
                if ($result['success']) {
                    update_option("site_metaimage",$result['file_name']);
                    $status = "success";
                    $message = __("Updated Successfully");
                } else {
                    $status = "error";
                    $message = __("Error: Please try again.");
                }
            }
            else {
                $status = "error";
                $message = __("Only allowed jpg, jpeg png");
            }

        }

        if($status == ""){
            $status = "success";
            $message = __("Saved Successfully");
        }
    }

    if (isset($_POST['general_setting'])) {
        $_POST['site_url'] = rtrim($_POST['site_url'], '/').'/';
        update_option("site_url",$_POST['site_url']);
        update_option("site_title",$_POST['site_title']);
        update_option("disable_landing_page", $_POST['disable_landing_page']);
        update_option("enable_maintenance_mode", $_POST['enable_maintenance_mode']);
        update_option("enable_user_registration", $_POST['enable_user_registration']);
        update_option("enable_faqs", $_POST['enable_faqs']);
        update_option("non_active_msg",$_POST['non_active_msg']);
        update_option("non_active_allow",$_POST['non_active_allow']);
        update_option("transfer_filter",$_POST['transfer_filter']);
        update_option("default_user_plan",validate_input($_POST['default_user_plan']));
        update_option("hide_plan_disabled_features",validate_input($_POST['hide_plan_disabled_features']));
        update_option("cron_exec_time",validate_input($_POST['cron_exec_time']));
        update_option("userlangsel",$_POST['userlangsel']);
        update_option("termcondition_link",validate_input($_POST['termcondition_link']));
        update_option("privacy_link",validate_input($_POST['privacy_link']));
        update_option("cookie_link",validate_input($_POST['cookie_link']));
        update_option("after_login_link",validate_input($_POST['after_login_link']));
        update_option("cookie_consent",$_POST['cookie_consent']);
        update_option("quickad_debug",$_POST['quickad_debug']);
        update_option("developer_credit",$_POST['developer_credit']);
        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['quick_map'])) {
        update_option("map_type",validate_input($_POST['map_type']));
        update_option("openstreet_access_token",validate_input($_POST['openstreet_access_token']));
        update_option("gmap_api_key",validate_input($_POST['gmap_api_key']));
        update_option("map_color",validate_input($_POST['map_color']));
        update_option("home_map_latitude",validate_input($_POST['home_map_latitude']));
        update_option("home_map_longitude",validate_input($_POST['home_map_longitude']));
        update_option("contact_latitude",validate_input($_POST['contact_latitude']));
        update_option("contact_longitude",validate_input($_POST['contact_longitude']));
        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['international'])) {

        if(isset($_POST['currency']))
        {
            $info = ORM::for_table($config['db']['pre'].'currencies')->find_one($_POST['currency']);

            $currency_sign = $info['html_entity'];
            $currency_code = $info['code'];
            $currency_pos = $info['in_left'];
        }
        update_option("specific_country",$_POST['specific_country']);
        update_option("lang",$_POST['lang']);
        update_option("browser_lang",(int) $_POST['browser_lang']);
        update_option("timezone",$_POST['timezone']);
        update_option("currency_sign",$currency_sign);
        update_option("currency_code",$currency_code);
        update_option("currency_pos",$currency_pos);
        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['email_setting'])) {

        update_option("admin_email",$_POST['admin_email']);
        update_option("from_email",$_POST['from_email']);
        update_option("email_type",$_POST['email_type']);

        update_option("smtp_host",$_POST['smtp_host']);
        update_option("smtp_port",$_POST['smtp_port']);
        update_option("smtp_username",$_POST['smtp_username']);
        update_option("smtp_password",$_POST['smtp_password']);
        update_option("smtp_secure",$_POST['smtp_secure']);
        update_option("smtp_auth",$_POST['smtp_auth']);

        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['theme_setting'])) {
        update_option("show_membershipplan_home",validate_input($_POST['show_membershipplan_home']));
        update_option("show_partner_logo_home",validate_input($_POST['show_partner_logo_home']));
        update_option("show_newsletter_form_home",validate_input($_POST['show_newsletter_form_home']));
        update_option("theme_color",validate_input($_POST['theme_color']));
        update_option("meta_keywords",validate_input($_POST['meta_keywords']));
        update_option("meta_description",validate_input($_POST['meta_description']));
        update_option("contact_address",validate_input($_POST['contact_address']));
        update_option("contact_phone",validate_input($_POST['contact_phone']));
        update_option("contact_email",validate_input($_POST['contact_email']));
        update_option("footer_text",validate_input($_POST['footer_text']));
        update_option("android_app_link",validate_input($_POST['android_app_link']));
        update_option("ios_app_link",validate_input($_POST['ios_app_link']));
        update_option("copyright_text",validate_input($_POST['copyright_text']));
        update_option("facebook_link",validate_input($_POST['facebook_link']));
        update_option("twitter_link",validate_input($_POST['twitter_link']));
        update_option("instagram_link",validate_input($_POST['instagram_link']));
        update_option("linkedin_link",validate_input($_POST['linkedin_link']));
        update_option("pinterest_link",validate_input($_POST['pinterest_link']));
        update_option("youtube_link",validate_input($_POST['youtube_link']));
        update_option("external_code",$_POST['external_code']);
        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['billing_details'])) {
        update_option("enable_tax_billing", validate_input($_POST['enable_tax_billing']));
        update_option("invoice_nr_prefix", validate_input($_POST['invoice_nr_prefix']));
        update_option("invoice_admin_name", validate_input($_POST['invoice_admin_name']));
        update_option("invoice_admin_email", validate_input($_POST['invoice_admin_email']));
        update_option("invoice_admin_phone", validate_input($_POST['invoice_admin_phone']));
        update_option("invoice_admin_address", validate_input($_POST['invoice_admin_address']));
        update_option("invoice_admin_city", validate_input($_POST['invoice_admin_city']));
        update_option("invoice_admin_state", validate_input($_POST['invoice_admin_state']));
        update_option("invoice_admin_zipcode", validate_input($_POST['invoice_admin_zipcode']));
        update_option("invoice_admin_country", validate_input($_POST['invoice_admin_country']));
        update_option("invoice_admin_tax_type", validate_input($_POST['invoice_admin_tax_type']));
        update_option("invoice_admin_tax_id", validate_input($_POST['invoice_admin_tax_id']));

        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['ai_settings'])) {
        update_option("bad_words", validate_input($_POST['bad_words']));
        update_option("single_model_for_plans", (int) validate_input($_POST['single_model_for_plans']));
        update_option("open_ai_model", validate_input($_POST['open_ai_model']));

        update_option("open_ai_api_key", validate_input($_POST['open_ai_api_key']));
        update_option("enable_ai_templates", validate_input($_POST['enable_ai_templates']));
        update_option("ai_languages", validate_input($_POST['ai_languages']));
        update_option("ai_default_lang", validate_input($_POST['ai_default_lang']));
        update_option("ai_default_quality_type", validate_input($_POST['ai_default_quality_type']));
        update_option("ai_default_tone_voice", validate_input($_POST['ai_default_tone_voice']));
        update_option("ai_default_max_langth", validate_input((int)$_POST['ai_default_max_langth']));

        update_option("enable_ai_images", validate_input($_POST['enable_ai_images']));
        update_option("ai_image_api", validate_input($_POST['ai_image_api']));
        update_option("ai_image_api_key", validate_input($_POST['ai_image_api_key']));
        update_option("show_ai_images_home", validate_input($_POST['show_ai_images_home']));
        update_option("ai_images_home_limit", (int)validate_input($_POST['ai_images_home_limit']));

        update_option("enable_speech_to_text", validate_input($_POST['enable_speech_to_text']));

        update_option("enable_ai_code", validate_input($_POST['enable_ai_code']));
        update_option("ai_code_max_token", validate_input($_POST['ai_code_max_token']));

        update_option("enable_ai_chat", validate_input($_POST['enable_ai_chat']));
        update_option("open_ai_chat_model", validate_input($_POST['open_ai_chat_model']));
        update_option("ai_chat_max_token", validate_input($_POST['ai_chat_max_token']));
        update_option("enable_default_chat_bot", validate_input($_POST['enable_default_chat_bot']));
        update_option("ai_chat_bot_name", validate_input($_POST['ai_chat_bot_name']));
        update_option("enable_ai_chat_mic", validate_input($_POST['enable_ai_chat_mic']));
        update_option("enable_ai_chat_prompts", validate_input($_POST['enable_ai_chat_prompts']));
        update_option("enable_chat_typing_effect", validate_input($_POST['enable_chat_typing_effect']));
        update_option("enable_ai_chat_enter_send", validate_input($_POST['enable_ai_chat_enter_send']));

        update_option("enable_text_to_speech", validate_input($_POST['enable_text_to_speech']));
        update_option("enable_tts_translation", validate_input($_POST['enable_tts_translation']));
        update_option("ai_tts_language", validate_input($_POST['ai_tts_language']));
        update_option("ai_tts_voice", validate_input($_POST['ai_tts_voice']));

        update_option("enable_aws_tts", validate_input($_POST['enable_aws_tts']));
        update_option("ai_tts_aws_access_key", validate_input($_POST['ai_tts_aws_access_key']));
        update_option("ai_tts_aws_secret_key", validate_input($_POST['ai_tts_aws_secret_key']));
        update_option("ai_tts_aws_region", validate_input($_POST['ai_tts_aws_region']));

        update_option("enable_google_tts", validate_input($_POST['enable_google_tts']));
        update_option("ai_tts_google_json_path", validate_input($_POST['ai_tts_google_json_path']));

        update_option("ai_proxies", validate_input($_POST['ai_proxies']));

        $valid_formats = array("jpg", "jpeg", "png"); // Valid image formats
        if (isset($_FILES['chat_bot_avatar']) && $_FILES['chat_bot_avatar']['tmp_name'] != "") {
            $filename = stripslashes($_FILES['chat_bot_avatar']['name']);
            $ext = getExtension($filename);
            $ext = strtolower($ext);
            //File extension check
            if (in_array($ext, $valid_formats)) {
                $uploaddir = "../storage/profile/"; //Image upload directory
                $filename = stripslashes($_FILES['chat_bot_avatar']['name']);

                $ext = getExtension($filename);
                $ext = strtolower($ext);
                $newLogo = $uploaddir . $config['chat_bot_avatar'];
                if (file_exists($newLogo) && !is_dir($newLogo)) {
                    unlink($newLogo);
                }
                $result = quick_file_upload('chat_bot_avatar', $uploaddir);
                //Moving file to uploads folder
                if ($result['success']) {
                    update_option("chat_bot_avatar", $result['file_name']);
                    resizeImage(300, $uploaddir . $result['file_name'], $uploaddir . $result['file_name']);
                } else {
                    $status = "error";
                    $message = __("Error: Please try again.");
                }
            } else {
                $status = "error";
                $message = __("Only allowed jpg, jpeg png");
            }

        }

        if($status == ""){
            $status = "success";
            $message = __("Saved Successfully");
        }
    }

    if (isset($_POST['affiliate_program_settings'])) {
        update_option("enable_affiliate_program",validate_input($_POST['enable_affiliate_program']));
        update_option("affiliate_rule",validate_input($_POST['affiliate_rule']));
        update_option("affiliate_commission_rate",validate_input((int) $_POST['affiliate_commission_rate']));
        update_option("allow_affiliate_payouts",validate_input((int) $_POST['allow_affiliate_payouts']));
        update_option("affiliate_minimum_payout",validate_input((int) $_POST['affiliate_minimum_payout']));
        update_option("affiliate_payout_methods",validate_input($_POST['affiliate_payout_methods']));
        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['live_chat_settings'])) {
        update_option("enable_live_chat",validate_input((int) $_POST['enable_live_chat']));
        update_option("tawkto_chat_link",validate_input($_POST['tawkto_chat_link']));
        update_option("tawkto_membership",validate_input((int) $_POST['tawkto_membership']));
        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['social_login_setting'])) {
        update_option("facebook_app_id",validate_input($_POST['facebook_app_id']));
        update_option("facebook_app_secret",validate_input($_POST['facebook_app_secret']));
        update_option("google_app_id",validate_input($_POST['google_app_id']));
        update_option("google_app_secret",validate_input($_POST['google_app_secret']));
        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['recaptcha_setting'])) {

        update_option("recaptcha_mode",validate_input($_POST['recaptcha_mode']));
        update_option("recaptcha_public_key",validate_input($_POST['recaptcha_public_key']));
        update_option("recaptcha_private_key",validate_input($_POST['recaptcha_private_key']));
        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['blog_setting'])) {

        update_option("blog_enable",validate_input($_POST['blog_enable']));
        update_option("blog_banner",validate_input($_POST['blog_banner']));
        update_option("show_blog_home",validate_input($_POST['show_blog_home']));
        update_option("blog_page_limit",validate_input((int) $_POST['blog_page_limit']));
        update_option("blog_comment_enable",validate_input($_POST['blog_comment_enable']));
        update_option("blog_comment_approval",validate_input($_POST['blog_comment_approval']));
        update_option("blog_comment_user",validate_input($_POST['blog_comment_user']));
        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['testimonials_setting'])) {

        update_option("testimonials_enable",validate_input($_POST['testimonials_enable']));
        update_option("show_testimonials_blog",validate_input($_POST['show_testimonials_blog']));
        update_option("show_testimonials_home",validate_input($_POST['show_testimonials_home']));
        $status = "success";
        $message = __("Saved Successfully");
    }

    if (isset($_POST['pwa_setting'])) {
        update_option("pwa_app_name",validate_input($_POST['pwa_app_name']));
        update_option("pwa_short_name",validate_input($_POST['pwa_short_name']));
        update_option("pwa_bg_color",validate_input($_POST['pwa_bg_color']));
        update_option("pwa_theme_color",validate_input($_POST['pwa_theme_color']));
        update_option("pwa_app_description",validate_input($_POST['pwa_app_description']));

        $status = "success";
        $message = __("Saved Successfully");

        $valid_formats = array("jpg", "jpeg", "png"); // Valid image formats
        if (isset($_FILES['pwa_icon']) && $_FILES['pwa_icon']['tmp_name'] != "") {
            $filename = stripslashes($_FILES['pwa_icon']['name']);
            $ext = getExtension($filename);
            $ext = strtolower($ext);
            //File extension check
            if (in_array($ext, $valid_formats)) {
                $uploaddir = "../storage/logo/"; //Image upload directory

                $old_icon = get_option('pwa_icon');

                $result = quick_file_upload('pwa_icon', $uploaddir);
                //Moving file to uploads folder
                if ($result['success']) {
                    update_option("pwa_icon", $result['file_name']);
                    resizeImage(512, $uploaddir . $result['file_name'], $uploaddir . $result['file_name']);
                    resizeImage(256, $uploaddir . 'icon-256-'.$result['file_name'], $uploaddir . $result['file_name']);
                    resizeImage(128, $uploaddir . 'icon-128-'.$result['file_name'], $uploaddir . $result['file_name']);

                    $icon_path = $uploaddir . $old_icon;
                    if (file_exists($icon_path) && !is_dir($icon_path)) {
                        unlink($icon_path);
                        unlink($uploaddir . 'icon-256-'.$old_icon);
                        unlink($uploaddir . 'icon-128-'.$old_icon);
                    }
                } else {
                    $status = "error";
                    $message = __("Error: Please try again.");
                }
            } else {
                $status = "error";
                $message = __("Only allowed jpg, jpeg png");
            }

        }
    }
    echo $json = '{"status" : "' . $status . '","message" : "' . $message . '"}';
    die();
}

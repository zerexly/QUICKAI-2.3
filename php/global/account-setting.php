<?php
if(isset($current_user['id']))
{
    $ses_userdata = $current_user;

    $author_image = $ses_userdata['image'];
    $author_lastactive = $ses_userdata['lastactive'];

    $errors = 0;
    $username_error = '';
    $email_error = '';
    $password_error = '';
    $delete_account_error = '';
    $avatarName = null;

    if(isset($_POST['submit']))
    {
        if(!check_allow()){
            $errors++;
            $username_error = 'Disabled on demo';
            $username_error = "<span class='status-not-available'> ".$username_error."</span>";
        }

        // Check if this is an Username availability check from signup page using ajax
        if($_POST["username"] != $_SESSION['user']['username'])
        {
            if(empty($_POST["username"]))
            {
                $errors++;
                $username_error = __("Please enter an username");
                $username_error = "<span class='status-not-available'> ".$username_error."</span>";
            }
            elseif(preg_match('/[^A-Za-z0-9]/',$_POST['username']))
            {
                $errors++;
                $username_error = __("Username may only contain alphanumeric characters");
                $username_error = "<span class='status-not-available'> ".$username_error." [A-Z,a-z,0-9]</span>";
            }
            elseif( (mb_strlen($_POST['username']) < 2) OR (mb_strlen($_POST['username']) > 16) )
            {
                $errors++;
                $username_error = __("Username must be between 2 and 15 characters long");
                $username_error = "<span class='status-not-available'> ".$username_error.".</span>";
            }
            else{
                $user_count = check_username_exists($_POST["username"]);
                if($user_count>0) {
                    $errors++;
                    $username_error = __("Username not available");
                    $username_error = "<span class='status-not-available'>".$username_error."</span>";
                }
            }
        }

        // Check if this is an Email availability check from signup page using ajax
        if(is_null($_POST["email"])) {
            $errors++;
            $email_error = __("Please enter an email address");
            $email_error = "<span class='status-not-available'> ".$email_error."</span>";
        }
        elseif($_POST["email"] != $ses_userdata['email'])
        {

            if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                $errors++;
                $email_error = __("This is not a valid email address");
                $email_error = "<span class='status-not-available'> " . $email_error . ".</span>";
            } else {
                $user_count = check_account_exists($_POST["email"]);
                if ($user_count > 0) {
                    $errors++;
                    $email_error = __("An account already exists with that e-mail address");
                    $email_error = "<span class='status-not-available'>" . $email_error . "</span>";
                }
            }
        }

        // Check if this is an Password availability check from signup page using ajax
        if(!empty($_POST["password"]) && !empty($_POST["re_password"]))
        {
            if( (mb_strlen($_POST['password']) < 5) OR (mb_strlen($_POST['password']) > 21) )
            {
                $errors++;
                $password_error = __("Password must be between 4 and 20 characters long");
                $password_error = "<span class='status-not-available'> ".$password_error.".</span>";
            }elseif ($_POST["password"] != $_POST["re_password"]){
                $errors++;
                $password_error = __("The passwords you entered did not match");
                $password_error = "<span class='status-not-available'> ".$password_error.".</span>";
            }
        }

        if ($errors == 0) {
            if (!empty($_FILES['avatar'])) {
                $file = $_FILES['avatar'];
                // Valid formats
                $valid_formats = array("jpeg", "jpg", "png");
                $filename = $file['name'];
                $ext = getExtension($filename);
                $ext = strtolower($ext);
                if (!empty($filename)) {
                    //File extension check
                    if (in_array($ext, $valid_formats)) {
                        $main_path = ROOTPATH . "/storage/profile/";
                        $filename = uniqid($_SESSION['user']['username'] . '_') . '.' . $ext;

                        $result = quick_file_upload('avatar', $main_path);
                        if ($result['success']) {
                            $avatarName = $result['file_name'];
                            resizeImage(150, $main_path . $avatarName, $main_path . $avatarName);
                            resizeImage(60, $main_path . 'small_' . $avatarName, $main_path . $avatarName);
                            if (file_exists($main_path . $author_image) && $author_image != 'default_user.png') {
                                unlink($main_path . $author_image);
                                unlink($main_path . 'small_' . $author_image);
                            }
                        } else {
                            $errors++;
                            $avatar_error = __("Error: Please try again.");
                            $avatar_error = "<span class='status-not-available'>" . $avatar_error . "</span>";
                        }
                    } else {
                        $errors++;
                        $avatar_error = __("Only jpg, jpeg & png allowed.");
                        $avatar_error = "<span class='status-not-available'>" . $avatar_error . "</span>";
                    }
                }
            }
        }

        if($errors == 0)
        {
            $queryVar = "";

            $person = ORM::for_table($config['db']['pre'].'user')->find_one($_SESSION['user']['id']);
            $oldemail = $person['email'];
            if($oldemail != $_POST["email"]){
                $person->set('status', '0');
            }
            $person->set('username', $_POST["username"]);
            $person->set('email', $_POST["email"]);
            $person->set_expr('updated_at', 'NOW()');

            if(!empty($_POST["password"]))
            {
                $password = $_POST["password"];
                $pass_hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 13]);

                $person->set('password_hash', $pass_hash);
            }
            if ($avatarName) {
                $person->set('image', $avatarName);
            }

            $person->save();


            //Updating Session Values
            $loggedin = get_user_data("",$_SESSION['user']['id']);
            create_user_session($loggedin['id'],$loggedin['username'],$loggedin['password'],$loggedin['user_type']);

            transfer($link['ACCOUNT_SETTING'],__("Settings Saved Successfully"),__("Settings Saved"));
            exit;
        }
    }

    $billing_error = 0;
    if(isset($_POST['billing-submit']))
    {
        if (
            (empty($_POST["billing_details_type"]) || trim($_POST["billing_details_type"]) == '') ||
            (empty($_POST["billing_name"]) || trim($_POST["billing_name"]) == '') ||
            (empty($_POST["billing_address"]) || trim($_POST["billing_address"]) == '') ||
            (empty($_POST["billing_city"]) || trim($_POST["billing_city"]) == '') ||
            (empty($_POST["billing_state"]) || trim($_POST["billing_state"]) == '') ||
            (empty($_POST["billing_zipcode"]) || trim($_POST["billing_zipcode"]) == '') ||
            (empty($_POST["billing_country"]) || trim($_POST["billing_country"]) == '')
        ) {
            $billing_error = 1;
        }else {
            update_user_option($_SESSION['user']['id'],'billing_details_type', validate_input($_POST['billing_details_type']));
            update_user_option($_SESSION['user']['id'],'billing_tax_id', validate_input($_POST['billing_tax_id']));
            update_user_option($_SESSION['user']['id'],'billing_name', validate_input($_POST['billing_name']));
            update_user_option($_SESSION['user']['id'],'billing_address', validate_input($_POST['billing_address']));
            update_user_option($_SESSION['user']['id'],'billing_city', validate_input($_POST['billing_city']));
            update_user_option($_SESSION['user']['id'],'billing_state', validate_input($_POST['billing_state']));
            update_user_option($_SESSION['user']['id'],'billing_zipcode', validate_input($_POST['billing_zipcode']));
            update_user_option($_SESSION['user']['id'],'billing_country', validate_input($_POST['billing_country']));

            transfer($link['ACCOUNT_SETTING'],__("Settings Saved Successfully"),__("Settings Saved Successfully"));
            exit;
        }
    }

    if(isset($_POST['submit'])) {
        $email_field = $ses_userdata['email'];
        $username_field = $_SESSION['user']['username'];
    }
    else {
        $email_field = $ses_userdata['email'];
        $username_field = $_SESSION['user']['username'];
        $username_error = '';
        $email_error = '';
        $password_error = '';
    }

    if(isset($_POST['delete-account'])) {
        if (!check_allow()) {
            $errors++;
            $username_error = 'Disabled on demo';
            $username_error = "<span class='status-not-available'> " . $username_error . "</span>";
        }

        $info = ORM::for_table($config['db']['pre'] . 'user')
            ->select_many('password_hash')
            ->where('id', $_SESSION['user']['id'])
            ->find_one();
        if (password_verify($_POST['password'], $info['password_hash'])) {

            /* Cancel subscription if there is any */
            cancel_recurring_payment($_SESSION['user']['id']);

            $users = ORM::for_table($config['db']['pre'].'user')
                ->select('image')
                ->where("id", $_SESSION['user']['id']);

            foreach ($users->find_array() as $row) {
                $uploaddir = ROOTPATH."/storage/profile/";
                // delete images
                if (trim($row['image']) != "" && $row['image'] != "default_user.png") {
                    $file = $uploaddir . $row['image'];
                    if (file_exists($file))
                        unlink($file);
                }
            }

            // delete documents of user
            ORM::for_table($config['db']['pre'] . 'ai_documents')
                ->where("id", $_SESSION['user']['id'])
                ->delete_many();
            ORM::for_table($config['db']['pre'] . 'word_used')
                ->where("id", $_SESSION['user']['id'])
                ->delete_many();

            // delete images of user
            $images = ORM::for_table($config['db']['pre'] . 'ai_images')
                ->select('image')
                ->where("id", $_SESSION['user']['id']);
            foreach ($images->find_array() as $row) {
                $image_dir = "../storage/ai_images/";
                $main_image = trim((string) $row['image']);
                // delete Image
                if (!empty($main_image)) {
                    $file = $image_dir . $main_image;
                    if (file_exists($file))
                        unlink($file);

                    $file = $image_dir . 'small_'.$main_image;
                    if (file_exists($file))
                        unlink($file);
                }
            }
            $images->delete_many();
            ORM::for_table($config['db']['pre'] . 'image_used')
                ->where("id", $_SESSION['user']['id'])
                ->delete_many();

            // delete audios of user
            $speeches = ORM::for_table($config['db']['pre'] . 'ai_speeches')
                ->select('file_name')
                ->where("id", $_SESSION['user']['id']);
            foreach ($speeches->find_array() as $row) {
                $dir = ROOTPATH."/storage/ai_audios/";
                $main_file = $row['file_name'];

                if (trim($main_file) != "") {
                    $file = $dir . $main_file;
                    if (file_exists($file))
                        unlink($file);
                }
            }
            $speeches->delete_many();
            ORM::for_table($config['db']['pre'] . 'text_to_speech_used')
                ->where("id", $_SESSION['user']['id'])
                ->delete_many();

            // delete speech_to_text_used of user
            ORM::for_table($config['db']['pre'] . 'speech_to_text_used')
                ->where("id", $_SESSION['user']['id'])
                ->delete_many();

            // Delete Users
            $users->delete_many();

            transfer($link['LOGOUT'],__("Account Deleted Successfully"),__("Account Deleted"));
            exit;
        } else {
            $errors++;
            $delete_account_error = __('Your current password is not valid.');
            $delete_account_error = "<span class='status-not-available'> " . $delete_account_error . "</span>";
        }
    }

    $billing_country = get_user_option($_SESSION['user']['id'],'billing_country');
    if(empty($billing_country)){
        $billing_country = strtoupper(check_user_country());
    }

    //Print Template
    HtmlTemplate::display('global/account-setting', array(
        'email_field' => $email_field,
        'username_field' => $username_field,
        'username_error' => $username_error,
        'email_error' => $email_error,
        'password_error' => $password_error,
        'delete_account_error' => $delete_account_error,
        'billing_error' => $billing_error,
        'billing_details_type' => get_user_option($_SESSION['user']['id'],'billing_details_type'),
        'billing_tax_id' => get_user_option($_SESSION['user']['id'],'billing_tax_id'),
        'billing_name' => get_user_option($_SESSION['user']['id'],'billing_name'),
        'billing_address' => get_user_option($_SESSION['user']['id'],'billing_address'),
        'billing_city' => get_user_option($_SESSION['user']['id'],'billing_city'),
        'billing_state' => get_user_option($_SESSION['user']['id'],'billing_state'),
        'billing_zipcode' => get_user_option($_SESSION['user']['id'],'billing_zipcode'),
        'billing_country' => $billing_country,
        'countries' => get_country_list($billing_country,"selected",0),
        'authoruname' => ucfirst($ses_userdata['username']),
        'authorname' => ucfirst($ses_userdata['name']),
        'authorimg' => $author_image,
        'lastactive' => $author_lastactive,
        'htmlpage' => get_html_pages()
    ));
    exit;
}
else{
    error(__("Page Not Found"), __LINE__, __FILE__, 1);
    exit();
}
?>

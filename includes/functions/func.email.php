<?php
/**
 * Send email with templates
 *
 * @param string $template
 * @param int|null $user_id
 * @param string|null $password
 * @param int|null $product_id
 * @param string|null $item_title
 */
function email_template($template, $user_id=null, $password=null, $product_id=null, $item_title=null){
    global $config,$lang,$link;

    if($user_id != null){
        $userdata = get_user_data(null,$user_id);
        $username = $userdata['username'];
        $user_email = $userdata['email'];
        $user_fullname = $userdata['name'];
        $confirm_id =  $userdata['confirm'];
    }

    /*SEND ACCOUNT DETAILS EMAIL*/
    if($template == "signup_details"){
        
        $html = $config['email_sub_signup_details'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{USER_ID}', $user_id, $html);
        $html = str_replace ('{USERNAME}', $username, $html);
        $html = str_replace ('{EMAIL}', $user_email, $html);
        $html = str_replace ('{USER_FULLNAME}', $user_fullname, $html);
        $html = str_replace ('{PASSWORD}', $password, $html);

        $email_subject = $html;

        
        $html = $config['email_message_signup_details'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{USER_ID}', $user_id, $html);
        $html = str_replace ('{USERNAME}', $username, $html);
        $html = str_replace ('{EMAIL}', $user_email, $html);
        $html = str_replace ('{USER_FULLNAME}', $user_fullname, $html);
        $html = str_replace ('{PASSWORD}', $password, $html);
        $email_body = $html;

        email($user_email,$user_fullname,$email_subject,$email_body);
        return;

        /* //Send 1 copy to admin
        *  //Uncomment if you want admin notify on register
        *  email($config['admin_email'],$config['site_title'],$email_subject,$email_body);
        * */
    }

    /*SEND CONFIRMATION EMAIL*/
    if($template == "signup_confirm"){
        
        $html = $config['email_sub_signup_confirm'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{USER_ID}', $user_id, $html);
        $html = str_replace ('{USERNAME}', $username, $html);
        $html = str_replace ('{EMAIL}', $user_email, $html);
        $html = str_replace ('{USER_FULLNAME}', $user_fullname, $html);
        $email_subject = $html;

        $confirmation_link = $link['SIGNUP']."?confirm=".$confirm_id."&user=".$user_id;
        
        $html = $config['email_message_signup_confirm'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{USER_ID}', $user_id, $html);
        $html = str_replace ('{USERNAME}', $username, $html);
        $html = str_replace ('{EMAIL}', $user_email, $html);
        $html = str_replace ('{USER_FULLNAME}', $user_fullname, $html);
        $html = str_replace ('{CONFIRMATION_LINK}', $confirmation_link, $html);
        $email_body = $html;

        email($user_email,$user_fullname,$email_subject,$email_body);
        return;
    }

    /*SEND AD APPROVE EMAIL*/
    if($template == "ad_approve"){

        $ad_link = $link['POST-DETAIL']."/".$product_id;
        
        $html = $config['email_sub_ad_approve'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{ADTITLE}', $item_title, $html);
        $html = str_replace ('{SELLER_NAME}', $user_fullname, $html);
        $html = str_replace ('{SELLER_EMAIL}', $user_email, $html);
        $email_subject = $html;
        
        $html = $config['email_message_ad_approve'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{ADTITLE}', $item_title, $html);
        $html = str_replace ('{ADLINK}', $ad_link, $html);
        $html = str_replace ('{SELLER_NAME}', $user_fullname, $html);
        $html = str_replace ('{SELLER_EMAIL}', $user_email, $html);
        $email_body = $html;

        email($user_email,$user_fullname,$email_subject,$email_body);
    }

    /*SEND RESUBMISSION AD APPROVE EMAIL*/
    if($template == "re_ad_approve"){
        $ad_link = $link['POST-DETAIL']."/".$product_id;

        $html = $config['email_sub_re_ad_approve'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{ADTITLE}', $item_title, $html);
        $html = str_replace ('{ADLINK}', $ad_link, $html);
        $html = str_replace ('{SELLER_NAME}', $user_fullname, $html);
        $html = str_replace ('{SELLER_EMAIL}', $user_email, $html);
        $email_subject = $html;

        $html = $config['email_message_re_ad_approve'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{ADTITLE}', $item_title, $html);
        $html = str_replace ('{ADLINK}', $ad_link, $html);
        $html = str_replace ('{SELLER_NAME}', $user_fullname, $html);
        $html = str_replace ('{SELLER_EMAIL}', $user_email, $html);
        $email_body = $html;

        email($user_email,$user_fullname,$email_subject,$email_body);
    }

    /*SEND CONTACT EMAIL TO SELLER*/
    if($template == "contact_seller"){
        $ad_link = $link['POST-DETAIL']."/".$product_id;
        
        $html = $config['email_sub_contact_seller'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{ADTITLE}', $item_title, $html);
        $html = str_replace ('{ADLINK}', $ad_link, $html);
        $html = str_replace ('{SELLER_NAME}', $user_fullname, $html);
        $html = str_replace ('{SELLER_EMAIL}', $user_email, $html);
        $html = str_replace ('{SENDER_NAME}', $_POST['name'], $html);
        $html = str_replace ('{SENDER_EMAIL}', $_POST['email'], $html);
        $html = str_replace ('{SENDER_PHONE}', $_POST['phone'], $html);
        $email_subject = $html;

        $html = $config['email_message_contact_seller'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{ADTITLE}', $item_title, $html);
        $html = str_replace ('{ADLINK}', $ad_link, $html);
        $html = str_replace ('{SELLER_NAME}', $user_fullname, $html);
        $html = str_replace ('{SELLER_EMAIL}', $user_email, $html);
        $html = str_replace ('{SENDER_NAME}', $_POST['name'], $html);
        $html = str_replace ('{SENDER_EMAIL}', $_POST['email'], $html);
        $html = str_replace ('{SENDER_PHONE}', $_POST['phone'], $html);
        $html = str_replace ('{MESSAGE}', $_POST['message'], $html);
        $email_body = $html;

        email($user_email,$user_fullname,$email_subject,$email_body);
    }

    /*SEND CONTACT EMAIL TO ADMIN*/
    if($template == "contact"){
        
        $html = $config['email_sub_contact'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{CONTACT_SUBJECT}', $_POST['subject'], $html);
        $html = str_replace ('{NAME}', $_POST['name'], $html);
        $html = str_replace ('{EMAIL}', $_POST['email'], $html);
        $email_subject = $html;

        
        $html = $config['email_message_contact'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{NAME}', $_POST['name'], $html);
        $html = str_replace ('{EMAIL}', $_POST['email'], $html);
        $html = str_replace ('{CONTACT_SUBJECT}', $_POST['subject'], $html);
        $html = str_replace ('{MESSAGE}', $_POST['message'], $html);
        $email_body = $html;

        email($config['admin_email'],$config['site_title'],$email_subject,$email_body);
    }
    /*SEND FEEDBACK TO ADMIN */
    if($template == "feedback"){
        
        $html = $config['email_sub_feedback'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{FEEDBACK_SUBJECT}', $_POST['subject'], $html);
        $html = str_replace ('{NAME}', $_POST['name'], $html);
        $html = str_replace ('{EMAIL}', $_POST['email'], $html);
        $email_subject = $html;
        
        $html = $config['email_message_feedback'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{NAME}', $_POST['name'], $html);
        $html = str_replace ('{EMAIL}', $_POST['email'], $html);
        $html = str_replace ('{FEEDBACK_SUBJECT}', $_POST['subject'], $html);
        $html = str_replace ('{MESSAGE}', $_POST['message'], $html);
        $email_body = $html;

        email($config['admin_email'],$config['site_title'],$email_subject,$email_body);
    }
    /*SEND REPORT TO ADMIN*/
    if($template == "report"){
        
        $html = $config['email_sub_report'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{EMAIL}', $_POST['email'], $html);
        $html = str_replace ('{NAME}', $_POST['name'], $html);
        $html = str_replace ('{USERNAME}', $_POST['username'], $html);
        $html = str_replace ('{VIOLATION}', $_POST['violation'], $html);
        $email_subject = $html;

        
        $html = $config['email_message_report'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{EMAIL}', $_POST['email'], $html);
        $html = str_replace ('{NAME}', $_POST['name'], $html);
        $html = str_replace ('{USERNAME}', $_POST['username'], $html);
        $html = str_replace ('USERNAME2', $_POST['username2'], $html);
        $html = str_replace ('{VIOLATION}', $_POST['violation'], $html);
        $html = str_replace ('{URL}', $_POST['url'], $html);
        $html = str_replace ('{DETAILS}', $_POST['details'], $html);
        $email_body = $html;

        email($config['admin_email'],$config['site_title'],$email_subject,$email_body);
    }

    if($template == "withdraw_rejected"){
        /*User : Withdraw request rejected*/
        $html = $config['email_sub_withdraw_rejected'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{USER_ID}', $user_id, $html);
        $html = str_replace ('{USERNAME}', $username, $html);
        $html = str_replace ('{EMAIL}', $user_email, $html);
        $html = str_replace ('{USER_FULLNAME}', $user_fullname, $html);
        $email_subject = $html;

        $html = $config['emailHTML_withdraw_rejected'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{USER_ID}', $user_id, $html);
        $html = str_replace ('{USERNAME}', $username, $html);
        $html = str_replace ('{EMAIL}', $user_email, $html);
        $html = str_replace ('{USER_FULLNAME}', $user_fullname, $html);
        $email_body = $html;

        email($user_email,$user_fullname,$email_subject,$email_body);
    }

    if($template == "withdraw_accepted"){
        /*User : Withdraw request accepted*/
        $html = $config['email_sub_withdraw_accepted'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{USER_ID}', $user_id, $html);
        $html = str_replace ('{USERNAME}', $username, $html);
        $html = str_replace ('{EMAIL}', $user_email, $html);
        $html = str_replace ('{USER_FULLNAME}', $user_fullname, $html);
        $email_subject = $html;

        $html = $config['emailHTML_withdraw_accepted'];
        $html = str_replace ('{SITE_TITLE}', $config['site_title'], $html);
        $html = str_replace ('{SITE_URL}', $config['site_url'], $html);
        $html = str_replace ('{USER_ID}', $user_id, $html);
        $html = str_replace ('{USERNAME}', $username, $html);
        $html = str_replace ('{EMAIL}', $user_email, $html);
        $html = str_replace ('{USER_FULLNAME}', $user_fullname, $html);
        $email_body = $html;

        email($user_email,$user_fullname,$email_subject,$email_body);
    }

    if($template == "rating_recieved"){

    }
}

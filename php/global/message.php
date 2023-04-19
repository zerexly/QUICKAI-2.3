<?php
if(checkloggedin()) {
    $userid = '';
    $chatid = '';
    $chat_username = '';
    $chat_fullname = '';
    $chat_userimg = '';
    $chat_userstatus = '';

    $postid = isset($_GET['postid'])? base64_url_decode($_GET['postid']) : "";
    $posttype = isset($_GET['posttype'])? $_GET['posttype'] : "";

    if(isset($_GET['userid'])){
        $userid = base64_url_decode($_GET['userid']);
        $userdata = get_user_data(null,$userid);
        if($userdata){
            $chatid = $userid.'_'.$postid.'_'.$posttype;
            $chat_username = $userdata['username'];
            $chat_fullname = ($userdata['name'] != '')? $userdata['name'] : $userdata['username'];
            $chat_userimg = ($userdata['image'] == "")? "default_user.png" : $userdata['image'];
            $chat_userstatus  = ($userdata['online'] == "0")? "offline" : "online";
        }
    }

    $ses_userdata = get_user_data($_SESSION['user']['username']);
    $author_image = $ses_userdata['image'];
    if($config['quickchat_ajax_on_off'] == 'on' || $config['quickchat_socket_on_off'] == 'on') {
        //Print Template
        HtmlTemplate::display('global/quickchat', array(
            'language_direction' => get_current_lang_direction(),
            'userimg' => $author_image,
            'chatid' => $chatid,
            'postid' => $postid,
            'posttype' => $posttype,
            'chat_userid' => $userid,
            'chat_fullname' => $chat_fullname,
            'chat_userimg' => $chat_userimg,
            'chat_userstatus' => $chat_userstatus
        ));
        exit;
    }else
        error(__("Page Not Found"), __LINE__, __FILE__, 1);
}else
    headerRedirect($link['LOGIN']);
?>
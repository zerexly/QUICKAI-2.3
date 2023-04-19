<?php

$recaptcha_error = '';
if(isset($_POST['Submit']))
{
    if($config['recaptcha_mode'] == 1){
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

        /*SEND FEEDBACK EMAIL TO ADMIN*/
        email_template("feedback");

        message(__("Thanks"),__("Thank you for your feedback."));
    }
}

// get recent blog
$rows = ORM::for_table($config['db']['pre'] . 'blog')
    ->where('status', 'publish')
    ->order_by_desc('created_at')
    ->limit(3)
    ->find_many();
$recent_blog = array();
$n = true;
foreach ($rows as $row) {
    $recent_blog[$row['id']]['id'] = $row['id'];
    $recent_blog[$row['id']]['title'] = $row['title'];
    $recent_blog[$row['id']]['created_at'] = timeAgo($row['created_at']);
    $recent_blog[$row['id']]['image'] = !empty($row['image']) ? $row['image'] : 'default.png';
    $recent_blog[$row['id']]['link'] = $link['BLOG-SINGLE'] . '/' . $row['id'] . '/' . create_slug($row['title']);
    $recent_blog[$row['id']]['class'] = ($n)? "active" : "";
    $n = false;
}

// get testimonials
$rows = ORM::for_table($config['db']['pre'] . 'testimonials')
    ->order_by_desc('id')
    ->limit(5)
    ->find_many();
$testimonials = array();
foreach ($rows as $row) {
    $testimonials[$row['id']]['id'] = $row['id'];
    $testimonials[$row['id']]['name'] = $row['name'];
    $testimonials[$row['id']]['designation'] = $row['designation'];
    $testimonials[$row['id']]['content'] = $row['content'];
    $testimonials[$row['id']]['image'] = !empty($row['image']) ? $row['image'] : 'default_user.png';
}
//Print Template
HtmlTemplate::display('global/feedback', array(
    'recaptcha_error' => $recaptcha_error,
    'recent_blog' => $recent_blog,
    'testimonials' => $testimonials
));
exit;
?>
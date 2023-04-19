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

        /*SEND REPORT EMAIL TO ADMIN*/
        email_template("contact");

        message(__("Thanks"),__("Thank you for contacting us."));
    }

}

//Print Template
HtmlTemplate::display('global/contact', array(
    'phone' => get_option("contact_phone"),
    'address' => get_option("contact_address"),
    'email' => get_option("contact_email"),
    'latitude' => get_option("contact_latitude"),
    'longitude' => get_option("contact_longitude"),
    'map_color' => get_option("map_color"),
    'recaptcha_error' => $recaptcha_error
));
exit;
?>
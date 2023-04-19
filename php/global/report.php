<?php
$email = isset($_POST['email'])? htmlentities($_POST['email']): "";
$name = isset($_POST['name'])? htmlentities($_POST['name']): "";
$type = isset($_POST['type'])? htmlentities($_POST['type']): "";
$username = isset($_POST['username'])? htmlentities($_POST['username']): "";
$username2 = isset($_POST['username2'])? htmlentities($_POST['username2']): "";
$details = isset($_POST['details'])? htmlentities($_POST['details']): "";
$url = isset($_POST['url'])? htmlentities($_POST['url']): "";
$violation = isset($_POST['violation'])? htmlentities($_POST['violation']): "";
$errors = 0;
$name_error = '';
$email_error = '';
$viol_error = '';
$redirect_url = '';
if(isset($_POST['Submit']))
{
	$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

	if(trim($_POST['email']) == '')
	{
		$errors++;
		$email_error = __("Please enter an email address");
	}
	elseif(!preg_match($regex, $_POST['email']))
	{
		$errors++;
		$email_error = __("This is not a valid email address");
	}

	if(trim($_POST['name']) == '')
	{
		$errors++;
		$name_error = __("Please enter your name");
	}

	if(trim($_POST['details']) == '')
	{
		$errors++;
		$viol_error = __("Please enter violation details");
	}

	if(isset($_SERVER['HTTP_REFERER'])) {
		$referer = $_SERVER['HTTP_REFERER'];
		if ((strpos($referer, $link['POST-DETAIL']) !== false)){
			$redirect_url = $_SERVER['HTTP_REFERER'];
		}
	}

	if($errors == 0)
	{
		/*SEND CONTACT EMAIL*/
		email_template("report");
		message( __("Thanks"), __('Thank you for reporting this violation.'));
	}
}
if(isset($_SESSION['user']['username'])) {
	$ses_userdata = get_user_data($_SESSION['user']['username']);
	$username = $_SESSION['user']['username'];
	$name = $ses_userdata['name'];
	$email = $ses_userdata['email'];
} else {
	$username = '';
	$name = '';
	$email = '';
}
//Print Template
HtmlTemplate::display('global/report', array(
	'username' => $username,
	'username2' => $username2,
	'name' => $name,
	'email' => $email,
	'details' => $details,
	'email_error' => $email_error,
	'name_error' => $name_error,
	'viol_error' => $viol_error,
	'redirect_url' => $redirect_url
));
exit;
?>

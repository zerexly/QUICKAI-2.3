<?php

$info = ORM::for_table($config['db']['pre'].'pages')
    ->select('translation_of')
    ->where(array(
        'slug' => $_GET['id'],
        'active' => '1'
    ))
    ->find_one();

$info2 = ORM::for_table($config['db']['pre'].'pages')
    ->where(array(
        'translation_lang' => $config['lang_code'],
        'translation_of' => $info['translation_of'],
        'active' => '1'
    ))
    ->find_one();
if (!empty($info2))
{
    $html = stripslashes($info2['content']);
    $name = stripslashes($info2['name']);
    $title = stripslashes($info2['title']);
    $type = $info2['type'];
}

if(!isset($title))
{
	message("Error",__('We are sorry, the page you requested cannot be found. You can go back to'));
}

if($type == 1)
{
	if(!isset($_SESSION['user']['id']))
	{
		message(__("Login to view"),__("You need to login first to view this page."));
	}
}

if(isset($_GET['basic'])) {
	$page = 'global/html_content_no';
}
else {
	$page = 'global/html_content';
}

//Print Template
HtmlTemplate::display($page, array(
    'name' => $name,
    'title' => $title,
    'html' => $html
));
exit;
?>
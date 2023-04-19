<?php
global $config;
if(checkloggedin())
{
    if(!empty($_GET['slug'])) {
        $_GET['slug'] = validate_input($_GET['slug']);

        $ai_template = ORM::for_table($config['db']['pre'] . 'ai_templates')
            ->where('active', '1')
            ->where('slug', $_GET['slug'])
            ->find_one();

        // check custom templates
        if(empty($ai_template)) {
            $ai_template = ORM::for_table($config['db']['pre'] . 'ai_custom_templates')
                ->where('active', '1')
                ->where('slug', $_GET['slug'])
                ->find_one();
        }

        if(!empty($ai_template)) {

            $start = date('Y-m-01');
            $end = date_create(date('Y-m-t'))->modify('+1 day')->format('Y-m-d');

            $total_words_used = ORM::for_table($config['db']['pre'] . 'word_used')
                ->where('user_id', $_SESSION['user']['id'])
                ->where_raw("(`date` BETWEEN '$start' AND '$end')")
                ->sum('words');

            $membership = get_user_membership_detail($_SESSION['user']['id']);
            $words_limit = $membership['settings']['ai_words_limit'];

            HtmlTemplate::display('ai-template-create', array(
                'ai_template' => $ai_template,
                'words_limit' => $words_limit,
                'total_words_used' => $total_words_used,
                'plan_templates' => $membership['settings']['ai_templates'],
                'languages' => get_ai_languages()
            ));
        } else {
            page_not_found();
        }
    } else {
        page_not_found();
    }
}
else{
    headerRedirect($link['LOGIN']);
}
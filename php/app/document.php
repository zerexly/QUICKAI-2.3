<?php
global $config;
if (checkloggedin()) {

    if(!empty($_GET['id'])){

        $document = ORM::for_table($config['db']['pre'] . 'ai_documents')
            ->where('id', $_GET['id'])
            ->where('user_id', $_SESSION['user']['id'])
            ->find_one();

        if(!empty($document['id'])){

            HtmlTemplate::display('document', array(
                'id' => $document['id'],
                'title' => $document['title'],
                'template_slug' => $document['template'],
                'content' => $document['content']
            ));
        } else {
            page_not_found();
        }
    } else {
        page_not_found();
    }
} else {
    headerRedirect($link['LOGIN']);
}
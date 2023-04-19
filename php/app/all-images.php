<?php
global $config;
if (checkloggedin()) {

    if (!isset($_GET['page']))
        $page = 1;
    else
        $page = $_GET['page'];

    $limit = 25;

    $orm = ORM::for_table($config['db']['pre'] . 'ai_images')
        ->where('user_id', $_SESSION['user']['id'])
        ->order_by_desc('id');

    $total = $orm->count();

    $rows = $orm
        ->limit($limit)
        ->offset(($page - 1) * $limit)
        ->find_many();

    $images = array();
    foreach ($rows as $row) {
        $images[$row['id']]['id'] = $row['id'];
        $images[$row['id']]['title'] = $row['title'];
        $images[$row['id']]['description'] = strip_tags($row['description']);
        $images[$row['id']]['image'] = $row['image'];
        $images[$row['id']]['resolution'] = $row['resolution'];
        $images[$row['id']]['date'] = date('d M, Y', strtotime($row['created_at']));
        $images[$row['id']]['time'] = date('H:i:s', strtotime($row['created_at']));
    }

    $pagging = pagenav($total, $page, $limit, $link['ALL_IMAGES']);

    $start = date('Y-m-01');
    $end = date_create(date('Y-m-t'))->modify('+1 day')->format('Y-m-d');

    $total_images_used = ORM::for_table($config['db']['pre'] . 'image_used')
        ->where('user_id', $_SESSION['user']['id'])
        ->where_raw("(`date` BETWEEN '$start' AND '$end')")
        ->sum('images');

    $membership = get_user_membership_detail($_SESSION['user']['id']);
    $images_limit = $membership['settings']['ai_images_limit'];

    HtmlTemplate::display('all-images', array(
        'images' => $images,
        'pagging' => $pagging,
        'show_paging' => (int)($total > $limit),
        'total_images_used' => $total_images_used,
        'images_limit' => $images_limit
    ));
} else {
    headerRedirect($link['LOGIN']);
}
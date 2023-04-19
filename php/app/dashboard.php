<?php
global $config, $link;
if (checkloggedin()) {
    $start = date('Y-m-01');
    $end = date_create(date('Y-m-t'))->modify('+1 day')->format('Y-m-d');

    $days = $word_used = [];
    $total_scans = 0;

    $period = new \DatePeriod(date_create($start), \DateInterval::createFromDateString('1 day'), date_create($end));
    /** @var \DateTime $dt */
    foreach ($period as $dt) {
        $days[] = date('d M', $dt->getTimestamp());
        $word_used[date('d M', $dt->getTimestamp())] = 0;
    }

    $total_words_used = ORM::for_table($config['db']['pre'] . 'word_used')
        ->where('user_id', $_SESSION['user']['id'])
        ->where_raw("(`date` BETWEEN '$start' AND '$end')")
        ->sum('words');

    $total_images_used = ORM::for_table($config['db']['pre'] . 'image_used')
        ->where('user_id', $_SESSION['user']['id'])
        ->where_raw("(`date` BETWEEN '$start' AND '$end')")
        ->sum('images');

    $total_speech_used = ORM::for_table($config['db']['pre'] . 'speech_to_text_used')
        ->where('user_id', $_SESSION['user']['id'])
        ->where_raw("(`date` BETWEEN '$start' AND '$end')")
        ->count();

    $membership = get_user_membership_detail($_SESSION['user']['id']);
    $membership_name = $membership['name'];
    $membership_settings = $membership['settings'];


    $sql = "SELECT DATE(`date`) AS created, SUM(`words`) AS used_words 
                FROM " . $config['db']['pre'] . "word_used 
                WHERE 
                    `user_id` = {$_SESSION['user']['id']} 
                    AND `date` BETWEEN '$start' AND '$end'
                GROUP BY DATE(`date`)";

    $result = ORM::for_table($config['db']['pre'] . 'word_used')
        ->raw_query($sql)
        ->find_many();

    foreach ($result as $data) {
        $word_used[date('d M', strtotime($data['created']))] = $data['used_words'];
    }

    //Print Template 'Home/index Page'
    HtmlTemplate::display('dashboard', array(
        'word_used' => json_encode(array_values($word_used)),
        'days' => json_encode(array_values($days)),
        'membership_name' => $membership_name,
        'membership_settings' => $membership_settings,
        'total_speech_used' => $total_speech_used,
        'total_words_used' => $total_words_used ?: 0,
        'total_images_used' => $total_images_used ?: 0
    ));
} else {
    headerRedirect($link['LOGIN']);
}
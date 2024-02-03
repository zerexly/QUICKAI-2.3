<?php
header('Content-type: text/xml');

function text_replace_for_xml($text){
    $text = str_replace("&","&amp;",stripslashes($text));
    $text = str_replace('<','&lt;',$text);
    $text = str_replace('>','&gt;',$text);
    return $text;
}

if($config['xml_latest'] == 1){
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9" xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0" xmlns:pagemap="http://www.google.com/schemas/sitemap-pagemap/1.0" xmlns:xhtml="http://www.w3.org/1999/xhtml" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

    echo '<url>';
    echo '<loc>' . $config['site_url'] . '</loc>';
    echo '</url>';

    echo '<url>';
    echo '<loc>' . $link['LOGIN'] . '</loc>';
    echo '</url>';

    if(get_option("enable_user_registration", '1')) {
        echo '<url>';
        echo '<loc>' . $link['SIGNUP'] . '</loc>';
        echo '</url>';
    }

    echo '<url>';
    echo '<loc>' . $link['FORGOT'] . '</loc>';
    echo '</url>';

    echo '<url>';
    echo '<loc>' . $link['REPORT'] . '</loc>';
    echo '</url>';

    echo '<url>';
    echo '<loc>' . $link['CONTACT'] . '</loc>';
    echo '</url>';

    echo '<url>';
    echo '<loc>' . $link['FAQ'] . '</loc>';
    echo '</url>';

    echo '<url>';
    echo '<loc>' . $link['FEEDBACK'] . '</loc>';
    echo '</url>';

    echo '<url>';
    echo '<loc>' . $link['BLOG'] . '</loc>';
    echo '</url>';

    // get recent blog
    $sql = "SELECT *
FROM `".$config['db']['pre']."blog` 
WHERE status = 'publish' ORDER BY created_at DESC";
    $rows = ORM::for_table($config['db']['pre'].'blog')->raw_query($sql)->find_many();
    $recent_blog = array();
    foreach ($rows as $info) {

        echo "<url>" .
            "<loc>" . $link['BLOG-SINGLE'].'/'.$info['id'].'/'.create_slug($info['title']) . "</loc>" .
            "<lastmod>" . date('Y-m-d', strtotime($info['updated_at'])) . "</lastmod>" .
            (
            !empty($info['image'])
                ? "<image:image>" .
                "<image:loc>" . $config['site_url'] . 'storage/blog/' . $info['image'] . "</image:loc>" .
                "<image:title>" . htmlspecialchars($info['title'], ENT_QUOTES) . "</image:title>" .
                "</image:image>"
                : ''
            ).
            "</url>";
    }


    echo '<url>';
    echo '<loc>' . $link['TESTIMONIALS'] . '</loc>';
    echo '</url>';

    echo '</urlset>';
}

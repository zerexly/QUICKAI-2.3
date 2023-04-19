<!DOCTYPE html>
<html lang="<?php _esc($config['lang_code']);?>">
<head>
    <title><?php _e('Site Maintenance'); ?></title>
    <meta charset="utf-8"/>
    <meta name="robots" content="noindex"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php _esc($config['site_url']);?>storage/logo/<?php _esc($config['site_favicon']);?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800&amp;subset=latin-ext">
    <style>
        body { text-align: center; padding: 20px; font: 20px Nunito, sans-serif; color: #333; }
        img {
            height: 100px;
        }
        @media (min-width: 768px){
            body{ padding-top: 150px; }
        }
        h1 { font-size: 45px;
            margin: 20px 0; }
        article { display: block; text-align: left; max-width: 650px; margin: 0 auto; }
        .team {color: <?php _esc($config['theme_color']);?>}
    </style>
</head>
<body>
<article>
    <img src="<?php _esc($config['site_url'].'storage/logo/'.$config['site_logo']);?>" alt="<?php _esc($config['site_title']);?>">
    <h1><?php _e("We'll be back soon!") ?></h1>
    <div>
        <p><?php _e("Sorry for the inconvenience but we're performing some maintenance at the moment. We'll be back online shortly!") ?></p>
        <p class="team">&mdash;&nbsp;<?php _e("The QuickAI Team") ?></p>
    </div>
</article>
</body>
</html>
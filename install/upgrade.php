<?php
global $config, $mysqli;
ignore_user_abort(1);

// version 1.2
if(version_compare($config['version'], '1.2', '<')){

    // add default values
    update_option("ai_default_lang", 'en');
    update_option("ai_default_quality_type", '0.75');
    update_option("ai_default_tone_voice", 'professional');
    update_option("ai_default_max_langth", 200);

    // create database tables
    $sql = "CREATE TABLE `".$config['db']['pre']."ai_template_categories` (
              `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `title` varchar(255) DEFAULT NULL,
              `translation_lang` longtext DEFAULT NULL,
              `translation_name` longtext DEFAULT NULL,
              `position` int(11) DEFAULT NULL,
              `active` tinyint(1) NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($mysqli,$sql);

    // insert data
    $sql = "INSERT INTO `".$config['db']['pre']."ai_template_categories` 
                (`id`, `title`, `translation_lang`, `translation_name`, `position`, `active`) VALUES
                (1, 'Article And Blogs', NULL, NULL, NULL, 1),
                (2, 'Ads And Marketing Tools', NULL, NULL, NULL, 1),
                (3, 'General Writing', NULL, NULL, NULL, 1),
                (4, 'Ecommerce', NULL, NULL, NULL, 1),
                (5, 'Social Media', NULL, NULL, NULL, 1),
                (6, 'Website', NULL, NULL, NULL, 1),
                (7, 'Other', NULL, NULL, NULL, 1);";
    mysqli_query($mysqli,$sql);

    // create database tables
    $sql = "CREATE TABLE `".$config['db']['pre']."ai_templates` (
              `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `category_id` int(11) DEFAULT NULL,
              `title` varchar(255) DEFAULT NULL,
              `slug` varchar(255) DEFAULT NULL,
              `icon` varchar(255) DEFAULT NULL,
              `description` text DEFAULT NULL,
              `position` int(11) DEFAULT NULL,
              `active` tinyint(1) NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($mysqli,$sql);

    // insert data
    $sql = "INSERT INTO `".$config['db']['pre']."ai_templates` (`id`, `category_id`, `title`, `slug`, `icon`, `description`, `position`, `active`) VALUES
                    (1, 1, 'Blog Ideas', 'blog-ideas', 'fa fa-comment', 'Article/blog ideas that you can use to generate more traffic, leads, and sales for your business.', NULL, 1),
                    (2, 1, 'Blog Intros', 'blog-intros', 'fa fa-align-left', 'Enticing article/blog introductions that capture the attention of the audience.', NULL, 1),
                    (3, 1, 'Blog Titles', 'blog-titles', 'fa fa-ellipsis-h', 'Nobody wants to read boring blog titles, generate catchy blog titles with this tool.', NULL, 1),
                    (4, 1, 'Blog Section', 'blog-section', 'fa fa-book', 'Write a few paragraphs about a subheading of your article.', NULL, 1),
                    (5, 1, 'Blog Conclusion', 'blog-conclusion', 'fa fa-check-square', 'Create powerful conclusion that will make a reader take action.', NULL, 1),
                    (6, 1, 'Article Writer', 'article-writer', 'fa fa-pencil-square', 'Create a fully complete high quality article from a title and outline text.', NULL, 1),
                    (7, 1, 'Article Rewriter', 'article-rewriter', 'fa fa-pencil-square-o', 'Copy an article, paste it in to the program, and with just one click you\'ll have an entirely different article to read.', NULL, 1),
                    (8, 1, 'Article Outlines', 'article-outlines', 'fa fa-list-ul', 'Detailed article outlines that help you write better content on a consistent basis.', NULL, 1),
                    (9, 1, 'Talking Points', 'talking-points', 'fa fa-list-ol', 'Write short, simple and informative points for the subheadings of your article', NULL, 1),
                    (10, 1, 'Paragraph Writer', 'paragraph-writer', 'fa fa-align-justify', 'Perfectly structured paragraphs that are easy to read and packed with persuasive words.', NULL, 1),
                    (11, 1, 'Content Rephrase', 'content-rephrase', 'fa fa-refresh', 'Rephrase your content in a different voice and style to appeal to different readers.', NULL, 1),
                    (12, 2, 'Facebook Ads', 'facebook-ads', 'fa fa-facebook-official', 'Facebook ad copies that make your ads truly stand out.', NULL, 1),
                    (13, 2, 'Facebook Ads Headlines', 'facebook-ads-headlines', 'fa fa-facebook-square', 'Write catchy and convincing headlines to make your Facebook Ads stand out.', NULL, 1),
                    (14, 2, 'Google Ad Titles', 'google-ad-titles', 'fa fa-google', 'Creating ads with unique and appealing titles that entice people to click on your ad and purchase from your site.', NULL, 1),
                    (15, 2, 'Google Ad Descriptions', 'google-ad-descriptions', 'fa fa-google', 'The best-performing Google ad copy converts visitors into customers.', NULL, 1),
                    (16, 2, 'LinkedIn Ad Headlines', 'linkedin-ad-headlines', 'fa fa-linkedin', 'Attention-grabbing, click-inducing, and high-converting ad headlines for Linkedin.', NULL, 1),
                    (17, 2, 'LinkedIn Ad Descriptions', 'linkedin-ad-descriptions', 'fa fa-linkedin', 'Professional and eye-catching ad descriptions that will make your product shine.', NULL, 1),
                    (18, 2, 'App and SMS Notifications', 'app-and-sms-notifications', 'fa fa-bell', 'Notification messages for your apps, websites, and mobile devices that keep users coming back for more.', NULL, 1),
                    (19, 3, 'Text Extender', 'text-extender', 'fa fa-text-width', 'Extend short sentences into more descriptive and interesting ones.', NULL, 1),
                    (20, 3, 'Content Shorten', 'content-shorten', 'fa fa-text-width', 'Short your content in a different voice and style to appeal to different readers.', NULL, 1),
                    (21, 3, 'Quora Answers', 'quora-answers', 'fa fa-quora', 'Answers to Quora questions that will position you as an authority.', NULL, 1),
                    (22, 3, 'Summarize for a 2nd grader', 'summarize-for-2nd-grader', 'fa fa-child', 'Translates difficult text into simpler concepts.', NULL, 1),
                    (23, 3, 'Stories', 'stories', 'fa fa-heart-o', 'Engaging and persuasive stories that will capture your reader\'s attention and interest.', NULL, 1),
                    (24, 3, 'Bullet Point Answers', 'bullet-point-answers', 'fa fa-list', 'Precise and informative bullet points that provide quick and valuable answers to your customers\' questions.', NULL, 1),
                    (25, 3, 'Definition', 'definition', 'fa fa-tasks', 'A definition for a word, phrase, or acronym that\'s used often by your target buyers.', NULL, 1),
                    (26, 3, 'Answers', 'answers', 'fa fa-check-circle', 'Instant, quality answers to any questions or concerns that your audience might have.', NULL, 1),
                    (27, 3, 'Questions', 'questions', 'fa fa-question-circle', 'A tool to create engaging questions and polls that increase audience participation and engagement.', NULL, 1),
                    (28, 3, 'Passive to Active Voice', 'passive-active-voice', 'fa fa-sort-alpha-desc', 'Easy and quick solution to converting your passive voice sentences into active voice sentences.', NULL, 1),
                    (29, 3, 'Pros and Cons', 'pros-cons', 'fa fa-key', 'List of the main benefits versus the most common problems and concerns.', NULL, 1),
                    (30, 3, 'Rewrite With Keywords', 'rewrite-with-keywords', 'fa fa-refresh', 'Rewrite your existing content to include more keywords and boost your search engine rankings.', NULL, 1),
                    (31, 3, 'Emails', 'emails', 'fa fa-envelope', 'Professional-looking emails that help you engage leads and customers.', NULL, 1),
                    (32, 3, 'Emails V2', 'emails-v2', 'fa fa-envelope', 'Personalized email outreach to your target prospects that get better results.', NULL, 1),
                    (33, 3, 'Email Subject Lines', 'email-subject-lines', 'fa fa-envelope-open-o', 'Powerful email subject lines that increase open rates.', NULL, 1),
                    (34, 3, 'Startup Name Generator', 'startup-name-generator', 'fa fa-bullhorn', 'Generate cool, creative, and catchy names for your startup in seconds.', NULL, 1),
                    (35, 3, 'Company Bios', 'company-bios', 'fa fa-file-text', 'Short and sweet company bio that will help you connect with your target audience.', NULL, 1),
                    (36, 3, 'Company Mission', 'company-mission', 'fa fa-file-text-o', 'A clear and concise statement of your company\'s goals and purpose.', NULL, 1),
                    (37, 3, 'Company Vision', 'company-vision', 'fa fa-align-left', 'A vision that attracts the right people, clients, and employees.', NULL, 1),
                    (38, 4, 'Product Name Generator', 'product-name-generator', 'fa fa-gift', 'Create creative product names from examples words.', NULL, 1),
                    (39, 4, 'Product Descriptions', 'product-descriptions', 'fa fa-gift', 'Authentic product descriptions that will compel, inspire, and influence.', NULL, 1),
                    (40, 4, 'Amazon Product Titles', 'amazon-product-titles', 'fa fa-amazon', 'Product titles that will make your product stand out in a sea of competition.', NULL, 1),
                    (41, 4, 'Amazon Product Descriptions', 'amazon-product-descriptions', 'fa fa-amazon', 'Descriptions for Amazon products that rank on the first page of the search results.', NULL, 1),
                    (42, 4, 'Amazon Product Features', 'amazon-product-features', 'fa fa-amazon', 'Advantages and features of your products that will make them irresistible to shoppers.', NULL, 1),
                    (43, 5, 'Social Media Post (Personal)', 'social-post-personal', 'fa fa-facebook', 'Write a social media post for yourself to be published on any platform.', NULL, 1),
                    (44, 5, 'Social Media Post (Business)', 'social-post-business', 'fa fa-facebook', 'Write a post for your business to be published on any social media platform.', NULL, 1),
                    (45, 5, 'Instagram Captions', 'instagram-captions', 'fa fa-instagram', 'Captions that turn your images into attention-grabbing Instagram posts.', NULL, 1),
                    (46, 5, 'Instagram Hashtags', 'instagram-hashtags', 'fa fa-instagram', 'Trending and highly relevant hashtags to help you get more followers and engagement.', NULL, 1),
                    (47, 5, 'Twitter Tweets', 'twitter-tweets', 'fa fa-twitter', 'Generate tweets using AI, that are relevant and on-trend.', NULL, 1),
                    (48, 5, 'YouTube Titles', 'youtube-titles', 'fa fa-youtube-play', 'Catchy titles that attract more views and increase the number of shares.', NULL, 1),
                    (49, 5, 'YouTube Descriptions', 'youtube-descriptions', 'fa fa-youtube-play', 'Catchy and persuasive YouTube descriptions that help your videos rank higher.', NULL, 1),
                    (50, 5, 'YouTube Outlines', 'youtube-outlines', 'fa fa-youtube-play', 'Video outlines that are a breeze to create and uber-engaging.', NULL, 1),
                    (51, 5, 'LinkedIn Posts', 'linkedin-posts', 'fa fa-linkedin', 'Inspiring LinkedIn posts that will help you build trust and authority in your industry.', NULL, 1),
                    (52, 5, 'TikTok Video Scripts', 'tiktok-video-scripts', 'fa fa-film', 'Video scripts that are ready to shoot and will make you go viral.', NULL, 1),
                    (53, 6, 'SEO Meta Tags (Blog Post)', 'meta-tags-blog', 'fa fa-google', 'A set of optimized meta title and meta description tags that will boost your search rankings for your blog.', NULL, 1),
                    (54, 6, 'SEO Meta Tags (Homepage)', 'meta-tags-homepage', 'fa fa-google', 'A set of optimized meta title and meta description tags that will boost your search rankings for your home page.', NULL, 1),
                    (55, 6, 'SEO Meta Tags (Product Page)', 'meta-tags-product', 'fa fa-google', 'A set of optimized meta title and meta description tags that will boost your search rankings for your product page.', NULL, 1),
                    (56, 7, 'Tone Changer', 'tone-changer', 'fa fa-refresh', 'Change the tone of your writing to match your audience and copy.', NULL, 1),
                    (57, 7, 'Song Lyrics', 'song-lyrics', 'fa fa-music', 'Unique song lyrics that will be perfect for your next hit song.', NULL, 1),
                    (58, 7, 'Translate', 'translate', 'fa fa-language', 'Translate your content into any language you want.', NULL, 1),
                    (59, 7, 'FAQs', 'faqs', 'fa fa-question-circle-o', 'Generate frequently asked questions based on your product description.', NULL, 1),
                    (60, 7, 'FAQ Answers', 'faq-answers', 'fa fa-question-circle-o', 'Generate creative answers to questions (FAQs) about your business or website.', NULL, 1),
                    (61, 7, 'Testimonials / Reviews', 'testimonials-reviews', 'fa fa-star-half-o', 'Add social proof to your website by generating user testimonials.', NULL, 1);";
    mysqli_query($mysqli,$sql);

    // create database tables
    $sql = "CREATE TABLE `".$config['db']['pre']."ai_custom_templates` (
              `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `category_id` int(11) DEFAULT NULL,
              `title` varchar(255) DEFAULT NULL,
              `slug` varchar(255) DEFAULT NULL,
              `icon` varchar(255) DEFAULT NULL,
              `description` text DEFAULT NULL,
              `prompt` longtext DEFAULT NULL,
              `parameters` longtext DEFAULT NULL,
              `position` int(11) DEFAULT NULL,
              `active` tinyint(1) NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($mysqli,$sql);
}

// version 1.3
if(version_compare($config['version'], '1.3', '<')){

    // add default values
    update_option("enable_ai_chat", '1');
    update_option("ai_chat_bot_name", 'AI Chat Bot');
    update_option("chat_bot_avatar", '');
    update_option('show_ai_images_home','1');

    // create database tables
    $sql = "CREATE TABLE `".$config['db']['pre']."ai_chat` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) DEFAULT NULL,
              `user_message` text DEFAULT NULL,
              `ai_message` text DEFAULT NULL,
              `date` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($mysqli,$sql);

}

// version 1.4
if(version_compare($config['version'], '1.4', '<')){

    // add default values
    update_option("ai_images_home_limit", '18');

    // Table: vc_api_keys
    // create database tables
    $sql = "CREATE TABLE `".$config['db']['pre']."api_keys` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(255) DEFAULT NULL,
              `api_key` varchar(255) DEFAULT NULL,
              `active` tinyint(1) NOT NULL DEFAULT 1,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($mysqli,$sql);

    // insert data
    $apikey = $config['open_ai_api_key'];
    $sql = "INSERT INTO `".$config['db']['pre']."api_keys` (`id`, `title`, `api_key`, `active`) VALUES
                (1, 'OpenAI', '$apikey', 1);";
    mysqli_query($mysqli,$sql);
    update_option("open_ai_api_key", '1');

}

// version 1.5
if(version_compare($config['version'], '1.5', '<')){
    // add default values
    update_option("enable_speech_to_text", '1');

    // create database tables
    $sql = "CREATE TABLE `".$config['db']['pre']."speech_to_text_used` (
              `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `user_id` int(11) DEFAULT NULL,
              `date` datetime DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($mysqli,$sql);

}

// version 1.7
if(version_compare($config['version'], '1.7', '<')){
    // add default values
    update_option("enable_ai_code", '1');
}

// version 1.8
if(version_compare($config['version'], '1.8', '<')){
    // add default values
    update_option("ai_languages", 'Arabic, Chinese, Danish, Dutch, English, French, German, Hebrew, Hindi, Indonesian, Italian, Japanese, Polish, Romanian, Russian, Spanish, Swedish, Turkish, Vietnamese');
    update_option("ai_default_lang", 'English');
}

// version 1.9
if(version_compare($config['version'], '1.9', '<')){

    $sql = "ALTER TABLE `".$config['db']['pre']."api_keys` ADD `type` VARCHAR(255) NULL DEFAULT NULL AFTER `api_key`;";
    mysqli_query($mysqli,$sql);

    // add default value for type
    $sql = "UPDATE `".$config['db']['pre']."api_keys` SET `type` = 'openai'";
    mysqli_query($mysqli,$sql);

    update_option("ai_image_api", 'openai');
    update_option("ai_image_api_key", get_option("open_ai_api_key"));
}

// version 2.0
if(version_compare($config['version'], '2.0', '<')){

    $sql = "ALTER TABLE `".$config['db']['pre']."user` ADD `referral_key` VARCHAR(255) NULL DEFAULT NULL AFTER `currency`;";
    mysqli_query($mysqli,$sql);

    $sql = "ALTER TABLE `".$config['db']['pre']."user` ADD `referred_by` INT(11) NULL DEFAULT NULL AFTER `referral_key`;";
    mysqli_query($mysqli,$sql);

    $sql = "CREATE TABLE `".$config['db']['pre']."affiliates` (
              `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `referrer_id` int(11) DEFAULT NULL,
              `referred_id` int(11) DEFAULT NULL,
              `transaction_id` int(11) DEFAULT NULL,
              `payment` float(11,2) DEFAULT NULL,
              `commission` float(11,2) DEFAULT NULL,
              `rate` float(11,2) DEFAULT NULL,
              `gateway` varchar(255) DEFAULT NULL,
              `date` datetime DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($mysqli,$sql);

    $sql = "CREATE TABLE `".$config['db']['pre']."withdrawal` (
              `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `user_id` int(11) DEFAULT NULL,
              `status` enum('success','pending','reject') NOT NULL DEFAULT 'pending',
              `amount` int(11) DEFAULT NULL,
              `payment_method_id` int(11) DEFAULT NULL,
              `account_details` text DEFAULT NULL,
              `reject_reason` text DEFAULT NULL,
              `created_at` datetime DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($mysqli,$sql);

    update_option("enable_affiliate_program",'1');
    update_option("affiliate_rule",'first');
    update_option("affiliate_commission_rate",'30');
    update_option("affiliate_minimum_payout",'50');

    update_option("emailHTML_withdraw_accepted",'<p>{SITE_TITLE}</p>
<p>Your withdrawal request accepted</p>
<p>amount : {AMOUNT}</p>
<p>------------------------------------<br /><br />Do not reply to this email.<br /><br />Regards<br />{SITE_TITLE}</p>');
    update_option("emailHTML_withdraw_rejected",'<p>{SITE_TITLE}</p>
<p>Your withdrawal request accepted</p>
<p>amount : {AMOUNT}</p>
<p>{REJECT_REASON}</p>
<p>------------------------------------<br /><br />Do not reply to this email.<br /><br />Regards<br />{SITE_TITLE}</p>');
    update_option("emailHTML_withdraw_request",'<p>{SITE_TITLE}</p>
<p>Got a new withdrawal request</p>
<p>Amount : {AMOUNT}</p>
<p>------------------------------------<br /><br />Do not reply to this email.<br /><br />Regards<br />{SITE_TITLE}</p>');

    update_option("email_sub_withdraw_accepted",'{SITE_TITLE} - Your withdrawal request accepted');
    update_option("email_sub_withdraw_rejected",'{SITE_TITLE} - Your withdrawal request rejected');
    update_option("email_sub_withdraw_request",'New withdrawal request');

}

// version 2.1
if(version_compare($config['version'], '2.1', '<')){

    $sql = "CREATE TABLE `".$config['db']['pre']."adsense` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `slug` text DEFAULT NULL,
              `size` text DEFAULT NULL,
              `provider_name` varchar(255) DEFAULT NULL,
              `large_track_code` text DEFAULT NULL,
              `tablet_track_code` text DEFAULT NULL,
              `phone_track_code` text DEFAULT NULL,
              `status` tinyint(1) NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($mysqli,$sql);

    $sql = "INSERT INTO `".$config['db']['pre']."adsense` (`id`, `slug`, `size`, `provider_name`, `large_track_code`, `tablet_track_code`, `phone_track_code`, `status`)
VALUES (1, 'header_top', NULL, 'Header Top', '', '', '', 0),
       (2, 'header_bottom', NULL, 'Header Bottom', '', '', '', 0),
       (3, 'home_page_1', NULL, 'Home Page 1', '', '', '', 0),
       (4, 'home_page_2', NULL, 'Home Page 2', '', '', '', 0),
       (5, 'home_page_3', NULL, 'Home Page 3', '', '', '', 0),
       (6, 'home_page_4', NULL, 'Home Page 4', '', '', '', 0),
       (7, 'home_page_5', NULL, 'Home Page 5', '', '', '', 0),
       (8, 'home_page_6', NULL, 'Home Page 6', '', '', '', 0),
       (9, 'blog_sidebar_top', NULL, 'Blog Sidebar Top', '', '', '', 0),
       (10, 'blog_sidebar_bottom', NULL, 'Blog Sidebar Bottom', '', '', '', 0),
       (11, 'footer_top', NULL, 'Footer Top', '', '', '', 0);";
    mysqli_query($mysqli,$sql);

    update_option("enable_ai_images", '1');
}

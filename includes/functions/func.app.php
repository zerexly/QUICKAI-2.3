<?php

/**
 * Get the ai templates
 *
 * @param null $category
 * @return array|array[]|null
 */
function get_ai_templates($category = null)
{
    global $config;

    $orm = ORM::for_table($config['db']['pre'] . 'ai_template_categories')
        ->where('active', '1')
        ->order_by_asc('position');

    if ($category) {
        $orm->where('id', $category);
    }
    $cats = $orm->find_array();

    foreach ($cats as $key => $cat) {
        $templates = ORM::for_table($config['db']['pre'] . 'ai_templates')
            ->where('active', '1')
            ->where('category_id', $cat['id'])
            ->order_by_asc('position')
            ->find_array();

        $custom_templates = ORM::for_table($config['db']['pre'] . 'ai_custom_templates')
            ->where('active', '1')
            ->where('category_id', $cat['id'])
            ->order_by_asc('position')
            ->find_array();

        $cats[$key]['templates'] = array_merge($templates, $custom_templates);
    }

    return $cats;
}

/**
 * Get api key
 *
 * @return array|mixed|null
 */
function get_api_key()
{
    global $config;

    $id = get_option('open_ai_api_key');
    $orm = ORM::for_table($config['db']['pre'] . 'api_keys')
        ->where('type', 'openai')
        ->where('active', '1');
    if ($id == 'random') {
        $data = $orm->find_array();
        $result = $data[array_rand($data)];
    } else {
        $result = $orm->find_one($id);
    }
    return !empty($result['api_key']) ? $result['api_key'] : null;

}

/**
 * Get api key for image generation
 *
 * @return array|mixed|null
 */
function get_image_api_key($type)
{
    global $config;

    $id = get_option('ai_image_api_key');
    $orm = ORM::for_table($config['db']['pre'] . 'api_keys')
        ->where('active', '1');
    if ($id == 'random') {
        $orm->where('type', $type);

        $data = $orm->find_array();
        $result = $data[array_rand($data)];
    } else {
        $result = $orm->find_one($id);
    }
    return !empty($result['api_key']) ? $result['api_key'] : null;
}

/**
 * Get api proxy
 *
 * @return string|null
 */
function get_api_proxy()
{
    global $config;

    $proxies = get_option('ai_proxies');
    if (!empty($proxies)) {
        $proxies = explode(',', $proxies);
        return $proxies[array_rand($proxies)];
    }
    return null;
}

/**
 * Get AI language
 *
 * @return array|string
 */
function get_ai_languages($key = null)
{

    $languages = get_option("ai_languages");
    $languages = explode(',', $languages);
    $languages = array_map('trim', $languages);

    /*$languages = array(
        'ar' => __('Arabic'),
        'zh' => __('Chinese'),
        'da' => __('Danish'),
        'nl' => __('Dutch'),
        'en' => __('English'),
        'fr' => __('French'),
        'de' => __('German'),
        'he' => __('Hebrew'),
        'hi' => __('Hindi'),
        'id' => __('Indonesian'),
        'it' => __('Italian'),
        'ja' => __('Japanese'),
        'pl' => __('Polish'),
        'ro' => __('Romanian'),
        'ru' => __('Russian'),
        'es' => __('Spanish'),
        'sv' => __('Swedish'),
        'tr' => __('Turkish'),
        'vi' => __('Vietnamese'),
    );*/

    if (!is_null($key))
        return $languages[$key];
    else
        return $languages;
}


/**
 * Get a list of OpenAI models
 *
 * @return array
 */
function get_opeai_models()
{
    return array(
        'text-ada-001' => __('Ada (Simple & Fastest)'),
        'text-babbage-001' => __('Babbage (Moderate)'),
        'text-curie-001' => __('Curie (Good)'),
        'text-davinci-003' => __('Davinci (Most Expensive & Powerful)'),
        'gpt-3.5-turbo' => __('ChatGPT 3.5'),
        'gpt-4' => __('ChatGPT 4 (Beta)'),
    );
}

/**
 * Check bad words
 *
 * @param $text
 * @return bool
 */
function check_bad_words($text)
{
    $bad_words = get_option("bad_words");
    $bad_words = explode(',', $bad_words);
    $bad_words = array_map('trim', $bad_words);

    foreach ($bad_words as $word) {
        // Search for the word in string
        if (strpos(mb_strtolower($text), mb_strtolower($word)) !== false) {
            return $word;
        }
    }

    return false;
}

/**
 * Print live chat js code
 */
function print_live_chat_code()
{
    if (get_option('enable_live_chat')) {

        if (get_option('tawkto_membership')) {
            // check membership
            $settings = get_user_membership_settings();
            if (!$settings['live_chat'])
                return;
        }

        $chat_link = get_option('tawkto_chat_link');
        $chat_link = str_replace('https://tawk.to/chat/', '', $chat_link);
        ?>
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
            var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
            <?php if(checkloggedin()){
            // add logged in user's data
            $user_data = get_user_data($_SESSION['user']['username']);
            ?>
            Tawk_API.visitor = {
                name: <?php _esc(json_encode($user_data['name'])) ?>,
                email: <?php _esc(json_encode($user_data['email'])) ?>
            };
            <?php } ?>
            (function () {
                var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
                s1.async = true;
                s1.src = 'https://embed.tawk.to/<?php _esc($chat_link) ?>';
                s1.charset = 'UTF-8';
                s1.setAttribute('crossorigin', '*');
                s0.parentNode.insertBefore(s1, s0);
            })();
        </script>
        <!--End of Tawk.to Script-->
        <?php
    }
}

/**
 * Get custom api error messages
 *
 * @param $http_response
 * @param $api
 * @return string
 */
function get_api_error_message($http_response){
    switch ($http_response){
        case 400:
            return __('API Error: The requested data is not valid for the API request.');
        case 401:
            return __('API Error: The API key is missing or invalid.');
        case 403:
            return __('API Error: You lack the necessary permissions to perform this action.');
        case 404:
            return __('API Error: The requested resource was not found.');
        case 429:
            return __('API Error: You are sending requests too quickly or you exceeded your current quota.');
        case 500:
            return __('API Error: The server had an error while processing your request, please try again.');
        default:
            return __('Unexpected error, please try again.');

    }
}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_blog_idea_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the ideas must be:\n" . $tone . "\n\n";
    }

    return $lang_text . "Write interesting blog ideas about:\n\n" . $description . "\n\n" . $tone_text;

}


/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_blog_intros_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the intro must be:\n" . $tone . "\n\n";
    }

    return $lang_text . "Write an interesting blog post intro about:\n\n" . $description . "\n\n Blog post title:\n" . $title . "\n\n" . $tone_text;
}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_blog_titles_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the titles must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Generate 10 catchy blog titles for:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_blog_section_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the paragraphs must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a full blog section with at least 5 large paragraphs about:\n\n" . $title . "\n\n Split by subheadings:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_blog_conclusion_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the conclusion must be:\n" . $tone . "\n\n";
    }

    return $lang_text . "Write a blog article conclusion for:\n\n" . $description . "\n\n Blog article title:\n" . $title . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_article_writer_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the article must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a complete article on this topic:\n\n" . $title . "\n\nUse following keywords in the article:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $keywords
 * @param $language
 * @param $tone
 * @return string
 */
function create_article_rewriter_prompt($description, $keywords, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the article must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Improve and rewrite this article in a creative and smart way:\n\n" . $description . "\n\nUse following keywords in the article:\n" . $keywords . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_article_outlines_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the outlines must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write 10 short and simple article outlines about:\n\n" . $description . "\n\nBlog article title:\n" . $title . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_talking_points_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the points must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write short, simple and informative talking points for:\n\n" . $title . "\n\nAnd also similar talking points for subheadings:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $keywords
 * @param $language
 * @param $tone
 * @return string
 */
function create_paragraph_writer_prompt($description, $keywords, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the paragraphs must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write 3 perfectly structured and meaningful paragraphs about:\n\n" . $description . "\n\nUse following keywords in the paragraphs:\n" . $keywords . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $keywords
 * @param $language
 * @param $tone
 * @return string
 */
function create_content_rephrase_prompt($description, $keywords, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the content must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Rephrase this content in a smart way:\n\n" . $description . "\n\nUse following keywords in the content:\n" . $keywords . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_facebook_ads_prompt($title, $description, $audience, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the ad must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a creative ad for the following product to run on Facebook aimed at:\n\n" . $audience . "\n\n Product name:\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_facebook_ads_headlines_prompt($title, $description, $audience, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the ad must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write 10 catchy and convincing headlines for the following product to run on Facebook ad aimed at:\n\n" . $audience . "\n\n Product name:\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_google_ads_titles_prompt($title, $description, $audience, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the titles must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write 10 interesting titles for Google ads of the following product aimed at:\n\n" . $audience . "\n\n Product name:\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text . "\n\n Title's length must be 30 characters\n\n";

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_google_ads_descriptions_prompt($title, $description, $audience, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the description must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write an interesting description for the Google ad of the following product aimed at:\n\n" . $audience . "\n\n Product name:\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text;
}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_linkedin_ads_headlines_prompt($title, $description, $audience, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the ads must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write 10 catchy headlines for the LinkedIn ads of the following product aimed at:\n\n" . $audience . "\n\n Product name:\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text;
}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_linkedin_ads_descriptions_prompt($title, $description, $audience, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the description must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a professional and eye-catching description for the LinkedIn ads of the following product aimed at:\n\n" . $audience . "\n\n Product name:\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_app_sms_notifications_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the messages must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Generate 10 eye catching notification messages about:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $keywords
 * @param $language
 * @param $tone
 * @return string
 */
function create_text_extender_prompt($description, $keywords, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the content must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Improve and extend this content:\n\n" . $description . "\n\nUse following keywords in the content:\n" . $keywords . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_content_shorten_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the summery must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Summarize this text in a short concise way:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_quora_answers_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the answer must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a detailed answer for Quora of this question:\n\n" . $title . "\n\nUse this content for more information:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_summarize_2nd_grader_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the summery must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Summarize this for a second-grade student:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $audience
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_stories_prompt($audience, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the story must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write an engaging and interesting story about:\n\n" . $description . "\n\n Audience of the story must be:\n" . $audience . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_bullet_point_answers_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the answer must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a detailed answer with bullet points:\n\n" . $description . "\n\n" . $tone_text;
}

/**
 * Create prompt
 *
 * @param $keyword
 * @param $language
 * @param $tone
 * @return string
 */
function create_definition_prompt($keyword, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the meaning must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "What is the meaning of:\n\n" . $keyword . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_answers_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the answer must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a long and detailed answer of:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_questions_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the questions must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Create 10 engaging questions from this paragraph:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_passive_active_voice_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the sentence must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Convert this passive voice sentence into active voice:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_pros_cons_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the pros and cons must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write pros and cons based on the following description:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $keywords
 * @param $language
 * @param $tone
 * @return string
 */
function create_rewrite_with_keywords_prompt($description, $keywords, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the content must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Improve and rewrite this content in a smart way:\n\n" . $description . "\n\nMust use following keywords in the content:\n" . $keywords . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_emails_prompt($recipient, $recipient_position, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the email must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write an engaging email about:\n\n" . $description . "\n\n Recipient:\n" . $recipient . "\n\n Recipient Position:\n" . $recipient_position . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_emails_v2_prompt($from, $to, $goal, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the email must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write an engaging email about:\n\n" . $description . "\n\n From:\n" . $from . "\n\n To:\n" . $to . "\n\n Main Goal of this email:\n" . $goal . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $title
 * @param $language
 * @param $tone
 * @return string
 */
function create_email_subject_lines_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the subject must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write 10 catchy email subject lines for this product:\n\n" . $title . "\n\nProduct Description:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $title
 * @param $language
 * @param $tone
 * @return string
 */
function create_startup_name_generator_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the names must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Generate 10 creative, and catchy company names about:\n\n" . $description . "\n\nUse the following keywords:\n" . $title . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $title
 * @param $platform
 * @param $language
 * @param $tone
 * @return string
 */
function create_company_bios_prompt($title, $description, $platform, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the bio must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a short and cool bio for " . ucfirst($platform) . "\n\nCompany Name:\n" . $title . "\n\nCompany Information:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $title
 * @param $language
 * @param $tone
 * @return string
 */
function create_company_mission_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the statement must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a clear and concise statement of the company's goals and purpose, Company Name:\n" . $title . "\n\nCompany Information:\n" . $description . "\n\n" . $tone_text;
}

/**
 * Create prompt
 *
 * @param $description
 * @param $title
 * @param $language
 * @param $tone
 * @return string
 */
function create_company_vision_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the vision must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a vision that attracts the right people and customers. \n\nCompany Name:\n" . $title . "\n\nCompany Information:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $keywords
 * @param $language
 * @param $tone
 * @return string
 */
function create_product_name_generator_prompt($description, $keywords, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the names must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Create 10 creative product names about:\n" . $description . "\n\nUse the following keywords:\n" . $keywords . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_product_descriptions_prompt($title, $description, $audience, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the description must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a detailed description of the following product aimed at:\n\n" . $audience . "\n\n Product name:\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_amazon_product_titles_prompt($title, $description, $audience, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the titles must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write 10 unique product titles to gain more sells on Amazon of the following product aimed at:\n\n" . $audience . "\n\n Product name:\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_amazon_product_descriptions_prompt($title, $description, $audience, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the description must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a interesting product description to gain more sells on Amazon of the following product aimed at:\n\n" . $audience . "\n\n Product name:\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $audience
 * @param $language
 * @param $tone
 * @return string
 */
function create_amazon_product_features_prompt($title, $description, $audience, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the features must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write 10 advantages and features to gain more sells on Amazon of the following product aimed at:\n\n" . $audience . "\n\n Product name:\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_social_post_personal_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the post must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a social media post for personal account about:\n\n" . $description . "\n\n" . $tone_text;

}


/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $information
 * @param $language
 * @param $tone
 * @return string
 */
function create_social_post_business_prompt($title, $information, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the post must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a social media post for company account about:\n\n" . $description . "\n\n Company name:\n" . $title . "\n\n Company Information:\n" . $information . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_instagram_captions_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the caption must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write an interesting caption for an Instagram post about:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_instagram_hashtags_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the hashtags must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write trendy hashtags for an Instagram post about:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_twitter_tweets_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the tweet must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write a trending tweet for a Twitter post about:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_youtube_titles_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the titles must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write 10 catchy titles for a Youtube video about:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_youtube_descriptions_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the description must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write an interesting description for a Youtube video about:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_youtube_outlines_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the outlines must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write interesting outlines for a Youtube video about:\n\n" . $description . "\n\n" . $tone_text;

}


/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_linkedin_posts_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the posts must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write inspiring posts for LinkedIn about:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_tiktok_video_scripts_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the ideas must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write 10 viral ideas for a short video about:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $keywords
 * @param $language
 * @param $tone
 * @return string
 */
function create_meta_tags_blog_prompt($title, $description, $keywords, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the generated text must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write SEO meta title and description for a blog post about:\n\n" . $description . "\n\n Blog title:\n" . $title . "\n\n Seed Words:\n" . $keywords . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $description
 * @param $keywords
 * @param $language
 * @param $tone
 * @return string
 */
function create_meta_tags_homepage_prompt($title, $description, $keywords, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the generated text must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write SEO meta title and description for a website about:\n" . $description . "\n\nWebsite Name:\n" . $title . "\n\nSeed Words:\n" . $keywords . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $company_name
 * @param $title
 * @param $description
 * @param $keywords
 * @param $language
 * @param $tone
 * @return string
 */
function create_meta_tags_product_prompt($company_name, $title, $description, $keywords, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the generated text must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write SEO meta title and description for a product page about:\n\n" . $description . "\n\n Product Title:\n" . $title . "\n\n Company Name:\n" . $company_name . "\n\n Seed Words:\n" . $keywords . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_tone_changer_prompt($description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone :\n" . $tone . "\n\n";
    }
    return $lang_text . "Change the tone of voice of this text:\n\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $title
 * @param $genre
 * @param $language
 * @param $tone
 * @return string
 */
function create_song_lyrics_prompt($title, $genre, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the lyrics must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Write unique song lyrics about:\n" . $title . "\n\nGenre of the song must be:\n" . $genre . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $language
 * @param $tone
 * @return string
 */
function create_translate_prompt($description, $language, $tone)
{

    return "Translate this into " . get_ai_languages($language) . " with the $tone tone of voice:\n\n" . $description . "\n\n";
}

/**
 * Create prompt
 *
 * @param $description
 * @param $title
 * @param $language
 * @param $tone
 * @return string
 */
function create_faqs_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the questions must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Generate list of 10 frequently asked questions based on description:\n\n" . $description . "\n\n Product name:\n" . $title . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $title
 * @param $question
 * @param $language
 * @param $tone
 * @return string
 */
function create_faq_answers_prompt($title, $description, $question, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the answers must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Generate creative 5 answers to question:\n\n" . $question . "\n\n Product name:\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text;

}

/**
 * Create prompt
 *
 * @param $description
 * @param $title
 * @param $language
 * @param $tone
 * @return string
 */
function create_testimonials_reviews_prompt($title, $description, $language, $tone)
{
    $lang_text = '';
    $tone_text = '';
    if (!is_null($language)) {
        $lang_text = "Provide response in " . get_ai_languages($language) . " language.\n\n ";
    }
    if ($tone) {
        $tone_text = "Tone of voice of the customer review must be:\n" . $tone . "\n\n";
    }
    return $lang_text . "Create 5 creative customer reviews for a product. Product name:\n\n" . $title . "\n\n Product description:\n" . $description . "\n\n" . $tone_text;
}


/**
 * Cron Jobs
 *
 * @return false|void
 */
function run_cron_job()
{

    global $config, $lang, $link;
    $pdo = ORM::get_db();
    $cron_time = isset($config['cron_time']) ? $config['cron_time'] : 0;
    $cron_exec_time = isset($config['cron_exec_time']) ? $config['cron_exec_time'] : "300";

    if ((time() - $cron_exec_time) > $cron_time) {

        ignore_user_abort(1);
        @set_time_limit(0);

        $start_time = time();
        update_option('cron_time', time());

        /**
         * START REMOVE OLD PENDING TRANSACTIONS IN 3 Days
         *
         */
        $expiry_time = time() - 259200;
        ORM::for_table($config['db']['pre'] . 'transaction')
            ->where_any_is(array(
                array('status' => 'pending', 'transaction_time' => $expiry_time)), array('transaction_time' => '<'))
            ->delete_many();
        // END REMOVE OLD PENDING TRANSACTIONS

        /**
         * START REMOVE EXPIRED UPGRADES IN 24 Hours
         *
         */
        $expire_membership = 0;
        $expiry_time = time() - 86400;


        $result = ORM::for_table($config['db']['pre'] . 'upgrades')
            ->select_many('upgrade_id', 'user_id')
            ->where_lt('upgrade_expires', $expiry_time)
            ->find_many();
        foreach ($result as $info) {
            $person_count = ORM::for_table($config['db']['pre'] . 'user')
                ->where('id', $info['user_id'])
                ->count();
            if ($person_count) {
                $person = ORM::for_table($config['db']['pre'] . 'user')->find_one($info['user_id']);
                $person->group_id = 'free';
                $person->save();
            }
            ORM::for_table($config['db']['pre'] . 'upgrades')
                ->where_equal('upgrade_id', $info['upgrade_id'])
                ->delete_many();

            $expire_membership++;
        }
        // END REMOVE EXPIRED UPGRADES

        $end_time = (time() - $start_time);

        $cron_details = "Expire membership: " . $expire_membership . "<br>";
        $cron_details .= "<br>";
        $cron_details .= "Cron Took: " . $end_time . " seconds";

        log_adm_action('Cron Run', $cron_details);
    } else {
        return false;
    }
}

run_cron_job();
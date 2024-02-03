<?php

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
        'text-ada-001' => __('Ada (Deprecated)'),
        'text-babbage-001' => __('Babbage (Deprecated)'),
        'text-curie-001' => __('Curie (Deprecated)'),
        'text-davinci-003' => __('Davinci (Deprecated)'),
        'gpt-3.5-turbo' => __('ChatGPT 3.5'),
        'gpt-3.5-turbo-16k' => __('ChatGPT 3.5 16k'),
        'gpt-4' => __('ChatGPT 4'),
        'gpt-4-32k' => __('ChatGPT 4 32k'),
    );
}

/**
 * Get a list of OpenAI Chat models
 *
 * @return array
 */
function get_opeai_chat_models()
{
    return array(
        'gpt-3.5-turbo' => __('ChatGPT 3.5'),
        'gpt-3.5-turbo-16k' => __('ChatGPT 3.5 16k'),
        'gpt-4' => __('ChatGPT 4'),
        'gpt-4-32k' => __('ChatGPT 4 32k'),
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
        /* Search for the word in string */
        $regex_start = '/(^|\b|\s)';
        $regex_end = '(\b|\s|$)/i';
        if (preg_match($regex_start . '(' . preg_quote(mb_strtolower($word), '/') . ')' . $regex_end, mb_strtolower($text))) {
            return $word;
        }
    }

    return false;
}

/**
 * Get custom api error messages
 *
 * @param $http_response
 * @param $api
 * @return string
 */
function get_api_error_message($http_response)
{
    switch ($http_response) {
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
 * @param $ai_message
 * @return array|string|string[]|null
 */
function filter_ai_response($ai_message)
{
    if(!class_exists("Parsedown")){
        include ROOTPATH. '/includes/lib/parsedown/Parsedown.php';
    }

    $ai_message = Parsedown::instance()->text($ai_message);

    return str_replace('</code></pre>', '</code><button class="copy-ai-code" onclick="copyAICode(this)"><svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> <span class="label-copy-code">' . __('Copy') . '</span></button></pre>', $ai_message);
}

/**
 * @return array[]
 */
function get_ai_voices()
{
    return array (
        'af-ZA' =>
            array (
                'language_code' => 'af-ZA',
                'language' => 'Afrikaans (South Africa)',
                'voices' =>
                    array (
                        'af-ZA-Standard-A' =>
                            array (
                                'voice' => 'Iminathi',
                                'voice_id' => 'af-ZA-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
        'ar-XA' =>
            array (
                'language_code' => 'ar-XA',
                'language' => 'Arabic',
                'voices' =>
                    array (
                        'ar-aws-std-zeina' =>
                            array (
                                'voice' => 'Zeina',
                                'voice_id' => 'ar-aws-std-zeina',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'ar-XA-Standard-A' =>
                            array (
                                'voice' => 'Amina',
                                'voice_id' => 'ar-XA-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ar-XA-Standard-B' =>
                            array (
                                'voice' => 'Ahmad',
                                'voice_id' => 'ar-XA-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ar-XA-Standard-C' =>
                            array (
                                'voice' => 'Abdulla',
                                'voice_id' => 'ar-XA-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ar-XA-Standard-D' =>
                            array (
                                'voice' => 'Saniya',
                                'voice_id' => 'ar-XA-Standard-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ar-XA-Wavenet-A' =>
                            array (
                                'voice' => 'Amina',
                                'voice_id' => 'ar-XA-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ar-XA-Wavenet-B' =>
                            array (
                                'voice' => 'Ahmad',
                                'voice_id' => 'ar-XA-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ar-XA-Wavenet-C' =>
                            array (
                                'voice' => 'Abdulla',
                                'voice_id' => 'ar-XA-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ar-XA-Wavenet-D' =>
                            array (
                                'voice' => 'Saniya',
                                'voice_id' => 'ar-XA-Wavenet-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'bn-IN' =>
            array (
                'language_code' => 'bn-IN',
                'language' => 'Bengali (India)',
                'voices' =>
                    array (
                        'bn-IN-Standard-A' =>
                            array (
                                'voice' => 'Aarya',
                                'voice_id' => 'bn-IN-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'bn-IN-Standard-B' =>
                            array (
                                'voice' => 'Achintya',
                                'voice_id' => 'bn-IN-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'bn-IN-Wavenet-A' =>
                            array (
                                'voice' => 'Aarya',
                                'voice_id' => 'bn-IN-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'bn-IN-Wavenet-B' =>
                            array (
                                'voice' => 'Achintya',
                                'voice_id' => 'bn-IN-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'bg-BG' =>
            array (
                'language_code' => 'bg-BG',
                'language' => 'Bulgarian (Bulgaria)',
                'voices' =>
                    array (
                        'bg-bg-Standard-A' =>
                            array (
                                'voice' => 'Maria',
                                'voice_id' => 'bg-bg-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
        'ca-ES' =>
            array (
                'language_code' => 'ca-ES',
                'language' => 'Catalan (Spain)',
                'voices' =>
                    array (
                        'ca-es-Standard-A' =>
                            array (
                                'voice' => 'Martina',
                                'voice_id' => 'ca-es-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
        'yue-HK' =>
            array (
                'language_code' => 'yue-HK',
                'language' => 'Chinese (Hong Kong)',
                'voices' =>
                    array (
                        'yue-HK-Standard-A' =>
                            array (
                                'voice' => 'Sonia',
                                'voice_id' => 'yue-HK-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'yue-HK-Standard-B' =>
                            array (
                                'voice' => 'Wing',
                                'voice_id' => 'yue-HK-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'yue-HK-Standard-C' =>
                            array (
                                'voice' => 'Bonnie',
                                'voice_id' => 'yue-HK-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'yue-HK-Standard-D' =>
                            array (
                                'voice' => 'Chi',
                                'voice_id' => 'yue-HK-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
        'cmn-CN' =>
            array (
                'language_code' => 'cmn-CN',
                'language' => 'Chinese (Mandarin)',
                'voices' =>
                    array (
                        'cn-aws-std-zhiyu' =>
                            array (
                                'voice' => 'Zhiyu',
                                'voice_id' => 'cn-aws-std-zhiyu',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'cmn-CN-Standard-A' =>
                            array (
                                'voice' => 'Changying',
                                'voice_id' => 'cmn-CN-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'cmn-CN-Standard-B' =>
                            array (
                                'voice' => 'Boqin',
                                'voice_id' => 'cmn-CN-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'cmn-CN-Standard-C' =>
                            array (
                                'voice' => 'Delun',
                                'voice_id' => 'cmn-CN-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'cmn-CN-Standard-D' =>
                            array (
                                'voice' => 'Tyantyan',
                                'voice_id' => 'cmn-CN-Standard-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'cmn-CN-Wavenet-A' =>
                            array (
                                'voice' => 'Changying',
                                'voice_id' => 'cmn-CN-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'cmn-CN-Wavenet-B' =>
                            array (
                                'voice' => 'Boqin',
                                'voice_id' => 'cmn-CN-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'cmn-CN-Wavenet-C' =>
                            array (
                                'voice' => 'Delun',
                                'voice_id' => 'cmn-CN-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'cmn-CN-Wavenet-D' =>
                            array (
                                'voice' => 'Tyantyan',
                                'voice_id' => 'cmn-CN-Wavenet-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'cmn-TW' =>
            array (
                'language_code' => 'cmn-TW',
                'language' => 'Chinese (Taiwan)',
                'voices' =>
                    array (
                        'cmn-TW-Standard-A' =>
                            array (
                                'voice' => 'Sunyo',
                                'voice_id' => 'cmn-TW-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'cmn-TW-Standard-B' =>
                            array (
                                'voice' => 'Chen',
                                'voice_id' => 'cmn-TW-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'cmn-TW-Standard-C' =>
                            array (
                                'voice' => 'Xianbao',
                                'voice_id' => 'cmn-TW-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'cmn-TW-Wavenet-A' =>
                            array (
                                'voice' => 'Sunyo',
                                'voice_id' => 'cmn-TW-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'cmn-TW-Wavenet-B' =>
                            array (
                                'voice' => 'Chen',
                                'voice_id' => 'cmn-TW-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'cmn-TW-Wavenet-C' =>
                            array (
                                'voice' => 'Xianbao',
                                'voice_id' => 'cmn-TW-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'cs-CZ' =>
            array (
                'language_code' => 'cs-CZ',
                'language' => 'Czech (Czech Republic)',
                'voices' =>
                    array (
                        'cs-CZ-Standard-A' =>
                            array (
                                'voice' => 'Eliska',
                                'voice_id' => 'cs-CZ-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'cs-CZ-Wavenet-A' =>
                            array (
                                'voice' => 'Eliska',
                                'voice_id' => 'cs-CZ-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'da-DK' =>
            array (
                'language_code' => 'da-DK',
                'language' => 'Danish (Denmark)',
                'voices' =>
                    array (
                        'dk-aws-std-naja' =>
                            array (
                                'voice' => 'Naja',
                                'voice_id' => 'dk-aws-std-naja',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'dk-aws-std-mads' =>
                            array (
                                'voice' => 'Mads',
                                'voice_id' => 'dk-aws-std-mads',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'da-DK-Standard-A' =>
                            array (
                                'voice' => 'Ida',
                                'voice_id' => 'da-DK-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'da-DK-Standard-C' =>
                            array (
                                'voice' => 'Morten',
                                'voice_id' => 'da-DK-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'da-DK-Standard-D' =>
                            array (
                                'voice' => 'Freja',
                                'voice_id' => 'da-DK-Standard-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'da-DK-Standard-E' =>
                            array (
                                'voice' => 'Josefine',
                                'voice_id' => 'da-DK-Standard-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'da-DK-Wavenet-A' =>
                            array (
                                'voice' => 'Ida',
                                'voice_id' => 'da-DK-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'da-DK-Wavenet-C' =>
                            array (
                                'voice' => 'Morten',
                                'voice_id' => 'da-DK-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'da-DK-Wavenet-D' =>
                            array (
                                'voice' => 'Freja',
                                'voice_id' => 'da-DK-Wavenet-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'da-DK-Wavenet-E' =>
                            array (
                                'voice' => 'Josefine',
                                'voice_id' => 'da-DK-Wavenet-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'nl-NL' =>
            array (
                'language_code' => 'nl-NL',
                'language' => 'Dutch (Netherlands)',
                'voices' =>
                    array (
                        'nl-aws-std-lotte' =>
                            array (
                                'voice' => 'Lotte',
                                'voice_id' => 'nl-aws-std-lotte',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'nl-aws-std-ruben' =>
                            array (
                                'voice' => 'Ruben',
                                'voice_id' => 'nl-aws-std-ruben',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'nl-NL-Standard-A' =>
                            array (
                                'voice' => 'Mila',
                                'voice_id' => 'nl-NL-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'nl-NL-Standard-B' =>
                            array (
                                'voice' => 'Arend',
                                'voice_id' => 'nl-NL-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'nl-NL-Standard-C' =>
                            array (
                                'voice' => 'Christiaan',
                                'voice_id' => 'nl-NL-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'nl-NL-Standard-D' =>
                            array (
                                'voice' => 'Lotte',
                                'voice_id' => 'nl-NL-Standard-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'nl-NL-Standard-E' =>
                            array (
                                'voice' => 'Fenna',
                                'voice_id' => 'nl-NL-Standard-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'nl-NL-Wavenet-A' =>
                            array (
                                'voice' => 'Mila',
                                'voice_id' => 'nl-NL-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'nl-NL-Wavenet-B' =>
                            array (
                                'voice' => 'Arend',
                                'voice_id' => 'nl-NL-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'nl-NL-Wavenet-C' =>
                            array (
                                'voice' => 'Christiaan',
                                'voice_id' => 'nl-NL-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'nl-NL-Wavenet-D' =>
                            array (
                                'voice' => 'Lotte',
                                'voice_id' => 'nl-NL-Wavenet-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'nl-NL-Wavenet-E' =>
                            array (
                                'voice' => 'Fenna',
                                'voice_id' => 'nl-NL-Wavenet-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'en-AU' =>
            array (
                'language_code' => 'en-AU',
                'language' => 'English (Australia)',
                'voices' =>
                    array (
                        'au-aws-std-nicole' =>
                            array (
                                'voice' => 'Nicole',
                                'voice_id' => 'au-aws-std-nicole',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'au-aws-std-russell' =>
                            array (
                                'voice' => 'Russell',
                                'voice_id' => 'au-aws-std-russell',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'au-aws-nrl-olivia' =>
                            array (
                                'voice' => 'Olivia',
                                'voice_id' => 'au-aws-nrl-olivia',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'en-AU-Standard-A' =>
                            array (
                                'voice' => 'Charlotte',
                                'voice_id' => 'en-AU-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-AU-Standard-B' =>
                            array (
                                'voice' => 'Noah',
                                'voice_id' => 'en-AU-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-AU-Standard-C' =>
                            array (
                                'voice' => 'Amelia',
                                'voice_id' => 'en-AU-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-AU-Standard-D' =>
                            array (
                                'voice' => 'Oliver',
                                'voice_id' => 'en-AU-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-AU-Wavenet-A' =>
                            array (
                                'voice' => 'Charlotte',
                                'voice_id' => 'en-AU-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-AU-Wavenet-B' =>
                            array (
                                'voice' => 'Noah',
                                'voice_id' => 'en-AU-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-AU-Wavenet-C' =>
                            array (
                                'voice' => 'Amelia',
                                'voice_id' => 'en-AU-Wavenet-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-AU-Wavenet-D' =>
                            array (
                                'voice' => 'Oliver',
                                'voice_id' => 'en-AU-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'en-IN' =>
            array (
                'language_code' => 'en-IN',
                'language' => 'English (India)',
                'voices' =>
                    array (
                        'in-aws-std-aditi' =>
                            array (
                                'voice' => 'Aditi',
                                'voice_id' => 'in-aws-std-aditi',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'in-aws-std-raveena' =>
                            array (
                                'voice' => 'Raveena',
                                'voice_id' => 'in-aws-std-raveena',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'en-IN-Standard-A' =>
                            array (
                                'voice' => 'Saanvi',
                                'voice_id' => 'en-IN-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-IN-Standard-B' =>
                            array (
                                'voice' => 'Sai',
                                'voice_id' => 'en-IN-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-IN-Standard-C' =>
                            array (
                                'voice' => 'Veer',
                                'voice_id' => 'en-IN-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-IN-Standard-D' =>
                            array (
                                'voice' => 'Prisha',
                                'voice_id' => 'en-IN-Standard-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-IN-Wavenet-A' =>
                            array (
                                'voice' => 'Saanvi',
                                'voice_id' => 'en-IN-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-IN-Wavenet-B' =>
                            array (
                                'voice' => 'Sai',
                                'voice_id' => 'en-IN-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-IN-Wavenet-C' =>
                            array (
                                'voice' => 'Veer',
                                'voice_id' => 'en-IN-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-IN-Wavenet-D' =>
                            array (
                                'voice' => 'Prisha',
                                'voice_id' => 'en-IN-Wavenet-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'en-GB' =>
            array (
                'language_code' => 'en-GB',
                'language' => 'English (UK)',
                'voices' =>
                    array (
                        'gb-aws-std-amy' =>
                            array (
                                'voice' => 'Amy',
                                'voice_id' => 'gb-aws-std-amy',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'gb-aws-std-emma' =>
                            array (
                                'voice' => 'Emma',
                                'voice_id' => 'gb-aws-std-emma',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'gb-aws-std-brian' =>
                            array (
                                'voice' => 'Brian',
                                'voice_id' => 'gb-aws-std-brian',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'gb-aws-nrl-amy' =>
                            array (
                                'voice' => 'Amy',
                                'voice_id' => 'gb-aws-nrl-amy',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'gb-aws-nrl-emma' =>
                            array (
                                'voice' => 'Emma',
                                'voice_id' => 'gb-aws-nrl-emma',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'gb-aws-nrl-brian' =>
                            array (
                                'voice' => 'Brian',
                                'voice_id' => 'gb-aws-nrl-brian',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'en-GB-Standard-A' =>
                            array (
                                'voice' => 'Poppy',
                                'voice_id' => 'en-GB-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-GB-Standard-B' =>
                            array (
                                'voice' => 'Charles',
                                'voice_id' => 'en-GB-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-GB-Standard-C' =>
                            array (
                                'voice' => 'Elsie',
                                'voice_id' => 'en-GB-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-GB-Standard-D' =>
                            array (
                                'voice' => 'Harry',
                                'voice_id' => 'en-GB-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-GB-Standard-F' =>
                            array (
                                'voice' => 'Nancy',
                                'voice_id' => 'en-GB-Standard-F',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-GB-Wavenet-A' =>
                            array (
                                'voice' => 'Poppy',
                                'voice_id' => 'en-GB-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-GB-Wavenet-B' =>
                            array (
                                'voice' => 'Charles',
                                'voice_id' => 'en-GB-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-GB-Wavenet-C' =>
                            array (
                                'voice' => 'Elsie',
                                'voice_id' => 'en-GB-Wavenet-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-GB-Wavenet-D' =>
                            array (
                                'voice' => 'Harry',
                                'voice_id' => 'en-GB-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-GB-Wavenet-F' =>
                            array (
                                'voice' => 'Nancy',
                                'voice_id' => 'en-GB-Wavenet-F',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'en-US' =>
            array (
                'language_code' => 'en-US',
                'language' => 'English (USA)',
                'voices' =>
                    array (
                        'us-aws-std-ivy' =>
                            array (
                                'voice' => 'Ivy',
                                'voice_id' => 'us-aws-std-ivy',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'us-aws-std-joanna' =>
                            array (
                                'voice' => 'Joanna',
                                'voice_id' => 'us-aws-std-joanna',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'us-aws-std-kendra' =>
                            array (
                                'voice' => 'Kendra',
                                'voice_id' => 'us-aws-std-kendra',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'us-aws-std-kimberly' =>
                            array (
                                'voice' => 'Kimberly',
                                'voice_id' => 'us-aws-std-kimberly',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'us-aws-std-salli' =>
                            array (
                                'voice' => 'Salli',
                                'voice_id' => 'us-aws-std-salli',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'us-aws-std-joey' =>
                            array (
                                'voice' => 'Joey',
                                'voice_id' => 'us-aws-std-joey',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'us-aws-std-justin' =>
                            array (
                                'voice' => 'Justin',
                                'voice_id' => 'us-aws-std-justin',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'us-aws-std-matthew' =>
                            array (
                                'voice' => 'Matthew',
                                'voice_id' => 'us-aws-std-matthew',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'us-aws-nrl-ivy' =>
                            array (
                                'voice' => 'Ivy',
                                'voice_id' => 'us-aws-nrl-ivy',
                                'gender' => 'Female(child)',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'us-aws-nrl-joanna' =>
                            array (
                                'voice' => 'Joanna',
                                'voice_id' => 'us-aws-nrl-joanna',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'us-aws-nrl-kendra' =>
                            array (
                                'voice' => 'Kendra',
                                'voice_id' => 'us-aws-nrl-kendra',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'us-aws-nrl-kimberly' =>
                            array (
                                'voice' => 'Kimberly',
                                'voice_id' => 'us-aws-nrl-kimberly',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'us-aws-nrl-salli' =>
                            array (
                                'voice' => 'Salli',
                                'voice_id' => 'us-aws-nrl-salli',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'us-aws-nrl-joey' =>
                            array (
                                'voice' => 'Joey',
                                'voice_id' => 'us-aws-nrl-joey',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'us-aws-nrl-justin' =>
                            array (
                                'voice' => 'Justin',
                                'voice_id' => 'us-aws-nrl-justin',
                                'gender' => 'Male(child)',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'us-aws-nrl-kevin' =>
                            array (
                                'voice' => 'Kevin',
                                'voice_id' => 'us-aws-nrl-kevin',
                                'gender' => 'Male(child)',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'us-aws-nrl-matthew' =>
                            array (
                                'voice' => 'Matthew',
                                'voice_id' => 'us-aws-nrl-matthew',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'en-US-Standard-A' =>
                            array (
                                'voice' => 'Dick',
                                'voice_id' => 'en-US-Standard-A',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-US-Standard-B' =>
                            array (
                                'voice' => 'Bush',
                                'voice_id' => 'en-US-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-US-Standard-C' =>
                            array (
                                'voice' => 'Caroline',
                                'voice_id' => 'en-US-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-US-Standard-D' =>
                            array (
                                'voice' => 'Trump',
                                'voice_id' => 'en-US-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-US-Standard-E' =>
                            array (
                                'voice' => 'Suzan',
                                'voice_id' => 'en-US-Standard-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-US-Standard-F' =>
                            array (
                                'voice' => 'Kimberly',
                                'voice_id' => 'en-US-Standard-F',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-US-Standard-G' =>
                            array (
                                'voice' => 'Ellen',
                                'voice_id' => 'en-US-Standard-G',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-US-Standard-H' =>
                            array (
                                'voice' => 'Teresa',
                                'voice_id' => 'en-US-Standard-H',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-US-Standard-I' =>
                            array (
                                'voice' => 'Oscar',
                                'voice_id' => 'en-US-Standard-I',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-US-Standard-J' =>
                            array (
                                'voice' => 'Obama',
                                'voice_id' => 'en-US-Standard-J',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'en-US-Wavenet-A' =>
                            array (
                                'voice' => 'Dick',
                                'voice_id' => 'en-US-Wavenet-A',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-US-Wavenet-B' =>
                            array (
                                'voice' => 'Bush',
                                'voice_id' => 'en-US-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-US-Wavenet-C' =>
                            array (
                                'voice' => 'Caroline',
                                'voice_id' => 'en-US-Wavenet-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-US-Wavenet-D' =>
                            array (
                                'voice' => 'Trump',
                                'voice_id' => 'en-US-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-US-Wavenet-E' =>
                            array (
                                'voice' => 'Susan',
                                'voice_id' => 'en-US-Wavenet-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-US-Wavenet-F' =>
                            array (
                                'voice' => 'Kimberly',
                                'voice_id' => 'en-US-Wavenet-F',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-US-Wavenet-G' =>
                            array (
                                'voice' => 'Ellen',
                                'voice_id' => 'en-US-Wavenet-G',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-US-Wavenet-H' =>
                            array (
                                'voice' => 'Teresa',
                                'voice_id' => 'en-US-Wavenet-H',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-US-Wavenet-I' =>
                            array (
                                'voice' => 'Oscar',
                                'voice_id' => 'en-US-Wavenet-I',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'en-US-Wavenet-J' =>
                            array (
                                'voice' => 'Obama',
                                'voice_id' => 'en-US-Wavenet-J',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'en-GB-WLS' =>
            array (
                'language_code' => 'en-GB-WLS',
                'language' => 'English (Wales)',
                'voices' =>
                    array (
                        'wls-aws-std-matthew' =>
                            array (
                                'voice' => 'Geraint',
                                'voice_id' => 'wls-aws-std-matthew',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
        'fil-PH' =>
            array (
                'language_code' => 'fil-PH',
                'language' => 'Filipino (Philippines)',
                'voices' =>
                    array (
                        'fil-PH-Standard-A' =>
                            array (
                                'voice' => 'Princess',
                                'voice_id' => 'fil-PH-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fil-PH-Standard-B' =>
                            array (
                                'voice' => 'Andrea',
                                'voice_id' => 'fil-PH-Standard-B',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fil-PH-Standard-C' =>
                            array (
                                'voice' => 'Angel',
                                'voice_id' => 'fil-PH-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fil-PH-Standard-D' =>
                            array (
                                'voice' => 'Nathaniel',
                                'voice_id' => 'fil-PH-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fil-PH-Wavenet-A' =>
                            array (
                                'voice' => 'Princess',
                                'voice_id' => 'fil-PH-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'fil-PH-Wavenet-B' =>
                            array (
                                'voice' => 'Andrea',
                                'voice_id' => 'fil-PH-Wavenet-B',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'fil-PH-Wavenet-C' =>
                            array (
                                'voice' => 'Angel',
                                'voice_id' => 'fil-PH-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'fil-PH-Wavenet-D' =>
                            array (
                                'voice' => 'Nathaniel',
                                'voice_id' => 'fil-PH-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'fi-FI' =>
            array (
                'language_code' => 'fi-FI',
                'language' => 'Finnish (Finland)',
                'voices' =>
                    array (
                        'fi-FI-Standard-A' =>
                            array (
                                'voice' => 'Enni',
                                'voice_id' => 'fi-FI-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fi-FI-Wavenet-A' =>
                            array (
                                'voice' => 'Enni',
                                'voice_id' => 'fi-FI-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'fr-CA' =>
            array (
                'language_code' => 'fr-CA',
                'language' => 'French (Canada)',
                'voices' =>
                    array (
                        'ca-aws-std-chantal' =>
                            array (
                                'voice' => 'Chantal',
                                'voice_id' => 'ca-aws-std-chantal',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'ca-aws-nrl-gabrielle' =>
                            array (
                                'voice' => 'Gabrielle',
                                'voice_id' => 'ca-aws-nrl-gabrielle',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'fr-CA-Standard-A' =>
                            array (
                                'voice' => 'Emma',
                                'voice_id' => 'fr-CA-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fr-CA-Standard-B' =>
                            array (
                                'voice' => 'Thomas',
                                'voice_id' => 'fr-CA-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fr-CA-Standard-C' =>
                            array (
                                'voice' => 'Lea',
                                'voice_id' => 'fr-CA-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fr-CA-Standard-D' =>
                            array (
                                'voice' => 'William',
                                'voice_id' => 'fr-CA-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fr-CA-Wavenet-A' =>
                            array (
                                'voice' => 'Emma',
                                'voice_id' => 'fr-CA-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'fr-CA-Wavenet-B' =>
                            array (
                                'voice' => 'Thomas',
                                'voice_id' => 'fr-CA-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'fr-CA-Wavenet-C' =>
                            array (
                                'voice' => 'Lea',
                                'voice_id' => 'fr-CA-Wavenet-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'fr-CA-Wavenet-D' =>
                            array (
                                'voice' => 'William',
                                'voice_id' => 'fr-CA-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'fr-FR' =>
            array (
                'language_code' => 'fr-FR',
                'language' => 'French (France)',
                'voices' =>
                    array (
                        'fr-aws-std-celine' =>
                            array (
                                'voice' => 'Celine',
                                'voice_id' => 'fr-aws-std-celine',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'fr-aws-std-lea' =>
                            array (
                                'voice' => 'Lea',
                                'voice_id' => 'fr-aws-std-lea',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'fr-aws-std-mathieu' =>
                            array (
                                'voice' => 'Mathieu',
                                'voice_id' => 'fr-aws-std-mathieu',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'fr-FR-Standard-A' =>
                            array (
                                'voice' => 'Mathilde',
                                'voice_id' => 'fr-FR-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fr-FR-Standard-B' =>
                            array (
                                'voice' => 'Foucauld',
                                'voice_id' => 'fr-FR-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fr-FR-Standard-C' =>
                            array (
                                'voice' => 'Louise',
                                'voice_id' => 'fr-FR-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fr-FR-Standard-D' =>
                            array (
                                'voice' => 'Florent',
                                'voice_id' => 'fr-FR-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fr-FR-Standard-E' =>
                            array (
                                'voice' => 'Alice',
                                'voice_id' => 'fr-FR-Standard-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'fr-FR-Wavenet-A' =>
                            array (
                                'voice' => 'Mathilde',
                                'voice_id' => 'fr-FR-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'fr-FR-Wavenet-B' =>
                            array (
                                'voice' => 'Foucauld',
                                'voice_id' => 'fr-FR-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'fr-FR-Wavenet-C' =>
                            array (
                                'voice' => 'Louise',
                                'voice_id' => 'fr-FR-Wavenet-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'fr-FR-Wavenet-D' =>
                            array (
                                'voice' => 'Florent',
                                'voice_id' => 'fr-FR-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'fr-FR-Wavenet-E' =>
                            array (
                                'voice' => 'Alice',
                                'voice_id' => 'fr-FR-Wavenet-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'de-DE' =>
            array (
                'language_code' => 'de-DE',
                'language' => 'German (Germany)',
                'voices' =>
                    array (
                        'de-aws-std-marlene' =>
                            array (
                                'voice' => 'Marlene',
                                'voice_id' => 'de-aws-std-marlene',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'de-aws-std-vicki' =>
                            array (
                                'voice' => 'Vicki',
                                'voice_id' => 'de-aws-std-vicki',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'de-aws-std-hans' =>
                            array (
                                'voice' => 'Hans',
                                'voice_id' => 'de-aws-std-hans',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'de-DE-Standard-A' =>
                            array (
                                'voice' => 'Eva',
                                'voice_id' => 'de-DE-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'de-DE-Standard-B' =>
                            array (
                                'voice' => 'Wilhelm',
                                'voice_id' => 'de-DE-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'de-DE-Standard-C' =>
                            array (
                                'voice' => 'Hannah',
                                'voice_id' => 'de-DE-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'de-DE-Standard-D' =>
                            array (
                                'voice' => 'Alfred',
                                'voice_id' => 'de-DE-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'de-DE-Standard-E' =>
                            array (
                                'voice' => 'Werner',
                                'voice_id' => 'de-DE-Standard-E',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'de-DE-Standard-F' =>
                            array (
                                'voice' => 'Merkel',
                                'voice_id' => 'de-DE-Standard-F',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'de-DE-Wavenet-A' =>
                            array (
                                'voice' => 'Eva',
                                'voice_id' => 'de-DE-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'de-DE-Wavenet-B' =>
                            array (
                                'voice' => 'Wilhelm',
                                'voice_id' => 'de-DE-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'de-DE-Wavenet-C' =>
                            array (
                                'voice' => 'Hannah',
                                'voice_id' => 'de-DE-Wavenet-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'de-DE-Wavenet-D' =>
                            array (
                                'voice' => 'Alfred',
                                'voice_id' => 'de-DE-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'de-DE-Wavenet-E' =>
                            array (
                                'voice' => 'Werner',
                                'voice_id' => 'de-DE-Wavenet-E',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'de-DE-Wavenet-F' =>
                            array (
                                'voice' => 'Merkel',
                                'voice_id' => 'de-DE-Wavenet-F',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'el-GR' =>
            array (
                'language_code' => 'el-GR',
                'language' => 'Greek (Greece)',
                'voices' =>
                    array (
                        'el-GR-Standard-A' =>
                            array (
                                'voice' => 'Eleni',
                                'voice_id' => 'el-GR-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'el-GR-Wavenet-A' =>
                            array (
                                'voice' => 'Eleni',
                                'voice_id' => 'el-GR-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'gu-IN' =>
            array (
                'language_code' => 'gu-IN',
                'language' => 'Gujarati (India)',
                'voices' =>
                    array (
                        'gu-IN-Standard-A' =>
                            array (
                                'voice' => 'Inika',
                                'voice_id' => 'gu-IN-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'gu-IN-Standard-B' =>
                            array (
                                'voice' => 'Jhinuk',
                                'voice_id' => 'gu-IN-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'gu-IN-Wavenet-A' =>
                            array (
                                'voice' => 'Inika',
                                'voice_id' => 'gu-IN-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'gu-IN-Wavenet-B' =>
                            array (
                                'voice' => 'Jhinuk',
                                'voice_id' => 'gu-IN-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'hi-IN' =>
            array (
                'language_code' => 'hi-IN',
                'language' => 'Hindi (India)',
                'voices' =>
                    array (
                        'hi-aws-std-aditi' =>
                            array (
                                'voice' => 'Aditi',
                                'voice_id' => 'hi-aws-std-aditi',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'hi-IN-Standard-A' =>
                            array (
                                'voice' => 'Arunima',
                                'voice_id' => 'hi-IN-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'hi-IN-Standard-B' =>
                            array (
                                'voice' => 'Ayaan',
                                'voice_id' => 'hi-IN-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'hi-IN-Standard-C' =>
                            array (
                                'voice' => 'Keshav',
                                'voice_id' => 'hi-IN-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'hi-IN-Standard-D' =>
                            array (
                                'voice' => 'Bhavna',
                                'voice_id' => 'hi-IN-Standard-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'hi-IN-Wavenet-A' =>
                            array (
                                'voice' => 'Arunima',
                                'voice_id' => 'hi-IN-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'hi-IN-Wavenet-B' =>
                            array (
                                'voice' => 'Ayaan',
                                'voice_id' => 'hi-IN-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'hi-IN-Wavenet-C' =>
                            array (
                                'voice' => 'Keshav',
                                'voice_id' => 'hi-IN-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'hi-IN-Wavenet-D' =>
                            array (
                                'voice' => 'Bhavna',
                                'voice_id' => 'hi-IN-Wavenet-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'hu-HU' =>
            array (
                'language_code' => 'hu-HU',
                'language' => 'Hungarian (Hungary)',
                'voices' =>
                    array (
                        'hu-HU-Standard-A' =>
                            array (
                                'voice' => 'Léna',
                                'voice_id' => 'hu-HU-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'hu-HU-Wavenet-A' =>
                            array (
                                'voice' => 'Lena',
                                'voice_id' => 'hu-HU-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'is-IS' =>
            array (
                'language_code' => 'is-IS',
                'language' => 'Icelandic (Iceland)',
                'voices' =>
                    array (
                        'is-aws-std-dora' =>
                            array (
                                'voice' => 'Dora',
                                'voice_id' => 'is-aws-std-dora',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'is-aws-std-karl' =>
                            array (
                                'voice' => 'Karl',
                                'voice_id' => 'is-aws-std-karl',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'is-is-Standard-A' =>
                            array (
                                'voice' => 'Helga',
                                'voice_id' => 'is-is-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
        'id-ID' =>
            array (
                'language_code' => 'id-ID',
                'language' => 'Indonesian (Indonesia)',
                'voices' =>
                    array (
                        'id-ID-Standard-A' =>
                            array (
                                'voice' => 'Aulia',
                                'voice_id' => 'id-ID-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'id-ID-Standard-B' =>
                            array (
                                'voice' => 'Arief',
                                'voice_id' => 'id-ID-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'id-ID-Standard-C' =>
                            array (
                                'voice' => 'Fadhlan',
                                'voice_id' => 'id-ID-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'id-ID-Standard-D' =>
                            array (
                                'voice' => 'Dewi',
                                'voice_id' => 'id-ID-Standard-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'id-ID-Wavenet-A' =>
                            array (
                                'voice' => 'Aulia',
                                'voice_id' => 'id-ID-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'id-ID-Wavenet-B' =>
                            array (
                                'voice' => 'Arief',
                                'voice_id' => 'id-ID-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'id-ID-Wavenet-C' =>
                            array (
                                'voice' => 'Fadhlan',
                                'voice_id' => 'id-ID-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'id-ID-Wavenet-D' =>
                            array (
                                'voice' => 'Dewi',
                                'voice_id' => 'id-ID-Wavenet-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'it-IT' =>
            array (
                'language_code' => 'it-IT',
                'language' => 'Italian (Italy)',
                'voices' =>
                    array (
                        'it-aws-std-carla' =>
                            array (
                                'voice' => 'Carla',
                                'voice_id' => 'it-aws-std-carla',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'it-aws-std-bianca' =>
                            array (
                                'voice' => 'Bianca',
                                'voice_id' => 'it-aws-std-bianca',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'it-aws-std-giorgio' =>
                            array (
                                'voice' => 'Giorgio',
                                'voice_id' => 'it-aws-std-giorgio',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'it-IT-Standard-A' =>
                            array (
                                'voice' => 'Federica',
                                'voice_id' => 'it-IT-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'it-IT-Standard-B' =>
                            array (
                                'voice' => 'Bella',
                                'voice_id' => 'it-IT-Standard-B',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'it-IT-Standard-C' =>
                            array (
                                'voice' => 'Luca',
                                'voice_id' => 'it-IT-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'it-IT-Standard-D' =>
                            array (
                                'voice' => 'Matteo',
                                'voice_id' => 'it-IT-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'it-IT-Wavenet-A' =>
                            array (
                                'voice' => 'Federica',
                                'voice_id' => 'it-IT-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'it-IT-Wavenet-B' =>
                            array (
                                'voice' => 'Bella',
                                'voice_id' => 'it-IT-Wavenet-B',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'it-IT-Wavenet-C' =>
                            array (
                                'voice' => 'Luca',
                                'voice_id' => 'it-IT-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'it-IT-Wavenet-D' =>
                            array (
                                'voice' => 'Matteo',
                                'voice_id' => 'it-IT-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'ja-JP' =>
            array (
                'language_code' => 'ja-JP',
                'language' => 'Japanese (Japan)',
                'voices' =>
                    array (
                        'jp-aws-std-mizuki' =>
                            array (
                                'voice' => 'Mizuki',
                                'voice_id' => 'jp-aws-std-mizuki',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'jp-aws-std-takumi' =>
                            array (
                                'voice' => 'Takumi',
                                'voice_id' => 'jp-aws-std-takumi',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'ja-JP-Standard-A' =>
                            array (
                                'voice' => 'Manami',
                                'voice_id' => 'ja-JP-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ja-JP-Standard-B' =>
                            array (
                                'voice' => 'Sakura',
                                'voice_id' => 'ja-JP-Standard-B',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ja-JP-Standard-C' =>
                            array (
                                'voice' => 'Sensei',
                                'voice_id' => 'ja-JP-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ja-JP-Standard-D' =>
                            array (
                                'voice' => 'Takuya',
                                'voice_id' => 'ja-JP-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ja-JP-Wavenet-A' =>
                            array (
                                'voice' => 'Manami',
                                'voice_id' => 'ja-JP-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ja-JP-Wavenet-B' =>
                            array (
                                'voice' => 'Mizuki',
                                'voice_id' => 'ja-JP-Wavenet-B',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ja-JP-Wavenet-C' =>
                            array (
                                'voice' => 'Sensei',
                                'voice_id' => 'ja-JP-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ja-JP-Wavenet-D' =>
                            array (
                                'voice' => 'Takuya',
                                'voice_id' => 'ja-JP-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'kn-IN' =>
            array (
                'language_code' => 'kn-IN',
                'language' => 'Kannada (India)',
                'voices' =>
                    array (
                        'kn-IN-Standard-A' =>
                            array (
                                'voice' => 'Aarushi',
                                'voice_id' => 'kn-IN-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'kn-IN-Standard-B' =>
                            array (
                                'voice' => 'Arjun',
                                'voice_id' => 'kn-IN-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'kn-IN-Wavenet-A' =>
                            array (
                                'voice' => 'Aarushi',
                                'voice_id' => 'kn-IN-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'kn-IN-Wavenet-B' =>
                            array (
                                'voice' => 'Arjun',
                                'voice_id' => 'kn-IN-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'ko-KR' =>
            array (
                'language_code' => 'ko-KR',
                'language' => 'Korean (South Korea)',
                'voices' =>
                    array (
                        'kr-aws-std-seoyeon' =>
                            array (
                                'voice' => 'Seoyeon',
                                'voice_id' => 'kr-aws-std-seoyeon',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'kr-aws-nrl-seoyeon' =>
                            array (
                                'voice' => 'Seoyeon',
                                'voice_id' => 'kr-aws-nrl-seoyeon',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'ko-KR-Standard-A' =>
                            array (
                                'voice' => 'Yong',
                                'voice_id' => 'ko-KR-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ko-KR-Standard-B' =>
                            array (
                                'voice' => 'Eun-Kyung',
                                'voice_id' => 'ko-KR-Standard-B',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ko-KR-Standard-C' =>
                            array (
                                'voice' => 'Beom-Soo',
                                'voice_id' => 'ko-KR-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ko-KR-Standard-D' =>
                            array (
                                'voice' => 'Byung-Joon',
                                'voice_id' => 'ko-KR-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ko-KR-Wavenet-A' =>
                            array (
                                'voice' => 'Yong',
                                'voice_id' => 'ko-KR-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ko-KR-Wavenet-B' =>
                            array (
                                'voice' => 'Eun-Kyung',
                                'voice_id' => 'ko-KR-Wavenet-B',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ko-KR-Wavenet-C' =>
                            array (
                                'voice' => 'Beom-Soo',
                                'voice_id' => 'ko-KR-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ko-KR-Wavenet-D' =>
                            array (
                                'voice' => 'Byung-joon',
                                'voice_id' => 'ko-KR-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'lv-LV' =>
            array (
                'language_code' => 'lv-LV',
                'language' => 'Latvian (Latvia)',
                'voices' =>
                    array (
                        'lv-lv-Standard-A' =>
                            array (
                                'voice' => 'Andris',
                                'voice_id' => 'lv-lv-Standard-A',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
        'ml-IN' =>
            array (
                'language_code' => 'ml-IN',
                'language' => 'Malayalam (India)',
                'voices' =>
                    array (
                        'ml-IN-Standard-A' =>
                            array (
                                'voice' => 'Abha',
                                'voice_id' => 'ml-IN-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ml-IN-Standard-B' =>
                            array (
                                'voice' => 'Akhil',
                                'voice_id' => 'ml-IN-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ml-IN-Wavenet-A' =>
                            array (
                                'voice' => 'Abha',
                                'voice_id' => 'ml-IN-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ml-IN-Wavenet-B' =>
                            array (
                                'voice' => 'Akhil',
                                'voice_id' => 'ml-IN-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'nb-NO' =>
            array (
                'language_code' => 'nb-NO',
                'language' => 'Norwegian (Norway)',
                'voices' =>
                    array (
                        'no-aws-std-liv' =>
                            array (
                                'voice' => 'Liv',
                                'voice_id' => 'no-aws-std-liv',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'nb-NO-Standard-A' =>
                            array (
                                'voice' => 'Chin',
                                'voice_id' => 'nb-NO-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'nb-NO-Standard-B' =>
                            array (
                                'voice' => 'Bjorn',
                                'voice_id' => 'nb-NO-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'nb-NO-Standard-C' =>
                            array (
                                'voice' => 'Inger',
                                'voice_id' => 'nb-NO-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'nb-NO-Standard-D' =>
                            array (
                                'voice' => 'Dag',
                                'voice_id' => 'nb-NO-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'nb-NO-Standard-E' =>
                            array (
                                'voice' => 'Janne',
                                'voice_id' => 'nb-NO-Standard-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'nb-NO-Wavenet-A' =>
                            array (
                                'voice' => 'Chin',
                                'voice_id' => 'nb-NO-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'nb-NO-Wavenet-B' =>
                            array (
                                'voice' => 'Bjorn',
                                'voice_id' => 'nb-NO-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'nb-NO-Wavenet-C' =>
                            array (
                                'voice' => 'Inger',
                                'voice_id' => 'nb-NO-Wavenet-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'nb-NO-Wavenet-D' =>
                            array (
                                'voice' => 'Dag',
                                'voice_id' => 'nb-NO-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'nb-NO-Wavenet-E' =>
                            array (
                                'voice' => 'Janne',
                                'voice_id' => 'nb-NO-Wavenet-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'pl-PL' =>
            array (
                'language_code' => 'pl-PL',
                'language' => 'Polish (Poland)',
                'voices' =>
                    array (
                        'pl-aws-std-ewa' =>
                            array (
                                'voice' => 'Ewa',
                                'voice_id' => 'pl-aws-std-ewa',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'pl-aws-std-maja' =>
                            array (
                                'voice' => 'Maja',
                                'voice_id' => 'pl-aws-std-maja',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'pl-aws-std-jacek' =>
                            array (
                                'voice' => 'Jacek',
                                'voice_id' => 'pl-aws-std-jacek',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'pl-aws-std-jan' =>
                            array (
                                'voice' => 'Jan',
                                'voice_id' => 'pl-aws-std-jan',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'pl-PL-Standard-A' =>
                            array (
                                'voice' => 'Sara',
                                'voice_id' => 'pl-PL-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'pl-PL-Standard-B' =>
                            array (
                                'voice' => 'Jan',
                                'voice_id' => 'pl-PL-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'pl-PL-Standard-C' =>
                            array (
                                'voice' => 'Jacob',
                                'voice_id' => 'pl-PL-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'pl-PL-Standard-D' =>
                            array (
                                'voice' => 'Lena',
                                'voice_id' => 'pl-PL-Standard-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'pl-PL-Standard-E' =>
                            array (
                                'voice' => 'Zofia',
                                'voice_id' => 'pl-PL-Standard-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'pl-PL-Wavenet-A' =>
                            array (
                                'voice' => 'Sara',
                                'voice_id' => 'pl-PL-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'pl-PL-Wavenet-B' =>
                            array (
                                'voice' => 'Jan',
                                'voice_id' => 'pl-PL-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'pl-PL-Wavenet-C' =>
                            array (
                                'voice' => 'Jacob',
                                'voice_id' => 'pl-PL-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'pl-PL-Wavenet-D' =>
                            array (
                                'voice' => 'Lena',
                                'voice_id' => 'pl-PL-Wavenet-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'pl-PL-Wavenet-E' =>
                            array (
                                'voice' => 'Zofia',
                                'voice_id' => 'pl-PL-Wavenet-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'pt-BR' =>
            array (
                'language_code' => 'pt-BR',
                'language' => 'Portuguese (Brazil)',
                'voices' =>
                    array (
                        'br-aws-std-camila' =>
                            array (
                                'voice' => 'Camila',
                                'voice_id' => 'br-aws-std-camila',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'br-aws-std-victoria' =>
                            array (
                                'voice' => 'Victoria',
                                'voice_id' => 'br-aws-std-victoria',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'br-aws-std-ricardo' =>
                            array (
                                'voice' => 'Ricardo',
                                'voice_id' => 'br-aws-std-ricardo',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'br-aws-nrl-camila' =>
                            array (
                                'voice' => 'Camila',
                                'voice_id' => 'br-aws-nrl-camila',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'pt-BR-Standard-A' =>
                            array (
                                'voice' => 'Francisca',
                                'voice_id' => 'pt-BR-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'pt-BR-Wavenet-A' =>
                            array (
                                'voice' => 'Francisca',
                                'voice_id' => 'pt-BR-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'pt-PT' =>
            array (
                'language_code' => 'pt-PT',
                'language' => 'Portuguese (Portugal)',
                'voices' =>
                    array (
                        'pt-aws-std-ines' =>
                            array (
                                'voice' => 'Ines',
                                'voice_id' => 'pt-aws-std-ines',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'pt-aws-std-cristiano' =>
                            array (
                                'voice' => 'Cristiano',
                                'voice_id' => 'pt-aws-std-cristiano',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'pt-PT-Standard-A' =>
                            array (
                                'voice' => 'Beatriz',
                                'voice_id' => 'pt-PT-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'pt-PT-Standard-B' =>
                            array (
                                'voice' => 'Ronaldo',
                                'voice_id' => 'pt-PT-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'pt-PT-Standard-C' =>
                            array (
                                'voice' => 'Lisboa',
                                'voice_id' => 'pt-PT-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'pt-PT-Standard-D' =>
                            array (
                                'voice' => 'Mariana',
                                'voice_id' => 'pt-PT-Standard-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'pt-PT-Wavenet-A' =>
                            array (
                                'voice' => 'Beatriz',
                                'voice_id' => 'pt-PT-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'pt-PT-Wavenet-B' =>
                            array (
                                'voice' => 'Ronaldo',
                                'voice_id' => 'pt-PT-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'pt-PT-Wavenet-C' =>
                            array (
                                'voice' => 'Lisboa',
                                'voice_id' => 'pt-PT-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'pt-PT-Wavenet-D' =>
                            array (
                                'voice' => 'Mariana',
                                'voice_id' => 'pt-PT-Wavenet-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'ro-RO' =>
            array (
                'language_code' => 'ro-RO',
                'language' => 'Romanian (Romania)',
                'voices' =>
                    array (
                        'ro-aws-std-carment' =>
                            array (
                                'voice' => 'Carmen',
                                'voice_id' => 'ro-aws-std-carment',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'ro-RO-Standard-A' =>
                            array (
                                'voice' => 'Mihaela',
                                'voice_id' => 'ro-RO-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ro-RO-Wavenet-A' =>
                            array (
                                'voice' => 'Mihaela',
                                'voice_id' => 'ro-RO-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'ru-RU' =>
            array (
                'language_code' => 'ru-RU',
                'language' => 'Russian (Russia)',
                'voices' =>
                    array (
                        'ru-aws-std-tatyana' =>
                            array (
                                'voice' => 'Tatyana',
                                'voice_id' => 'ru-aws-std-tatyana',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'ru-aws-std-maxim' =>
                            array (
                                'voice' => 'Maxim',
                                'voice_id' => 'ru-aws-std-maxim',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'ru-RU-Standard-A' =>
                            array (
                                'voice' => 'Oksana',
                                'voice_id' => 'ru-RU-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ru-RU-Standard-B' =>
                            array (
                                'voice' => 'Vlad',
                                'voice_id' => 'ru-RU-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ru-RU-Standard-C' =>
                            array (
                                'voice' => 'Veronika',
                                'voice_id' => 'ru-RU-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ru-RU-Standard-D' =>
                            array (
                                'voice' => 'Dapohui',
                                'voice_id' => 'ru-RU-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ru-RU-Standard-E' =>
                            array (
                                'voice' => 'Svetlana',
                                'voice_id' => 'ru-RU-Standard-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ru-RU-Wavenet-A' =>
                            array (
                                'voice' => 'Oksana',
                                'voice_id' => 'ru-RU-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ru-RU-Wavenet-B' =>
                            array (
                                'voice' => 'Vlad',
                                'voice_id' => 'ru-RU-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ru-RU-Wavenet-C' =>
                            array (
                                'voice' => 'Veronika',
                                'voice_id' => 'ru-RU-Wavenet-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ru-RU-Wavenet-D' =>
                            array (
                                'voice' => 'Dapohui',
                                'voice_id' => 'ru-RU-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ru-RU-Wavenet-E' =>
                            array (
                                'voice' => 'Svetlana',
                                'voice_id' => 'ru-RU-Wavenet-E',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'sr-RS' =>
            array (
                'language_code' => 'sr-RS',
                'language' => 'Serbian (Serbia)',
                'voices' =>
                    array (
                        'sr-RS-Standard-A' =>
                            array (
                                'voice' => 'Olga',
                                'voice_id' => 'sr-RS-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
        'sk-SK' =>
            array (
                'language_code' => 'sk-SK',
                'language' => 'Slovak (Slovakia)',
                'voices' =>
                    array (
                        'sk-SK-Standard-A' =>
                            array (
                                'voice' => 'Anna',
                                'voice_id' => 'sk-SK-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'sk-SK-Wavenet-A' =>
                            array (
                                'voice' => 'Anna',
                                'voice_id' => 'sk-SK-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'es-MX' =>
            array (
                'language_code' => 'es-MX',
                'language' => 'Spanish (Mexico)',
                'voices' =>
                    array (
                        'mx-aws-std-mia' =>
                            array (
                                'voice' => 'Mia',
                                'voice_id' => 'mx-aws-std-mia',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
        'es-ES' =>
            array (
                'language_code' => 'es-ES',
                'language' => 'Spanish (Spain)',
                'voices' =>
                    array (
                        'es-aws-std-conchita' =>
                            array (
                                'voice' => 'Conchita',
                                'voice_id' => 'es-aws-std-conchita',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'es-aws-std-lucia' =>
                            array (
                                'voice' => 'Lucia',
                                'voice_id' => 'es-aws-std-lucia',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'es-aws-std-enrique' =>
                            array (
                                'voice' => 'Enrique',
                                'voice_id' => 'es-aws-std-enrique',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'es-ES-Standard-A' =>
                            array (
                                'voice' => 'Hector',
                                'voice_id' => 'es-ES-Standard-A',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'es-ES-Standard-B' =>
                            array (
                                'voice' => 'Vanessa',
                                'voice_id' => 'es-ES-Standard-B',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'es-ES-Standard-C' =>
                            array (
                                'voice' => 'Laura',
                                'voice_id' => 'es-ES-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'es-ES-Standard-D' =>
                            array (
                                'voice' => 'Isabella',
                                'voice_id' => 'es-ES-Standard-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'es-ES-Wavenet-B' =>
                            array (
                                'voice' => 'Hector',
                                'voice_id' => 'es-ES-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'es-ES-Wavenet-C' =>
                            array (
                                'voice' => 'Vanessa',
                                'voice_id' => 'es-ES-Wavenet-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'es-ES-Wavenet-D' =>
                            array (
                                'voice' => 'Laura',
                                'voice_id' => 'es-ES-Wavenet-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'es-US' =>
            array (
                'language_code' => 'es-US',
                'language' => 'Spanish (USA)',
                'voices' =>
                    array (
                        'us-aws-std-lupe' =>
                            array (
                                'voice' => 'Lupe',
                                'voice_id' => 'us-aws-std-lupe',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'us-aws-std-penelope' =>
                            array (
                                'voice' => 'Penelope',
                                'voice_id' => 'us-aws-std-penelope',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'us-aws-std-miguel' =>
                            array (
                                'voice' => 'Miguel',
                                'voice_id' => 'us-aws-std-miguel',
                                'gender' => 'Male',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'us-aws-nrl-lupe' =>
                            array (
                                'voice' => 'Lupe',
                                'voice_id' => 'us-aws-nrl-lupe',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_nrl',
                                'vendor' => 'aws',
                                'voice_type' => 'neural',
                            ),
                        'es-US-Standard-A' =>
                            array (
                                'voice' => 'Mia',
                                'voice_id' => 'es-US-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'es-US-Standard-B' =>
                            array (
                                'voice' => 'Pedro',
                                'voice_id' => 'es-US-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'es-US-Standard-C' =>
                            array (
                                'voice' => 'Sanches',
                                'voice_id' => 'es-US-Standard-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'es-US-Wavenet-A' =>
                            array (
                                'voice' => 'Isabella',
                                'voice_id' => 'es-US-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'es-US-Wavenet-B' =>
                            array (
                                'voice' => 'Pedro',
                                'voice_id' => 'es-US-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'es-US-Wavenet-C' =>
                            array (
                                'voice' => 'Sanches',
                                'voice_id' => 'es-US-Wavenet-C',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'sv-SE' =>
            array (
                'language_code' => 'sv-SE',
                'language' => 'Swedish (Sweden)',
                'voices' =>
                    array (
                        'se-aws-std-astrid' =>
                            array (
                                'voice' => 'Astrid',
                                'voice_id' => 'se-aws-std-astrid',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'sv-SE-Standard-A' =>
                            array (
                                'voice' => 'Karin',
                                'voice_id' => 'sv-SE-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'sv-SE-Wavenet-A' =>
                            array (
                                'voice' => 'Karin',
                                'voice_id' => 'sv-SE-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'ta-IN' =>
            array (
                'language_code' => 'ta-IN',
                'language' => 'Tamil (India)',
                'voices' =>
                    array (
                        'ta-IN-Standard-A' =>
                            array (
                                'voice' => 'Aarushi',
                                'voice_id' => 'ta-IN-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ta-IN-Standard-B' =>
                            array (
                                'voice' => 'Aadhish',
                                'voice_id' => 'ta-IN-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'ta-IN-Wavenet-A' =>
                            array (
                                'voice' => 'Aarushi',
                                'voice_id' => 'ta-IN-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'ta-IN-Wavenet-B' =>
                            array (
                                'voice' => 'Aadhish',
                                'voice_id' => 'ta-IN-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'te-IN' =>
            array (
                'language_code' => 'te-IN',
                'language' => 'Telugu (India)',
                'voices' =>
                    array (
                        'te-IN-Standard-A' =>
                            array (
                                'voice' => 'Aasthika',
                                'voice_id' => 'te-IN-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'te-IN-Standard-B' =>
                            array (
                                'voice' => 'Aadhavan',
                                'voice_id' => 'te-IN-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
        'th-TH' =>
            array (
                'language_code' => 'th-TH',
                'language' => 'Thai (Thailand)',
                'voices' =>
                    array (
                        'th-TH-Standard-A' =>
                            array (
                                'voice' => 'Busaba',
                                'voice_id' => 'th-TH-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
        'tr-TR' =>
            array (
                'language_code' => 'tr-TR',
                'language' => 'Turkish (Turkey)',
                'voices' =>
                    array (
                        'tr-aws-std-filiz' =>
                            array (
                                'voice' => 'Filiz',
                                'voice_id' => 'tr-aws-std-filiz',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                        'tr-TR-Standard-A' =>
                            array (
                                'voice' => 'Zeynep',
                                'voice_id' => 'tr-TR-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'tr-TR-Standard-B' =>
                            array (
                                'voice' => 'Oktay',
                                'voice_id' => 'tr-TR-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'tr-TR-Standard-C' =>
                            array (
                                'voice' => 'Fatima',
                                'voice_id' => 'tr-TR-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'tr-TR-Standard-D' =>
                            array (
                                'voice' => 'Gulchatay',
                                'voice_id' => 'tr-TR-Standard-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'tr-TR-Standard-E' =>
                            array (
                                'voice' => 'Yanar',
                                'voice_id' => 'tr-TR-Standard-E',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'tr-TR-Wavenet-A' =>
                            array (
                                'voice' => 'Zeynep',
                                'voice_id' => 'tr-TR-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'tr-TR-Wavenet-B' =>
                            array (
                                'voice' => 'Oktay',
                                'voice_id' => 'tr-TR-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'tr-TR-Wavenet-C' =>
                            array (
                                'voice' => 'Fatima',
                                'voice_id' => 'tr-TR-Wavenet-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'tr-TR-Wavenet-D' =>
                            array (
                                'voice' => 'Gulchatay',
                                'voice_id' => 'tr-TR-Wavenet-D',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'tr-TR-Wavenet-E' =>
                            array (
                                'voice' => 'Yanar',
                                'voice_id' => 'tr-TR-Wavenet-E',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'uk-UA' =>
            array (
                'language_code' => 'uk-UA',
                'language' => 'Ukrainian (Ukraine)',
                'voices' =>
                    array (
                        'uk-UA-Standard-A' =>
                            array (
                                'voice' => 'Oksana',
                                'voice_id' => 'uk-UA-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'uk-UA-Wavenet-A' =>
                            array (
                                'voice' => 'Oksana',
                                'voice_id' => 'uk-UA-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'vi-VN' =>
            array (
                'language_code' => 'vi-VN',
                'language' => 'Vietnamese (Vietnam)',
                'voices' =>
                    array (
                        'vi-VN-Standard-A' =>
                            array (
                                'voice' => 'Chau',
                                'voice_id' => 'vi-VN-Standard-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'vi-VN-Standard-B' =>
                            array (
                                'voice' => 'Dung',
                                'voice_id' => 'vi-VN-Standard-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'vi-VN-Standard-C' =>
                            array (
                                'voice' => 'Cam',
                                'voice_id' => 'vi-VN-Standard-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'vi-VN-Standard-D' =>
                            array (
                                'voice' => 'Duy',
                                'voice_id' => 'vi-VN-Standard-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_std',
                                'vendor' => 'gcp',
                                'voice_type' => 'standard',
                            ),
                        'vi-VN-Wavenet-A' =>
                            array (
                                'voice' => 'Chau',
                                'voice_id' => 'vi-VN-Wavenet-A',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'vi-VN-Wavenet-B' =>
                            array (
                                'voice' => 'Dung',
                                'voice_id' => 'vi-VN-Wavenet-B',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'vi-VN-Wavenet-C' =>
                            array (
                                'voice' => 'Cam',
                                'voice_id' => 'vi-VN-Wavenet-C',
                                'gender' => 'Female',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                        'vi-VN-Wavenet-D' =>
                            array (
                                'voice' => 'Duy',
                                'voice_id' => 'vi-VN-Wavenet-D',
                                'gender' => 'Male',
                                'vendor_id' => 'gcp_nrl',
                                'vendor' => 'gcp',
                                'voice_type' => 'neural',
                            ),
                    ),
            ),
        'cy-GB' =>
            array (
                'language_code' => 'cy-GB',
                'language' => 'Welsh (Wales)',
                'voices' =>
                    array (
                        'cy-aws-std-gwyneth' =>
                            array (
                                'voice' => 'Gwyneth',
                                'voice_id' => 'cy-aws-std-gwyneth',
                                'gender' => 'Female',
                                'vendor_id' => 'aws_std',
                                'vendor' => 'aws',
                                'voice_type' => 'standard',
                            ),
                    ),
            ),
    );
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
    return $lang_text . "Write a complete 3000+ words long article with your maximum capacity with introduction and conclusion. The article should be nice looking with headings, sub headings and lists. Use this topic:\n\n" . $title . "\n\nUse following keywords in the article:\n" . $description . "\n\n" . $tone_text;

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

                // reset user's data
                update_user_option($info['user_id'], 'total_words_used', 0);
                update_user_option($info['user_id'], 'total_images_used', 0);
                update_user_option($info['user_id'], 'total_speech_used', 0);
                update_user_option($info['user_id'], 'total_text_to_speech_used', 0);

                update_user_option($info['user_id'], 'last_reset_time', time());
            }
            ORM::for_table($config['db']['pre'] . 'upgrades')
                ->where_equal('upgrade_id', $info['upgrade_id'])
                ->delete_many();

            $expire_membership++;
        }
        // END REMOVE EXPIRED UPGRADES

        // reset user's data after 30 days
        $expiry_time = time() - (86400 * 30);

        $result = ORM::for_table($config['db']['pre'] . 'user_options')
            ->where('option_name', 'last_reset_time')
            ->where_lt('option_value', $expiry_time)
            ->find_array();
        foreach ($result as $info) {
            $person_count = ORM::for_table($config['db']['pre'] . 'user')
                ->where('id', $info['user_id'])
                ->count();

            if ($person_count) {
                // reset user's data
                update_user_option($info['user_id'], 'total_words_used', 0);
                update_user_option($info['user_id'], 'total_images_used', 0);
                update_user_option($info['user_id'], 'total_speech_used', 0);
                update_user_option($info['user_id'], 'total_text_to_speech_used', 0);

                update_user_option($info['user_id'], 'last_reset_time', time());
            }
        }

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

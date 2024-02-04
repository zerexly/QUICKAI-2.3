<?php
require_once("includes/lib/curl/curl.php");
require_once("includes/lib/curl/CurlResponse.php");

if(isset($current_user['id']))
{
    if(isset($_REQUEST['upgrade']))
    {
        if(!check_allow()){
            message(__('Disabled'),'Disabled on the demo.',$link['MEMBERSHIP'],false);
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $plan_taxes = array();
        $price_without_inclusive = 0;
        $tax_ids = array();
        if($_REQUEST['upgrade'] == 'trial'){
            if(get_user_option($user_id,'package_trial_done')){
                error(__("Your trial option was already used, you can't use it anymore."), __LINE__, __FILE__, 1);
                exit();
            }
            $plan = json_decode(get_option('trial_membership_plan'), true);
            $price = 0;
            $term = $plan['days'];
        } else if($_POST['upgrade'] == 'free'){
            $plan = json_decode(get_option('free_membership_plan'), true);
            $price = 0;
            $term = 'LIFETIME';

        } else {

            /* Check tax and billing enabled */
            if (get_option('enable_tax_billing', 1)) {
                if (isset($_REQUEST['billing-submit'])) {
                    /* Save billing details */

                    update_user_option($_SESSION['user']['id'], 'billing_details_type', validate_input($_POST['billing_details_type']));
                    update_user_option($_SESSION['user']['id'], 'billing_tax_id', validate_input($_POST['billing_tax_id']));
                    update_user_option($_SESSION['user']['id'], 'billing_name', validate_input($_POST['billing_name']));
                    update_user_option($_SESSION['user']['id'], 'billing_address', validate_input($_POST['billing_address']));
                    update_user_option($_SESSION['user']['id'], 'billing_city', validate_input($_POST['billing_city']));
                    update_user_option($_SESSION['user']['id'], 'billing_state', validate_input($_POST['billing_state']));
                    update_user_option($_SESSION['user']['id'], 'billing_zipcode', validate_input($_POST['billing_zipcode']));
                    update_user_option($_SESSION['user']['id'], 'billing_country', validate_input($_POST['billing_country']));
                }

                if (empty(get_user_option($_SESSION['user']['id'], 'billing_name'))) {

                    $billing_country = get_user_option($_SESSION['user']['id'], 'billing_country');
                    if (empty($billing_country)) {
                        $billing_country = strtoupper(check_user_country());
                    }

                    /* Ask billing details if not available */
                    HtmlTemplate::display('global/membership_billing_details', array(
                        'upgrade' => $_REQUEST['upgrade'],
                        'plan_id' => $_REQUEST['upgrade'],
                        'billed_type' => $_REQUEST['billed-type'],
                        'billing_details_type' => get_user_option($_SESSION['user']['id'], 'billing_details_type'),
                        'billing_tax_id' => get_user_option($_SESSION['user']['id'], 'billing_tax_id'),
                        'billing_name' => get_user_option($_SESSION['user']['id'], 'billing_name'),
                        'billing_address' => get_user_option($_SESSION['user']['id'], 'billing_address'),
                        'billing_city' => get_user_option($_SESSION['user']['id'], 'billing_city'),
                        'billing_state' => get_user_option($_SESSION['user']['id'], 'billing_state'),
                        'billing_zipcode' => get_user_option($_SESSION['user']['id'], 'billing_zipcode'),
                        'billing_country' => $billing_country,
                        'countries' => get_country_list($billing_country, "selected", 0),
                    ));
                    exit;
                }
            }

            $plan = ORM::for_table($config['db']['pre'].'plans')
                ->where('id', $_REQUEST['upgrade'])
                ->find_one();

            switch ($_REQUEST['billed-type']){
                case 'monthly':
                    $price = $plan['monthly_price'];
                    $term = 'MONTHLY';
                    break;
                case 'yearly':
                    $price = $plan['annual_price'];
                    $term = 'YEARLY';
                    break;
                case 'lifetime':
                    $price = $plan['lifetime_price'];
                    $term = 'LIFETIME';
                    break;
            }

            $base_amount = $price;

            if (get_option('enable_tax_billing', 1)) {
                if (!empty($plan['taxes_ids'])) {
                    $taxes = ORM::for_table($config['db']['pre'] . 'taxes')
                        ->where_id_in(explode(',', $plan['taxes_ids']))
                        ->find_many();

                    $inclusive_tax = $exclusive_tax = 0;
                    $inclusive_tax_fixed = 0;
                    $inclusive_tax_percents = 0;

                    foreach ($taxes as $tax) {

                        /* filter plan taxes */

                        /* Type */
                        if (
                            $tax['billing_type'] != get_user_option($_SESSION['user']['id'], 'billing_details_type') &&
                            $tax['billing_type'] != 'both'
                        ) {
                            continue;
                        }

                        /* Countries */
                        if (
                            $tax['countries'] &&
                            !in_array(get_user_option($_SESSION['user']['id'], 'billing_country'), explode(',', $tax['countries']))
                        ) {
                            continue;
                        }

                        /* Create variable */
                        $plan_taxes[$tax['id']]['id'] = $tax['id'];
                        $plan_taxes[$tax['id']]['name'] = $tax['name'];
                        $plan_taxes[$tax['id']]['description'] = $tax['description'];
                        $plan_taxes[$tax['id']]['type'] = $tax['type'] == 'inclusive' ? __("Inclusive") : __("Exclusive");
                        $plan_taxes[$tax['id']]['value_formatted'] = $tax['value_type'] == 'percentage' ? (float)$tax['value'] . '%' : price_format($tax['value'], $config['currency_code']);

                        /* calculate inclusive taxes */
                        if ($tax['type'] == 'inclusive') {
                            //$inclusive_tax += $tax['value_type'] == 'percentage' ? $price * ($tax['value'] / 100) : $tax['value'];
                            if ($tax['value_type'] == 'percentage') {
                                $inclusive_tax_percents += $tax['value'];
                            } else {
                                $inclusive_tax_fixed += $tax['value'];
                            }
                        }

                        $tax_ids[] = $tax['id'];
                    }

                    $inclusive_tax = $price - ($price / (1 + $inclusive_tax_percents / 100));
                    $inclusive_tax += $inclusive_tax_fixed;

                    $price_without_inclusive = $price - $inclusive_tax;

                    /* calculate exclusive taxes */
                    foreach ($taxes as $tax) {
                        /* filter plan taxes */

                        /* Type */
                        if (
                            $tax['billing_type'] != get_user_option($_SESSION['user']['id'], 'billing_details_type') &&
                            $tax['billing_type'] != 'both'
                        ) {
                            continue;
                        }

                        /* Countries */
                        if (
                            $tax['countries'] &&
                            !in_array(get_user_option($_SESSION['user']['id'], 'billing_country'), explode(',', $tax['countries']))
                        ) {
                            continue;
                        }

                        if ($tax['type'] == 'exclusive') {
                            $exclusive_tax += $tax['value_type'] == 'percentage' ? $price_without_inclusive * ($tax['value'] / 100) : $tax['value'];
                        }
                    }
                    /* total price */
                    $price += $exclusive_tax;
                }
            }
        }

        $title = $plan['name'];
        $amount = price_format($price,$config['currency_code']);

        $payment_type = "subscr";

        if(isset($_POST['payment_method_id']) && $_POST['payment_method_id'] != "")
        {
            if($_REQUEST['upgrade'] == 'trial'){
                if(get_user_option($user_id,'package_trial_done')){
                    error(__("Your trial option was already used, you can't use it anymore."), __LINE__, __FILE__, 1);
                    exit();
                }

                ORM::for_table($config['db']['pre'].'upgrades')
                    ->where_equal('user_id', $user_id)
                    ->delete_many();

                $upgrades_insert = ORM::for_table($config['db']['pre'].'upgrades')->create();
                $upgrades_insert->sub_id = $_REQUEST['upgrade'];
                $upgrades_insert->user_id = $user_id;
                $upgrades_insert->upgrade_lasttime = time();
                $upgrades_insert->upgrade_expires = time() + $plan['days'] * 86400;
                $upgrades_insert->status = 'Active';
                $upgrades_insert->save();

                $person = ORM::for_table($config['db']['pre'].'user')->find_one($user_id);
                $person->group_id = $_REQUEST['upgrade'];
                $person->save();

                // reset user's data
                update_user_option($user_id, 'total_words_used', 0);
                update_user_option($user_id, 'total_images_used', 0);
                update_user_option($user_id, 'total_speech_used', 0);
                update_user_option($user_id, 'total_text_to_speech_used', 0);

                update_user_option($user_id, 'last_reset_time', time());

                update_user_option($user_id, 'package_trial_done',1);
                message(__("Success"),__("Payment Successful"),$link['MEMBERSHIP']);
                exit();
            } else if($_POST['upgrade'] == 'free'){

                ORM::for_table($config['db']['pre'].'upgrades')
                    ->where_equal('user_id', $user_id)
                    ->delete_many();

                $person = ORM::for_table($config['db']['pre'].'user')->find_one($user_id);
                $person->group_id = $_POST['upgrade'];
                $person->save();

                // reset user's data
                update_user_option($user_id, 'total_words_used', 0);
                update_user_option($user_id, 'total_images_used', 0);
                update_user_option($user_id, 'total_speech_used', 0);
                update_user_option($user_id, 'total_text_to_speech_used', 0);

                update_user_option($user_id, 'last_reset_time', time());

                message(__("Success"),__("Payment Successful"),$link['MEMBERSHIP']);
                exit();
            } else {
                $access_token = uniqid();
                $_SESSION['quickad'][$access_token]['name'] = $title . " " . __("Membership Plan");
                $_SESSION['quickad'][$access_token]['amount'] = $price;
                $_SESSION['quickad'][$access_token]['base_amount'] = $base_amount;
                $_SESSION['quickad'][$access_token]['payment_type'] = $payment_type;
                $_SESSION['quickad'][$access_token]['sub_id'] = $_REQUEST['upgrade'];
                $_SESSION['quickad'][$access_token]['plan_interval'] = $term;
                $_SESSION['quickad'][$access_token]['taxes_ids'] = implode(',',$tax_ids);

                if($_POST['payment_method_id'] == "wallet"){
                    $_SESSION['quickad'][$access_token]['folder'] = "wallet";
                    $amount = $base_amount;
                    $user_data = get_user_data(null,$_SESSION['user']['id']);
                    $user_balance = $user_data['balance'];
                    if($user_balance < $amount)
                    {
                        $message = __("Wallet balance must be grater than").' '.$config['currency_sign'].$amount.'.';
                        error($message, __LINE__, __FILE__, 1);
                        exit();
                    }
                    else {
                        $deducted = $user_balance - $amount;
                        //Minus From Employer Account
                        $user_update = ORM::for_table($config['db']['pre'] . 'user')->find_one($_SESSION['user']['id']);
                        $user_update->set('balance', $deducted);
                        $user_update->save();
                    }
                    /*Success*/
                    payment_success_save_detail($access_token);
                    exit();
                }

                $info = ORM::for_table($config['db']['pre'] . 'payments')
                    ->where(array(
                        'payment_id' => $_POST['payment_method_id'],
                        'payment_install' => '1'
                    ))
                    ->find_one();

                $folder = $info['payment_folder'];

                if ($folder == "2checkout") {
                    $_SESSION['quickad'][$access_token]['firstname'] = $_POST['checkoutCardFirstName'];
                    $_SESSION['quickad'][$access_token]['lastname'] = $_POST['checkoutCardLastName'];
                    $_SESSION['quickad'][$access_token]['BillingAddress'] = $_POST['checkoutBillingAddress'];
                    $_SESSION['quickad'][$access_token]['BillingCity'] = $_POST['checkoutBillingCity'];
                    $_SESSION['quickad'][$access_token]['BillingState'] = $_POST['checkoutBillingState'];
                    $_SESSION['quickad'][$access_token]['BillingZipcode'] = $_POST['checkoutBillingZipcode'];
                    $_SESSION['quickad'][$access_token]['BillingCountry'] = $_POST['checkoutBillingCountry'];
                }

                $_SESSION['quickad'][$access_token]['payment_mode'] = !empty($_POST['payment_mode']) ? $_POST['payment_mode'] : 'one_time';
                if($folder == 'paypal' || $folder == 'stripe'){
                    $payment_mode = get_option($folder.'_payment_mode');
                    if($payment_mode == 'both'){
                        $_SESSION['quickad'][$access_token]['payment_mode'] = !empty($_POST['payment_mode']) ? $_POST['payment_mode'] : 'one_time';
                    }else{
                        $_SESSION['quickad'][$access_token]['payment_mode'] = $payment_mode;
                    }
                }

                $_SESSION['quickad'][$access_token]['folder'] = $folder;

                if (file_exists('includes/payments/' . $folder . '/pay.php')) {
                    require_once('includes/payments/' . $folder . '/pay.php');
                } else {
                    error(__("This payment method is not enabled."), __LINE__, __FILE__, 1);
                    exit();
                }
            }
        }
        else
        {
            $payment_types = array();
            $rows = ORM::for_table($config['db']['pre'].'payments')
                ->where('payment_install', '1')
                ->order_by_asc('position')
                ->find_many();

            $num_rows = count($rows);
            foreach ($rows as $info)
            {
                $payment_image = $config['site_url']."includes/payments/".$info['payment_folder']."/logo/logo.png";
                $payment_types[$info['payment_id']]['id'] = $info['payment_id'];
                $payment_types[$info['payment_id']]['title'] = $info['payment_title'];
                $payment_types[$info['payment_id']]['folder'] = $info['payment_folder'];
                $payment_types[$info['payment_id']]['desc'] = $info['payment_desc'];
                $payment_types[$info['payment_id']]['image'] = $payment_image;
            }

            $period = 0;
            if($_REQUEST['upgrade'] == 'trial'){
                $period = (int) $plan['days'] * 86400;
            }elseif($_POST['upgrade'] == 'free'){
                $period = 0;
            }else{
                if($_REQUEST['billed-type'] == "monthly") {
                    $period = 2678400;
                }
                elseif($_REQUEST['billed-type'] == "yearly") {
                    $period = 31536000;
                }
            }

            $expires = (time()+$period);
            $start_date = date("d-m-Y",time());
            $expiry_date = $period ? date("d-m-Y",$expires) : __("Lifetime");

            // assign posted variables to local variables
            $bank_information = nl2br(get_option('company_bank_info'));
            $userdata = $current_user;
            $email = $userdata['email'];
            $user_balance = $userdata['balance'];
            //Print Template
            HtmlTemplate::display('global/membership_payment', array(
                'payment_types' => $payment_types,
                'upgrade' => $_REQUEST['upgrade'],
                'plan_id' => $_REQUEST['upgrade'],
                'billed_type' => $_REQUEST['billed-type'],
                'payment_method_count' => $num_rows,
                'bank_info' => $bank_information,
                'start_date' => $start_date,
                'expiry_date' => $expiry_date,
                'order_title' => $title,
                'amount' => $amount,
                'price' => $price,
                'user_balance' => $user_balance,
                'price_without_inclusive' => price_format($price_without_inclusive, $config['currency_code']),
                'email' => $email,
                'country_code' => strtoupper(check_user_country()),
                'show_taxes' => (int) !empty($plan_taxes),
                'taxes' => $plan_taxes,
                'stripe_publishable_key' => isset($config['stripe_publishable_key'])? $config['stripe_publishable_key']: '',
                'paystack_public_key' => isset($config['paystack_public_key'])? $config['paystack_public_key']: '',
                'sandbox_mode_2checkout' => isset($config['2checkout_sandbox_mode'])? $config['2checkout_sandbox_mode']: '',
                'checkout_account_number' => isset($config['checkout_account_number'])? $config['checkout_account_number']: '',
                'checkout_public_key' => isset($config['checkout_public_key'])? $config['checkout_public_key']: '',
                'token' => ''
            ));
            exit;
        }
    }
    elseif (isset($_REQUEST['buy-prepaid-plan'])){
        if(!check_allow()){
            message(__('Disabled'),'Disabled on the demo.',$link['MEMBERSHIP'],false);
            exit;
        }
        $user_id = $_SESSION['user']['id'];
        $plan_taxes = array();
        $price_without_inclusive = 0;
        $tax_ids = array();

        /* Check tax and billing enabled */
        if (get_option('enable_tax_billing', 1)) {
            if (isset($_REQUEST['billing-submit'])) {
                /* Save billing details */

                update_user_option($_SESSION['user']['id'], 'billing_details_type', validate_input($_POST['billing_details_type']));
                update_user_option($_SESSION['user']['id'], 'billing_tax_id', validate_input($_POST['billing_tax_id']));
                update_user_option($_SESSION['user']['id'], 'billing_name', validate_input($_POST['billing_name']));
                update_user_option($_SESSION['user']['id'], 'billing_address', validate_input($_POST['billing_address']));
                update_user_option($_SESSION['user']['id'], 'billing_city', validate_input($_POST['billing_city']));
                update_user_option($_SESSION['user']['id'], 'billing_state', validate_input($_POST['billing_state']));
                update_user_option($_SESSION['user']['id'], 'billing_zipcode', validate_input($_POST['billing_zipcode']));
                update_user_option($_SESSION['user']['id'], 'billing_country', validate_input($_POST['billing_country']));
            }

            if (empty(get_user_option($_SESSION['user']['id'], 'billing_name'))) {

                $billing_country = get_user_option($_SESSION['user']['id'], 'billing_country');
                if (empty($billing_country)) {
                    $billing_country = strtoupper(check_user_country());
                }

                /* Ask billing details if not available */
                HtmlTemplate::display('global/membership_billing_details', array(
                    'buy-prepaid-plan' => $_REQUEST['buy-prepaid-plan'],
                    'plan_id' => $_REQUEST['buy-prepaid-plan'],
                    'buy_prepaid_plan' => 1,
                    'billing_details_type' => get_user_option($_SESSION['user']['id'], 'billing_details_type'),
                    'billing_tax_id' => get_user_option($_SESSION['user']['id'], 'billing_tax_id'),
                    'billing_name' => get_user_option($_SESSION['user']['id'], 'billing_name'),
                    'billing_address' => get_user_option($_SESSION['user']['id'], 'billing_address'),
                    'billing_city' => get_user_option($_SESSION['user']['id'], 'billing_city'),
                    'billing_state' => get_user_option($_SESSION['user']['id'], 'billing_state'),
                    'billing_zipcode' => get_user_option($_SESSION['user']['id'], 'billing_zipcode'),
                    'billing_country' => $billing_country,
                    'countries' => get_country_list($billing_country, "selected", 0),
                ));
                exit;
            }
        }

        $plan = ORM::for_table($config['db']['pre'].'prepaid_plans')
            ->where('id', $_REQUEST['buy-prepaid-plan'])
            ->find_one();
        $price = $plan['price'];

        $base_amount = $price;

        if (get_option('enable_tax_billing', 1)) {
            if (!empty($plan['taxes_ids'])) {
                $taxes = ORM::for_table($config['db']['pre'] . 'taxes')
                    ->where_id_in(explode(',', $plan['taxes_ids']))
                    ->find_many();

                $inclusive_tax = $exclusive_tax = 0;
                $inclusive_tax_fixed = 0;
                $inclusive_tax_percents = 0;

                foreach ($taxes as $tax) {

                    /* filter plan taxes */

                    /* Type */
                    if (
                        $tax['billing_type'] != get_user_option($_SESSION['user']['id'], 'billing_details_type') &&
                        $tax['billing_type'] != 'both'
                    ) {
                        continue;
                    }

                    /* Countries */
                    if (
                        $tax['countries'] &&
                        !in_array(get_user_option($_SESSION['user']['id'], 'billing_country'), explode(',', $tax['countries']))
                    ) {
                        continue;
                    }

                    /* Create variable */
                    $plan_taxes[$tax['id']]['id'] = $tax['id'];
                    $plan_taxes[$tax['id']]['name'] = $tax['name'];
                    $plan_taxes[$tax['id']]['description'] = $tax['description'];
                    $plan_taxes[$tax['id']]['type'] = $tax['type'] == 'inclusive' ? __("Inclusive") : __("Exclusive");
                    $plan_taxes[$tax['id']]['value_formatted'] = $tax['value_type'] == 'percentage' ? (float)$tax['value'] . '%' : price_format($tax['value'], $config['currency_code']);

                    /* calculate inclusive taxes */
                    if ($tax['type'] == 'inclusive') {
                        //$inclusive_tax += $tax['value_type'] == 'percentage' ? $price * ($tax['value'] / 100) : $tax['value'];
                        if ($tax['value_type'] == 'percentage') {
                            $inclusive_tax_percents += $tax['value'];
                        } else {
                            $inclusive_tax_fixed += $tax['value'];
                        }
                    }

                    $tax_ids[] = $tax['id'];
                }

                $inclusive_tax = $price - ($price / (1 + $inclusive_tax_percents / 100));
                $inclusive_tax += $inclusive_tax_fixed;

                $price_without_inclusive = $price - $inclusive_tax;

                /* calculate exclusive taxes */
                foreach ($taxes as $tax) {
                    /* filter plan taxes */

                    /* Type */
                    if (
                        $tax['billing_type'] != get_user_option($_SESSION['user']['id'], 'billing_details_type') &&
                        $tax['billing_type'] != 'both'
                    ) {
                        continue;
                    }

                    /* Countries */
                    if (
                        $tax['countries'] &&
                        !in_array(get_user_option($_SESSION['user']['id'], 'billing_country'), explode(',', $tax['countries']))
                    ) {
                        continue;
                    }

                    if ($tax['type'] == 'exclusive') {
                        $exclusive_tax += $tax['value_type'] == 'percentage' ? $price_without_inclusive * ($tax['value'] / 100) : $tax['value'];
                    }
                }
                /* total price */
                $price += $exclusive_tax;
            }
        }

        $title = $plan['name'];
        $amount = price_format($price,$config['currency_code']);

        $payment_type = "prepaid_plan";

        $access_token = uniqid();
        $_SESSION['quickad'][$access_token]['name'] = $title;
        $_SESSION['quickad'][$access_token]['amount'] = $price;
        $_SESSION['quickad'][$access_token]['base_amount'] = $base_amount;
        $_SESSION['quickad'][$access_token]['payment_type'] = $payment_type;
        $_SESSION['quickad'][$access_token]['sub_id'] = $_REQUEST['buy-prepaid-plan'];
        $_SESSION['quickad'][$access_token]['taxes_ids'] = implode(',',$tax_ids);
        $_SESSION['quickad'][$access_token]['price_without_inclusive'] = price_format($price_without_inclusive, $config['currency_code']);
        $_SESSION['quickad'][$access_token]['taxes'] = $plan_taxes;

        headerRedirect(url('PAYMENT', false).'/'.$access_token);
    } else
	{
		$upgrades = array();

		if(isset($_GET['change_plan']) && $_GET['change_plan'] == "changeplan")
		{
            
            //check_validation_for_subscribePlan();
            $sub_info = get_user_membership_detail($_SESSION['user']['id']);

            // custom settings
            $plan_custom = ORM::for_table($config['db']['pre'].'plan_options')
                ->where('active', 1)
                ->order_by_asc('position')
                ->find_many();
            if(!empty($plan_custom)) {
                foreach ($plan_custom as $custom) {
                    if (!empty($custom['title']) && trim($custom['title']) != '') {
                        $custom['title'] = get_planSettings_title_by_id($custom['id']);
                    }
                }
            }

            $sub_types = array();

            $plan = json_decode(get_option('free_membership_plan'), true);
            if($plan['status']){
                if($plan['id'] == $sub_info['id']) {
                    $sub_types[$plan['id']]['Selected'] = 1;
                } else {
                    $sub_types[$plan['id']]['Selected'] = 0;
                }

                $sub_types[$plan['id']]['id'] = $plan['id'];
                $sub_types[$plan['id']]['title'] = $plan['name'];

                /* hack to visible these plans */
                $sub_types[$plan['id']]['monthly_price_number'] = 1;
                $sub_types[$plan['id']]['annual_price_number'] = 1;
                $sub_types[$plan['id']]['lifetime_price_number'] = 1;

                $sub_types[$plan['id']]['monthly_price'] = price_format(0,$config['currency_code']);
                $sub_types[$plan['id']]['annual_price'] = price_format(0,$config['currency_code']);
                $sub_types[$plan['id']]['lifetime_price'] = price_format(0,$config['currency_code']);

                $settings = $plan['settings'];
                $sub_types[$plan['id']]['settings'] = $settings;

                $sub_types[$plan['id']]['ai_model'] = $settings['ai_model'];
                $sub_types[$plan['id']]['ai_chat'] = $settings['ai_chat'];
                $sub_types[$plan['id']]['ai_chatbots'] = !empty($settings['ai_chatbots']) ? $settings['ai_chatbots'] : [];
                $sub_types[$plan['id']]['ai_code'] = $settings['ai_code'];
                $sub_types[$plan['id']]['show_ads'] = $settings['show_ads'];
                $sub_types[$plan['id']]['live_chat'] = $settings['live_chat'];
                $sub_types[$plan['id']]['ai_templates'] = $settings['ai_templates'];
                $sub_types[$plan['id']]['ai_words_limit'] = ($settings['ai_words_limit'] == -1)? __("Unlimited"): $settings['ai_words_limit'];
                $sub_types[$plan['id']]['ai_images_limit'] = ($settings['ai_images_limit'] == -1)? __("Unlimited"): $settings['ai_images_limit'];
                $sub_types[$plan['id']]['ai_speech_to_text_limit'] = ($settings['ai_speech_to_text_limit'] == -1)? __("Unlimited"): $settings['ai_speech_to_text_limit'];
                $sub_types[$plan['id']]['ai_speech_to_text_file_limit'] = ($settings['ai_speech_to_text_file_limit'] == -1)? __("Unlimited"): $settings['ai_speech_to_text_file_limit'];
                $sub_types[$plan['id']]['ai_text_to_speech_limit'] = ($settings['ai_text_to_speech_limit'] == -1)? __("Unlimited"): $settings['ai_text_to_speech_limit'];

                $sub_types[$plan['id']]['custom_settings'] = '';
                if(!empty($plan_custom)) {
                    foreach ($plan_custom as $custom) {
                        if(!empty($custom['title']) && trim($custom['title']) != '') {
                            $tpl = '<li><span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span> ' . $custom['title'] . '</li>';

                            if (isset($settings['custom'][$custom['id']]) && $settings['custom'][$custom['id']] == '1') {
                                $tpl = '<li><span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span> ' . $custom['title'] . '</li>';
                            }
                            $sub_types[$plan['id']]['custom_settings'] .= $tpl;
                        }
                    }
                }

                /* get template names */
                $ai_template_titles = ORM::for_table($config['db']['pre'].'ai_templates')
                    ->select_expr('1 as custom_group, GROUP_CONCAT(title) as titles')
                    ->where_raw('slug IN ("'.join('","', $settings['ai_templates']).'")')
                    ->order_by_asc('position')
                    ->group_by('custom_group')
                    ->find_one();
                $sub_types[$plan['id']]['ai_template_titles'] = $ai_template_titles['titles'];

                $ai_custom_template_titles = ORM::for_table($config['db']['pre'].'ai_custom_templates')
                    ->select_expr('1 as custom_group, GROUP_CONCAT(title) as titles')
                    ->where_raw('slug IN ("'.join('","', $settings['ai_templates']).'")')
                    ->order_by_asc('position')
                    ->group_by('custom_group')
                    ->find_one();
                $sub_types[$plan['id']]['ai_template_titles'] .= ','.$ai_custom_template_titles['titles'];
            }

            $plan = json_decode(get_option('trial_membership_plan'), true);
            if($plan['status']){
                if($plan['id'] == $sub_info['id']) {
                    $sub_types[$plan['id']]['Selected'] = 1;
                } else {
                    $sub_types[$plan['id']]['Selected'] = 0;
                }

                $sub_types[$plan['id']]['id'] = $plan['id'];
                $sub_types[$plan['id']]['title'] = $plan['name'];

                /* hack to visible these plans */
                $sub_types[$plan['id']]['monthly_price_number'] = 1;
                $sub_types[$plan['id']]['annual_price_number'] = 1;
                $sub_types[$plan['id']]['lifetime_price_number'] = 1;

                $sub_types[$plan['id']]['monthly_price'] = price_format(0,$config['currency_code']);
                $sub_types[$plan['id']]['annual_price'] = price_format(0,$config['currency_code']);
                $sub_types[$plan['id']]['lifetime_price'] = price_format(0,$config['currency_code']);

                $settings = $plan['settings'];
                $sub_types[$plan['id']]['settings'] = $settings;

                $sub_types[$plan['id']]['ai_model'] = $settings['ai_model'];
                $sub_types[$plan['id']]['ai_chat'] = $settings['ai_chat'];
                $sub_types[$plan['id']]['ai_chatbots'] = !empty($settings['ai_chatbots']) ? $settings['ai_chatbots'] : [];
                $sub_types[$plan['id']]['ai_code'] = $settings['ai_code'];
                $sub_types[$plan['id']]['show_ads'] = $settings['show_ads'];
                $sub_types[$plan['id']]['live_chat'] = $settings['live_chat'];
                $sub_types[$plan['id']]['ai_templates'] = $settings['ai_templates'];
                $sub_types[$plan['id']]['ai_words_limit'] = ($settings['ai_words_limit'] == -1)? __("Unlimited"): $settings['ai_words_limit'];
                $sub_types[$plan['id']]['ai_images_limit'] = ($settings['ai_images_limit'] == -1)? __("Unlimited"): $settings['ai_images_limit'];
                $sub_types[$plan['id']]['ai_speech_to_text_limit'] = ($settings['ai_speech_to_text_limit'] == -1)? __("Unlimited"): $settings['ai_speech_to_text_limit'];
                $sub_types[$plan['id']]['ai_speech_to_text_file_limit'] = ($settings['ai_speech_to_text_file_limit'] == -1)? __("Unlimited"): $settings['ai_speech_to_text_file_limit'];
                $sub_types[$plan['id']]['ai_text_to_speech_limit'] = ($settings['ai_text_to_speech_limit'] == -1)? __("Unlimited"): $settings['ai_text_to_speech_limit'];

                $sub_types[$plan['id']]['custom_settings'] = '';
                if(!empty($plan_custom)) {
                    foreach ($plan_custom as $custom) {
                        if(!empty($custom['title']) && trim($custom['title']) != '') {
                            $tpl = '<li><span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span> ' . $custom['title'] . '</li>';

                            if (isset($settings['custom'][$custom['id']]) && $settings['custom'][$custom['id']] == '1') {
                                $tpl = '<li><span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span> ' . $custom['title'] . '</li>';
                            }
                            $sub_types[$plan['id']]['custom_settings'] .= $tpl;
                        }
                    }
                }

                /* get template names */
                $ai_template_titles = ORM::for_table($config['db']['pre'].'ai_templates')
                    ->select_expr('1 as custom_group, GROUP_CONCAT(title) as titles')
                    ->where_raw('slug IN ("'.join('","', $settings['ai_templates']).'")')
                    ->order_by_asc('position')
                    ->group_by('custom_group')
                    ->find_one();
                $sub_types[$plan['id']]['ai_template_titles'] = $ai_template_titles['titles'];

                $ai_custom_template_titles = ORM::for_table($config['db']['pre'].'ai_custom_templates')
                    ->select_expr('1 as custom_group, GROUP_CONCAT(title) as titles')
                    ->where_raw('slug IN ("'.join('","', $settings['ai_templates']).'")')
                    ->order_by_asc('position')
                    ->group_by('custom_group')
                    ->find_one();
                $sub_types[$plan['id']]['ai_template_titles'] .= ','.$ai_custom_template_titles['titles'];
            }

            $total_monthly = $total_annual = $total_lifetime = 0;

            $rows = ORM::for_table($config['db']['pre'].'plans')
                ->where('status', '1')
                ->order_by_asc('position')
                ->find_many();

            foreach ($rows as $plan)
            {
                if($plan['id'] == $sub_info['id']) {
                    $sub_types[$plan['id']]['Selected'] = 1;
                } else {
                    $sub_types[$plan['id']]['Selected'] = 0;
                }

                $sub_types[$plan['id']]['id'] = $plan['id'];
                $sub_types[$plan['id']]['title'] = $plan['name'];
                $sub_types[$plan['id']]['recommended'] = $plan['recommended'];

                $total_monthly += $plan['monthly_price'];
                $total_annual += $plan['annual_price'];
                $total_lifetime += $plan['lifetime_price'];

                $sub_types[$plan['id']]['monthly_price_number'] = $plan['monthly_price'];
                $sub_types[$plan['id']]['annual_price_number'] = $plan['annual_price'];
                $sub_types[$plan['id']]['lifetime_price_number'] = $plan['lifetime_price'];

                $sub_types[$plan['id']]['monthly_price'] = price_format($plan['monthly_price'],$config['currency_code']);
                $sub_types[$plan['id']]['annual_price'] = price_format($plan['annual_price'],$config['currency_code']);
                $sub_types[$plan['id']]['lifetime_price'] = price_format($plan['lifetime_price'],$config['currency_code']);

                $settings = json_decode($plan['settings'], true);
                $sub_types[$plan['id']]['settings'] = $settings;

                $sub_types[$plan['id']]['ai_model'] = $settings['ai_model'];
                $sub_types[$plan['id']]['ai_chat'] = $settings['ai_chat'];
                $sub_types[$plan['id']]['ai_chatbots'] = !empty($settings['ai_chatbots']) ? $settings['ai_chatbots'] : [];
                $sub_types[$plan['id']]['ai_code'] = $settings['ai_code'];
                $sub_types[$plan['id']]['show_ads'] = $settings['show_ads'];
                $sub_types[$plan['id']]['live_chat'] = $settings['live_chat'];
                $sub_types[$plan['id']]['ai_templates'] = $settings['ai_templates'];
                $sub_types[$plan['id']]['ai_words_limit'] = ($settings['ai_words_limit'] == -1)? __("Unlimited"): $settings['ai_words_limit'];
                $sub_types[$plan['id']]['ai_images_limit'] = ($settings['ai_images_limit'] == -1)? __("Unlimited"): $settings['ai_images_limit'];
                $sub_types[$plan['id']]['ai_speech_to_text_limit'] = ($settings['ai_speech_to_text_limit'] == -1)? __("Unlimited"): $settings['ai_speech_to_text_limit'];
                $sub_types[$plan['id']]['ai_speech_to_text_file_limit'] = ($settings['ai_speech_to_text_file_limit'] == -1)? __("Unlimited"): $settings['ai_speech_to_text_file_limit'];
                $sub_types[$plan['id']]['ai_text_to_speech_limit'] = ($settings['ai_text_to_speech_limit'] == -1)? __("Unlimited"): $settings['ai_text_to_speech_limit'];

                $sub_types[$plan['id']]['custom_settings'] = '';
                if(!empty($plan_custom)) {
                    foreach ($plan_custom as $custom) {
                        if(!empty($custom['title']) && trim($custom['title']) != '') {
                            $tpl = '<li><span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span> ' . $custom['title'] . '</li>';

                            if (isset($settings['custom'][$custom['id']]) && $settings['custom'][$custom['id']] == '1') {
                                $tpl = '<li><span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span> ' . $custom['title'] . '</li>';
                            }
                            $sub_types[$plan['id']]['custom_settings'] .= $tpl;
                        }
                    }
                }

                /* get template names */
                $ai_template_titles = ORM::for_table($config['db']['pre'].'ai_templates')
                    ->select_expr('1 as custom_group, GROUP_CONCAT(title) as titles')
                    ->where_raw('slug IN ("'.join('","', $settings['ai_templates']).'")')
                    ->order_by_asc('position')
                    ->group_by('custom_group')
                    ->find_one();
                $sub_types[$plan['id']]['ai_template_titles'] = $ai_template_titles['titles'];

                $ai_custom_template_titles = ORM::for_table($config['db']['pre'].'ai_custom_templates')
                    ->select_expr('1 as custom_group, GROUP_CONCAT(title) as titles')
                    ->where_raw('slug IN ("'.join('","', $settings['ai_templates']).'")')
                    ->order_by_asc('position')
                    ->group_by('custom_group')
                    ->find_one();
                $sub_types[$plan['id']]['ai_template_titles'] .= ','.$ai_custom_template_titles['titles'];
            }

            $rows = ORM::for_table($config['db']['pre'].'prepaid_plans')
                ->where('status', '1')
                ->order_by_asc('position')
                ->find_many();
            $prepaid_plans = array();
            foreach ($rows as $plan)
            {
                $prepaid_plans[$plan['id']]['id'] = $plan['id'];
                $prepaid_plans[$plan['id']]['title'] = $plan['name'];
                $prepaid_plans[$plan['id']]['recommended'] = $plan['recommended'];
                $prepaid_plans[$plan['id']]['price'] = price_format($plan['price'],$config['currency_code']);

                $settings = json_decode($plan['settings'], true);
                $prepaid_plans[$plan['id']]['ai_words_limit'] = $settings['ai_words_limit'];
                $prepaid_plans[$plan['id']]['ai_images_limit'] = $settings['ai_images_limit'];
                $prepaid_plans[$plan['id']]['ai_speech_to_text_limit'] = $settings['ai_speech_to_text_limit'];
                $prepaid_plans[$plan['id']]['ai_text_to_speech_limit'] = $settings['ai_text_to_speech_limit'];
            }

            //Print Template
            HtmlTemplate::display('global/membership_plan', array(
                'sub_types' => $sub_types,
                'total_monthly' => $total_monthly,
                'total_annual' => $total_annual,
                'total_lifetime' => $total_lifetime,
                'prepaid_plans' => $prepaid_plans,
            ));
            exit;
		}
        else if(isset($_GET['action']) && $_GET['action'] == "cancel_auto_renew")
        {
            $action = $_GET['action'];

            $sub_info = get_user_membership_detail($_SESSION['user']['id']);

            if ( isset($sub_info['id'])) {

                $subscription = ORM::for_table($config['db']['pre'].'upgrades')
                    ->where('user_id', $_SESSION['user']['id'])
                    ->find_one();


                if ( $subscription['pay_mode'] == 'recurring' ) {
                    try {
                        cancel_recurring_payment($_SESSION['user']['id']);
                    } catch (\Exception $exception) {
                        error_log($exception->getCode());
                        error_log($exception->getMessage());
                    }
                }
                transfer($link['MEMBERSHIP'],__("Settings Saved Successfully"),__("Settings Saved Successfully"));
                exit;
            }
        }
		else
		{

            $info = ORM::for_table($config['db']['pre'].'upgrades')
                ->where('user_id', $_SESSION['user']['id'])
                ->find_one();

            $show_cancel_button = 0;
            $payment_mode = 'one_time';
            if(empty($info['sub_id'])){
                $sub_info = get_user_membership_detail($_SESSION['user']['id']);
                $price = 0;
                $upgrades_term = $upgrades_start_date = $upgrades_expiry_date = '-';
            }else{
                if($info['sub_id'] == 'trial'){
                    $sub_info = json_decode(get_option('trial_membership_plan'), true);
                    $price = 0;
                    $upgrades_term = '-';
                }else{
                    $sub_info = ORM::for_table($config['db']['pre'].'plans')
                        ->where('id', $info['sub_id'])
                        ->find_one();
                    $price = $sub_info['monthly_price'];
                    $payment_mode = $info['pay_mode'];
                    $show_cancel_button = (int) ($payment_mode == 'recurring');
                }
                $upgrades_start_date = date("d M Y",$info['upgrade_lasttime']);
                $upgrades_expiry_date = date("d M Y",$info['upgrade_expires']);
            }

            $upgrades_title = $sub_info['name'];
            $upgrades_cost = price_format($price,$config['currency_code']);

            //Print Template
            HtmlTemplate::display('global/membership_current', array(
                'upgrades_title' => $upgrades_title,
                'upgrades_start_date' => $upgrades_start_date,
                'upgrades_expiry_date' => $upgrades_expiry_date,
                'payment_mode' => $payment_mode,
                'show_cancel_button' => $show_cancel_button
            ));
            exit;
		}
	}
}
else
{
    headerRedirect($link['LOGIN']);
}

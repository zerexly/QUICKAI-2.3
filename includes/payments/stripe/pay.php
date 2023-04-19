<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

include 'stripe-php/init.php';

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');


// manually set action for stripe payments
if (empty($action)) {
    $action = 'stripe_payment';
}

$currency = $config['currency_code'];
$user_id = $_SESSION['user']['id'];
$code = '';

if (isset($access_token)) {
    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
    $currency = $config['currency_code'];
    $user_id = $_SESSION['user']['id'];

    $total = $_SESSION['quickad'][$access_token]['amount'];
    $taxes_ids = isset($_SESSION['quickad'][$access_token]['taxes_ids']) ? $_SESSION['quickad'][$access_token]['taxes_ids'] : null;

    if ($payment_type == "subscr") {
        $base_amount = $_SESSION['quickad'][$access_token]['base_amount'];
        $plan_interval = $_SESSION['quickad'][$access_token]['plan_interval'];
        $payment_mode = $_SESSION['quickad'][$access_token]['payment_mode'];
        $package_id = $_SESSION['quickad'][$access_token]['sub_id'];

        if ($plan_interval == 'LIFETIME') {
            $payment_mode = 'one_time';
        }

        $cancel_url = $link['PAYMENT'] . "/?access_token=" . $access_token . "&status=cancel";

        $stripe_secret_key = get_option('stripe_secret_key');
        $stripe_publishable_key = get_option('stripe_publishable_key');
    } elseif ($payment_type == "premium" || $payment_type == "banner-advertise") {
        $payment_mode = "one_time";
        $item_pro_id = $_SESSION['quickad'][$access_token]['product_id'];
        $title = $_SESSION['quickad'][$access_token]['name'];
        $amount = $_SESSION['quickad'][$access_token]['amount'];
        $base_amount = isset($_SESSION['quickad'][$access_token]['base_amount']) ? $_SESSION['quickad'][$access_token]['base_amount'] : $amount;
        $trans_desc = $_SESSION['quickad'][$access_token]['trans_desc'];

        if ($payment_type == "premium") {
            $item_featured = $_SESSION['quickad'][$access_token]['featured'];
            $item_urgent = $_SESSION['quickad'][$access_token]['urgent'];
            $item_highlight = $_SESSION['quickad'][$access_token]['highlight'];
        } else {
            $item_featured = 0;
            $item_urgent = 0;
            $item_highlight = 0;
        }

        $cancel_url = $link['PAYMENT'] . "/?access_token=" . $access_token . "&status=cancel";

        $stripe_secret_key = get_option('stripe_secret_key');
        $stripe_publishable_key = get_option('stripe_publishable_key');
    } else {
        $title = $_SESSION['quickad'][$access_token]['name'];
        $plan_interval = 'Order';
        $payment_mode = 'one_time';
        $order_id = $_SESSION['quickad'][$access_token]['order_id'];
        $restaurant_id = $_SESSION['quickad'][$access_token]['restaurant_id'];
        $restaurant = ORM::for_table($config['db']['pre'] . 'restaurant')
            ->find_one($restaurant_id);

        $userdata = get_user_data(null, $restaurant['user_id']);
        $currency = !empty($userdata['currency']) ? $userdata['currency'] : get_option('currency_code');

        $cancel_url = $link['PAYMENT'] . "/?access_token=" . $access_token;

        $stripe_secret_key = get_restaurant_option($restaurant_id, 'restaurant_stripe_secret_key');
        $stripe_publishable_key = get_restaurant_option($restaurant_id, 'restaurant_stripe_publishable_key');
    }
}

if (!empty($action)) {
    switch ($action) {
        case 'stripe_payment':

            /* Initiate Stripe */
            \Stripe\Stripe::setApiKey($stripe_secret_key);
            \Stripe\Stripe::setApiVersion('2020-08-27');

            $stripe_formatted_price = in_array($currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? number_format($total, 0, '.', '') : number_format($total, 2, '.', '') * 100;

            switch ($payment_mode) {
                case 'one_time':

                    if ($payment_type == "subscr") {
                        $meta_data = array(
                            'user_id' => $user_id,
                            'package_id' => $package_id,
                            'payment_type' => $payment_type,
                            'payment_frequency' => $plan_interval,
                            'base_amount' => $base_amount,
                            'taxes_ids' => $taxes_ids
                        );
                    } elseif ($payment_type == "premium" || $payment_type == "banner-advertise") {
                        $meta_data = array(
                            'user_id' => $user_id,
                            'product_id' => $item_pro_id,
                            'title' => $title,
                            'amount' => $amount,
                            'trans_desc' => $trans_desc,
                            'payment_type' => $payment_type,
                            'taxes_ids' => $taxes_ids,
                            'item_featured' => $item_featured,
                            'item_urgent' => $item_urgent,
                            'item_highlight' => $item_highlight
                        );
                    } else {
                        $meta_data = array(
                            'order_id' => $order_id,
                            'restaurant_id' => $restaurant_id,
                            'amount' => $total
                        );
                    }

                    try {
                        $stripe_session = \Stripe\Checkout\Session::create([
                            'line_items' => array(
                                array(
                                    'name' => $title,
                                    'description' => $title,
                                    'amount' => $stripe_formatted_price,
                                    'currency' => $currency,
                                    'quantity' => 1,
                                )
                            ),
                            'metadata' => $meta_data,
                            'success_url' => $link['PAYMENT'] . "/?access_token=" . $access_token . "&i=stripe&action=stripe_ipn",
                            'cancel_url' => $cancel_url,
                        ]);
                    } catch (\Exception $exception) {
                        error_log($exception->getMessage());
                        payment_fail_save_detail($access_token);
                        payment_error("error", addslashes($exception->getMessage()), $access_token);
                    }
                    break;

                case 'recurring':

                    try {
                        $stripe_product = \Stripe\Product::retrieve($package_id);
                    } catch (\Exception $exception) {

                    }

                    if (!isset($stripe_product)) {
                        try {
                            $stripe_product = \Stripe\Product::create(array(
                                'id' => $package_id,
                                'name' => $title,
                            ));
                        } catch (Exception $exception) {
                            error_log($exception->getMessage());
                            payment_fail_save_detail($access_token);
                            payment_error("error", $exception->getMessage(), $access_token);
                        }
                    }

                    $stripe_plan_id = $package_id . '_' . $plan_interval . '_' . $stripe_formatted_price . '_' . $currency;

                    try {
                        $stripe_plan = \Stripe\Plan::retrieve($stripe_plan_id);
                    } catch (\Exception $exception) {
                    }

                    if (!isset($stripe_plan)) {
                        try {
                            $stripe_plan = \Stripe\Plan::create([
                                'amount' => $stripe_formatted_price,
                                'interval' => 'day',
                                'interval_count' => $plan_interval == 'MONTHLY' ? 30 : 365,
                                'product' => $stripe_product->id,
                                'currency' => $currency,
                                'id' => $stripe_plan_id,
                            ]);
                        } catch (\Exception $exception) {
                            error_log($exception->getMessage());
                            payment_fail_save_detail($access_token);
                            payment_error("error", $exception->getMessage(), $access_token);
                        }
                    }

                    try {
                        $stripe_session = \Stripe\Checkout\Session::create(array(
                            'subscription_data' => array(
                                'items' => array(
                                    array('plan' => $stripe_plan->id)
                                ),
                                'metadata' => array(
                                    'user_id' => $user_id,
                                    'package_id' => $package_id,
                                    'payment_frequency' => $plan_interval,
                                    'base_amount' => $base_amount,
                                    'taxes_ids' => $taxes_ids,
                                    'payment_type' => $payment_type
                                ),
                            ),
                            'metadata' => array(
                                'user_id' => $user_id,
                                'package_id' => $package_id,
                                'payment_frequency' => $plan_interval,
                                'base_amount' => $base_amount,
                                'taxes_ids' => $taxes_ids,
                                'payment_type' => $payment_type
                            ),
                            'success_url' => $link['PAYMENT'] . "/?access_token=" . $access_token . "&i=stripe&action=stripe_ipn",
                            'cancel_url' => $link['PAYMENT'] . "/?access_token=" . $access_token . "&status=cancel",
                        ));
                    } catch (\Exception $exception) {
                        error_log($exception->getMessage());
                        payment_fail_save_detail($access_token);
                        payment_error("error", $exception->getMessage(), $access_token);
                    }

                    break;
            }

            // redirect to stripe
            headerRedirect($stripe_session->url);
            die();

            break;

        case 'stripe_ipn':

            /* Success */
            if ($payment_type == "order") {

                $resto = ORM::for_table($config['db']['pre'] . 'restaurant')
                    ->find_one($restaurant_id);
                ?>
                <script>
                    <?php if(!empty($_SESSION['quickad'][$access_token]['whatsapp_url'])){ ?>
                    window.open("<?php echo $_SESSION['quickad'][$access_token]['whatsapp_url'] ?>", "_blank");
                    <?php } ?>
                    location.href = '<?php echo $config['site_url'] . $resto['slug'] . '?return=success' ?>';

                </script>
                <?php
            } else {
                message(__('Success'), __('Payment Successful'), $link['TRANSACTION']);
            }
            unset($_SESSION['quickad'][$access_token]);
            exit();
            break;
    }
}
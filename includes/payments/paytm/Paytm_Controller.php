<?php
/**
 * Created by PhpStorm.
 * User: SSI
 * Date: 09-03-2019
 * Time: 12:45 PM
 */

namespace inc\payment;


use lib\core\FormMsg;
use lib\core\SessionMsg;
use lib\core\System;
use lib\entity\Cart;
use lib\entity\Order;
use lib\entity\Product;
use lib\entity\Taxes;
use lib\entity\User;
use lib\payment\paytm\Paytm;

class Paytm_Controller
{
    /**
     * Init checkout transaction
     */
    function init()
    {
        header("Pragma: no-cache");
        header("Cache-Control: no-cache");
        header("Expires: 0");

        $cart = new Cart();
        $product = new Product();
        $paytm = new Paytm();
        foreach ($cart->products as $index => $cart_product) {
            $product->load_by_id($cart_product->id)->load_all_data();
            if ($product->seller == wp_get_current_user()->ID) {
                $cart->remove_from_cart($index);
                return 'OWN_PRODUCT';
                break;
            }
            $price = $cart_product->license == 0 ? $product->regular_price : $product->extended_price;
            if (!$price) {
                continue;
            }
            $license = $cart_product->license == 0 ? 'Regular License' : 'Extended License';
            $item = new \stdClass();
            $item->name = $product->title . ' (' . $license . ')';
            $item->price = $price;
            $item->qty = 1;
            $paytm->addProduct($item);
        }
        $tax = new Taxes();
        $taxes = $tax->get_taxes_by_payment();
        if (isset($taxes[Order::PAYMENT_METHOD_PAYTM])) {
            foreach ($taxes[Order::PAYMENT_METHOD_PAYTM] as $t) {
                $item = new \stdClass();
                $item->name = $t->title;
                $item->price = $t->fee_dollar;
                $item->qty = 1;
                $paytm->addProduct($item);
            }
        }
        $paytm->sendRequest();
        return true;
    }



    /**
     * Process Checkout cancel request.
     * @param $msg
     */
    function cancel($msg)
    {
        wp_redirect(wp_sanitize_redirect(add_query_arg(array(
            'codentheme_action' => 'paytm-error',
            'error_msg' => urlencode($msg),
        ), remove_query_arg(Paytm::$remove_parameters, System::get_current_page_url()))));
        exit;
    }
}
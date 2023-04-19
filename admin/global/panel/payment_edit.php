<?php
include '../datatable-json/includes.php';

$info = ORM::for_table($config['db']['pre'].'payments')
    ->use_id_column('payment_id')
    ->find_one($_GET['id']);
$status = $info['payment_install'];
$folder = $info['payment_folder'];
?>

<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo ucfirst($folder);?> <?php _e('- Settings') ?></h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn-icon btn-primary" title="<?php _e('Save') ?>">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn-icon slidePanel-close" title="<?php _e('Close') ?>">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form method="post" id="sidePanel_form" data-ajax-action="paymentEdit">
                <input type="hidden" name="id" value="<?php _esc($_GET['id'])?>">
                <?php
                if($folder == "paypal") {
                    ?>
                    <div class="form-group">
                        <h4><?php _e('Integration Steps:') ?></h4>
                        <ol>
                            <li><?php _e('Go to <a href="https://developer.paypal.com/" target="_blank">PayPal Developer Console</a> and Login to your account after clicking the Log into Dashboard button.') ?></li>
                            <li><?php _e('Go to <strong>REST API apps</strong> section and click the <strong>Create App</strong> button.') ?></li>
                            <li><?php _e('Add your own details for the new app and create it.') ?></li>
                            <li><?php _e("Switch to Live by clicking the button near your new App's Name.") ?></li>
                            <li><?php _e('Copy the <strong>Client ID</strong> and <strong>Secret</strong> and paste below.') ?></li>
                            <li><?php _e('Go to the newly created App in the Paypal Developer Console and click on the <strong>Add Webhook</strong> button.') ?></li>
                            <li><?php _e('In the Webhook Url field, paste the Webhook Url') ?> <code><?php echo _esc($config['site_url'], false).'webhook/paypal'?></code></li>
                            <li><?php _e('In the <strong>Event types</strong> field, check the <strong>Payment sale completed</strong> event and submit the Webhook.') ?></li>
                        </ol>
                    </div>
                    <?php
                }elseif($folder == "stripe"){
                    ?>
                    <div class="form-group">
                        <h4><?php _e('Integration Steps:') ?></h4>
                        <ol>
                            <li><?php _e('Go to <a href="https://dashboard.stripe.com/">Stripe Dashboard</a> and Login to your account.') ?></li>
                            <li><?php _e('Go to <a href="https://dashboard.stripe.com/account/apikeys">Stripe API Keys</a> page.') ?></li>
                            <li><?php _e('Make sure your API keys are set to <strong>Live Mode</strong> so that you can accept real payments.') ?></li>
                            <li><?php _e('Copy the <strong>Publishable key</strong> and <strong>Secret key</strong> and paste below.') ?></li>
                            <li><?php _e('From the sidebar, under <strong>Developers</strong> click on <strong>Webhooks</strong> link.') ?></li>
                            <li><?php _e('Click on the <strong>Add endpoint</strong> button</li>') ?>
                            <li><?php _e('In the <strong>Endpoint URL</strong> field, paste the Webhook Url') ?> <code><?php echo _esc($config['site_url'], false).'webhook/stripe'?></code></li>
                            <li><?php _e('In the <strong>Events to send</strong> field, select the <strong>checkout.session.completed</strong>, <strong>invoice.paid</strong>, <strong>invoice.upcoming</strong> and click the <strong>Add endpoint</strong> button.') ?></li>
                            <li><?php _e('Copy the <strong>Signing secret</strong> key and paste below in <strong>Webhook Secret</strong> field.') ?></li>
                        </ol>
                    </div>
                    <?php
                }
                ?>
                <div class="form-group">
                    <label for="title"><?php _e('Title') ?></label>
                    <input name="title" id="title" type="text" class="form-control" value="<?php _esc($info['payment_title'])?>">
                </div>

                <div class="form-group">
                    <label for="install"><?php _e('Enable') ?></label>
                    <select name="install" id="install" class="form-control">
                        <option value="1" <?php if($status == '1') echo "selected"; ?>><?php _e('Enable') ?></option>
                        <option value="0" <?php if($status == '0') echo "selected"; ?>><?php _e('Disable') ?></option>
                    </select>
                </div>
                <?php
                if($folder == "paypal"){
                    ?>
                    <div class="form-group">
                        <label for="paypal_sandbox_mode"><?php _e('Live Mode/Sandbox Mode') ?></label>
                        <select name="paypal_sandbox_mode" id="paypal_sandbox_mode" class="form-control">
                            <option value="Yes" <?php if(get_option('paypal_sandbox_mode') == 'Yes') echo "selected"; ?>><?php _e('Sandbox Test Mode') ?></option>
                            <option value="No" <?php if(get_option('paypal_sandbox_mode') == 'No') echo "selected"; ?>><?php _e('Live Mode') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="paypal_payment_mode"><?php _e('Payment Mode') ?></label>
                        <select name="paypal_payment_mode" id="paypal_payment_mode" class="form-control">
                            <option value="one_time" <?php if(get_option('paypal_payment_mode') == 'one_time') echo "selected"; ?>><?php _e('One Time') ?></option>
                            <option value="recurring" <?php if(get_option('paypal_payment_mode') == 'recurring') echo "selected"; ?>><?php _e('Recurring') ?></option>
                            <option value="both" <?php if(get_option('paypal_payment_mode') == 'both') echo "selected"; ?>><?php _e('Both') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="paypal_api_client_id"><?php _e('Paypal API Client Id') ?></label>
                        <input name="paypal_api_client_id" id="paypal_api_client_id" type="text" class="form-control" value="<?php echo get_option('paypal_api_client_id')?>">
                    </div>
                    <div class="form-group">
                        <label for="paypal_api_secret"><?php _e('Paypal API Secret') ?></label>
                        <input name="paypal_api_secret" id="paypal_api_secret" type="text" class="form-control" value="<?php echo get_option('paypal_api_secret')?>">
                    </div>
                    <div class="form-group">
                        <label for="paypal_webhook"><?php _e('Paypal API WebHook Url') ?></label>
                        <input type="text" id="paypal_webhook" class="form-control" value="<?php echo _esc($config['site_url'], false).'webhook/paypal'?>" readonly>
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "stripe"){
                    ?>
                    <div class="form-group">
                        <label for="stripe_payment_mode"><?php _e('Payment Mode') ?></label>
                        <select name="stripe_payment_mode" id="stripe_payment_mode" class="form-control">
                            <option value="one_time" <?php if(get_option('stripe_payment_mode') == 'one_time') echo "selected"; ?>><?php _e('One Time') ?></option>
                            <option value="recurring" <?php if(get_option('stripe_payment_mode') == 'recurring') echo "selected"; ?>><?php _e('Recurring') ?></option>
                            <option value="both" <?php if(get_option('stripe_payment_mode') == 'both') echo "selected"; ?>><?php _e('Both') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stripe_publishable_key"><?php _e('Stripe Publishable Key') ?></label>
                        <input name="stripe_publishable_key" id="stripe_publishable_key" type="text" class="form-control" value="<?php echo get_option('stripe_publishable_key')?>">
                    </div>
                    <div class="form-group">
                        <label for="stripe_secret_key"><?php _e('Stripe Secret Key') ?></label>
                        <input name="stripe_secret_key" id="stripe_secret_key" type="text" class="form-control" value="<?php echo get_option('stripe_secret_key')?>">
                    </div>
                    <div class="form-group">
                        <label for="stripe_webhook_secret"><?php _e('Stripe Webhook Secret') ?></label>
                        <input name="stripe_webhook_secret" id="stripe_webhook_secret" type="text" class="form-control" value="<?php echo get_option('stripe_webhook_secret')?>">
                    </div>
                    <div class="form-group">
                        <label for="stripe_webhook"><?php _e('Stripe WebHook Url') ?></label>
                        <input type="text" id="stripe_webhook" class="form-control" value="<?php echo _esc($config['site_url'], false).'webhook/stripe'?>" readonly>
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "ccavenue"){
                    ?>
                    <div class="form-group">
                        <label for="CCAVENUE_MERCHANT_KEY"><?php _e('CCAvenue Merchant key') ?></label>
                        <input name="CCAVENUE_MERCHANT_KEY" id="CCAVENUE_MERCHANT_KEY" type="text" class="form-control" value="<?php echo get_option('CCAVENUE_MERCHANT_KEY')?>">
                    </div>
                    <div class="form-group">
                        <label for="CCAVENUE_ACCESS_CODE"><?php _e('CCAvenue Access Code') ?></label>
                        <input name="CCAVENUE_ACCESS_CODE" id="CCAVENUE_ACCESS_CODE" type="text" class="form-control" value="<?php echo get_option('CCAVENUE_ACCESS_CODE')?>">
                    </div>
                    <div class="form-group">
                        <label for="CCAVENUE_WORKING_KEY"><?php _e('CCAvenue Working Key') ?></label>
                        <input name="CCAVENUE_WORKING_KEY" id="CCAVENUE_WORKING_KEY" type="text" class="form-control" value="<?php echo get_option('CCAVENUE_WORKING_KEY')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "paytm"){
                    ?>
                    <div class="form-group">
                        <label for="PAYTM_ENVIRONMENT"><?php _e('Live Mode/Sandbox Mode') ?></label>
                        <select name="PAYTM_ENVIRONMENT" id="PAYTM_ENVIRONMENT" class="form-control">
                            <option value="TEST" <?php if(get_option('PAYTM_ENVIRONMENT') == 'TEST') echo "selected"; ?>><?php _e('Sandbox Test Mode') ?></option>
                            <option value="PROD" <?php if(get_option('PAYTM_ENVIRONMENT') == 'PROD') echo "selected"; ?>><?php _e('Live Mode') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="PAYTM_MERCHANT_KEY"><?php _e('Paytm Merchant key') ?></label>
                        <input name="PAYTM_MERCHANT_KEY" id="PAYTM_MERCHANT_KEY" type="text" class="form-control"  value="<?php echo get_option('PAYTM_MERCHANT_KEY')?>">
                    </div>
                    <div class="form-group">
                        <label for="PAYTM_MERCHANT_MID"><?php _e('Paytm Merchant ID') ?></label>
                        <input name="PAYTM_MERCHANT_MID" id="PAYTM_MERCHANT_MID" type="text" class="form-control" value="<?php echo get_option('PAYTM_MERCHANT_MID')?>">
                    </div>
                    <div class="form-group">
                        <label for="PAYTM_MERCHANT_WEBSITE"><?php _e('Paytm Website name') ?></label>
                        <input name="PAYTM_MERCHANT_WEBSITE" id="PAYTM_MERCHANT_WEBSITE" type="text" class="form-control" value="<?php echo get_option('PAYTM_MERCHANT_WEBSITE')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "paystack"){
                    ?>
                    <div class="form-group">
                        <label for="paystack_secret_key"><?php _e('Paystack Secret Key') ?></label>
                        <input name="paystack_secret_key" id="paystack_secret_key" type="text" class="form-control" value="<?php echo get_option('paystack_secret_key')?>">
                    </div>
                    <div class="form-group">
                        <label for="paystack_public_key"><?php _e('Paystack Public Key') ?></label>
                        <input name="paystack_public_key" id="paystack_public_key" type="text" class="form-control" value="<?php echo get_option('paystack_public_key')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "payumoney"){
                    ?>
                    <div class="form-group">
                        <label for="payumoney_sandbox_mode"><?php _e('Live Mode/Sandbox Mode') ?></label>
                        <select name="payumoney_sandbox_mode" id="payumoney_sandbox_mode" class="form-control">
                            <option value="test" <?php if(get_option('payumoney_sandbox_mode') == 'test') echo "selected"; ?>><?php _e('Sandbox Test Mode') ?></option>
                            <option value="live" <?php if(get_option('payumoney_sandbox_mode') == 'live') echo "selected"; ?>><?php _e('Live Mode') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payumoney_merchant_id"><?php _e('Payumoney Merchant ID') ?></label>
                        <input name="payumoney_merchant_id" id="payumoney_merchant_id" type="text" class="form-control" value="<?php echo get_option('payumoney_merchant_id')?>">
                    </div>
                    <div class="form-group">
                        <label for="payumoney_merchant_key"><?php _e('Payumoney Merchant Key') ?></label>
                        <input name="payumoney_merchant_key" id="payumoney_merchant_key" type="text" class="form-control" value="<?php echo get_option('payumoney_merchant_key')?>">
                    </div>
                    <div class="form-group">
                        <label for="payumoney_merchant_salt"><?php _e('Payumoney Merchant Salt') ?></label>
                        <input name="payumoney_merchant_salt" id="payumoney_merchant_salt" type="text" class="form-control" value="<?php echo get_option('payumoney_merchant_salt')?>">
                    </div>

                    <?php
                }
                ?>
                <?php
                if($folder == "2checkout"){
                    ?>
                    <div class="form-group">
                        <label for="2checkout_sandbox_mode"><?php _e('Live Mode/Sandbox Mode') ?></label>
                        <select name="2checkout_sandbox_mode" id="2checkout_sandbox_mode" class="form-control">
                            <option value="sandbox" <?php if(get_option('2checkout_sandbox_mode') == 'sandbox') echo "selected"; ?>><?php _e('Sandbox Test Mode') ?></option>
                            <option value="production" <?php if(get_option('2checkout_sandbox_mode') == 'production') echo "selected"; ?>><?php _e('Live Mode') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="checkout_account_number"><?php _e('2Checkout Account Number') ?></label>
                        <input name="checkout_account_number" id="checkout_account_number" type="text" class="form-control" value="<?php echo get_option('checkout_account_number')?>">
                    </div>
                    <div class="form-group">
                        <label for="checkout_public_key"><?php _e('Publishable Key') ?></label>
                        <input name="checkout_public_key" id="checkout_public_key" type="text" class="form-control" value="<?php echo get_option('checkout_public_key')?>">
                    </div>
                    <div class="form-group">
                        <label for="checkout_private_key"><?php _e('Private API Key') ?></label>
                        <input name="checkout_private_key" id="checkout_private_key" type="text" class="form-control" value="<?php echo get_option('checkout_private_key')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "moneybookers"){
                    ?>
                    <div class="form-group">
                        <label for="skrill_merchant_id"><?php _e('Skrill Merchant Id') ?></label>
                        <input name="skrill_merchant_id" id="skrill_merchant_id" type="text" class="form-control" value="<?php echo get_option('skrill_merchant_id')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "nochex"){
                    ?>
                    <div class="form-group">
                        <label for="nochex_merchant_id"><?php _e('NoChex Merchant Id') ?></label>
                        <input name="nochex_merchant_id" id="nochex_merchant_id" type="text" class="form-control" value="<?php echo get_option('nochex_merchant_id')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "wire_transfer"){
                    ?>
                    <div class="form-group">
                        <label for="company_bank_info"><?php _e('Bank Information') ?></label>
                        <textarea id="company_bank_info" name="company_bank_info" rows="6" type="text" class="form-control"><?php echo get_option('company_bank_info')?></textarea>
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "cheque"){
                    ?>
                    <div class="form-group">
                        <label for="company_cheque_info"><?php _e('Cheque Information') ?></label>
                        <textarea id="company_cheque_info" name="company_cheque_info" rows="6" type="text" placeholder="Write Cheque Information" class="form-control"><?php echo get_option('company_cheque_info')?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="cheque_payable_to"><?php _e('Cheque Payable To') ?></label>
                        <input id="cheque_payable_to" name="cheque_payable_to" type="text" class="form-control" value="<?php echo get_option('cheque_payable_to')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "mollie"){
                    ?>
                    <div class="form-group">
                        <label for="mollie_api_key"><?php _e('API Key') ?></label>
                        <input id="mollie_api_key" class="form-control" type="text"
                                   name="mollie_api_key"
                                   value="<?php echo get_option('mollie_api_key')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "iyzico"){
                    ?>
                    <div class="form-group">
                        <label for="iyzico_sandbox_mode"><?php _e('Live Mode/Sandbox Mode') ?></label>
                        <select id="iyzico_sandbox_mode" name="iyzico_sandbox_mode"  class="form-control">
                            <option value="test" <?php if(get_option('iyzico_sandbox_mode') == 'test') echo "selected"; ?>><?php _e('Sandbox Test Mode') ?></option>
                            <option value="live" <?php if(get_option('iyzico_sandbox_mode') == 'live') echo "selected"; ?>><?php _e('Live Mode') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="iyzico_api_key"><?php _e('Iyzico API Key') ?></label>
                        <input name="iyzico_api_key" id="iyzico_api_key" type="text" class="form-control" value="<?php echo get_option('iyzico_api_key')?>">
                    </div>
                    <div class="form-group">
                        <label for="iyzico_secret_key"><?php _e('Iyzico Secret Key') ?></label>
                        <input id="iyzico_secret_key" name="iyzico_secret_key" type="text" class="form-control" value="<?php echo get_option('iyzico_secret_key')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "midtrans"){
                    ?>
                    <div class="form-group">
                        <label for="midtrans_sandbox_mode"><?php _e('Live Mode/Sandbox Mode') ?></label>
                        <select name="midtrans_sandbox_mode" id="midtrans_sandbox_mode" class="form-control">
                            <option value="test" <?php if(get_option('midtrans_sandbox_mode') == 'test') echo "selected"; ?>><?php _e('Sandbox Test Mode') ?></option>
                            <option value="live" <?php if(get_option('midtrans_sandbox_mode') == 'live') echo "selected"; ?>><?php _e('Live Mode') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="midtrans_client_key"><?php _e('Midtrans Client Key') ?></label>
                        <input name="midtrans_client_key" id="midtrans_client_key" type="text" class="form-control" value="<?php echo get_option('midtrans_client_key')?>">
                    </div>

                    <div class="form-group">
                        <label for="midtrans_server_key"><?php _e('Midtrans Server Key') ?></label>
                        <input name="midtrans_server_key" id="midtrans_server_key" type="text" class="form-control" value="<?php echo get_option('midtrans_server_key')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "paytabs"){
                    ?>
                    <div class="form-group">
                        <label for="paytabs_profile_id"><?php _e('Paytabs Profile id') ?></label>
                        <input name="paytabs_profile_id" id="paytabs_profile_id" type="text" class="form-control" value="<?php echo get_option('paytabs_profile_id')?>">
                    </div>
                    <div class="form-group">
                        <label for="paytabs_secret_key"><?php _e('Paytabs Server Key') ?></label>
                        <input name="paytabs_secret_key" id="paytabs_secret_key" type="text" class="form-control" value="<?php echo get_option('paytabs_secret_key')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "telr"){
                    ?>
                    <div class="form-group">
                        <label for="telr_sandbox_mode"><?php _e('Live Mode/Sandbox Mode') ?></label>
                        <select name="telr_sandbox_mode" id="telr_sandbox_mode" class="form-control">
                            <option value="test" <?php if(get_option('telr_sandbox_mode') == 'test') echo "selected"; ?>><?php _e('Sandbox Test Mode') ?></option>
                            <option value="live" <?php if(get_option('telr_sandbox_mode') == 'live') echo "selected"; ?>><?php _e('Live Mode') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="telr_store_id"><?php _e('Telr Store ID') ?></label>
                        <input name="telr_store_id" id="telr_store_id" type="text" class="form-control" value="<?php echo get_option('telr_store_id')?>">
                    </div>
                    <div class="form-group">
                        <label for="telr_authkey"><?php _e('Telr Auth Key') ?></label>
                        <input name="telr_authkey" id="telr_authkey" type="text" class="form-control" value="<?php echo get_option('telr_authkey')?>">
                    </div>
                    <?php
                }
                ?>
                <?php
                if($folder == "razorpay"){
                    ?>
                    <div class="form-group">
                        <label for="razorpay_api_key"><?php _e('Razorpay API Key') ?></label>
                        <input name="razorpay_api_key" id="razorpay_api_key" type="text" class="form-control" value="<?php echo get_option('razorpay_api_key')?>">
                    </div>
                    <div class="form-group">
                        <label for="razorpay_secret_key"><?php _e('Razorpay Secret Key') ?></label>
                        <input name="razorpay_secret_key" id="razorpay_secret_key" type="text" class="form-control" value="<?php echo get_option('razorpay_secret_key')?>">
                    </div>
                    <?php
                }
                ?>
        </form>
    </div>
</div>
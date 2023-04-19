<?php
overall_header(__("Payment Method"));
?>
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php _e("Payment Method") ?></h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><?php _e("Payment Method") ?></li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xl-8 col-lg-8 content-right-offset">
            <h3><?php _e("Payment Method") ?></h3>
            <form id="subscribeForm" method="POST" novalidate="novalidate">
                <div class="payment margin-top-30">
                    <!--WALLET PAYMENT-->
                    <?php
                    $i = 0;
                    if($user_balance >= $amount && $payment_type != "deposit"){ ?>
                        <div class="payment-tab payment-tab-active">
                            <div class="payment-tab-trigger">
                                <input name="payment_method_id" class="payment_method_id" id="<?php _esc("wallet");?>"
                                       type="radio" value="wallet" data-name="<?php _esc("wallet");?>">
                                <label for="wallet"><?php _e("Wallet");?></label>
                                <div class="payment-logo"><?php _esc(price_format($user_balance),true) ?></div>
                            </div>
                            <div class="payment-tab-content">
                                <p><?php _e("You will be charge from your wallet amount.") ?></p>

                            </div>
                        </div>

                    <?php $i++; } ?>
                    <!--WALLET PAYMENT-->
                    <?php
                    foreach($payment_types as $payment){
                    ?>

                    <div class="payment-tab <?php if($i == 0){ echo 'payment-tab-active';} ?>">
                        <div class="payment-tab-trigger">
                            <input name="payment_method_id" class="payment_method_id" id="<?php _esc($payment['folder']);?>"
                                   type="radio" value="<?php _esc($payment['id']);?>" data-name="<?php _esc($payment['folder']);?>">
                            <label for="<?php _esc($payment['folder']);?>"><?php _esc($payment['title']);?></label>
                            <img class="payment-logo <?php _esc($payment['folder']);?>"
                                 src="<?php _esc($payment['image']);?>" alt="<?php _esc($payment['title']);?>">
                        </div>
                        <div class="payment-tab-content">
                            <p><?php _e("You will be redirected to the payment page for complete payment.") ?></p>

                            <?php if($payment['folder'] == "wire_transfer"){ ?>
                                <div class="quickad-template">
                                    <table class="default-table table-alt-row PaymentMethod-infoTable">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <h4 class="PaymentMethod-heading">
                                                    <strong><?php _e("Bank Account details") ?></strong></h4>
                                                <span class="PaymentMethod-info"><?php _esc($bank_info) ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 class="PaymentMethod-heading"><strong><?php _e("Reference") ?></strong></h4>
                                                <span class="PaymentMethod-info">
                                                            <?php _e("Order") ?> : <?php _esc($order_title) ?><br>
                                                            <?php _e("Username") ?>: <?php _esc($username) ?><br>
                                                            <em><small><?php _e("Include a note with Reference so that we know which account to credit.") ?></small></em>
                                                        </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 class="PaymentMethod-heading"><strong><?php _e("Amount to send") ?></strong>
                                                </h4>
                                                <span class="PaymentMethod-info"><?php _esc(price_format($amount),true) ?> </span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </div>
                            <?php } ?>

                            <?php if($payment['folder'] == "2checkout"){ ?>
                                <!-- CREDIT CARD FORM STARTS HERE -->
                                <div class="row payment-form-row">
                                    <div class="col-12">
                                        <div class="card-label form-group">
                                            <input type="text" class="form-control" name="checkoutCardNumber"
                                                   placeholder="<?php _e("CARD NUMBER") ?>" autocomplete="cc-number" autofocus/>
                                        </div>
                                    </div>
                                    <div class="col-7">
                                        <div class="card-label form-group">
                                            <input type="tel" class="form-control" name="checkoutCardExpiry"
                                                   placeholder="MM / YYYY" autocomplete="cc-exp" aria-required="true"
                                                   aria-invalid="false">
                                        </div>
                                    </div>
                                    <div class="col-5 pull-right">
                                        <div class="card-label form-group">
                                            <input type="tel" class="form-control" name="checkoutCardCVC"
                                                   placeholder="CVV" autocomplete="cc-csc"/>
                                        </div>
                                    </div>
                                    <div class="col-7">
                                        <div class="card-label form-group">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="checkoutCardFirstName"
                                                    placeholder="<?php _e("First Name") ?>"

                                            />
                                        </div>
                                    </div>
                                    <div class="col-5 pull-right">
                                        <div class="card-label form-group">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="checkoutCardLastName"
                                                    placeholder="<?php _e("Last Name") ?>"

                                            />
                                        </div>
                                    </div>
                                    <div class="col-7">
                                        <div class="card-label form-group">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="checkoutBillingAddress"
                                                    placeholder="<?php _e("Address") ?>"

                                            />
                                        </div>
                                    </div>
                                    <div class="col-5 pull-right">
                                        <div class="card-label form-group">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="checkoutBillingCity"
                                                    placeholder="<?php _e("City") ?>"

                                            />
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="card-label form-group">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="checkoutBillingState"
                                                    placeholder="<?php _e("State") ?>"

                                            />
                                        </div>
                                    </div>
                                    <div class="col-4 pull-right">
                                        <div class="card-label form-group">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="checkoutBillingZipcode"
                                                    placeholder="<?php _e("Zip code") ?>"

                                            />
                                        </div>
                                    </div>
                                    <div class="col-4 pull-right">
                                        <div class="card-label form-group">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="checkoutBillingCountry"
                                                    placeholder="<?php _e("Country") ?>"

                                            />
                                        </div>
                                    </div>

                                    <div id="checkoutPaymentErrors" class="text-danger" style="display:none;">
                                        <div class="col-12">
                                            <p class="payment-errors"></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- CREDIT CARD FORM ENDS HERE -->
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                    $i++;
                    }
                    ?>
                </div>
                <input type="hidden" name="token" value="<?php _esc($token) ?>"/>
                <input type="hidden" name="upgrade" value="<?php _esc($upgrade) ?>"/>
                <button type="submit" name="Submit" class="button big ripple-effect margin-top-40 margin-bottom-65 subscribeNow" id="subscribeNow"><?php _e("Confirm and Pay") ?></button>
            </form>
        </div>
        <div class="col-xl-4 col-lg-4 margin-top-0 margin-bottom-60">
            <div class="boxed-widget summary margin-top-0">
                <div class="boxed-widget-headline">
                    <h3><?php _e("Package Summary") ?></h3>
                </div>
                <div class="boxed-widget-inner">
                    <ul>
                        <li><?php _e("Title") ?> <span><?php _esc($order_title) ?></span></li>
                        <li><?php _e("Order") ?> <span><?php _esc($order_desc) ?></span></li>
                        <li class="total-costs"><?php _e("Total Cost") ?> <span><?php _esc($config['currency_sign']) ?><?php _esc($amount) ?> <?php _esc($config['currency_code']) ?></span></li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

    <script type="text/javascript" src="<?php _esc(TEMPLATE_URL);?>/js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?php _esc(TEMPLATE_URL);?>/js/jquery.payment.min.js"></script>

    <!-- payment js -->
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://www.2checkout.com/checkout/api/2co.min.js"></script>
    <script src="https://js.stripe.com/v2/"></script>

    <script>
        var packagePrice = 1;
        var LANG_CONFIRM_PAY = "<?php _e("Confirm and Pay") ?>";
        var LANG_PROCCESSING = "<?php _e("Processing") ?>";
        var LANG_VALIDATING = "<?php _e("Validating") ?>";
        var LANG_TRY_AGAIN = "<?php _e("Error: Please try again.") ?>";
        var LANG_INV_EXP_DATE = "<?php _e("Invalid expiration date.") ?>";
        var LANG_INV_CVV = "<?php _e("Invalid CVV.") ?>";
        var LANG_FIELD_REQ = "<?php _e("This field is required.") ?>";
        var LANG_CODE = "<?php _esc($config['lang_code']) ?>";

        $(document).ready(function () {
            /* Show price & Payment Methods */
            var paymentMethod = $('input[name="payment_method_id"]:checked').data("name");

            /* Select a Payment Method */
            $('.payment_method_id').on('change', function () {
                paymentMethod = $(this).data('name');
                var $payment_tab_content = $(this).closest('.payment-tab').find('.payment-tab-content');
                $payment_tab_content.find('[name="payment_mode"]').first().prop('checked',true);
            });

            $('.payment_method_id').first().prop('checked',true).trigger('change');

            /* Fancy restrictive input formatting via jQuery.payment library */
            $('input[name=checkoutCardNumber]').payment('formatCardNumber');
            $('input[name=checkoutCardCVC]').payment('formatCardCVC');
            $('input[name=checkoutCardExpiry]').payment('formatCardExpiry');

            $('input[name=stripeCardNumber]').payment('formatCardNumber');
            $('input[name=stripeCardCVC]').payment('formatCardCVC');
            $('input[name=stripeCardExpiry]').payment('formatCardExpiry');

            /* Pull in the public encryption key for our environment (2Checkout) */
            TCO.loadPubKey('<?php _esc($sandbox_mode_2checkout) ?>');

            /* Form Default Submission */
            $('#subscribeNow').on('click', function (e) {
                e.preventDefault();

                paymentMethod = $('input[name="payment_method_id"]:checked').data("name");
                var $form = $('#subscribeForm');

                if (packagePrice <= 0) {
                    $form.submit();
                }

                switch (paymentMethod) {
                    case 'wallet':
                    case 'wire_transfer':
                    case 'paypal':
                    case 'stripe':
                    case 'ccavenue':
                    case 'paytm':
                    case 'payumoney':
                    case 'mollie':
                    case 'iyzico':
                    case 'hyperpay':
                    case 'paytabs':
                    case 'midtrans':
                    case 'telr':
                    case 'razorpay':
                    case 'flutterwave':
                    case 'yoomoney':
                    case 'trial':
                        $form.submit();
                        break;
                    case 'paystack':
                        payWithPaystack();
                        break;
                    case '2checkout':
                        if (ccFormValidationForCheckout()) {
                            payWithCheckout();
                        }
                        break;
                }

                return false;
            });

            function payWithPaystack() {
                var amount = '<?php _esc($price) ?>';
                amount = 100 * amount;
                var $form = $('#subscribeForm');
                $form.find('#subscribeNow').html(LANG_PROCCESSING + ' <i class="fa fa-spinner fa-pulse"></i>');

                var handler = PaystackPop.setup({
                            key: '<?php _esc($paystack_public_key) ?>',
                            email: '<?php _esc($email) ?>',
                            amount: amount,
                            currency: '<?php _esc($config['currency_code']) ?>',
                            metadata: {
                                custom_fields: [
                                    {
                                        display_name: "Blank",
                                        product_id: "Blank",
                                        value: "Blank"
                                    }
                                ]
                            }
                            ,
                            callback: function (response) {
                                var paystackReference = response.reference;
                                /* Insert the token into the form so it gets submitted to the server */
                                $form.append($('<input type="hidden" name="paystackReference" />').val(paystackReference));
                                $form.submit();
                            }
                            ,
                            onClose: function () {
                                $form.find('#subscribeNow').html(LANG_CONFIRM_PAY);
                            }
                        }
                    )
                ;
                handler.openIframe();
            }

            function ccFormValidationForCheckout() {
                var $form = $('#subscribeForm');

                /* Form validation */
                /*jQuery.validator.addMethod('checkoutCardExpiry', function(value, element) {
                 *//* Regular expression to match Credit Card expiration date *//*
             var reg = new RegExp('^(0[1-9]|1[0-2])\\s?\/\\s?([0-9]|[0-9])$');
             return this.optional(element) || reg.test(value);
             }, "Invalid expiration date");*/

                jQuery.validator.addMethod(
                    "checkoutCardExpiry",
                    function (value, element, params) {
                        var minMonth = new Date().getMonth() + 1;
                        var minYear = new Date().getFullYear();

                        var checkoutCardExpiry = $('input[name=checkoutCardExpiry]').val().split('/');
                        var $month = (0 in checkoutCardExpiry) ? checkoutCardExpiry[0].replace(/\s/g, '') : '';
                        var $year = (1 in checkoutCardExpiry) ? checkoutCardExpiry[1].replace(/\s/g, '') : '';

                        var month = parseInt($month, 10);
                        var year = parseInt($year, 10);

                        return ((year > minYear) || ((year === minYear) && (month >= minMonth)));
                    }
                    ,
                    LANG_INV_EXP_DATE);

                jQuery.validator.addMethod('checkoutCardCVC', function (value, element) {
                    /* Regular expression matching a 3 or 4 digit CVC (or CVV) of a Credit Card */
                    var reg = new RegExp('^[0-9]{3,4}$');
                    return this.optional(element) || reg.test(value);
                }, LANG_INV_CVV);

                var validator = $form.validate({
                    lang: LANG_CODE,
                    rules: {
                        checkoutCardNumber: {
                            required: true
                        },
                        checkoutCardExpiry: {
                            required: true,
                            checkoutCardExpiry: true
                        },
                        checkoutCardCVC: {
                            required: true,
                            checkoutCardCVC: true
                        },
                        checkoutCardHolderFirstName: {
                            required: true
                        },
                        checkoutCardHolderLastName: {
                            required: true
                        },
                        checkoutBillingAddress: {
                            required: true
                        },
                        checkoutBillingCity: {
                            required: true
                        },
                        checkoutBillingState: {
                            required: true
                        },
                        checkoutBillingZipcode: {
                            required: true
                        },
                        checkoutBillingCountry: {
                            required: true
                        }
                    },
                    highlight: function (element) {
                        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                    },
                    unhighlight: function (element) {
                        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                    },
                    errorPlacement: function (error, element) {
                        $(element).closest('.form-group').append(error);
                    }
                });

                /* Abort if invalid form data */
                return validator.form();
            }

            function payWithCheckout() {
                var $form = $('#subscribeForm');

                /* Visual feedback */
                $form.find('#subscribeNow').html(LANG_VALIDATING + ' <i class="fa fa-spinner fa-pulse"></i>').prop('disabled', true);

                /* Setup token request arguments */
                var checkoutCardExpiry = $('input[name=checkoutCardExpiry]').val().split('/');

                var args = {
                    sellerId: "<?php _esc($checkout_account_number) ?>",
                    publishableKey: "<?php _esc($checkout_public_key) ?>",
                    ccNo: $('input[name=checkoutCardNumber]').val().replace(/\s/g, ''),
                    cvv: $('input[name=checkoutCardCVC]').val(),
                    expMonth: (0 in checkoutCardExpiry) ? checkoutCardExpiry[0].replace(/\s/g, '') : '',
                    expYear: (1 in checkoutCardExpiry) ? checkoutCardExpiry[1].replace(/\s/g, '') : ''
                };

                /* Make the token request */
                TCO.requestToken(function (data) {
                    /* Visual feedback */
                    $form.find('#subscribeNow').html(LANG_PROCCESSING + ' <i class="fa fa-spinner fa-pulse"></i>');

                    /* Hide Stripe errors on the form */
                    $form.find('#checkoutPaymentErrors').hide();
                    $form.find('#checkoutPaymentErrors').find('.payment-errors').text('');

                    /* Set the token as the value for the token input */
                    var checkoutToken = data.response.token.token;
                    $form.append($('<input type="hidden" name="2checkoutToken" />').val(checkoutToken));

                    /* IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop. */
                    $form.submit();

                }, function (data) {
                    if (data.errorCode === 200) {
                        tokenRequest();
                    } else {
                        /* Visual feedback */
                        $form.find('#subscribeNow').html(LANG_TRY_AGAIN).prop('disabled', false);

                        /* Show errors on the form */
                        $form.find('#checkoutPaymentErrors').find('.payment-errors').text(data.errorMsg);
                        $form.find('#checkoutPaymentErrors').show();
                    }
                }, args);
            }

            function payWithStripe() {
                var $form = $('#subscribeForm');

                /* Visual feedback */
                $form.find('#subscribeNow').html(LANG_VALIDATING + ' <i class="fa fa-spinner fa-pulse"></i>').prop('disabled', true);

                var PublishableKey = '<?php _esc($stripe_publishable_key) ?>';
                Stripe.setPublishableKey(PublishableKey);

                /* Create token */
                var expiry = $form.find('[name=stripeCardExpiry]').payment('cardExpiryVal');
                var ccData = {
                    number: $form.find('[name=stripeCardNumber]').val().replace(/\s/g, ''),
                    cvc: $form.find('[name=stripeCardCVC]').val(),
                    exp_month: expiry.month,
                    exp_year: expiry.year
                };

                Stripe.card.createToken(ccData, function stripeResponseHandler(status, response) {
                    if (response.error) {
                        /* Visual feedback */
                        $form.find('#subscribeNow').html(LANG_TRY_AGAIN).prop('disabled', false);

                        /* Show errors on the form */
                        $form.find('#stripePaymentErrors').find('.payment-errors').text(response.error.message);
                        $form.find('#stripePaymentErrors').show();
                    } else {
                        /* Visual feedback */
                        $form.find('#subscribeNow').html(LANG_PROCCESSING + ' <i class="fa fa-spinner fa-pulse"></i>');

                        /* Hide Stripe errors on the form */
                        $form.find('#stripePaymentErrors').hide();
                        $form.find('#stripePaymentErrors').find('.payment-errors').text('');

                        /* Response contains id and card, which contains additional card details */
                        var stripeToken = response.id;
                        /* Insert the token into the form so it gets submitted to the server */
                        $form.append($('<input type="hidden" name="stripeToken" />').val(stripeToken));
                        $form.append($('<input type="hidden" name="exp_month" />').val(response.card.exp_month));
                        $form.append($('<input type="hidden" name="exp_year" />').val(response.card.exp_year));

                        /* and submit */
                        $form.submit();
                    }
                });
            }
        });

    </script>

<?php
overall_footer();
?>
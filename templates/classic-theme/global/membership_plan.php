<?php
overall_header(__("Membership Plan"));
?>
<?php print_adsense_code('header_bottom'); ?>
    <!-- Titlebar
    ================================================== -->
    <div id="titlebar" class="gradient">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2><?php _e("Membership Plan") ?></h2>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><?php _e("Membership Plan") ?></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content
    ================================================== -->
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <form name="form1" method="post">
                    <div class="billing-cycle-radios margin-bottom-70">
                        <?php
                        if ($total_monthly) {
                            ?>
                            <div class="radio billed-monthly-radio">
                                <input id="radio-monthly" name="billed-type" type="radio" value="monthly" checked="">
                                <label for="radio-monthly"><span class="radio-label"></span> <?php _e("Monthly") ?>
                                </label>
                            </div>
                            <?php
                        }
                        if ($total_annual) {
                            ?>
                            <div class="radio billed-yearly-radio">
                                <input id="radio-yearly" name="billed-type" type="radio" value="yearly">
                                <label for="radio-yearly"><span class="radio-label"></span> <?php _e("Yearly") ?>
                                </label>
                            </div>
                            <?php
                        }
                        if ($total_lifetime) {
                            ?>
                            <div class="radio billed-lifetime-radio">
                                <input id="radio-lifetime" name="billed-type" type="radio" value="lifetime">
                                <label for="radio-lifetime"><span class="radio-label"></span> <?php _e("Lifetime") ?>
                                </label>
                            </div>
                        <?php } ?>
                    </div>
                    <!-- Pricing Plans Container -->
                    <div class="pricing-plans-container">
                        <?php
                        foreach ($sub_types as $plan) {
                            ?>
                            <!-- Plan -->
                            <div class='pricing-plan <?php if (isset($plan['recommended']) && $plan['recommended'] == "yes") {
                                echo 'recommended';
                            } ?>'>

                                <?php
                                if (isset($plan['recommended']) && $plan['recommended'] == "yes") {
                                    echo '<div class="recommended-badge">' . __("Recommended") . '</div> ';
                                }
                                ?>
                                <h3><?php _esc($plan['title']) ?></h3>
                                <?php
                                if ($plan['id'] == "free" || $plan['id'] == "trial") {
                                    ?>
                                    <div class="pricing-plan-label"><strong>
                                            <?php
                                            if ($plan['id'] == "free")
                                                _e("Free");
                                            else
                                                _e("Trial");
                                            ?>
                                        </strong></div>

                                    <?php
                                } else {
                                    if ($total_monthly != 0)
                                        echo '<div class="pricing-plan-label billed-monthly-label"><strong>' . _esc($plan['monthly_price'], false) . '</strong>/ ' . __("Monthly") . '</div>';
                                    if ($total_annual != 0)
                                        echo '<div class="pricing-plan-label billed-yearly-label"><strong>' . _esc($plan['annual_price'], false) . '</strong>/ ' . __("Yearly") . '</div>';
                                    if ($total_lifetime != 0)
                                        echo '<div class="pricing-plan-label billed-lifetime-label"><strong>' . _esc($plan['lifetime_price'], false) . '</strong>/ ' . __("Lifetime") . '</div>';
                                }
                                ?>

                                <div class="pricing-plan-features">
                                    <strong><?php _e("Features of") ?>&nbsp;<?php _esc($plan['title']) ?></strong>
                                    <ul>
                                        <?php if (!get_option('single_model_for_plans')) { ?>
                                            <li>
                                                <strong>
                                                    <?php
                                                    $models = get_opeai_models();
                                                    _esc($models[$plan['ai_model']])
                                                    ?>
                                                </strong>&nbsp;<br><em><small><?php _e("Open AI Model") ?></small></em>
                                            </li>
                                        <?php } ?>
                                        <li>
                                            <strong><?php _esc(count($plan['ai_templates'])) ?></strong> <?php _e("AI Document Templates") ?>
                                        </li>
                                        <li>
                                            <strong><?php _esc(is_string($plan['ai_words_limit']) ? $plan['ai_words_limit'] : number_format($plan['ai_words_limit'])) ?></strong> <?php _e("Words per month") ?>
                                        </li>
                                        <?php if ($config['enable_ai_images']) { ?>
                                            <li>
                                                <strong><?php _esc(is_string($plan['ai_images_limit']) ? $plan['ai_images_limit'] : number_format($plan['ai_images_limit'])) ?></strong> <?php _e("Images per month") ?>
                                            </li>
                                        <?php }
                                        if ($config['enable_speech_to_text']) { ?>
                                            <li>
                                                <strong><?php _esc(is_string($plan['ai_speech_to_text_limit']) ? $plan['ai_speech_to_text_limit'] : number_format($plan['ai_speech_to_text_limit'])) ?></strong> <?php _e("Speech to Text per month") ?>
                                                <i class="icon-feather-help-circle margin-left-2"
                                                   data-tippy-placement="top"
                                                   title="<?php _e("Create audio transcription") ?>"></i></li>
                                            <li>
                                                <strong><?php _esc(is_string($plan['ai_speech_to_text_file_limit']) ? $plan['ai_speech_to_text_file_limit'] : number_format($plan['ai_speech_to_text_file_limit']) . ' MB') ?></strong> <?php _e("Audio file size limit") ?>
                                            </li>
                                        <?php } ?>
                                        <?php if ($config['enable_ai_chat']) { ?>
                                            <li>
                                                <?php if ($plan['ai_chat']) { ?>
                                                    <span class="icon-text yes"><i
                                                                class="icon-feather-check-circle margin-right-2"></i></span>
                                                <?php } else { ?>
                                                    <span class="icon-text no"><i
                                                                class="icon-feather-x-circle margin-right-2"></i></span>
                                                <?php } ?>
                                                <?php _e("AI Chat") ?>
                                                <i class="icon-feather-help-circle margin-left-2"
                                                   data-tippy-placement="top"
                                                   title="<?php _e("Chat with the AI bot") ?>"></i>
                                            </li>
                                        <?php } ?>
                                        <?php if ($config['enable_ai_code']) { ?>
                                            <li>
                                                <?php if ($plan['ai_code']) { ?>
                                                    <span class="icon-text yes"><i
                                                                class="icon-feather-check-circle margin-right-2"></i></span>
                                                <?php } else { ?>
                                                    <span class="icon-text no"><i
                                                                class="icon-feather-x-circle margin-right-2"></i></span>
                                                <?php } ?>
                                                <?php _e("AI Code") ?>
                                                <i class="icon-feather-help-circle margin-left-2"
                                                   data-tippy-placement="top"
                                                   title="<?php _e("Generate code of any programming language with the AI") ?>"></i>
                                            </li>
                                        <?php } ?>
                                        <li>
                                            <?php if (!$plan['show_ads']) { ?>
                                                <span class="icon-text yes"><i
                                                            class="icon-feather-check-circle margin-right-2"></i></span>
                                            <?php } else { ?>
                                                <span class="icon-text no"><i
                                                            class="icon-feather-x-circle margin-right-2"></i></span>
                                            <?php } ?>
                                            <?php _e("Hide Ads") ?>
                                        </li>
                                        <?php if ($config['enable_live_chat'] && $config['tawkto_membership']) { ?>
                                            <li>
                                                <?php if ($plan['live_chat']) { ?>
                                                    <span class="icon-text yes"><i
                                                                class="icon-feather-check-circle margin-right-2"></i></span>
                                                <?php } else { ?>
                                                    <span class="icon-text no"><i
                                                                class="icon-feather-x-circle margin-right-2"></i></span>
                                                <?php } ?>
                                                <?php _e("Live Chat Support") ?>
                                            </li>
                                        <?php } ?>
                                        <?php _esc($plan['custom_settings']) ?>
                                    </ul>
                                </div>

                                <?php
                                if ($plan['Selected'] == 0) {
                                    echo '<button type="submit" class="button full-width margin-top-20 ripple-effect" name="upgrade" value="' . _esc($plan['id'], false) . '">' . __("Upgrade") . '</button>';
                                }
                                if ($plan['Selected'] == 1) {
                                    echo '<a href="javascript:void(0);" class="button full-width margin-top-20 ripple-effect">' . __("Current Plan") . '</a>';
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="margin-top-80"></div>
<?php
overall_footer();
?>
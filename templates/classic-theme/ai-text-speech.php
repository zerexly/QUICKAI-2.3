<?php

overall_header(__("Text to Speech"));


?>
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <?php
        include_once TEMPLATE_PATH . '/dashboard_sidebar.php';
        ?>
        <!-- Dashboard Content
        ================================================== -->
        <div class="dashboard-content-container" data-simplebar>
            <div class="dashboard-content-inner">
                <?php print_adsense_code('header_bottom'); ?>
                <!-- Dashboard Headline -->
                <div class="dashboard-headline">
                    <h3 class="d-flex align-items-center">
                        <?php _e("Text to Speech") ?>
                        <div class="word-used-wrapper margin-left-10">
                            <i class="icon-feather-bar-chart-2"></i>
                            <?php echo '<i id="quick-images-left">' .
                                _esc(number_format((float)$total_character_used), 0) . '</i> / ' .
                                ($characters_limit == -1
                                    ? __('Unlimited')
                                    : _esc(number_format($characters_limit + get_user_option($_SESSION['user']['id'], 'total_text_to_speech_available', 0)), 0)); ?>
                            <strong><?php _e('Characters Used'); ?></strong>
                        </div>
                    </h3>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><?php _e("Text to Speech") ?></li>
                        </ul>
                    </nav>
                </div>
                <form id="ai_text_speech" name="ai_text_speech" method="post" action="#">
                    <div class="submit-field margin-bottom-10">
                        <h6><?php _e("Text") ?></h6>
                        <textarea name="description"
                                  class="with-border quick-text-counter" maxlength="3000" data-maxlength="3000"
                                  required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="submit-field margin-bottom-20">
                                <h6><?php _e("Title") ?></h6>
                                <input name="title" class="with-border small-input" type="text"
                                       value="<?php _e("New Audio") ?>">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="submit-field margin-bottom-20">
                                <h6><?php _e("Language") ?></h6>
                                <select name="language" id="language"
                                        class="with-border small-input" required>
                                    <?php foreach (get_ai_voices() as $language) {
                                        $v = [];
                                        foreach ($language['voices'] as $voice) {
                                            if ($voice['vendor'] == 'gcp' && get_option('enable_google_tts'))
                                                $v[] = $voice;
                                            elseif ($voice['vendor'] == 'aws' && get_option('enable_aws_tts', get_option('enable_text_to_speech')))
                                                $v[] = $voice;
                                        }
                                        if (!empty($v)) {
                                            ?>
                                            <option value="<?php _esc($language['language_code']) ?>" <?php echo $language['language_code'] == get_option('ai_tts_language') ? 'selected' : '' ?>><?php _esc($language['language']) ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="submit-field margin-bottom-20">
                                <h6><?php _e("Voice") ?></h6>
                                <select id="voice_id" name="voice_id" class="with-border small-input" required>
                                    <?php foreach (get_ai_voices() as $language){
                                        foreach ($language['voices'] as $voice) {
                                            if ($voice['vendor'] == 'gcp' && !get_option('enable_google_tts'))
                                                continue;
                                            elseif ($voice['vendor'] == 'aws' && !get_option('enable_aws_tts', get_option('enable_text_to_speech')))
                                                continue;
                                            ?>
                                        <option value="<?php _esc($voice['voice_id']) ?>" class="lang-<?php _esc($language['language_code']) ?>" <?php echo $voice['voice_id'] == get_option('ai_tts_voice') ? 'selected' : '' ?>>
                                        <?php _esc($voice['voice'] . ' ('. $voice['gender'] . ')');
                                        if($voice['voice_type'] == 'neural')
                                            _esc(' Neural');
                                        ?>
                                        </option>
                                    <?php }
                                    }?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php if (get_option('enable_tts_translation')) { ?>
                        <div class="submit-field margin-bottom-10">
                            <div class="checkbox">
                                <input type="checkbox" id="translate_tts_text" name="translate_tts_text">
                                <label for="translate_tts_text"><span
                                            class="checkbox-icon"></span> <?php _e("Translate the text into the selected language before converting it to speech.") ?>
                                    <i class="fa fa-question-circle" data-tippy-placement="top"
                                       title="<?php _e("This feature will use your AI words.") ?>"></i></label>
                            </div>
                        </div>
                    <?php } ?>
                    <button type="submit" name="submit"
                            class="button ripple-effect"><?php _e("Generate") ?>
                        <i class="icon-feather-arrow-right"></i></button>
                    <div>
                        <small class="form-error"></small>
                    </div>
                </form>
                <div class="dashboard-box margin-top-30 margin-bottom-30">
                    <!-- Headline -->
                    <div class="headline">
                        <h3>
                            <i class="icon-feather-volume-2"></i><?php _e('Speeches') ?>
                        </h3>
                    </div>
                    <div class="content with-padding">
                        <table class="basic-table">
                            <thead>
                            <tr>
                                <th><?php _e("Text") ?></th>
                                <th><?php _e("Audio") ?></th>
                                <th><?php _e("Voice") ?></th>
                                <th class="small-width"><?php _e("Date") ?></th>
                                <th data-priority="2" class="small-width"><?php _e("Action") ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(empty($speeches)){ ?>
                                <tr class="no-order-found">
                                    <td colspan="5" class="text-center"><?php _e("No audios found.") ?></td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($speeches as $speech) { ?>
                                <tr>
                                    <td data-label="<?php _e("Text") ?>">
                                        <div><strong><?php _esc($speech['title']) ?></strong></div>
                                        <small data-tippy-placement="top" title="<?php _esc(escape($speech['text'])) ?>"><?php _esc($speech['text_short']) ?></small>
                                    </td>
                                    <td data-label="<?php _e("Audio") ?>">
                                        <audio controls="" preload="none"><source src="<?php _esc($speech['file_url']) ?>" type="audio/mpeg"></audio>
                                    </td>
                                    <td data-label="<?php _e("Voice") ?>">
                                        <span><?php _esc($speech['voice']['voice']) ?></span>, <small><?php _esc($speech['voice']['gender']) ?><?php if($speech['voice']['voice_type'] == 'neural') _esc(', Neural'); ?></small>
                                        <br>
                                        <small><strong><?php _esc($speech['language']['language']) ?></strong></small>
                                    </td>
                                    <td data-label="<?php _e("Date") ?>">
                                        <small><?php echo _esc($speech['date'], 0) . ' <br><strong>' . _esc($speech['time'], 0) . '</strong>' ?></small>
                                    </td>
                                    <td data-label="<?php _e("Action") ?>">
                                        <a href="<?php _esc($speech['file_url']); ?>" class="button ripple-effect btn-sm"
                                           data-tippy-placement="top"
                                           title="<?php _e("Download") ?>" download><i class="fa fa-download"></i>
                                        </a>
                                        <a href="#" class="button red ripple-effect btn-sm quick-delete"
                                           data-id="<?php _esc($speech['id']) ?>"
                                           data-action="delete_speech"
                                           data-tippy-placement="top"
                                           title="<?php _e("Delete") ?>"><i class="fa fa-trash-o"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php print_adsense_code('footer_top'); ?>
                <!-- Footer -->
                <div class="dashboard-footer-spacer"></div>
                <div class="small-footer margin-top-15">
                    <div class="footer-copyright">
                        <?php _esc($config['copyright_text']); ?>
                    </div>
                    <ul class="footer-social-links">
                        <?php
                        if ($config['facebook_link'] != "")
                            echo '<li><a href="' . _esc($config['facebook_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-facebook"></i></a></li>';
                        if ($config['twitter_link'] != "")
                            echo '<li><a href="' . _esc($config['twitter_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-twitter"></i></a></li>';
                        if ($config['instagram_link'] != "")
                            echo '<li><a href="' . _esc($config['instagram_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-instagram"></i></a></li>';
                        if ($config['linkedin_link'] != "")
                            echo '<li><a href="' . _esc($config['linkedin_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-linkedin"></i></a></li>';
                        if ($config['pinterest_link'] != "")
                            echo '<li><a href="' . _esc($config['pinterest_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-pinterest"></i></a></li>';
                        if ($config['youtube_link'] != "")
                            echo '<li><a href="' . _esc($config['youtube_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-youtube"></i></a></li>';
                        ?>
                    </ul>
                    <div class="clearfix"></div>
                </div>

            </div>
        </div>
    </div>
<?php ob_start() ?>
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/jquery-simple-txt-counter.min.js"></script>
<script>
    // text counter
    $('.quick-text-counter').each(function () {
        var $this = $(this);

        $this.simpleTxtCounter({
            maxLength: $this.data('maxlength'),
            countElem: '<div class="form-text"></div>',
            lineBreak: false,
        });
    });

    $('#language').on('change', function (e){
        $('#voice_id option').hide().addClass('hidden').removeClass('visible');
        $('#voice_id option.lang-'+ $(this).val()).show().removeClass('hidden').addClass('visible');

        $("#voice_id option").attr('selected', false);
        $("#voice_id option.visible:first").attr("selected", "selected");
    });
    $('#voice_id option').hide().addClass('hidden').removeClass('visible');
    $('#voice_id option.lang-'+ $('#language').val()).show().removeClass('hidden').addClass('visible');
</script>
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';

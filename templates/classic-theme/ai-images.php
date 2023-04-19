<?php

overall_header(__("AI Images"));


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
                        <?php _e("AI Images") ?>
                        <div class="word-used-wrapper margin-left-10">
                            <i class="icon-feather-bar-chart-2"></i>
                            <?php echo '<i id="quick-images-left">' .
                                _esc(number_format((float)$total_images_used), 0) . '</i> / ' .
                                ($images_limit == -1
                                    ? __('Unlimited')
                                    : _esc(number_format($images_limit), 0)); ?>
                            <strong><?php _e('Images Used'); ?></strong>
                        </div>
                    </h3>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><?php _e("AI Images") ?></li>
                        </ul>
                    </nav>
                </div>
                <?php
                $placeholders = [
                    escape(__('A boombox reflecting the surroundings in a cave, Painting by H.R. Giger, Closeup')),
                    escape(__('SpongeBob SquarePants talking to a mouse in an airport, 1960s Cartoon')),
                    escape(__('SpongeBob SquarePants dressed as a mailman drinking a cup of coffee in a mountainside scene, watercolors by 5 year old')),
                    escape(__('A cactus sitting next to onion rings in a farm, 1960s Cartoon')),
                    escape(__('Garfield driving a school bus in a rock concert, Painting by Leonardo Da Vinci')),
                    escape(__('A mouse riding on a horse in a mountainside scene, Painting by Rembrandt')),
                    escape(__('Super Mario dressed as a medieval knight riding a pterodactyl in the back of a bus, Baroque painting'))
                ];
                ?>
                <form id="ai_images" name="ai_images" method="post" action="#">
                    <h4 class="margin-bottom-10 padding-left-5"><?php _e("Start with a detailed description.") ?> <a href="#" class="try-example"><strong><?php _e("Try an example") ?></strong></a></h4>
                    <div class="message-reply ai_image_description margin-bottom-10">
                                <textarea name="description"
                                          class="with-border small-input image-description"
                                          placeholder="<?php _esc($placeholders[array_rand($placeholders)]) ?>"
                                          required></textarea>
                        <button type="submit" name="submit"
                                class="button ripple-effect border-pilled"><?php _e("Generate") ?>
                            <i class="icon-feather-arrow-right"></i></button>
                    </div>
                    <div class="row image-advance-settings" style="display: none">
                        <div class="col-sm-3">
                            <div class="submit-field margin-bottom-20">
                                <h6><?php _e("Image Title") ?></h6>
                                <input name="title" class="with-border small-input" type="text"
                                       value="<?php _e("New Image") ?>">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="submit-field margin-bottom-20">
                                <h6><?php _e("Art style") ?></h6>
                                <select name="style" id="style"
                                        class="with-border small-input selectpicker">
                                    <option value="" selected="selected"><?php _e('None') ?></option>
                                    <option value="3d_render"><?php _e('3D render') ?></option>
                                    <option value="pixel"><?php _e('Pixel') ?></option>
                                    <option value="sticker"><?php _e('Sticker') ?></option>
                                    <option value="realistic"><?php _e('Realistic') ?></option>
                                    <option value="isometric"><?php _e('Isometric') ?></option>
                                    <option value="cyberpunk"><?php _e('Cyberpunk') ?></option>
                                    <option value="line"><?php _e('Line art') ?></option>
                                    <option value="pencil"><?php _e('Pencil drawing') ?></option>
                                    <option value="ballpoint_pen"><?php _e('Ballpoint pen drawing') ?></option>
                                    <option value="watercolor"><?php _e('Watercolor') ?></option>
                                    <option value="origami"><?php _e('Origami') ?></option>
                                    <option value="cartoon"><?php _e('Cartoon') ?></option>
                                    <option value="retro"><?php _e('Retro') ?></option>
                                    <option value="anime"><?php _e('Anime') ?></option>
                                    <option value="renaissance"><?php _e('Renaissance') ?></option>
                                    <option value="clay"><?php _e('Clay') ?></option>
                                    <option value="vaporwave"><?php _e('Vaporwave') ?></option>
                                    <option value="steampunk"><?php _e('Steampunk') ?></option>
                                    <option value="glitchcore"><?php _e('Glitchcore') ?></option>
                                    <option value="bauhaus"><?php _e('Bauhaus') ?></option>
                                    <option value="vector"><?php _e('Vector') ?></option>
                                    <option value="low_poly"><?php _e('Low poly') ?></option>
                                    <option value="ukiyo_e"><?php _e('Ukiyo-e') ?></option>
                                    <option value="cubism"><?php _e('Cubism') ?></option>
                                    <option value="modern"><?php _e('Modern') ?></option>
                                    <option value="pop"><?php _e('Pop') ?></option>
                                    <option value="contemporary"><?php _e('Contemporary') ?></option>
                                    <option value="impressionism"><?php _e('Impressionism') ?></option>
                                    <option value="pointillism"><?php _e('Pointillism') ?></option>
                                    <option value="minimalism"><?php _e('Minimalism') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="submit-field margin-bottom-20">
                                <h6><?php _e("Lighting style") ?></h6>
                                <select id="lighting" name="lighting" class="with-border small-input selectpicker">
                                    <option value="" selected="selected"><?php _e('None') ?></option>
                                    <option value="warm"><?php _e('Warm') ?></option>
                                    <option value="cold"><?php _e('Cold') ?></option>
                                    <option value="golden_hour"><?php _e('Golden Hour') ?></option>
                                    <option value="blue_hour"><?php _e('Blue Hour') ?></option>
                                    <option value="ambient"><?php _e('Ambient') ?></option>
                                    <option value="studio"><?php _e('Studio') ?></option>
                                    <option value="neon"><?php _e('Neon') ?></option>
                                    <option value="dramatic"><?php _e('Dramatic') ?></option>
                                    <option value="cinematic"><?php _e('Cinematic') ?></option>
                                    <option value="natural"><?php _e('Natural') ?></option>
                                    <option value="foggy"><?php _e('Foggy') ?></option>
                                    <option value="backlight"><?php _e('Backlight') ?></option>
                                    <option value="hard"><?php _e('Hard') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="submit-field margin-bottom-20">
                                <h6><?php _e("Mood") ?></h6>
                                <select id="mood" name="mood" class="with-border small-input selectpicker">
                                    <option value="" selected="selected"><?php _e('None') ?></option>
                                    <option value="aggressive"><?php _e('Aggressive') ?></option>
                                    <option value="angry"><?php _e('Angry') ?></option>
                                    <option value="boring"><?php _e('Boring') ?></option>
                                    <option value="bright"><?php _e('Bright') ?></option>
                                    <option value="calm"><?php _e('Calm') ?></option>
                                    <option value="cheerful"><?php _e('Cheerful') ?></option>
                                    <option value="chilling"><?php _e('Chilling') ?></option>
                                    <option value="colorful"><?php _e('Colorful') ?></option>
                                    <option value="dark"><?php _e('Dark') ?></option>
                                    <option value="neutral"><?php _e('Neutral') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="submit-field margin-bottom-20">
                                <h6><?php _e("Resolution") ?></h6>
                                <select name="resolution" id="resolution"
                                        class="with-border small-input selectpicker" required>
                                    <?php if (get_option('ai_image_api') != 'stable-diffusion') { ?>
                                        <option value="256x256"><?php _e('Small Image (256x256)') ?></option>
                                    <?php } ?>
                                    <option value="512x512"><?php _e('Medium Image (512x512)') ?></option>
                                    <option value="1024x1024"><?php _e('Large Image (1024x1024)') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="submit-field margin-bottom-20">
                                <h6><?php _e("Number of Images") ?></h6>
                                <select name="no_of_images" id="results"
                                        class="with-border small-input selectpicker"
                                        required>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <small><a href="#" class="image-advance-settings-trigger"><?php _e('Advanced Settings'); ?> <strong>+</strong></a></small>
                    <div>
                        <small class="form-error"></small>
                    </div>
                </form>
                <hr>
                <div class="row margin-top-25" id="generated_images_wrapper">
                    <?php foreach ($ai_images as $ai_image) { ?>
                        <div class="col-sm-4 col-md-2 col-6 margin-bottom-30">
                            <a href="<?php echo _esc($config['site_url'], 0) . 'storage/ai_images/' . $ai_image['image']; ?>"
                               target="_blank">
                                <img class="lazy-load rounded"
                                     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                     data-original="<?php echo _esc($config['site_url'], 0) . 'storage/ai_images/small_' . $ai_image['image']; ?>"
                                     alt="<?php _esc($ai_image['description']) ?>" data-tippy-placement="top"
                                     title="<?php _esc($ai_image['description']) ?>">
                            </a>
                        </div>
                    <?php } ?>
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

        var examples = <?php echo json_encode($placeholders); ?>;
        $('.try-example').on('click', function (e){
            e.preventDefault();

            $('.image-description').val(examples[Math.floor(Math.random()*examples.length)]);
        })
    </script>
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';
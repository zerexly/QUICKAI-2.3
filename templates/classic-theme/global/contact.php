<?php
overall_header(__("Contact Us"));
?>
<?php print_adsense_code('header_bottom'); ?>
    <div id="titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2><?php _e("Contact Us") ?></h2>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><?php _e("Contact Us") ?></li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
    <div class="container margin-bottom-50">
        <?php if (!empty($latitude)) { ?>
            <div class="map margin-bottom-50" id="singleListingMap" data-latitude="<?php _esc($latitude) ?>"
                 data-longitude="<?php _esc($longitude) ?>" data-map-icon="fa fa-marker"></div>
        <?php } ?>
        <div class="business-info">
            <div class="row">
                <div class="col-sm-8">
                    <div class="contactUs">
                        <h2 class="margin-bottom-30"><?php _e("Contact Us") ?></h2>
                        <form id="contact-form" class="contact-form" name="contact-form" method="post" action="#">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" class="with-border" required="required"
                                               placeholder="<?php _e("Your Name") ?>" name="name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="email" class="with-border" required="required"
                                               placeholder="<?php _e("Your E-Mail") ?>" name="email">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input type="text" class="with-border" required="required"
                                               placeholder="<?php _e("Subject") ?>" name="subject">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <textarea name="message" id="message" required="required" class="with-border"
                                                  rows="7" placeholder="<?php _e("Message") ?>"></textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <?php
                                        if ($config['recaptcha_mode'] == '1') {
                                            echo '<div class="g-recaptcha" data-sitekey="' . _esc($config['recaptcha_public_key'], false) . '"></div>';
                                        }
                                        if ($recaptcha_error != '') {
                                            echo '<span class="status-not-available">' . $recaptcha_error . '</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="Submit" class="button"><?php _e("Send Message") ?></button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Enquiry Form-->
                <!-- contact-detail -->
                <div class="col-sm-4">
                    <div class="dashboard-box margin-top-0">
                        <div class="headline">
                            <h3><?php _e("Get In Touch") ?></h3>
                        </div>
                        <div class="content with-padding">
                            <?php _e("Please get in touch and our expert support team will answer all your questions.") ?>
                        </div>
                    </div>
                    <div class="dashboard-box">
                        <div class="headline">
                            <h3><?php _e("Contact Information") ?></h3>
                        </div>
                        <div class="content with-padding">
                            <ul>
                                <?php
                                if ($address != '') {
                                    echo '<li class="job-property margin-bottom-10"><i class="la la-map-marker"></i>
                                        ' . _esc($address, false) . '</li>';
                                }
                                if ($phone != '') {
                                    echo '<li class="job-property margin-bottom-10"><i class="la la-phone"></i>
                                        <a href="tel:' . _esc($phone, false) . '" rel="nofollow">' . _esc($phone, false) . '</a></li>';
                                }
                                if ($email != '') {
                                    echo '<li class="job-property margin-bottom-10"><i class="la la-envelope"></i>
                                        <a href="mailto:' . _esc($email, false) . '" rel="nofollow">' . _esc($email, false) . '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- contact-detail -->
            </div>
            <!-- row -->
        </div>
    </div>
    <script src='https://www.google.com/recaptcha/api.js'></script>

<?php
if (!empty($latitude)) {
    if ($config['map_type'] == "google") {
        ?>
        <link href="<?php _esc($config['site_url']); ?>includes/assets/plugins/map/google/map-marker.css"
              type="text/css" rel="stylesheet">
        <script type='text/javascript'
                src='//maps.google.com/maps/api/js?key=<?php _esc($config['gmap_api_key']) ?>&#038;libraries=places%2Cgeometry&#038;ver=2.2.1'></script>
        <script type='text/javascript'
                src='<?php _esc($config['site_url']); ?>includes/assets/plugins/map/google/richmarker-compiled.js'></script>
        <script type='text/javascript'
                src='<?php _esc($config['site_url']); ?>includes/assets/plugins/map/google/markerclusterer_packed.js'></script>
        <script type='text/javascript'
                src='<?php _esc($config['site_url']); ?>includes/assets/plugins/map/google/gmapAdBox.js'></script>
        <script type='text/javascript'
                src='<?php _esc($config['site_url']); ?>includes/assets/plugins/map/google/maps.js'></script>
        <script>
            var element = "singleListingMap";
            var getCity = false;
            var _latitude = '<?php _esc($latitude)?>';
            var _longitude = '<?php _esc($longitude)?>';
            var color = '<?php _esc($map_color)?>';
            var site_url = '<?php _esc($config['site_url']);?>';
            var path = site_url;
            simpleMap(_latitude, _longitude, element);
        </script>
        <?php
    } else {
        ?>
        <script>
            var openstreet_access_token = '<?php _esc($config['openstreet_access_token'])?>';
        </script>
        <link rel="stylesheet"
              href="<?php _esc($config['site_url']); ?>includes/assets/plugins/map/openstreet/css/style.css">
        <!-- Leaflet // Docs: https://leafletjs.com/ -->
        <script src="<?php _esc($config['site_url']); ?>includes/assets/plugins/map/openstreet/leaflet.min.js"></script>

        <!-- Leaflet Maps Scripts (locations are stored in leaflet-quick.js) -->
        <script src="<?php _esc($config['site_url']); ?>includes/assets/plugins/map/openstreet/leaflet-markercluster.min.js"></script>
        <script src="<?php _esc($config['site_url']); ?>includes/assets/plugins/map/openstreet/leaflet-gesture-handling.min.js"></script>
        <script src="<?php _esc($config['site_url']); ?>includes/assets/plugins/map/openstreet/leaflet-quick.js"></script>

        <!-- Leaflet Geocoder + Search Autocomplete // Docs: https://github.com/perliedman/leaflet-control-geocoder -->
        <script src="<?php _esc($config['site_url']); ?>includes/assets/plugins/map/openstreet/leaflet-autocomplete.js"></script>
        <script src="<?php _esc($config['site_url']); ?>includes/assets/plugins/map/openstreet/leaflet-control-geocoder.js"></script>

        <?php
    }
}
overall_footer();
?>
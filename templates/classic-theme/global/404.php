<?php
overall_header(__("404 - Page Not Found"));
?>
    <div class="margin-top-70"></div>
<div class="wrapper-404 d-flex align-items-center">
    <div class="container">

        <div class="row">
            <div class="col-xl-12">

                <section id="not-found" class="center margin-top-50 margin-bottom-25">
                    <h2>404 <i class="fa ti-face-sad"></i></h2>
                    <p><?php _e("We're sorry, but the page you were looking for doesn't exist") ;?></p>
                </section>
                <div class="row">
                    <div class="col-xl-8 offset-xl-2">
                        <form action="<?php url('BLOG') ?>" method="get">
                        <div class="intro-banner-search-form not-found-search margin-bottom-50">
                            <!-- Search Field -->
                            <div class="intro-search-field ">
                                <input id="intro-keywords" name="s" type="text" placeholder="<?php _e('Looking for other content?') ;?>">
                            </div>
                            <!-- Button -->
                            <div class="intro-search-button">
                                <button type="submit" class="button ripple-effect"><?php _e('Search') ;?></button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
    <div class="margin-top-70"></div>
<?php overall_footer();

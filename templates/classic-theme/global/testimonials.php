<?php
overall_header(__("Testimonials"));
?>
<?php print_adsense_code('header_bottom'); ?>
    <div id="titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2><?php _e("Testimonials") ?></h2>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><a href="<?php url("TESTIMONIALS") ?>"><?php _e("Testimonials") ?></a></li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
    <div class="container margin-bottom-50">
        <div class="row">
            <?php foreach($testimonials as $testimonial){ ?>
                <div class="col-md-4">
                    <div class="single-testimonial">
                        <div class="single-inner">
                            <div class="testimonial-content">
                                <p><?php _esc($testimonial['content']) ?></p>
                            </div>
                            <div class="testi-author-info">
                                <div class="image"><img src="<?php _esc($config['site_url']);?>storage/testimonials/<?php _esc($testimonial['image']) ?>" alt="<?php _esc($testimonial['name']) ?>"></div>
                                <h5 class="name"><?php _esc($testimonial['name']) ?></h5>
                                <span class="designation"><?php _esc($testimonial['designation']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php if($show_paging){ ?>
            <div class="pagination-container margin-top-20">
                <nav class="pagination">
                    <ul>
                        <?php
                        foreach($pages as $page) {
                            if ($page['current'] == 0)
                                echo '<li><a href="' . _esc($page['link'],false) . '" class="ripple-effect">' . _esc($page['title'],false) . '</a></li>';
                            else
                                echo '<li><a href="#" class="current-page ripple-effect">' . _esc($page['title'],false) . '</a></li>';
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        <?php } ?>
    </div>
<?php
overall_footer();
?>
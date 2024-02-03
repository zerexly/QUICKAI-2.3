<?php
overall_header($title);
?>
<?php print_adsense_code('header_bottom'); ?>
<!-- Content
================================================== -->
<div id="titlebar" class="white margin-bottom-30">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php _esc($title)?></h2>
                <span><?php _e("Recent Blog") ?></span>

                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><a href="<?php url("BLOG") ?>"><?php _e("Blog") ?></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Section -->
<div class="section gray">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-8">

                <!-- Section Headline -->
                <div class="section-headline margin-top-60 margin-bottom-35">
                    <h4><?php _e("Recent Blog") ?></h4>
                </div>
                <?php
                if($result_found){
                    foreach($items as $blog){
                        ?>
                        <!-- Blog Post -->
                        <a href="<?php _esc($blog['link']) ?>" class="blog-post">
                            <!-- Blog Post Thumbnail -->
                            <div class="blog-post-thumbnail">
                                <div class="blog-post-thumbnail-inner">
                                    <span class="blog-item-tag"><?php _esc($blog['author']) ?></span>
                                    <?php if($config['blog_banner']){ ?>
                                        <img src="<?php _esc($config['site_url']);?>storage/blog/<?php _esc($blog['image']) ?>" alt="<?php _esc($blog['title']) ?>">
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- Blog Post Content -->
                            <div class="blog-post-content">
                                <span class="blog-post-date"><?php _esc($blog['created_at']) ?></span>
                                <h3 class="margin-bottom-0"><?php _esc($blog['title']) ?></h3>
                                <div class="margin-bottom-15"><?php _esc($blog['categories']) ?></div>
                                <p><?php _esc($blog['description']) ?></p>
                            </div>
                            <!-- Icon -->
                            <div class="entry-icon"></div>
                        </a>
                        <?php
                    }
                    ?>
                    <!-- Pagination -->
                    <div class="clearfix"></div>
                    <?php if($show_paging){ ?>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Pagination -->
                            <div class="pagination-container margin-top-10 margin-bottom-20">
                                <nav class="pagination">
                                    <ul>
                                        <?php
                                        foreach($pagging as $page) {
                                            if ($page['current'] == 0){
                                                ?>
                                                <li><a href="<?php _esc($page['link'])?>"><?php _esc($page['title'])?></a></li>
                                            <?php }else{
                                                ?>
                                                <li><a href="#" class="current-page"><?php _esc($page['title'])?></a></li>
                                            <?php }
                                        }
                                        ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                }
                else{
                    ?>
                    <div class="blog-not-found">
                        <h2><span>:</span>(</h2>
                        <p>
                            <?php _e("Sorry, we could not found the blog you are looking for!") ?>
                        </p>
                    </div>
                <?php } ?>
            </div>


            <div class="col-xl-4 col-lg-4 content-left-offset">
                <div class="sidebar-container margin-top-65">
                    <?php print_adsense_code('blog_sidebar_top'); ?>

                    <form action="<?php url("BLOG") ?>">
                        <div class="sidebar-widget margin-bottom-40">
                            <div class="input-with-icon">
                                <input class="with-border" type="text" placeholder="<?php _e("Search") ?>..." name="s"
                                       id="search-widget" value="<?php _esc($search) ?>">
                                <i class="icon-material-outline-search"></i>
                            </div>
                        </div>
                    </form>

                    <!-- Category Widget -->
                    <div class="margin-bottom-40">
                        <h3 class="widget-title"><?php _e("Categories") ?></h3>
                        <div class="widget-content">
                            <ul>
                                <?php
                                foreach($blog_cat as $blog_cats){
                                    ?>
                                    <li class="clearfix">
                                        <a href="<?php _esc($blog_cats['link']) ?>">
                                            <span class="pull-left"><?php _esc($blog_cats['title']) ?></span>
                                            <span class="pull-right">(<?php _esc($blog_cats['blog']) ?>)</span></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <!-- Category Widget / End-->

                    <?php
                    if($config['testimonials_enable'] && $config['show_testimonials_blog']){
                        ?>
                        <!-- Testimonials Widget -->
                        <div class="sidebar-widget">
                            <h3><?php _e("Testimonials") ?></h3>
                            <div class="single-carousel">
                                <?php
                                foreach($testimonials as $testimonial){
                                    ?>
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
                                <?php } ?>
                            </div>
                        </div>
                        <!-- Testimonials Widget / End-->
                    <?php } ?>

                    <!-- Tags Widget -->
                    <div class="sidebar-widget blog-tag-widget">
                        <h3><?php _e("Tags") ?></h3>
                        <div class="task-tags">
                            <?php _esc($all_tags) ?>
                        </div>
                    </div>

                    <!-- Social Widget -->
                    <div class="sidebar-widget">
                        <h3><?php _e("Social Profiles") ?></h3>
                        <div class="freelancer-socials margin-top-25">
                            <ul>
                                <?php
                                if($config['facebook_link'] != "")
                                    echo '<li><a href="'._esc($config['facebook_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-facebook"></i></a></li>';
                                if($config['twitter_link'] != "")
                                    echo '<li><a href="'._esc($config['twitter_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-twitter"></i></a></li>';
                                if($config['instagram_link'] != "")
                                    echo '<li><a href="'._esc($config['instagram_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-instagram"></i></a></li>';
                                if($config['linkedin_link'] != "")
                                    echo '<li><a href="'._esc($config['linkedin_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-linkedin"></i></a></li>';
                                if($config['pinterest_link'] != "")
                                    echo '<li><a href="'._esc($config['pinterest_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-pinterest"></i></a></li>';
                                if($config['youtube_link'] != "")
                                    echo '<li><a href="'._esc($config['youtube_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-youtube"></i></a></li>';
                                ?>
                            </ul>
                        </div>
                    </div>
                    <?php print_adsense_code('blog_sidebar_bottom'); ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Spacer -->
    <div class="padding-top-40"></div>
    <!-- Spacer -->

</div>
<!-- Section / End -->
<?php
overall_footer();
?>

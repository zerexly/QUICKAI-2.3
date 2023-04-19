<?php
overall_header($title, $meta_desc, $meta_image, true);
?>
<?php print_adsense_code('header_bottom'); ?>
<!-- Content
================================================== -->
<div id="titlebar" class="gradient">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php _esc($title);?></h2>
                <span><?php _e("by") ?> <?php _esc($author);?></span>

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

<!-- Post Content -->
<div class="container">
    <div class="row">

        <!-- Inner Content -->
        <div class="col-xl-8 col-lg-8">
            <!-- Blog Post -->
            <div class="blog-post single-post">

                <?php if($config['blog_banner'] && isset($image)){ ?>
                <!-- Blog Post Thumbnail -->
                <div class="blog-post-thumbnail">
                    <div class="blog-post-thumbnail-inner">
                            <img src="<?php _esc($config['site_url']);?>storage/blog/<?php _esc($image);?>" alt="<?php _esc($title);?>">
                    </div>
                </div>
                <?php } ?>
                <!-- Blog Post Content -->
                <div class="blog-post-content">
                    <h3 class="margin-bottom-10"><?php _esc($title);?></h3>
                    <div class="blog-post-info-list margin-bottom-20">
                        <span class="blog-post-info"><i class="la la-clock-o"></i> <?php _esc($created_at);?></span>
                        <span class="blog-post-info"><i class="fa fa-folder-o"></i> <?php _esc($categories);?></span>
                    </div>
                    <div class="user-html"><?php _esc($description);?></div>
                    <?php if($show_tag){ ?>
                        <div class="task-tags margin-bottom-20">
                            <?php _e("Tags") ?>: <?php _esc($blog_tags);?>
                        </div>
                    <?php } ?>
                    <!-- Share Buttons -->
                    <div class="share-buttons margin-top-25">
                        <div class="share-buttons-trigger"><i class="icon-feather-share-2"></i></div>
                        <div class="share-buttons-content">
                            <span><?php _e("Interesting?") ?> <strong><?php _e("Share It!") ?></strong></span>
                            <ul class="share-buttons-icons">

                                <li><a href="mailto:?subject=<?php _esc($title);?>&body=<?php _esc($blog_link) ?>" data-button-color="#dd4b39"
                                       title="<?php _e("Share on Email") ?>" data-tippy-placement="top" rel="nofollow"
                                       target="_blank"><i class="fa fa-envelope"></i></a></li>
                                <li><a href="https://facebook.com/sharer/sharer.php?u=<?php _esc($blog_link) ?>"
                                       data-button-color="#3b5998" title="<?php _e("Share on Facebook") ?>"
                                       data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-facebook"></i></a></li>
                                <li><a href="https://twitter.com/share?url=<?php _esc($blog_link) ?>&text=<?php _esc($title);?>"
                                       data-button-color="#1da1f2" title="<?php _e("Share on Twitter") ?>"
                                       data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-twitter"></i></a></li>
                                <li><a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php _esc($blog_link) ?>"
                                       data-button-color="#0077b5" title="<?php _e("Share on LinkedIn") ?>"
                                       data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-linkedin"></i></a></li>
                                <li>
                                    <a href="https://pinterest.com/pin/create/bookmarklet/?&url=<?php _esc($blog_link) ?>&description=<?php _esc($title);?>"
                                       data-button-color="#bd081c" title="<?php _e("Share on Pinterest") ?>"
                                       data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-pinterest-p"></i></a></li>
                                <li><a href="https://web.whatsapp.com/send?text=<?php _esc($blog_link) ?>" data-button-color="#25d366"
                                       title="<?php _e("Share on WhatsApp") ?>" data-tippy-placement="top" rel="nofollow"
                                       target="_blank"><i class="fa fa-whatsapp"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Blog Post Content / End -->
            <div id="comments">
                <?php if($config['blog_comment_enable']){
                    ?>
                        <div class="blog-widget">
                            <h3 class="widget-title margin-bottom-25"><?php _e("Comments") ?> (<?php _esc($comments_count) ?>)</h3>

                            <div class="latest-comments">
                                <ul>
                                    <?php
                                    foreach($comments as $comment){
                                        ?>
                                        <li id="li-comment-<?php _esc($comment['id']) ?>" <?php if($comment['is_child']){ echo 'class="children-'._esc($comment['level'],false).'"'; } ?>>
                                            <div class="comments-box" id="comment-<?php _esc($comment['id']) ?>">
                                                <div class="comments-avatar">
                                                    <img src="<?php _esc($config['site_url']);?>storage/profile/<?php _esc($comment['avatar']) ?>" alt="<?php _esc($comment['name']) ?>">
                                                </div>
                                                <div class="comments-text">
                                                    <div class="avatar-name">
                                                        <h5><?php _esc($comment['name']) ?></h5>
                                                        <span><?php _esc($comment['created_at']) ?></span>
                                                        <?php if($comment['level'] < 3){ ?>
                                                        <a class="reply comments-reply comment-reply-link" href="javascript:void(0)"
                                                           data-commentid="<?php _esc($comment['id']) ?>" data-postid="<?php _esc($blog_id) ?>"
                                                           data-belowelement="comment-<?php _esc($comment['id']) ?>"
                                                           data-respondelement="respond"><i class="fa fa-reply"></i><?php _e("Reply") ?></a>
                                                        <?php } ?>
                                                    </div>
                                                    <p><?php _esc($comment['comment']) ?></p>
                                                </div>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>

                        <?php if($show_paging){ ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pagination-container margin-top-10 margin-bottom-20">
                                        <nav class="pagination">
                                            <ul>
                                                <?php
                                                foreach($comment_paging as $c_paging) {
                                                    if ($c_paging['current'] == 0)
                                                        echo '<li><a href="' . _esc($c_paging['link'],false) . '">' . _esc($c_paging['title'],false) . '</a></li>';
                                                    else
                                                        echo '<li><a href="#" class="current-page">' . _esc($c_paging['title'],false) . '</a></li>';
                                                }
                                                ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        <?php }

                    if($show_comment_form){ ?>
                        <!-- Leave a Comment -->
                        <div class="blog-widget" id="respond">
                            <h3 class="widget-title"><?php _e("Post a Comment") ?>
                                <small><a rel="nofollow" id="cancel-comment-reply-link" href="javascript:void(0)"
                                          style="display: none;"><?php _e("Cancel reply") ?></a></small>
                            </h3>

                            <div>
                                <?php
                                if($comment_error){
                                    echo '<div class="notification error"><p>'._esc($comment_error,false).'</p></div>';
                                }
                                if($comment_success){
                                    echo '<div class="notification success"><p>'._esc($comment_success,false).'</p></div>';
                                }
                                ?>

                                <form action="#respond" method="post" id="commentform" class="blog-comment-form">
                                    <div class="row">

                                        <?php
                                        if(!($admin_logged_in || $is_login)){ ?>
                                        <div class="col-xl-6">
                                            <div class="input-with-icon-left no-border">
                                                <i class="icon-material-outline-account-circle"></i>
                                                <input class="input-text" type="text" placeholder="<?php _e("Your Name") ?> *" name="user_name"
                                                       value="<?php _esc($user_name) ?>" required="">
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="input-with-icon-left no-border">
                                                <i class="icon-material-baseline-mail-outline"></i>
                                                <input class="input-text" type="email" placeholder="<?php _e("Your E-Mail") ?> *"
                                                       name="user_email" value="<?php _esc($user_email) ?>" required>
                                            </div>
                                        </div>

                                        <?php }
                                        if($admin_logged_in && $is_login){ ?>
                                        <div class="col-md-12">
                                            <div class="commenting-as">
                                                <label for="commenting-as"><?php _e("You are commenting as:") ?></label>
                                                <select id="commenting-as" name="commenting-as"
                                                        class="selectpicker with-border col-md-4">
                                                    <option value="admin"><?php _esc($admin_username) ?> (<?php _e("Admin") ?>)</option>
                                                    <option value="user"><?php _esc($username) ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php }
                                        else if($admin_logged_in){ ?>
                                        <div class="col-md-12">
                                            <p><?php _e("You are commenting as:") ?> <strong><?php _esc($admin_username) ?></strong> (<?php _e("Admin") ?>)</p>
                                        </div>
                                        <?php }
                                        else if($is_login){ ?>
                                        <div class="col-md-12">
                                            <p><?php _e("You are commenting as:") ?> <strong><?php _esc($username) ?></strong></p>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-12">
                                    <textarea class="with-border" rows="5" id="comment-field" name="comment" placeholder="<?php _e("Your comment...") ?>"
                                              required></textarea>
                                            <button type="submit" name="comment-submit"
                                                    class="button ripple-effect"><?php _e("Submit") ?></button>
                                            <input type="hidden" name="comment_parent" id="comment_parent" value="0">
                                            <input type="hidden" name="comment_post_ID" value="<?php _esc($blog_id) ?>" id="comment_post_ID">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Leave a Comment / End -->
                    <?php }else{ ?>
                        <div class="blog-widget">
                            <?php _e("Please login to post a comment.") ?>
                        </div>
                    <?php } ?>

                <?php } ?>


            </div>

        </div>
        <!-- Inner Content / End -->


        <div class="col-xl-4 col-lg-4 content-left-offset">
            <div class="sidebar-container">
                <?php print_adsense_code('blog_sidebar_top'); ?>
                <div class="margin-bottom-40">
                <form action="<?php url("BLOG") ?>">
                        <div class="input-with-icon">
                            <input class="with-border" type="text" placeholder="<?php _e("Search...") ?>" name="s"
                                   id="search-widget">
                            <i class="icon-material-outline-search"></i>
                        </div>
                </form>
                </div>
                <div class="margin-bottom-40">
                    <h3 class="widget-title"><?php _e("Recent Blog") ?></h3>
                    <div class="recent-post-widget">
                        <?php
                        foreach($recent_blog as $recent_blogs){
                        $image_url = $config['site_url'].'storage/blog/'.$recent_blogs['image'];
                        ?>
                        <div>
                            <?php
                            if($config['blog_banner']){ ?>
                            <a href="<?php _esc($recent_blogs['link']) ?>">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="<?php _esc($image_url) ?>" alt="<?php _esc($recent_blogs['title']) ?>"
                                     class="post-thumb lazy-load">
                            </a>
                            <?php } ?>
                            <div class="recent-post-widget-content">
                                <h2><a href="<?php _esc($recent_blogs['link']) ?>"><?php _esc($recent_blogs['title']) ?></a></h2>
                                <div class="post-date">
                                    <i class="icon-feather-clock"></i> <?php _esc($recent_blogs['created_at']) ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>

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
                <div class="sidebar-widget">
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


<script src="<?php _esc(TEMPLATE_URL);?>/js/comment-reply.js"></script>
<?php
overall_footer();
?>
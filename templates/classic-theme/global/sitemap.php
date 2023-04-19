<?php
overall_header(__("Sitemap"));
?>
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php _e("Sitemap") ?></h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><?php _e("Sitemap") ?></li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
    <div class="container margin-bottom-50">
        <div class="section">
            <h2 class="text-center sitemap-h2"><?php _e("Categories") ?></h2>
            <hr>
            <div class="row cg-nav-wrapper cg-nav-wrapper-row-2" data-role="cg-nav-wrapper">
                <?php foreach ($category as $cat){ ?>
                <div>
                    <div class="anchor-wrap anchor<?php _esc($cat['main_id']) ?>-wrap" data-role="anchor<?php _esc($cat['main_id']) ?>">
                        <a class="anchor<?php _esc($cat['main_id']) ?> jumper" data-role="cont" href="#anchor<?php _esc($cat['main_id']) ?>">
                            <i class="caticon <?php _esc($cat['icon']) ?>"></i>
                        <span class="desc">
                            <?php _esc($cat['main_title']) ?>
                        </span>
                        </a>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="cg-main">
                <?php foreach ($subcategory as $subcat){ ?>
                <div class="item clearfix" data-spm="0">
                    <h3 class="big-title anchor<?php _esc($subcat['main_id']) ?> anchor-agricuture" data-role="anchor<?php _esc($subcat['main_id']) ?>-scroll">
                        <span id="anchor<?php _esc($subcat['main_id']) ?>" class="anchor-subsitution"></span>
                        <i class="cg-icon <?php _esc($subcat['icon']) ?>"></i><?php _esc($subcat['main_title']) ?>
                    </h3>

                    <div class="sub-item-wrapper clearfix">
                        <div class="sub-item">
                            <h4 class="sub-title">
                                <a href="<?php _esc($subcat['catlink']) ?>"><?php _esc($subcat['main_title']) ?></a><span> (<?php _esc($subcat['main_ads_count']) ?>)</span>
                            </h4>
                            <?php if($subcat['sub_cat']){ ?>
                            <div class="sub-item-cont-wrapper">
                                <ul class="sub-item-cont clearfix">
                                    <?php _esc($subcat['sub_title']) ?>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                </div>
                <?php } ?>
            </div>

        </div>
    </div>
<script>
    $(document).ready(function() {
        $(".jumper").on("click", function( e ) {

            e.preventDefault();

            $("body, html").animate({
                scrollTop: $( $(this).attr('href') ).offset().top
            }, 600);

        });
    });
</script>
<?php
overall_footer();
?>

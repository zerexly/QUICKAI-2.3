<?php
overall_header(__("Error"));
?>
<section id="main" class="clearfix text-center margin-top-50 margin-bottom-50">
    <div class="container">
        <div class="row">
            <div class="col-sm-10 margin-0-auto">
                <div class="found-section section">
                    <h1 class="margin-bottom-20"><?php _esc($message);?></h1>
                    <p>
                    <?php
                    if($content == ""){
                        _e('We can not seem to find the page you were looking for');
                    }else{
                        _esc($content);
                    }
                    ?>
                    </p>
                    <a href="<?php url("INDEX") ?>" class="button ripple-effect"><?php _e("Go to Home") ?></a></div>
            </div>
        </div>
    </div>
    <!-- container -->
</section>
<!-- main -->
<?php
overall_footer();
?>

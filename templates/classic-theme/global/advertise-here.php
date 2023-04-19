<?php overall_header(__('Advertise here')); ?>
<script src="https://checkout.stripe.com/v2/checkout.js"></script>

<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php _e("Advertise with us") ?></h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><?php _e("Advertise with us") ?></li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container margin-bottom-50">

    <div class="row">
        <div class="col-sm-12">
            <div class="found-section section">
                <?php if($is_login){
                    echo '<div class="section html-pages"><div class="qbm-box"></div></div>';
                }else{
                    ?>
                    <h1 class="margin-bottom-20"><?php _e("Login required") ?></h1>
                    <p><?php _e("Login required to access this page.") ?></p>
                    <a href="#sign-in-dialog" class="button ripple-effect popup-with-zoom-anim "><?php _e("Click Here") ?>   </a>
                <?php } ?>

            </div>
        </div>
    </div>
</div>
<?php
overall_footer();
?>

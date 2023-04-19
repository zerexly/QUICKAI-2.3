<meta http-equiv="refresh" content="2;URL=<?php _esc($forward) ?>">
<?php
overall_header(__("Message"));
?>
<div class="container margin-top-50 margin-bottom-50">
    <div class="row">
        <div class="col-md-12">
            <div class="margin-0-auto">
                <h1 class="margin-bottom-20"><?php _esc($heading)?></h1>
                <p><?php _esc($message) ?>, <?php _e("if you are not forwarded to") ?> <?php _esc($forward) ?> <?php _e("within 10 seconds") ?> <a href="<?php _esc($forward) ?>"><?php _e("click here") ?></a></p>
            </div>
        </div>
    </div>
</div>
<?php
overall_footer();
?>
<?php
overall_header(__("Message"));
?>
<div class="container margin-top-50 margin-bottom-50">
<div class="row">
    <div class="col-md-12">
    	<div class="margin-0-auto">
            <h1 class="margin-bottom-20"><?php _esc($heading)?>!</h1>
            <p><?php _esc($message) ?></p>
            <button onClick="window.location.href='javascript:history.back();'"
                    class="button ripple-effect"><?php _e("Back") ?>
            </button>
        </div>
    </div>
</div>
</div>
<?php
overall_footer();
?>

<!-- footer start-->
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 footer-copyright">
                <p class="mb-0"><?php _e('Copyright') ?> © <?php _esc(date('Y'))  ?> <a href="https://Sahil.com" target="_blank">Sahil</a>. <?php _e('All rights reserved.') ?> </p>
            </div>
            <div class="col-md-6">
                <p class="float-right mb-0"><?php _e('Hand-crafted & made with') ?> <i class="icon-feather-heart"></i></p>
            </div>
        </div>
    </div>
</footer>

</div>
</div>
</div>
<script>
    var LANG_ARE_YOU_SURE = "<?php _e("Are you sure?") ?>";
    var LANG_ERROR_LOADING_DETAILS = "<?php _e("There was an error loading the details. Please retry.") ?>";
    var LANG_PROBLEM_INSTALLATION = "<?php _e("Problem in Installation, Please try again.") ?>";
    var LANG_PROBLEM_UNINSTALL = "<?php _e("Problem in Uninstall, Please try again.") ?>";
    var LANG_VARIABLE_EDITED = "<?php _e("Success! variable edited.") ?>";
    var LANG_UNEXPECTED_ERROR = "<?php _e("Unexpected error, Please try again.") ?>";
    var LANG_SUCCESS = "<?php _e("Success") ?>";
    var LANG_ERROR = "<?php _e("Error") ?>";
</script>
<script src="<?php echo ADMINURL; ?>assets/js/jquery.min.js"></script>
<script src="<?php echo ADMINURL; ?>assets/js/jquery-ui.min.js"></script>
<script src="<?php echo ADMINURL; ?>assets/js/popper.min.js"></script>
<script src="<?php echo ADMINURL; ?>assets/js/bootstrap.js"></script>
<script src="<?php echo ADMINURL; ?>assets/js/tippy.all.min.js"></script>
<script src="<?php echo ADMINURL; ?>assets/js/jquery.dataTables.min.js"></script>
<script src="<?php echo ADMINURL; ?>assets/js/jquery-slidePanel.min.js"></script>
<script src="<?php echo ADMINURL; ?>assets/js/select2.full.min.js"></script>
<script src="<?php echo ADMINURL; ?>assets/js/sidebar-menu.js"></script>
<script src="<?php echo ADMINURL; ?>assets/js/sweetalert.min.js"></script>
<?php if(isset($footer_content)) {
    echo $footer_content;
} ?>
<script src="<?php echo ADMINURL; ?>assets/js/jquery.form.js"></script>
<script src="<?php echo ADMINURL; ?>assets/js/admin-ajax.js?ver=<?php echo $config['version']; ?>"></script>
<script src="<?php echo ADMINURL; ?>assets/js/script.js?ver=<?php echo $config['version'];?>"></script>
</body>
</html>
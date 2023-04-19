
</div>
<!-- Wrapper / End -->
<script>
    $(document).ready(function () {
        $("#header-container").removeClass('transparent-header').addClass('dashboard-header sticky');
        $('.header-icon').removeClass('d-none');
    });
</script>

<script>
    var session_uname = "<?php _esc($username)?>";
    var session_uid = "<?php _esc($user_id)?>";
    var session_img = "<?php _esc($userpic)?>";
    // Language Var
    var LANG_ERROR_TRY_AGAIN = "<?php _e("Error: Please try again.") ?>";
    var LANG_LOGGED_IN_SUCCESS = "<?php _e("Logged in successfully. Redirecting...") ?>";
    var LANG_ERROR = "<?php _e("Error") ?>";
    var LANG_CANCEL = "<?php _e("Cancel") ?>";
    var LANG_DELETED = "<?php _e("Deleted") ?>";
    var LANG_ARE_YOU_SURE = "<?php _e("Are you sure?") ?>";
    var LANG_YOU_WANT_DELETE = "<?php _e("You want to delete this job") ?>";
    var LANG_YES_DELETE = "<?php _e("Yes, delete it") ?>";
    var LANG_PROJECT_CLOSED = "<?php _e("Project has been closed") ?>";
    var LANG_PROJECT_DELETED = "<?php _e("Project has been deleted") ?>";
    var LANG_RESUME_DELETED = "<?php _e("Resume Deleted.") ?>";
    var LANG_EXPERIENCE_DELETED = "<?php _e("Experience Deleted.") ?>";
    var LANG_COMPANY_DELETED = "<?php _e("Company Deleted.") ?>";
    var LANG_SHOW = "<?php _e("Show") ?>";
    var LANG_HIDE = "<?php _e("Hide") ?>";
    var LANG_HIDDEN = "<?php _e("Hidden") ?>";
    var LANG_TYPE_A_MESSAGE = "<?php _e("Type a message") ?>";
    var LANG_ADD_FILES_TEXT = "<?php _e("Add files to the upload queue and click the start button.") ?>";
    var LANG_ENABLE_CHAT_YOURSELF = "<?php _e("Could not able to chat yourself.") ?>";
    var LANG_JUST_NOW = "<?php _e("Just now") ?>";
    var LANG_PREVIEW = "<?php _e("Preview") ?>";
    var LANG_SEND = "<?php _e("Send") ?>";
    var LANG_FILENAME = "<?php _e("Filename") ?>";
    var LANG_STATUS = "<?php _e("Status") ?>";
    var LANG_SIZE = "<?php _e("Size") ?>";
    var LANG_DRAG_FILES_HERE = "<?php _e("Drag files here") ?>";
    var LANG_STOP_UPLOAD = "<?php _e("Stop Upload") ?>";
    var LANG_ADD_FILES = "<?php _e("Add files") ?>";
    var LANG_CHATS = "<?php _e("Chats") ?>";
    var LANG_NO_MSG_FOUND = "<?php _e("No message found") ?>";
    var LANG_ONLINE = "<?php _e("Online") ?>";
    var LANG_OFFLINE = "<?php _e("Offline") ?>";
    var LANG_TYPING = "<?php _e("Typing...") ?>";
    var LANG_GOT_MESSAGE = "<?php _e("You got a message") ?>";
    var LANG_COPIED_SUCCESSFULLY = "<?php _e("Copied successfully.") ?>";
    var DEVELOPER_CREDIT = <?php _esc(get_option('developer_credit',1)) ?>;
    var LIVE_CHAT = <?php _esc(get_option('enable_live_chat', 0)) ?>;

    if ($("body").hasClass("rtl")) {
        var rtl = true;
    }else{
        var rtl = false;
    }
</script>
<!-- Scripts
================================================== -->

<script src="<?php _esc(TEMPLATE_URL);?>/js/chosen.min.js"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/jquery.lazyload.min.js"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/tippy.all.min.js"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/simplebar.min.js"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/bootstrap-slider.min.js"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/bootstrap-select.min.js"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/snackbar.js"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/counterup.min.js"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/magnific-popup.min.js"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/slick.min.js"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/jquery.cookie.min.js?ver=<?php _esc($config['version']);?>"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/user-ajax.js?ver=<?php _esc($config['version']);?>"></script>
<script src="<?php _esc(TEMPLATE_URL);?>/js/custom.js?ver=<?php _esc($config['version']);?>"></script>
<?php if(isset($footer_content)) {
    echo $footer_content;
} ?>
</body>
</html>

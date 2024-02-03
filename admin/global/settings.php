<?php
include '../includes.php';
$page_title = __('Settings');
include '../header.php'; ?>

    <!-- Page Body Start-->
    <div class="page-body-wrapper">
<?php include '../sidebar.php'; ?>

    <!-- Page Sidebar Ends-->
    <div class="page-body">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-lg-6 main-header">
                        <h2><?php _esc($page_title) ?></h2>
                        <h6 class="mb-0"><?php _e('admin panel') ?></h6>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="tab-content">
                <?php
                require_once 'settings/general.php';
                require_once 'settings/logo.php';
                require_once 'settings/map.php';
                require_once 'settings/international.php';
                require_once 'settings/email.php';
                require_once 'settings/theme.php';
                require_once 'settings/ai.php';
                require_once 'settings/affiliate.php';
                require_once 'settings/live_chat.php';
                require_once 'settings/billing.php';
                require_once 'settings/social_login.php';
                require_once 'settings/pwa.php';
                require_once 'settings/recaptcha.php';
                require_once 'settings/blog.php';
                require_once 'settings/testimonials.php';
                require_once 'settings/purchase_code.php';
                ?>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
    <script id="quick-sidebar-menu-js-extra">
        var QuickMenu = {"page": "settings"};
    </script>
<?php ob_start() ?>
    <script>
        $(function () {
            $('.api-type').on('change', function () {
                if ($(this).val() == 'any') {
                    $('.ai-image-api-key option').show();
                } else {
                    $('.ai-image-api-key option').hide();
                    $('.ai-image-api-key option[data-type="' + $(this).val() + '"]').show();
                    // display random field always
                    $('.ai-image-api-key option:first-child').show();
                }
            }).trigger('change');

            $('#single_model_for_plans').on('change', function () {
                if ($(this).is(':checked'))
                    $('.open_ai_model').fadeIn();
                else
                    $('.open_ai_model').fadeOut();
            }).trigger('change');

            $('#ai_tts_language').on('change', function (e){
                $('#ai_tts_voice option').hide().addClass('hidden').removeClass('visible');
                $('#ai_tts_voice option.lang-'+ $(this).val()).show().removeClass('hidden').addClass('visible');

                $("#ai_tts_voice option").attr('selected', false);
                $("#ai_tts_voice option.visible:first").attr("selected", "selected");
            });
            $('#ai_tts_voice option').hide().addClass('hidden').removeClass('visible');
            $('#ai_tts_voice option.lang-'+ $('#ai_tts_language').val()).show().removeClass('hidden').addClass('visible');

            var hash = window.location.hash;
            hash && $('ul.nav a[href="' + hash + '"]').click();
            $('.nav a').on('click', function (e) {
                var scrollmem = $('body').scrollTop();
                window.location.hash = this.hash;
                $('html,body').scrollTop(scrollmem);
            });
        });
    </script>
    <link rel="stylesheet" href="../assets/css/datatables.css"/>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
<?php
$footer_content = ob_get_clean();

include '../footer.php';

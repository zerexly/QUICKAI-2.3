<?php
include '../includes.php';

$page_title = __('Email Notifications');
include '../header.php';

$default_shortcodes = [
    [
        'title' => __('Site Title'),
        'code' => '{SITE_TITLE}'
    ],
    [
        'title' => __('Site URL'),
        'code' => '{SITE_URL}'
    ]
];
$email_template = [
    [
        'id' => 'signup-details',
        'title' => __('User account details email'),
        'subject' => 'email_sub_signup_details',
        'message' => 'email_message_signup_details',
        'shortcodes' => array_merge($default_shortcodes, [
            [
                'title' => __('User id'),
                'code' => '{USER_ID}'
            ],
            [
                'title' => __('Username'),
                'code' => '{USERNAME}'
            ],
            [
                'title' => __('User full name'),
                'code' => '{USER_FULLNAME}'
            ],
            [
                'title' => __('User email id'),
                'code' => '{EMAIL}'
            ],
            [
                'title' => __('Password'),
                'code' => '{PASSWORD}'
            ]
        ]),
    ],
    [
        'id' => 'create-account',
        'title' => __('Create account confirmation email'),
        'subject' => 'email_sub_signup_confirm',
        'message' => 'email_message_signup_confirm',
        'shortcodes' => array_merge($default_shortcodes, [
            [
                'title' => __('User id'),
                'code' => '{USER_ID}'
            ],
            [
                'title' => __('Username'),
                'code' => '{USERNAME}'
            ],
            [
                'title' => __('User full name'),
                'code' => '{USER_FULLNAME}'
            ],
            [
                'title' => __('User email id'),
                'code' => '{EMAIL}'
            ],
            [
                'title' => __('Registration Confirmation Link'),
                'code' => '{CONFIRMATION_LINK}'
            ]
        ]),
    ],
    [
        'id' => 'forgot-pass',
        'title' => __('Forgot Password Email'),
        'subject' => 'email_sub_forgot_pass',
        'message' => 'email_message_forgot_pass',
        'shortcodes' => array_merge($default_shortcodes, [
            [
                'title' => __('User id'),
                'code' => '{USER_ID}'
            ],
            [
                'title' => __('Username'),
                'code' => '{USERNAME}'
            ],
            [
                'title' => __('User full name'),
                'code' => '{USER_FULLNAME}'
            ],
            [
                'title' => __('User email id'),
                'code' => '{EMAIL}'
            ],
            [
                'title' => __('Forgot password reset link'),
                'code' => '{FORGET_PASSWORD_LINK}'
            ]
        ]),
    ],
    [
        'id' => 'contact_us',
        'title' => __('Contact Us Email'),
        'subject' => 'email_sub_contact',
        'message' => 'email_message_contact',
        'shortcodes' => array_merge($default_shortcodes, [
            [
                'title' => __('User full name'),
                'code' => '{NAME}'
            ],
            [
                'title' => __('User email id'),
                'code' => '{EMAIL}'
            ],
            [
                'title' => __('Contact email subject'),
                'code' => '{CONTACT_SUBJECT}'
            ],
            [
                'title' => __('Contact email message'),
                'code' => '{MESSAGE}'
            ]
        ]),
    ],
    [
        'id' => 'feedback',
        'title' => __('Feedback Email'),
        'subject' => 'email_sub_feedback',
        'message' => 'email_message_feedback',
        'shortcodes' => array_merge($default_shortcodes, [
            [
                'title' => __('User full name'),
                'code' => '{NAME}'
            ],
            [
                'title' => __('User Email id'),
                'code' => '{EMAIL}'
            ],
            [
                'title' => __('Feedback email subject'),
                'code' => '{FEEDBACK_SUBJECT}'
            ],
            [
                'title' => __('Feedback email message'),
                'code' => '{MESSAGE}'
            ]
        ]),
    ],
    [
        'id' => 'withdraw_accepted',
        'title' => 'Withdraw : Request Accepted by Admin',
        'subject' => 'email_sub_withdraw_accepted',
        'message' => 'emailHTML_withdraw_accepted',
        'shortcodes' => array_merge($default_shortcodes,[
            [
                'title' => __('User id'),
                'code' => '{USER_ID}'
            ],
            [
                'title' => __('Username'),
                'code' => '{USERNAME}'
            ],
            [
                'title' => __('User full name'),
                'code' => '{USER_FULLNAME}'
            ],
            [
                'title' => __('User email id'),
                'code' => '{EMAIL}'
            ],
            [
                'title' => 'Withdrawal amount',
                'code' => '{AMOUNT}'
            ]
        ]),
    ],
    [
        'id' => 'withdraw_rejected',
        'title' => 'Withdraw : Request Rejected By Admin',
        'subject' => 'email_sub_withdraw_rejected',
        'message' => 'emailHTML_withdraw_rejected',
        'shortcodes' => array_merge($default_shortcodes,[
            [
                'title' => __('User id'),
                'code' => '{USER_ID}'
            ],
            [
                'title' => __('Username'),
                'code' => '{USERNAME}'
            ],
            [
                'title' => __('User full name'),
                'code' => '{USER_FULLNAME}'
            ],
            [
                'title' => __('User email id'),
                'code' => '{EMAIL}'
            ],
            [
                'title' => 'Withdrawal amount',
                'code' => '{AMOUNT}'
            ]
            ,
            [
                'title' => 'Reject Reason',
                'code' => '{REJECT_REASON}'
            ]
        ]),
    ],
    [
        'id' => 'new_withdraw_request',
        'title' => 'Admin : New Withdraw Request',
        'subject' => 'email_sub_withdraw_request',
        'message' => 'emailHTML_withdraw_request',
        'shortcodes' => array_merge($default_shortcodes,[
            [
                'title' => __('User id'),
                'code' => '{USER_ID}'
            ],
            [
                'title' => __('Username'),
                'code' => '{USERNAME}'
            ],
            [
                'title' => __('User full name'),
                'code' => '{USER_FULLNAME}'
            ],
            [
                'title' => __('User email id'),
                'code' => '{EMAIL}'
            ],
            [
                'title' => 'Withdrawal amount',
                'code' => '{AMOUNT}'
            ]
        ]),
    ],
];
?>
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
            <form method="post" class="ajax_submit_form" data-action="saveEmailTemplate" data-ajax-sidepanel="true"
                  id="saveEmailTemplate">
                <div class="quick-card card">
                    <div class="card-header d-flex align-items-center">
                        <h5><?php _e('Email Notifications'); ?></h5>
                        <div class="card-header-right">
                            <a href="<?php echo ADMINURL ?>global/settings.php#quickad_email"
                               class="btn btn-primary ripple-effect">
                                <i class="icon-feather-settings"></i> <?php _e('Setting'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="quick-accordion" id="accordion">
                            <?php
                            $i = 1;
                            foreach ($email_template as $template) {
                                ?>
                                <div class="card quick-card">
                                    <div class="card-header d-flex align-items-center">
                                        <h5 class="mb-0 d-flex align-items-center">
                                            <button class="btn btn-link pl-0" data-toggle="collapse"
                                                    data-target="#notification_<?php _esc($template['id']) ?>"
                                                    aria-expanded="false"
                                                    aria-controls="notification_<?php _esc($template['id']) ?>"
                                                    type="button">
                                                <?php _esc($i) ?>. <?php _esc($template['title']) ?>
                                            </button>
                                        </h5>
                                    </div>
                                    <div class="collapse" id="notification_<?php _esc($template['id']) ?>"
                                         aria-labelledby="notification_<?php _esc($template['id']) ?>"
                                         data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label><?php _e('Subject'); ?></label>
                                                        <input name="<?php _esc($template['subject']) ?>"
                                                               class="form-control" type="text"
                                                               value="<?php echo get_option($template['subject']) ?>">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mailMethods mailMethod-0">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="pageContent"><?php _e('Message'); ?></label>
                                                        <textarea name="<?php _esc($template['message']) ?>" id="pageContent" rows="6"
                                                                  class="form-control tiny-editor"><?php echo get_option($template['message']) ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label><?php _e('Shortcodes'); ?></label>
                                                        <div class='quick-shortcode-wrapper'>
                                                            <?php foreach ($template['shortcodes'] as $shortcode) { ?>
                                                                <div class="quick-shortcode-box">
                                                                    <div class="bg-light" title="<?php _esc($shortcode['title']) ?>" data-tippy-placement="top"><?php _esc($shortcode['code']) ?></div>
                                                                    <button class="btn-icon" title="<?php _e('Copy'); ?>" data-tippy-placement="top" type="button" data-code="<?php _esc($shortcode['code']) ?>"><i class="icon-feather-copy"></i></button>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php $i++;
                            } ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="float-right">
                            <button type="button" class="btn btn-secondary" data-toggle="modal"
                                    data-target="#test-email-notification"><?php _e('Test Email Notifications'); ?></button>
                        </div>
                        <button name="email_setting" type="submit"
                                class="btn btn-primary mr-1 save-changes"><?php _e('Save'); ?></button>
                        <button class="btn btn-default" type="reset"><?php _e('Reset'); ?></button>
                    </div>
                </div>
            </form>
        </div>
        <!-- Container-fluid Ends-->
    </div>

    <div id="test-email-notification" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="test_notification_send" method="post" class="ajax_submit_form"
                      data-action="testEmailTemplate" data-ajax-sidepanel="true">
                    <div class="modal-header">
                        <h4 class="modal-title"><?php _e('Test Email Notifications'); ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                                    class=" icon-feather-x"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="test_to_name"><?php _e('To name'); ?></label>
                                    <input id="test_to_name" name="test_to_name" class="form-control" type="text"/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="test_to_email"><?php _e('To email'); ?></label>
                                    <input id="test_to_email" name="test_to_email" class="form-control" type="text"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="templates"><?php _e('Notification templates'); ?></label>
                            <select class="form-control quick-multi-select" id="templates" name="templates[]"
                                    data-select-all="true" multiple>
                                <?php
                                foreach ($email_template as $template) {
                                    ?>
                                    <option value="<?php _esc($template['id']) ?>"><?php _esc($template['title']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php _e('Cancel'); ?></button>
                        <button type="submit" class="btn btn-primary"><?php _e('Send'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        var QuickMenu = {"page": "email-template"};
    </script>

<?php ob_start() ?>
    <link rel="stylesheet" href="<?php echo ADMINURL; ?>assets/css/jquery.multiselect.css"/>
    <script src="<?php echo ADMINURL; ?>assets/js/jquery.multiselect.js"></script>
    <script src="<?php echo ADMINURL; ?>assets/plugins/tinymce/tinymce.min.js"></script>
<?php
$footer_content = ob_get_clean();

include '../footer.php';

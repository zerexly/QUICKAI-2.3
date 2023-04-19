<!-- Dashboard Sidebar
    ================================================== -->
<div class="dashboard-sidebar">
    <div class="dashboard-sidebar-inner" data-simplebar>
        <div class="dashboard-nav-container">

            <!-- Responsive Navigation Trigger -->
            <a href="#" class="dashboard-responsive-nav-trigger">
					<span class="hamburger hamburger--collapse">
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</span>
                <span class="trigger-title"><?php _e("Dashboard Navigation") ?></span>
            </a>
            <!-- Navigation -->
            <div class="dashboard-nav">
                <div class="dashboard-nav-inner">
                    <ul data-submenu-title="<?php _e("My Account") ?>">
                        <li class="<?php echo CURRENT_PAGE == 'app/dashboard' ? 'active' : ''; ?>"><a
                                    href="<?php url("DASHBOARD") ?>"><i
                                        class="icon-feather-grid"></i> <?php _e("Dashboard") ?></a></li>
                        <li class="<?php echo CURRENT_PAGE == 'app/all-images' || CURRENT_PAGE == 'app/all-documents' ? 'active-submenu' : ''; ?>">
                            <a href="#"><i class="icon-feather-file-text"></i> <?php _e("My Documents") ?></a>
                            <ul>
                                <li class="<?php echo CURRENT_PAGE == 'app/all-documents' ? 'active' : ''; ?>"><a
                                            href="<?php url("ALL_DOCUMENTS") ?>"><?php _e("All Documents") ?></a></li>
                                <li class="<?php echo CURRENT_PAGE == 'app/all-images' ? 'active' : ''; ?>"><a
                                            href="<?php url("ALL_IMAGES") ?>"><?php _e("All AI Images") ?></a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul data-submenu-title="<?php _e("Organize and Manage") ?>">
                        <li class="<?php echo CURRENT_PAGE == 'app/ai-templates' ? 'active' : ''; ?>">
                            <a href="<?php url("AI_TEMPLATES") ?>"><i
                                        class="icon-feather-layers"></i> <?php _e("Templates") ?></a></li>

                        <?php if ($config['enable_ai_images']) { ?>
                        <li class="<?php echo CURRENT_PAGE == 'app/ai-images' ? 'active' : ''; ?>"><a
                                    href="<?php url("AI_IMAGES") ?>"><i
                                        class="icon-feather-image"></i> <?php _e("AI Images") ?></a></li>
                        <?php }

                        if ($config['enable_ai_chat']) { ?>
                            <li class="<?php echo CURRENT_PAGE == 'app/ai-chat' ? 'active' : ''; ?>"><a
                                        href="<?php url("AI_CHAT") ?>"><i
                                            class="icon-feather-message-circle"></i> <?php _e("AI Chat") ?></a></li>
                        <?php }

                        if ($config['enable_speech_to_text']) { ?>
                            <li class="<?php echo CURRENT_PAGE == 'app/ai-speech-text' ? 'active' : ''; ?>"><a
                                        href="<?php url("AI_SPEECH_TEXT") ?>"><i
                                            class="icon-feather-headphones"></i> <?php _e("Speech to Text") ?></a></li>
                        <?php }

                        if ($config['enable_ai_code']) { ?>
                            <li class="<?php echo CURRENT_PAGE == 'app/ai-code' ? 'active' : ''; ?>"><a
                                        href="<?php url("AI_CODE") ?>"><i
                                            class="icon-feather-code"></i> <?php _e("AI Code") ?></a></li>
                        <?php } ?>
                    </ul>

                    <ul data-submenu-title="<?php _e("Account") ?>">
                        <?php if ($config['enable_affiliate_program']) { ?>
                        <li class="<?php echo CURRENT_PAGE == 'global/affiliate-program' || CURRENT_PAGE == 'global/withdrawals' ? 'active-submenu' : ''; ?>">
                            <a href="<?php url("AFFILIATE-PROGRAM") ?>"><i
                                        class="icon-feather-share-2"></i> <?php _e("Affiliate Program") ?></a>
                            <ul>
                                <li class="<?php echo CURRENT_PAGE == 'global/affiliate-program' ? 'active' : ''; ?>"><a
                                            href="<?php url("AFFILIATE-PROGRAM") ?>"><?php _e("Affiliate Program") ?></a></li>
                                <li class="<?php echo CURRENT_PAGE == 'global/withdrawals' ? 'active' : ''; ?>"><a
                                            href="<?php url("WITHDRAWALS") ?>"><?php _e("Withdrawals") ?></a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <li class="<?php echo CURRENT_PAGE == 'global/membership' ? 'active' : ''; ?>"><a
                                    href="<?php url("MEMBERSHIP") ?>"><i
                                        class="icon-feather-gift"></i> <?php _e("Membership") ?></a></li>
                        <li class="<?php echo CURRENT_PAGE == 'global/transaction' ? 'active' : ''; ?>"><a
                                    href="<?php url("TRANSACTION") ?>"><i
                                        class="icon-feather-file-text"></i> <?php _e("Transactions") ?></a></li>
                        <li class="<?php echo CURRENT_PAGE == 'global/account-setting' ? 'active' : ''; ?>"><a
                                    href="<?php url("ACCOUNT_SETTING") ?>"><i
                                        class="icon-feather-log-out"></i> <?php _e("Account Setting") ?></a></li>
                        <li><a href="<?php url("LOGOUT") ?>"><i
                                        class="icon-material-outline-power-settings-new"></i> <?php _e("Logout") ?></a>
                        </li>
                    </ul>

                </div>
            </div>
            <!-- Navigation / End -->
        </div>
    </div>
</div>
<!-- Dashboard Sidebar / End -->
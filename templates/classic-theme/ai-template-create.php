<?php

overall_header($ai_template['title']);


?>
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <?php
        include_once TEMPLATE_PATH . '/dashboard_sidebar.php';
        ?>
        <!-- Dashboard Content
        ================================================== -->
        <div class="dashboard-content-container" data-simplebar>
            <div class="dashboard-content-inner">
                <?php print_adsense_code('header_bottom'); ?>
                <!-- Dashboard Headline -->
                <div class="dashboard-headline">
                    <h3 class="d-flex align-items-center">
                        <?php _esc($ai_template['title']) ?>
                        <div class="word-used-wrapper margin-left-10">
                            <i class="icon-feather-bar-chart-2"></i>
                            <?php echo '<i id="quick-words-left">' .
                                _esc(number_format((float)$total_words_used), 0) . '</i> / ' .
                                ($words_limit == -1
                                    ? __('Unlimited')
                                    : _esc(number_format($words_limit), 0));
                            ?>
                            <strong><?php _e('Words Used'); ?></strong>
                        </div>
                    </h3>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs" class="dark">
                        <ul>
                            <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                            <li><a href="<?php url("AI_TEMPLATES") ?>"><?php _e("Templates") ?></a></li>
                            <li><?php _esc($ai_template['title']) ?></li>
                        </ul>
                    </nav>
                </div>

                <div class="row">
                    <!-- Dashboard Box -->
                    <div class="col-md-4">
                        <form id="ai_form" name="ai_form" method="post" action="#">
                            <div class="dashboard-box margin-top-0 margin-bottom-30">
                                <!-- Headline -->
                                <div class="headline">
                                    <h3>
                                        <i class="<?php _esc($ai_template['icon']) ?>"></i><?php _esc($ai_template['title']) ?>
                                    </h3>
                                    <?php if (!in_array($ai_template['slug'], $plan_templates)) { ?>
                                        <span class="dashboard-status-button yellow margin-bottom-0 margin-left-10"
                                              title="<?php _e("Upgrade your plan to use this template") ?>"
                                              data-tippy-placement="top"><i
                                                    class="fa fa-gift"></i> <?php _e("Pro") ?></span>
                                    <?php } ?>
                                </div>
                                <div class="content with-padding">
                                    <div class="notification small-notification notice"><?php _esc($ai_template['description']) ?></div>
                                    <?php
                                    switch ($ai_template['slug']) {
                                        case 'blog-ideas':
                                        case 'blog-titles':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("What is your blog is about?") ?><span
                                                            class="form-required">*</span></h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400"
                                                          placeholder="<?php _e("Describe your blog here...") ?>"
                                                          required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'blog-intros':
                                        case 'blog-conclusion':
                                        case 'article-outlines':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Blog Title") ?><span class="form-required">*</span></h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="200" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("What is your blog is about?") ?><span
                                                            class="form-required">*</span></h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400"
                                                          placeholder="<?php _e("Describe your blog here...") ?>"
                                                          required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'blog-section':
                                        case 'talking-points':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Blog Title") ?><span class="form-required">*</span></h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       placeholder="<?php _e("Best restaurants in Japan") ?>"
                                                       data-maxlength="200" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Subheadings") ?><span class="form-required">*</span></h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400"
                                                          placeholder="<?php _e("Subheading 1, subheading 2") ?>"
                                                          required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'article-writer':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Article Title") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       placeholder="<?php _e("Best restaurants in Japan") ?>"
                                                       data-maxlength="500" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Keywords") ?><span class="form-required">*</span></h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          placeholder="<?php _e("Keyword 1, keyword 2") ?>"
                                                          data-maxlength="200"
                                                          required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'article-rewriter':
                                        case 'content-rephrase':
                                        case 'rewrite-with-keywords':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("What would you like to rewrite?") ?><span
                                                            class="form-required">*</span></h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          placeholder="<?php _e("Enter your content to rewrite") ?>"
                                                          data-maxlength="5000"
                                                          required></textarea>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Keywords") ?><span
                                                            class="form-required">*</span></h6>
                                                <textarea name="keywords"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="200"
                                                          placeholder="<?php _e("Keyword 1, keyword 2") ?>"></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'paragraph-writer':
                                        case 'text-extender':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Description") ?><span
                                                            class="form-required">*</span></h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          placeholder="<?php _e("Describe your content here...") ?>"
                                                          data-maxlength="400"
                                                          required></textarea>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Keywords") ?></h6>
                                                <textarea name="keywords"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="200"
                                                          placeholder="<?php _e("Keyword 1, keyword 2") ?>"></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'facebook-ads':
                                        case 'facebook-ads-headlines':
                                        case 'google-ad-titles':
                                        case 'google-ad-descriptions':
                                        case 'google-ads':
                                        case 'linkedin-ad-headlines':
                                        case 'linkedin-ad-descriptions':
                                        case 'linkedin-ads':
                                        case 'product-descriptions':
                                        case 'amazon-product-titles':
                                        case 'amazon-product-descriptions':
                                        case 'amazon-product-features':
                                        case 'problem-agitate-solution':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Product name") ?><span
                                                            class="form-required">*</span></h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Audience") ?><span
                                                            class="form-required">*</span></h6>
                                                <input name="audience"
                                                       class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Product Description") ?><span
                                                            class="form-required">*</span></h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'app-and-sms-notifications':
                                        case 'pros-cons':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Description") ?><span
                                                            class="form-required">*</span></h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'stories':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Audience") ?><span
                                                            class="form-required">*</span></h6>
                                                <input name="audience"
                                                       class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Description") ?><span
                                                            class="form-required">*</span></h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'content-shorten':
                                        case 'summarize-for-2nd-grader':
                                        case 'questions':
                                        case 'passive-active-voice':
                                        case 'tone-changer':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Content") ?><span
                                                            class="form-required">*</span></h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="600" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'quora-answers':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Question") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Information") ?></h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'bullet-point-answers':
                                        case 'answers':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Question") ?><span class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="300" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'definition':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Keyword") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="keyword" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <?php
                                            break;
                                        case 'emails':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Recipient") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="recipient"
                                                       class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Recipient Position") ?><span
                                                            class="form-required">*</span>
                                                </h6>
                                                <input name="recipient-position"
                                                       class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Description") ?><span class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'emails-v2':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("From") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="from" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("To") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="to" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Goal") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="goal" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Description") ?><span class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'email-subject-lines':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Product Name") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Email Description") ?><span class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'startup-name-generator':
                                        case 'product-name-generator':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Keywords") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Description") ?><span class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'company-bios':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Company Name") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Company Information") ?><span
                                                            class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Platform") ?></h6>
                                                <select name="platform"
                                                        class="with-border small-input selectpicker" required>
                                                    <option value="website"><?php _e('Website') ?></option>
                                                    <option value="twitter"><?php _e('Twitter') ?></option>
                                                    <option value="instagram"><?php _e('Instagram') ?></option>
                                                    <option value="linkedin"><?php _e('LinkedIn') ?></option>
                                                </select>
                                            </div>
                                            <?php
                                            break;
                                        case 'company-mission':
                                        case 'company-vision':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Company Name") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Company Information") ?><span
                                                            class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'social-post-personal':
                                        case 'instagram-hashtags':
                                        case 'instagram-captions':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("What is this post about?") ?><span
                                                            class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'social-post-business':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Company Name") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Company Information") ?><span
                                                            class="form-required">*</span>
                                                </h6>
                                                <textarea name="information"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("What is this post about?") ?><span
                                                            class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'twitter-tweets':
                                        case 'linkedin-posts':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Topic") ?><span class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'youtube-titles':
                                        case 'youtube-descriptions':
                                        case 'youtube-outlines':
                                        case 'tiktok-video-scripts':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("What is your video about?") ?><span class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'meta-tags-blog':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Blog Title") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Blog Description") ?><span class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Search Term") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="keywords"
                                                       class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <?php
                                            break;
                                        case 'meta-tags-homepage':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Website Name") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Website Description") ?><span
                                                            class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Search Term/Keywords") ?><span
                                                            class="form-required">*</span>
                                                </h6>
                                                <input name="keywords"
                                                       class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <?php
                                            break;
                                        case 'meta-tags-product':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Company Name") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="company_name"
                                                       class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Product Name") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Product/Service Description") ?><span
                                                            class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Search Term") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="keywords"
                                                       class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <?php
                                            break;
                                        case 'song-lyrics':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Topic") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Genre") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="genre" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <?php
                                            break;
                                        case 'translate':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Content") ?><span class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="5000" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'faqs':
                                        case 'testimonials-reviews':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Product Name") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Product Description") ?><span
                                                            class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'faq-answers':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Product Name") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="title" class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Question") ?><span class="form-required">*</span>
                                                </h6>
                                                <input name="question"
                                                       class="with-border quick-text-counter small-input"
                                                       data-maxlength="100" required>
                                            </div>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Product Description") ?><span
                                                            class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="400" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                        case 'custom-prompt':
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _e("Question or task") ?><span class="form-required">*</span>
                                                </h6>
                                                <textarea name="description"
                                                          class="with-border quick-text-counter small-input"
                                                          data-maxlength="500" required></textarea>
                                            </div>
                                            <?php
                                            break;
                                    }

                                    if (!empty($ai_template['parameters'])) {
                                        $parameters = json_decode($ai_template['parameters'], true);
                                        foreach ($parameters as $key => $parameter) {
                                            ?>
                                            <div class="submit-field margin-bottom-20">
                                                <h6><?php _esc($parameter['title']) ?><span
                                                            class="form-required">*</span></h6>
                                                <?php if ($parameter['type'] == 'textarea') { ?>
                                                    <textarea name="parameter[<?php _esc($key) ?>]"
                                                              class="with-border small-input"
                                                              placeholder="<?php _esc($parameter['placeholder']) ?>"
                                                              required></textarea>
                                                <?php } else if($parameter['type'] == 'text') { ?>
                                                    <input name="parameter[<?php _esc($key) ?>]"
                                                           class="with-border small-input"
                                                           placeholder="<?php _esc($parameter['placeholder']) ?>"
                                                           required>
                                                <?php } else if($parameter['type'] == 'select') {
                                                    $options = explode(',', $parameter['options']);
                                                    ?>
                                                    <select name="parameter[<?php _esc($key) ?>]"
                                                            class="with-border small-input selectpicker" required>
                                                        <?php foreach ($options as $value) {
                                                            $value = trim($value);
                                                            ?>
                                                            <option value="<?php _esc($value) ?>"><?php _esc($value) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <div class="submit-field margin-bottom-20">
                                        <h6><?php _e("Language") ?></h6>
                                        <select name="language" id="language"
                                                class="with-border small-input selectpicker" data-live-search="true"
                                                required>
                                            <?php foreach ($languages as $key => $value) { ?>
                                                <option value="<?php _esc($key) ?>" <?php if ($value == get_option('ai_default_lang')) echo 'selected'; ?>><?php _esc($value) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="submit-field margin-bottom-20">
                                        <h6><?php _e("Quality type") ?></h6>
                                        <select name="quality" id="quality"
                                                class="with-border small-input selectpicker" required>
                                            <option value="0.25" <?php if ('0.25' == get_option('ai_default_quality_type')) echo 'selected'; ?>><?php _e('Economy') ?></option>
                                            <option value="0.5" <?php if ('0.5' == get_option('ai_default_quality_type')) echo 'selected'; ?>><?php _e('Average') ?></option>
                                            <option value="0.75" <?php if ('0.75' == get_option('ai_default_quality_type')) echo 'selected'; ?>><?php _e('Good') ?></option>
                                            <option value="1" <?php if ('1' == get_option('ai_default_quality_type')) echo 'selected'; ?>><?php _e('Premium') ?></option>
                                        </select>
                                    </div>
                                    <div class="submit-field margin-bottom-20">
                                        <h6><?php _e("Tone of Voice") ?>
                                            <i class="fa fa-question-circle"
                                               data-tippy-placement="top"
                                               title="<?php _e('Set the tone of the result.') ?>"></i>
                                        </h6>
                                        <select name="tone" id="tone" class="with-border small-input selectpicker"
                                                required>
                                            <option value="funny" <?php if ('funny' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Funny') ?></option>
                                            <option value="casual" <?php if ('casual' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Casual') ?></option>
                                            <option value="excited" <?php if ('excited' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Excited') ?></option>
                                            <option value="professional" <?php if ('professional' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Professional') ?></option>
                                            <option value="witty" <?php if ('witty' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Witty') ?></option>
                                            <option value="sarcastic" <?php if ('witty' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Sarcastic') ?></option>
                                            <option value="feminine" <?php if ('feminine' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Feminine') ?></option>
                                            <option value="masculine" <?php if ('masculine' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Masculine') ?></option>
                                            <option value="bold" <?php if ('bold' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Bold') ?></option>
                                            <option value="dramatic" <?php if ('dramatic' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Dramatic') ?></option>
                                            <option value="gumpy" <?php if ('gumpy' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Gumpy') ?></option>
                                            <option value="secretive" <?php if ('secretive' == get_option('ai_default_tone_voice')) echo 'selected'; ?>><?php _e('Secretive') ?></option>
                                        </select>
                                    </div>
                                    <div class="submit-field margin-bottom-20">
                                        <h6><?php _e("Number of Results") ?></h6>
                                        <select name="no_of_results" id="results"
                                                class="with-border small-input selectpicker"
                                                required>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    </div>
                                    <div class="submit-field margin-bottom-20">
                                        <h6><?php _e("Max Results Length") ?>
                                            <i class="fa fa-question-circle"
                                               data-tippy-placement="top"
                                               title="<?php _e('Maximum words for each result.') ?>"></i>
                                        </h6>
                                        <input name="max_results" type="number" class="with-border small-input"
                                               value="<?php _esc(get_option('ai_default_max_langth')) ?>" min="50">
                                    </div>
                                    <div>
                                        <small class="form-error"></small>
                                        <?php if (!in_array($ai_template['slug'], $plan_templates)) { ?>
                                            <div class="notification small-notification error"><?php _e("Upgrade your plan to use this template") ?></div>
                                        <?php } else { ?>
                                            <button type="submit" name="submit"
                                                    class="button ripple-effect full-width"><?php _e("Generate") ?>
                                                <i class="icon-feather-arrow-right"></i></button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="ai_template" value="<?php _esc($ai_template['slug']); ?>">
                        </form>
                    </div>
                    <div class="col-md-8">
                        <div class="dashboard-box margin-top-0 margin-bottom-30">
                            <!-- Headline -->
                            <div class="headline">
                                <h3><i class="fa fa-align-left"></i><?php _e("Generated Result") ?></h3>
                                <div class="margin-left-auto line-height-1">
                                    <a href="#" class="button ripple-effect btn-sm" id="export_to_word"
                                       data-tippy-placement="top"
                                       title="<?php _e("Export as Word Document") ?>"><i class="fa fa-file-word-o"></i></a>
                                    <a href="#" class="button ripple-effect btn-sm" id="export_to_txt"
                                       title="<?php _e("Export as Text File") ?>"
                                       data-tippy-placement="top"><i class="fa fa-file-text-o"></i></a>
                                    <a href="#" class="button ripple-effect btn-sm" id="copy_text"
                                       title="<?php _e("Copy Text") ?>"
                                       data-tippy-placement="top"><i class="fa fa-copy"></i></a>
                                </div>
                            </div>
                            <div class="content with-padding">
                                <form id="ai_document_form" name="ai_document_form" method="post" action="#">
                                    <input type="hidden" name="id" id="post_id" value="">
                                    <input type="hidden" name="ai_template"
                                           value="<?php _esc($ai_template['slug']); ?>">
                                    <div class="d-flex margin-bottom-10">
                                        <input name="title" type="text" class="with-border small-input"
                                               value="<?php _e("Untitled Document") ?>" required>
                                        <button class="button btn-sm margin-left-5 ripple-effect"
                                                name="submit"
                                                type="submit"
                                                title="<?php _e("Save Document") ?>"
                                                data-tippy-placement="top"><i class="icon-feather-save"></i></button>
                                    </div>
                                    <small class="form-error margin-bottom-10"></small>
                                    <textarea name="content" class="tiny-editor"></textarea>
                                </form>
                            </div>
                            <div id="content-focus"></div>
                        </div>
                    </div>
                </div>
                <?php print_adsense_code('footer_top'); ?>
                <!-- Footer -->
                <div class="dashboard-footer-spacer"></div>
                <div class="small-footer margin-top-15">
                    <div class="footer-copyright">
                        <?php _esc($config['copyright_text']); ?>
                    </div>
                    <ul class="footer-social-links">
                        <?php
                        if ($config['facebook_link'] != "")
                            echo '<li><a href="' . _esc($config['facebook_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-facebook"></i></a></li>';
                        if ($config['twitter_link'] != "")
                            echo '<li><a href="' . _esc($config['twitter_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-twitter"></i></a></li>';
                        if ($config['instagram_link'] != "")
                            echo '<li><a href="' . _esc($config['instagram_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-instagram"></i></a></li>';
                        if ($config['linkedin_link'] != "")
                            echo '<li><a href="' . _esc($config['linkedin_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-linkedin"></i></a></li>';
                        if ($config['pinterest_link'] != "")
                            echo '<li><a href="' . _esc($config['pinterest_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-pinterest"></i></a></li>';
                        if ($config['youtube_link'] != "")
                            echo '<li><a href="' . _esc($config['youtube_link'], false) . '" target="_blank" rel="nofollow"><i class="fa fa-youtube"></i></a></li>';
                        ?>
                    </ul>
                    <div class="clearfix"></div>
                </div>

            </div>
        </div>
    </div>
<?php ob_start() ?>
    <script src="<?php _esc(TEMPLATE_URL); ?>/js/jquery-simple-txt-counter.min.js"></script>
    <script src="<?php _esc($config['site_url'] . $config['admin_folder']); ?>/assets/plugins/tinymce/tinymce.min.js"></script>
    <script>
        // text counter
        $('.quick-text-counter').each(function () {
            var $this = $(this);

            $this.simpleTxtCounter({
                maxLength: $this.data('maxlength'),
                countElem: '<div class="form-text"></div>',
                lineBreak: false,
            });
        });

        // tinymce
        tinymce.init({
            selector: '.tiny-editor',
            min_height: 500,
            resize: true,
            plugins: 'advlist lists table autolink link wordcount fullscreen autoresize',
            toolbar: [
                "blocks | bold italic underline strikethrough | alignleft aligncenter alignright  | link blockquote",
                "undo redo | removeformat | table | bullist numlist | outdent indent"
            ],
            menubar: "",
            // link
            relative_urls: false,
            link_assume_external_targets: true,
            content_style: 'body { font-size:14px }'
        });
    </script>
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';
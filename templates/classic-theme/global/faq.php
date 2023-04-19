<?php
overall_header(__("Frequently Asked Questions"));
?>
<?php print_adsense_code('header_bottom'); ?>
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php _e("Frequently Asked Questions") ?></h2>
                <span><?php _e("Got Questions? We've Got Answers!") ?></span>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><?php _e("FAQ") ?></li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
    <div class="container">
        <div class="margin-bottom-50">

            <!-- Accordion -->
            <div class="accordion js-accordion">

                <?php
                $i = 0;
                foreach($faq as $qa){ ?>
                    <!-- Accordion Item -->
                    <div class="accordion__item js-accordion-item <?php if($i==0){ ?> active <?php } ?>">
                        <div class="accordion-header js-accordion-header"><?php _esc($qa['title']) ?></div>

                        <!-- Accordtion Body -->
                        <div class="accordion-body js-accordion-body">

                            <!-- Accordion Content -->
                            <div class="accordion-body__contents">
                                <?php _esc($qa['content']) ?>
                            </div>

                        </div>
                        <!-- Accordion Body / End -->
                    </div>
                    <!-- Accordion Item / End -->
                <?php $i++; } ?>

            </div>
            <!-- Accordion / End -->
        </div>
        <!-- faq-page -->
    </div>
<?php
overall_footer();
?>

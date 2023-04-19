<?php
overall_header(__("Dashboard"));
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
                <h3><?php _e("Dashboard") ?></h3>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><?php _e("Dashboard") ?></li>
                    </ul>
                </nav>
            </div>

            <!-- Fun Facts Container -->
            <div class="fun-facts-container">
                <div class="fun-fact" data-fun-fact-color="#b81b7f">
                    <div class="fun-fact-text">
                        <span><?php _e("Words Used"); ?></span>
                        <h4>
                            <?php _esc(number_format($total_words_used)); ?>
                            <small>/ <?php _esc(
                                    $membership_settings['ai_words_limit'] == -1
                                        ? __('Unlimited')
                                        : number_format($membership_settings['ai_words_limit'])
                                ); ?></small>
                        </h4>
                    </div>
                    <div class="fun-fact-icon"><i class="icon-feather-trending-up"></i></div>
                </div>
                <div class="fun-fact" data-fun-fact-color="#36bd78">
                    <div class="fun-fact-text">
                        <span><?php _e("Images Used"); ?></span>
                        <h4>
                            <?php _esc(number_format($total_images_used)); ?>
                            <small>/ <?php _esc(
                                    $membership_settings['ai_images_limit'] == -1
                                        ? __('Unlimited')
                                        : number_format($membership_settings['ai_images_limit'])
                                ); ?></small>
                        </h4>
                    </div>
                    <div class="fun-fact-icon"><i class="icon-feather-bar-chart-2"></i></div>
                </div>
                <div class="fun-fact" data-fun-fact-color="#efa80f">
                    <div class="fun-fact-text">
                        <span><?php _e("Speech to Text"); ?></span>
                        <h4>
                            <?php _esc(number_format($total_speech_used)); ?>
                            <small>/ <?php _esc(
                                    $membership_settings['ai_speech_to_text_file_limit'] == -1
                                        ? __('Unlimited')
                                        : number_format($membership_settings['ai_speech_to_text_file_limit'])
                                ); ?></small>
                        </h4>
                    </div>
                    <div class="fun-fact-icon"><i class="icon-feather-headphones"></i></div>
                </div>
            </div>

            <!-- Dashboard Box -->
            <div class="dashboard-box main-box-in-row">
                <div class="headline">
                    <h3><i class="icon-feather-bar-chart-2"></i> <?php _e("Word used this month"); ?></h3>
                </div>
                <div class="content">
                    <!-- Chart -->
                    <div class="chart">
                        <canvas id="chart" width="100" height="45"></canvas>
                    </div>
                </div>
            </div>
            <!-- Dashboard Box / End -->
            <?php print_adsense_code('footer_top'); ?>
            <!-- Footer -->
            <div class="dashboard-footer-spacer"></div>
            <div class="small-footer margin-top-15">
                <div class="footer-copyright">
                    <?php _esc($config['copyright_text']); ?>
                </div>
                <ul class="footer-social-links">
                    <?php
                    if($config['facebook_link'] != "")
                        echo '<li><a href="'._esc($config['facebook_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-facebook"></i></a></li>';
                    if($config['twitter_link'] != "")
                        echo '<li><a href="'._esc($config['twitter_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-twitter"></i></a></li>';
                    if($config['instagram_link'] != "")
                        echo '<li><a href="'._esc($config['instagram_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-instagram"></i></a></li>';
                    if($config['linkedin_link'] != "")
                        echo '<li><a href="'._esc($config['linkedin_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-linkedin"></i></a></li>';
                    if($config['pinterest_link'] != "")
                        echo '<li><a href="'._esc($config['pinterest_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-pinterest"></i></a></li>';
                    if($config['youtube_link'] != "")
                        echo '<li><a href="'._esc($config['youtube_link'],false).'" target="_blank" rel="nofollow"><i class="fa fa-youtube"></i></a></li>';
                    ?>
                </ul>
                <div class="clearfix"></div>
            </div>

        </div>
    </div>
</div>
<?php ob_start() ?>
<script src="<?php _esc(TEMPLATE_URL); ?>/js/chart.min.js"></script>
<script>
    Chart.defaults.global.defaultFontFamily = "Nunito";
    Chart.defaults.global.defaultFontColor = '#888';
    Chart.defaults.global.defaultFontSize = '14';

    var ctx = document.getElementById('chart').getContext('2d');

    var chart = new Chart(ctx, {
        type: 'line',

        // The data for our dataset
        data: {
            labels: <?php _esc($days);?>,
            // Information about the dataset
            datasets: [{
                label: "<?php _e('Words Used');?>",
                backgroundColor: '<?php _esc($config['theme_color']);?>15',
                borderColor: '<?php _esc($config['theme_color']);?>',
                borderWidth: "3",
                data: <?php _esc($word_used);?>,
                pointRadius: 5,
                pointHoverRadius: 5,
                pointHitRadius: 10,
                pointBackgroundColor: "#fff",
                pointHoverBackgroundColor: "#fff",
                pointBorderWidth: "2",
            }]
        },

        // Configuration options
        options: {
            layout: {
                padding: 10,
            },
            legend: {display: false},
            title: {display: false},
            scales: {
                yAxes: [{
                    scaleLabel: {
                        display: false
                    },
                    gridLines: {
                        borderDash: [6, 10],
                        color: "#d8d8d8",
                        lineWidth: 1,
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }],
                xAxes: [{
                    scaleLabel: {display: false},
                    gridLines: {display: false},
                }],
            },
            tooltips: {
                backgroundColor: '#333',
                titleFontSize: 13,
                titleFontColor: '#fff',
                bodyFontColor: '#fff',
                bodyFontSize: 13,
                displayColors: false,
                xPadding: 10,
                yPadding: 10,
                intersect: false
            }
        },
    });

</script>

<!-- Footer / End -->
<?php
$footer_content = ob_get_clean();
include_once TEMPLATE_PATH . '/overall_footer_dashboard.php';
?>





<?php
overall_header(__("Countries"));
?>
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php _e("Countries") ?></h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="<?php url("INDEX") ?>"><?php _e("Home") ?></a></li>
                        <li><?php _e("Countries") ?></li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container margin-bottom-50">
    <div class="row">
        <?php
        foreach($countrylist as $countries){
            _esc($countries['tpl']);
        } ?>
    </div>
</div>
<script>
    $('#getCountry').on('click', 'ul li a', function (e) {
        e.stopPropagation();
        e.preventDefault();

        localStorage.Quick_placeText = "";
        localStorage.Quick_PlaceId = "";
        localStorage.Quick_PlaceType = "";
        var url = $(this).attr('href');
        window.location.href = url;
    });
</script>
<?php
overall_footer();
?>

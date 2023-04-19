<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php _e("Invoice") ?> - <?php _esc($config['site_title']) ?></title>
    <style>
        :root{--theme-color-1: <?php _esc($config['theme_color']) ?>;}
    </style>
    <link rel="stylesheet" href="<?php _esc(TEMPLATE_URL);?>/css/invoice.css">
</head>
<body>

<!-- Print Button -->
<div class="print-button-container">
    <a href="javascript:window.print()" class="print-button"><?php _e("Print this invoice") ?></a>
</div>

<!-- Invoice -->
<div id="invoice">
    <!-- Header -->
    <div class="row">
        <div class="col-xl-6">
            <div id="logo"><img src="<?php _esc($config['site_url']) ?>storage/logo/<?php _esc($config['site_logo']) ?>" alt="<?php _esc($config['site_title']) ?>"></div>
        </div>
        <div class="col-xl-6">
            <p id="details">
                <strong><?php _e("Invoice") ?>:</strong> <?php _esc($config['invoice_nr_prefix']); _esc($invoice_id); ?> <br>
                <strong><?php _e("Date") ?>:</strong> <?php _esc($invoice_date) ?>
            </p>
        </div>
    </div>


    <!-- Client & Supplier -->
    <div class="row">
        <div class="col-xl-12">
            <h2><?php _e("Invoice") ?></h2>
        </div>
        <div class="col-md-6">
            <h3 class="margin-bottom-5"><?php _e("Supplier") ?></h3>
            <p>
                <?php
                if(!empty($config['invoice_admin_name']))
                    echo '<strong>'.__("Name").'</strong> '._esc($config['invoice_admin_name'],false).'<br>';
                if(!empty($config['invoice_admin_address']))
                    echo '<strong>'.__("Address").'</strong> '._esc($config['invoice_admin_address'],false).'<br>';
                if(!empty($config['invoice_admin_city']))
                    echo '<strong>'.__("City").'</strong> '._esc($config['invoice_admin_city'],false).'<br>';
                if(!empty($config['invoice_admin_state']))
                    echo '<strong>'.__("State").'</strong> '._esc($config['invoice_admin_state'],false).'<br>';
                if(!empty($config['invoice_admin_country']))
                    echo '<strong>'.__("Country").'</strong> '._esc($config['invoice_admin_country'],false).'<br>';
                if(!empty($config['invoice_admin_zipcode']))
                    echo '<strong>'.__("Zip code").'</strong> '._esc($config['invoice_admin_zipcode'],false).'<br>';

                if(!empty($config['invoice_admin_tax_type']) && !empty($config['invoice_admin_tax_id']))
                    echo '<strong>'._esc($config['invoice_admin_tax_type'],false).'</strong> '._esc($config['invoice_admin_tax_id'],false).'<br>';

                if(!empty($config['invoice_admin_custom_name_1']) && !empty($config['invoice_admin_custom_value_1']))
                    echo '<strong>'._esc($config['invoice_admin_custom_name_1'],false).'</strong> '._esc($config['invoice_admin_custom_value_1'],false).'<br>';

                if(!empty($config['invoice_admin_custom_name_2']) && !empty($config['invoice_admin_custom_value_2']))
                    echo '<strong>'._esc($config['invoice_admin_custom_name_2'],false).'</strong> '._esc($config['invoice_admin_custom_value_2'],false).'<br>';

                ?>
            </p>
        </div>
        <div class="col-md-6">
            <h3 class="margin-bottom-5"><?php _e("Customer") ?></h3>
            <p>
                <?php
                if(!empty($billing_name))
                    echo '<strong>'.__("Name").'</strong> '._esc($billing_name,false).'<br>';
                if(!empty($billing_address))
                    echo '<strong>'.__("Address").'</strong> '._esc($billing_address,false).'<br>';
                if(!empty($billing_city))
                    echo '<strong>'.__("City").'</strong> '._esc($billing_city,false).'<br>';
                if(!empty($billing_state))
                    echo '<strong>'.__("State").'</strong> '._esc($billing_state,false).'<br>';
                if(!empty($billing_country))
                    echo '<strong>'.__("Country").'</strong> '._esc($billing_country,false).'<br>';
                if(!empty($billing_zipcode))
                    echo '<strong>'.__("Zip code").'</strong> '._esc($billing_zipcode,false).'<br>';
                if($billing_details_type != "business" && !empty($billing_tax_id)){
                    if(!empty($config['invoice_admin_tax_type'])){
                        $taxid = $config['invoice_admin_tax_type'];
                    }else{
                        $taxid = __("Tax ID");
                    }
                    echo '<strong>'.$taxid.'</strong> '._esc($billing_tax_id,false).'<br>';
                }
                ?>
            </p>
        </div>
    </div>
    <!-- Invoice -->
    <div class="row">
        <div class="col-xl-12">
            <table class="margin-top-20">
                <tr>
                    <th><?php _e("Item") ?></th>
                    <th><?php _e("Amount") ?></th>
                </tr>
                <tr>
                    <td><?php _esc($item_name) ?></td>
                    <td><?php _esc($item_amount) ?></td>
                </tr>
                <?php foreach($taxes as $tax){ ?>
                <tr>
                    <td><?php _esc($tax['name']) ?><br><small><?php _esc($tax['description']) ?></small></td>
                    <td><?php _esc($tax['value_formatted']) ?></td>
                </tr>
                <?php } ?>
            </table>
            <table id="totals">
                <tr>
                    <th><?php _e("Total") ?><br><small><?php _e("Paid via");?> <?php _esc($paid_via) ?></small></th>
                    <th><span><?php _esc($total_amount) ?></span></th>
                </tr>
            </table>
        </div>
    </div>
    <!-- Footer -->
    <div class="row">
        <div class="col-xl-12">
            <ul id="footer">
                <li><?php _esc($config['site_url']) ?></span></li>
                <li><?php _esc($config['invoice_admin_email']) ?></li>
                <li><?php _esc($config['invoice_admin_phone']) ?></li>
            </ul>
        </div>
    </div>
</div>
</html>
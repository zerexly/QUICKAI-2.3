<?php
overall_header(__("Bank Deposit"));
?>
<style>
    .quickad-template{
        margin: 20px;
        font-family: Roboto,"Helvetica Neue",Helvetica,Arial,sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    .PaymentMethod-content-inner {
        position: relative;
        padding: 16px 24px 24px;
        border-radius: 0 0 3px 3px;
        background-color: #fff;
    }
    .quickad-template .default-table {
        width: 100%!important;
        border: none;
        border-collapse: collapse;
    }
    .PaymentMethod-infoTable {
        margin-bottom: 24px;
    }

    .quickad-template .default-table tbody {
        border: none;
        border-bottom: 1px solid #DEDEDE;
    }
    .quickad-template .default-table.table-alt-row tr:nth-child(even) {
        background-color: #F0F0F0;
    }
    .quickad-template .default-table tbody tr {
        border-top: 1px solid #DEDEDE;
        border-left: 1px solid #DEDEDE;
        border-right: 1px solid #DEDEDE;
        -webkit-transition: all .2s ease-out;
        transition: all .2s ease-out;
    }
    .quickad-template .default-table tbody tr:hover {
        -webkit-transition: all .2s ease-out;
        transition: all .2s ease-out;
        background-color: #dbf4ff!important;
        border: 1px solid #75d5ff!important;
    }
    .quickad-template .default-table tbody td {
        vertical-align: top;
    }
    .quickad-template .default-table td, .quickad-template .default-table th {
        padding: 13px;
    }
    .PaymentMethod-heading {
        font-size: 14px;
        line-height: 1.43;
        margin-bottom: 4px;
        color: #1f2836;
        font-weight: bold;
    }
    .PaymentMethod-label {
        border-radius: 3px 3px 0 0;
        font-size: 20px;
        font-weight: 700;
        color: #F7F7F7;
        background-color: #000;
        padding: 15px;
    }
    .PaymentMethod-info{font-size: 14px;
        line-height: 1.4;
        color: #1f2836;}
    .PaymentMethod-info b{font-weight: 600;}
</style>

<div class="container">
    <div class="quickad-template">
        <div class="PaymentMethod-label">
            <span><i class="fa fa-university" aria-hidden="true"></i> <?php _e("Bank Deposit") ?></span>
        </div>
        <div class="PaymentMethod-content-inner">
            <div class="alert alert-success notification success">
                <?php _e("We have received your offline payment request. We will wait to receive your payment to process your request.") ?>
            </div>
            <table class="default-table table-alt-row PaymentMethod-infoTable">
                <tbody>
                <tr>
                    <td>
                        <h5 class="PaymentMethod-heading"><?php _e("Bank Account details") ?></h5>
                        <span class="PaymentMethod-info"><?php _esc($bank_info) ?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h5 class="PaymentMethod-heading"><?php _e("Reference") ?></h5>
                        <span class="PaymentMethod-info">
                            <?php _e("Order") ?>: <?php _esc($order_title) ?><br>
                            <?php _e("Username") ?>: <?php _esc($username) ?><br><br>
                            <?php _e("Include a note with Reference so that we know which account to credit.") ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h5 class="PaymentMethod-heading"><?php _e("Amount to send") ?></h5>
                        <span class="PaymentMethod-info"><?php _esc($amount) ?></span>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="text-right"><a href="<?php url("TRANSACTION") ?>" class="button btn btn-primary"><?php _e("Transactions") ?></a></div>
        </div>
    </div>
</div>
<?php
overall_footer();
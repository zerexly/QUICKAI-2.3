<?php
require_once('includes.php');

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
if($params['draw'] == 1)
    $params['order'][0]['dir'] = "desc";

//define index of column
$columns = array(
    't.product_id',
    'u.username',
    't.amount',
    't.featured',
    't.transaction_gatway',
    't.status',
    't.transaction_time'
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ) {
    $where .=" WHERE ";
    $where .=" ( t.amount LIKE '".$params['search']['value']."%' ";
    $where .=" OR t.transaction_gatway LIKE '".$params['search']['value']."%' ";
    $where .=" OR u.username LIKE '".$params['search']['value']."%' ";
    $where .=" OR t.status LIKE '".$params['search']['value']."%' )";
}

// getting total number records without any search
$sql = "SELECT t.*, u.username as username FROM `".$config['db']['pre']."transaction` as t
INNER JOIN `".$config['db']['pre']."user` as u ON u.id = t.seller_id ";
$sqlTot .= $sql;
$sqlRec .= $sql;
//concatenate search sql if value exist
if(isset($where) && $where != '') {

    $sqlTot .= $where;
    $sqlRec .= $where;
}


$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]." ".$params['order'][0]['dir']." LIMIT ".$params['start']." ,".$params['length']." ";

$queryTot = $pdo->query($sqlTot);
$totalRecords = $queryTot->rowCount();
$queryRecords = $pdo->query($sqlRec);

//iterate on results row and create new index array of data
foreach ($queryRecords as $row) {

    $id = $row['id'];
    $username = $row['username'];
    $post_id = $row['product_id'];
    $post_title = $row['product_name'];
    $amount = $row['amount'];
    $payment_method = $row['transaction_gatway'];
    $featured = $row['featured'];
    $urgent = $row['urgent'];
    $highlight = $row['highlight'];
    $t_status = $row['status'];
    $transaction_time = date('d M Y', $row['transaction_time']);
    $tans_link = '';
    $premium = '';
    if($row['transaction_method'] == 'Subscription'){

        $premium = '<span class="badge badge-secondary">'.__("Membership").'</span>';
        $trans_link = '#';
    }else{
        $trans_link = '#';
        $featured = $row['featured'];
        $urgent = $row['urgent'];
        $highlight = $row['highlight'];


        if ($featured == "1") {
            $premium = $premium . '<span class="badge badge-warning">'.__("Featured").'</span>';
        }

        if ($urgent == "1") {
            $premium = $premium . '<span class="badge badge-success">'.__("Urgent").'</span>';
        }

        if ($highlight == "1") {
            $premium = $premium . '<span class="badge badge-info">'.__("Highlight").'</span>';
        }
    }

    $status = $invoice = '';
    if ($t_status == "success"){
        $status = '<span class="badge badge-success">'.__("Success").'</span>';
        $invoice = '<a href="'.$config['site_url'].'invoice/'.$id.'" target="_blank" class="btn-icon mr-1 btn-primary" title="'.__('Invoice').'" data-tippy-placement="top"><i class="icon-feather-file-text"></i></a>';
    }
    elseif($t_status == "pending") {
        $status = '<span class="badge badge-warning">'.__("Pending").'</span>';
    }
    elseif($t_status == "failed") {
        $status = '<span class="badge badge-danger">'.__("Failed").'</span>';
    }else{
        $status = '<span class="badge badge-danger">'.__("Cancelled").'</span>';
    }

    $row0 = '<td><a href="'.$trans_link.'">'.$post_title.'</a></td>';
    $row1 = '<td>'.$username.'</td>';
    $row2 = '<td>'.price_format($amount).'</td>';
    $row3 = '<td>'.$premium.'</td>';
    $row4 = '<td>'.$status.'</td>';
    $row5 = '<td>'.$payment_method.'</td>';
    $row6 = '<td>'.$transaction_time.'</td>';
    $row7 = '<td class="text-center">
                <div class="btn-group">
                '.$invoice.'
                    <a href="#" data-url="panel/transaction_edit.php?id='.$id.'" data-toggle="slidePanel" title="'.__('Edit').'" class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                    <a href="#" class="btn-icon btn-danger item-js-delete" title="'.__('Delete').'" data-tippy-placement="top" data-ajax-action="deleteTransaction"><i class="icon-feather-trash-2"></i></a>
                </div>
            </td>';
    $row8 = '<td>
                <div class="checkbox">
                    <input type="checkbox" id="check_'.$id.'" value="'.$id.'" class="quick-check">
                    <label for="check_'.$id.'"><span class="checkbox-icon"></span></label>
                </div>
            </td>';

    $value = array(
        "DT_RowId" => $id,
        0 => $row0,
        1 => $row1,
        2 => $row2,
        3 => $row3,
        4 => $row4,
        5 => $row5,
        6 => $row6,
        7 => $row7,
        8 => $row8
    );
    $data[] = $value;
}

$json_data = array(
    "draw"            => intval( $params['draw'] ),
    "recordsTotal"    => intval( $totalRecords ),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data
);

echo json_encode($json_data);
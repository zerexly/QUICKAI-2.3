<?php
include '../../global/datatable-json/includes.php';

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
if ($params['draw'] == 1)
    $params['order'][0]['dir'] = "desc";
//define index of column
$columns = array(
    'username',
    'w.amount',
    'payment_title',
    'payment_details',
    'w.status',
    'w.created_at',
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .=" ( w.status LIKE '%".$params['search']['value']."%' ";
    $where .=" OR u.username LIKE '%".$params['search']['value']."%' ";
    $where .=" OR p.payment_title LIKE '%".$params['search']['value']."%'  ) ";
}

// getting total number records without any search
$sql = "SELECT w.*, u.username as username, p.payment_title as payment_title
FROM `".$config['db']['pre']."withdrawal` as w
INNER JOIN `".$config['db']['pre']."user` as u ON u.id = w.user_id 
INNER JOIN `".$config['db']['pre']."payments` as p ON p.payment_id = w.payment_method_id ";

$sqlTot .= $sql;
$sqlRec .= $sql;
//concatenate search sql if value exist
if (isset($where) && $where != '') {
    $sqlTot .= $where;
    $sqlRec .= $where;
}

$sqlRec .= " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . $params['order'][0]['dir'] . "  LIMIT " . $params['start'] . " ," . $params['length'] . " ";

$queryTot = $pdo->query($sqlTot);
$totalRecords = $queryTot->rowCount();
$queryRecords = $pdo->query($sqlRec);

//iterate on results row and create new index array of data
foreach ($queryRecords as $row) {
    //$data[] = $row;
    $id = $row['id'];
    $username = escape($row['username']);
    $amount = price_format($row['amount']);
    $payment_title = escape($row['payment_title']);
    $account_details = nl2br(escape($row['account_details']));
    $created_at  = date('d M Y h:i A', strtotime($row['created_at']));

    $t_status = $row['status'];
    $status = '';
    if ($t_status == "success") {
        $status = '<span class="badge badge-primary">'.__("Paid").'</span>';
    } elseif ($t_status == "pending") {
        $status = '<span class="badge badge-warning">'.__("Pending").'</span>';
    } else{
        $status = '<span class="badge badge-danger">'.__("Reject").'</span>';
    }

    $rows = array();
    $rows[] = '<td>' . $username . '</td>';
    $rows[] = '<td>' . $amount . '</td>';
    $rows[] = '<td>' . $payment_title . '</td>';
    $rows[] = '<td>' . $account_details . '</td>';
    $rows[] = '<td>' . $status . '</td>';
    $rows[] = '<td>' . $created_at . '</td>';
    $rows[] = '<td class="text-center">
                <div class="btn-group">
                <a href="#" title="' . __('Edit') . '" data-url="panel/withdrawals.php?id=' . $id . '" data-toggle="slidePanel"  class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                </div>
            </td>';
    $rows[] = '<td>
                <div class="checkbox">
                <input type="checkbox" id="check_' . $id . '" value="' . $id . '" class="quick-check">
                <label for="check_' . $id . '"><span class="checkbox-icon"></span></label>
            </div>
            </td>';
    $rows['DT_RowId'] = $id;
    $data[] = $rows;
}

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data   // total data array
);

echo json_encode($json_data);

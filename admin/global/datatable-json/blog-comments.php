<?php
require_once('includes.php');

// initilize all variable
$params = $columns = $order = $totalRecords = $data = array();
$params = $_REQUEST;
if ($params['order'][0]['column'] == 0) {
    $params['order'][0]['dir'] = "desc";
}
//define index of column
$columns = array(
    0 => 'created_at',
    1 => 'name',
    2 => 'comment',
    3 => 'blog_id',
    4 => 'created_at',
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if (!empty($params['search']['value'])) {
    if (isset($_GET['status'])) {
        $where .= " WHERE ";
        $where .= " comment LIKE '%" . $params['search']['value'] . "%' ";
        $where .= " OR name LIKE '" . $params['search']['value'] . "%' ";
        $where .= " OR email LIKE '" . $params['search']['value'] . "%' ";
    } elseif (isset($_GET['hide'])) {
        $where .= " WHERE ";
        $where .= " comment LIKE '%" . $params['search']['value'] . "%' ";
        $where .= " OR name LIKE '" . $params['search']['value'] . "%' ";
        $where .= " OR email LIKE '" . $params['search']['value'] . "%' ";
    } else {
        $where .= " WHERE ";
        $where .= " comment LIKE '%" . $params['search']['value'] . "%' ";
        $where .= " OR name LIKE '" . $params['search']['value'] . "%' ";
        $where .= " OR email LIKE '" . $params['search']['value'] . "%' ";
    }
}


// getting total number records without any search
$sql = "SELECT * FROM `" . $config['db']['pre'] . "blog_comment`";
$sqlTot .= $sql;
$sqlRec .= $sql;
//concatenate search sql if value exist
if (isset($where) && $where != '') {
    $sqlTot .= $where;
    $sqlRec .= $where;
} else {
    if (isset($_GET['status'])) {
        $where .= " Where ( b.status = '" . $_GET['status'] . "' )";
        $sqlTot .= $where;
        $sqlRec .= $where;
    }
}

$sqlRec .= " ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . " LIMIT " . $params['start'] . " ," . $params['length'] . " ";


$queryTot = $pdo->query($sqlTot);
$totalRecords = $queryTot->rowCount();
$queryRecords = $pdo->query($sqlRec);

//iterate on results row and create new index array of data
foreach ($queryRecords as $row) {
    $id = $row['id'];
    $name = $row['name'];
    $email = $row['email'];
    $cmnt = $row['comment'];
    $status = '';
    if ($row['active'] == "0") {
        $status = '<span class="label label-warning">'.__("Unapproved").'</span>';
    } elseif ($row['active'] == "1") {
        $status = '<span class="label label-success">'.__("Approved").'</span>';
    }
    $info = ORM::for_table($config['db']['pre'] . 'blog')->find_one($row['blog_id']);

    $rows = array();

    $rows[] = '<td class="text-center">
                <p class="font-500">' . $name . '</p>
                <small>' . $email . '</small>
            </td>';
    $rows[] = '<td>' . $cmnt . '</td>';
    $rows[] = '<td><a href="' . $config['site_url'] . 'blog/' . $info['id'] . '" target="_blank">' . $info['title'] . '</a></td>';
    $rows[] = '<td>' . $status . '</td>';
    $rows[] = '<td>' . date('d, M Y H:i:s', strtotime($row['created_at'])) . '</td>';
    $rows[] = '<td>
                <div class="btn-group">'.
        ($row['active'] == "0"?'<a href="#" class="btn-icon btn-primary mr-1 item-ajax-button" data-ajax-action="approveComment" title="'.__('Approve').'" class="btn-icon mr-1" data-tippy-placement="top" data-alert-message="'.__('Are you sure?').'"><i class="icon-feather-check"></i></a>':'').'
                   
                    <a href="#" class="btn-icon btn-danger item-js-delete" title="'.__('Delete').'" data-tippy-placement="top" data-ajax-action="deleteComment"><i class="icon-feather-trash-2"></i></a>
                </div>
            </td>';
    $rows[] = '<td>
                <div class="checkbox">
                <input type="checkbox" id="check_'.$id.'" value="'.$id.'" class="quick-check">
                <label for="check_'.$id.'"><span class="checkbox-icon"></span></label>
            </div>
            </td>';

    $rows['DT_RowId'] = $id;
    $data[] = $rows;
}

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data
);
echo json_encode($json_data);
?>

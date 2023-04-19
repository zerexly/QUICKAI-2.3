<?php
require_once('includes.php');

// initilize all variable
$params = $columns = $order = $totalRecords = $data = array();
$params = $_REQUEST;

//define index of column
$columns = array(
    'id',
    'email',
    'joined',
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ) {
    $where .=" WHERE ";
    $where .=" ( id LIKE '".$params['search']['value']."%' ";
    $where .=" OR email LIKE '%".$params['search']['value']."%' ";
    $where .=" OR joined LIKE '%".$params['search']['value']."%' ) ";
}

// getting total number records without any search
$sql = "SELECT * FROM `".$config['db']['pre']."subscriber` ";
$sqlTot .= $sql;
$sqlRec .= $sql;
//concatenate search sql if value exist
if(isset($where) && $where != '') {
    $sqlTot .= $where;
    $sqlRec .= $where;
}


$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";

$queryTot = $pdo->query($sqlTot);
$totalRecords = $queryTot->rowCount();
$queryRecords = $pdo->query($sqlRec);

//iterate on results row and create new index array of data
foreach ($queryRecords as $row) {
    //$data[] = $row;
    $id = $row['id'];
    $email = $row['email'];
    $joined = date('d, M Y', strtotime($row['joined']));

    $rows = array();
    $rows[] = '<td>'.$id.'</td>';
    $rows[] = '<td>'.$email.'</td>';
    $rows[] = '<td>'.$joined.'</td>';
    $rows[] = '<td>
                <div class="btn-group">
                <a href="#" data-url="panel/subscriber.php?id='.$id.'" data-toggle="slidePanel" title="'.__('Edit').'" class="btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
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
    "draw"            => intval( $params['draw'] ),
    "recordsTotal"    => intval( $totalRecords ),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format
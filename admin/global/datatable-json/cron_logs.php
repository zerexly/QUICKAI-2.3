<?php
require_once('includes.php');

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
if($params['draw'] == 1)
    $params['order'][0]['dir'] = "desc";
//define index of column
$columns = array(
    'log_id',
    'log_date',
    'log_summary'
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ) {
    $where .=" WHERE ";
    $where .=" ( log_summary LIKE '".$params['search']['value']."%' )";
}

// getting total number records without any search
$sql = "SELECT * FROM `".$config['db']['pre']."logs` ";
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
    $id = $row['log_id'];
    $log_date = date("Y-m-d H:i:s",$row['log_date']);
    $log_summary = stripslashes($row['log_summary']);
    $log_details = stripslashes($row['log_details']);

    $row0 = '<td>'.$log_summary.'</td>';
    $row1 = '<td>'.$log_date.'</td>';
    $row2 = '<td>'.$log_details.'</td>';

    $value = array(
        "DT_RowId" => $id,
        0 => $row0,
        1 => $row1,
        2 => $row2,
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

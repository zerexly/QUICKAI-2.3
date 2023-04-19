<?php
require_once('includes.php');

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;

//define index of column
$columns = array(
    'code',
    'name',
    'html_entity',
    'in_left'
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ) {
    $where .=" WHERE ";
    $where .=" ( code LIKE '".$params['search']['value']."%' ";
    $where .=" OR name LIKE '".$params['search']['value']."%' ";
    $where .=" OR html_entity LIKE '".$params['search']['value']."%' ";
    $where .=" OR in_left LIKE '".$params['search']['value']."%' )";
}

// getting total number records without any search
$sql = "SELECT * FROM `".$config['db']['pre']."currencies` ";
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
    
    $id          = $row['id'];
    $code        = $row['code'];
    $name       = $row['name'];
    $html_code   = $row['html_entity'];
    $in_left      = $row['in_left'] == 1 ? __('Yes') : __('No');

    $row0 = '<td>'.$code.'</td>';
    $row1 = '<td>'.$name.'</td>';
    $row2 = '<td>'.$html_code.'</td>';
    $row3 = '<td>'.$in_left.'</td>';
    $row4 = '<td class="text-center">
                <div class="btn-group">
                    <a href="#" data-url="panel/currency_edit.php?id='.$id.'" data-toggle="slidePanel" title="'.__('Edit').'" class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                    <a href="#" class="btn-icon btn-danger item-js-delete" title="'.__('Delete').'" data-tippy-placement="top" data-ajax-action="deleteCurrency"><i class="icon-feather-trash-2"></i></a>
                </div>
            </td>';
    $row5 = '<td>
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
        5 => $row5
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
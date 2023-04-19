<?php
require_once('includes.php');

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
if($params['draw'] == 1){
    $params['order'][0]['column'] = 2;
    $params['order'][0]['dir'] = "asc";
}

//define index of column
$columns = array(
    'code',
    'name',
    'direction',
    'active',
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ) {
    $where .=" WHERE ";
    $where .=" ( code LIKE '".$params['search']['value']."%' ";
    $where .=" OR name LIKE '".$params['search']['value']."%' ";
    $where .=" OR direction LIKE '".$params['search']['value']."%' ";
    $where .=" OR active LIKE '".$params['search']['value']."%' )";
}

// getting total number records without any search
$sql = "SELECT * FROM `".$config['db']['pre']."languages` ";
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
    $code = $row['code'];
    $name = $row['name'];
    $file_name = $row['file_name'];
    $direction = $row['direction'];
    $active = $row['active'];

    if ($active == "1"){
        $active = '<span class="badge badge-primary">'.__("Active").'</span>';
    }
    else{
        $active = '<span class="badge badge-secondary">'.__("Not Active").'</span>';
    }

    $row0 = '<td>'.$code.'</td>';
    $row1 = '<td>'.$name.'</td>';
    $row2 = '<td>'.$direction.'</td>';
    $row3 = '<td>'.$active.'</td>';
    $row4 = '<td class="text-center">
                <div class="btn-group">
                <a href="language_file.php?file='.$file_name.'" class="btn-icon btn-primary mr-1" title="'.__('Edit file text').'" data-tippy-placement="top"> <i class="icon-feather-file-text"></i></a>
                    <a href="#" data-url="panel/language.php?id='.$id.'" data-toggle="slidePanel" title="'.__('Edit').'" class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                    <a href="#" class="btn-icon btn-danger item-js-delete" title="'.__('Delete').'" data-tippy-placement="top" data-ajax-action="deleteLanguage"><i class="icon-feather-trash-2"></i></a>
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
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format
?>

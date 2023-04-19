<?php
require_once('includes.php');

// initilize all variable
$params = $columns = $order = $totalRecords = $data = array();
$params = $_REQUEST;

//define index of column
$columns = array(
    'id'
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " name LIKE '%" . $params['search']['value'] . "%' ";
}


// getting total number records without any search
$sql = "SELECT * FROM `" . $config['db']['pre'] . "testimonials`";
$sqlTot .= $sql;
$sqlRec .= $sql;
//concatenate search sql if value exist
if (isset($where) && $where != '') {
    $sqlTot .= $where;
    $sqlRec .= $where;
}

$sqlRec .= " ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . " LIMIT " . $params['start'] . " ," . $params['length'] . " ";

$queryTot = $pdo->query($sqlTot);
$totalRecords = $queryTot->rowCount();
$queryRecords = $pdo->query($sqlRec);

//iterate on results row and create new index array of data
foreach ($queryRecords as $row) {
    $id = $row['id'];
    $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8' );
    $designation = htmlspecialchars($row['designation'], ENT_QUOTES, 'UTF-8' );
    $content = $row['content'];

    if($row['image'] != ""){
        $image = $row['image'];
    }else{
        $image = "default_user.png";
    }

    $row0 = '<td>
                <div class="d-flex align-items-center">
                    <img class="m-r-10 img-round" src="'.$config['site_url'].'storage/testimonials/'.$image.'" width="60">
                    <div>
                        <h6 class="m-b-5">'.$name.'</h6>
                        <p class="text-muted mb-0">'.$designation.'</p>
                    </div>
                </div>
            </td>';
    $row1 = '<td>' . $content . '</td>';
    $row2 = '<td class="text-center">
                <div class="btn-group">
                <a href="#" data-url="panel/testimonials.php?id='.$id.'" data-toggle="slidePanel" title="'.__('Edit').'" class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                    <a href="#" class="btn-icon btn-danger item-js-delete" title="'.__('Delete').'" data-tippy-placement="top" data-ajax-action="deleteTestimonial"><i class="icon-feather-trash-2"></i></a>
                </div>
            </td>';
    $row3 = '<td>
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
        3 => $row3
    );
    $data[] = $value;



}

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data   
);

echo json_encode($json_data);
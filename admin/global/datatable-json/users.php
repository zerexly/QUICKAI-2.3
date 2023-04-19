<?php
require_once('includes.php');
global $config;
$params = $columns = $order = $totalRecords = $data = array();
$params = $_REQUEST;

//define index of column
$columns = array(
    'id',
    'name',
    'email',
    'sex',
    'status',
    'created_at'
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ) {
    $where .=" WHERE ";
    $where .=" ( username LIKE '".$params['search']['value']."%' ";
    $where .=" OR name LIKE '".$params['search']['value']."%' ";
    $where .=" OR email LIKE '".$params['search']['value']."%' ";
    $where .=" OR sex LIKE '".$params['search']['value']."%' )";
}

// getting total number records without any search
$sql = "SELECT * FROM `".$config['db']['pre']."user` ";
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
    $id = $row['id'];
    $username = $row['username'];
    $name = $row['name'];
    $type = $row['user_type'] == 'user' ? '': 'Employer';
    $email = $row['email'];
    $sex = $row['sex'];
    $image = $row['image'];
    $status = $row['status'];
    $joined  = date('d M, y', strtotime($row['created_at']));
    if ($image == "")
        $image = "default_user.png";

    if ($status == "0"){
        $status = '<span class="badge badge-info">'.__('ACTIVE').'</span>';
    }
    elseif($status == "1")
    {
        $status = '<span class="badge badge-success">'.__('VERIFIED').'</span>';
    }
    else{
        $status = '<span class="badge badge-warning">'.__('BANNED').'</span>';
    }

    $rows = array();
    $rows[] = '<td>'.$id.'</td>';
    $rows[] = '<td>
                <div class="d-flex align-items-center">
                    <img class="m-r-10" src="'.$config['site_url'].'storage/profile/'.$image.'" width="50">
                    <div>
                        <h6>'.$name.'</h6>
                        <span>@'.$username.'</span>
                    </div>
                </div>
            </td>';
    $rows[] = '<td>'.$email.'</td>';
    $rows[] = '<td>'.$sex.'</td>';
    $rows[] = '<td>'.$status.'</td>';
    $rows[] = '<td>'.$joined.'</td>';
    $rows[] = '<td>
                <div class="btn-group">
                    <a href="#" title="'.__('Login as user').'" class="btn-icon btn-primary mr-1 login-as-user" data-user-id="'.$id.'" data-tippy-placement="top"><i class="icon-feather-log-in"></i></a>
                    <a href="#" data-url="panel/users_edit.php?id='.$id.'" data-toggle="slidePanel" title="'.__('Edit').'" class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                    <a href="javascript:void(0)" class="btn-icon btn-xs btn-danger item-js-delete" data-ajax-action="deleteusers" title="'.__('Delete').'" data-tippy-placement="top"><i class="icon-feather-user-x"></i></a>
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
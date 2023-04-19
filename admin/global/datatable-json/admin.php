<?php
require_once('includes.php');
global $config;
$params = $columns = $order = $totalRecords = $data = array();
$params = $_REQUEST;

//define index of column
$columns = array(
    'id',
    'name',
    'username',
    'email'
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ){
    $where .=" ( name LIKE '%".$params['search']['value']."%' ";
    $where .=" OR username LIKE '%".$params['search']['value']."%' ";
    $where .=" OR email LIKE '%".$params['search']['value']."%' ) ";
}


$orm = $admin = ORM::for_table($config['db']['pre'].'admins');

if(!empty($where))
    $orm->where_raw($where);

$totalRecords = $orm->count();

$order = $columns[$params['order'][0]['column']]." ".$params['order'][0]['dir'];

$result = $orm->order_by_expr($order)
    ->limit($params['length'])
    ->offset($params['start'])
    ->find_many();

foreach ($result as $row) {
    $id = $row['id'];
    $name = $row['name'];
    $username = $row['username'];
    $email = $row['email'];

    $image_url = $config['site_url'].'/storage/profile/'.$row['image'];

    $rows = array();
    $rows[] = '<td>'.$id.'</td>';
    $rows[] = '<td>
                <div class="d-flex align-items-center">
                    <div class="image-box m-r-10">
                        <img class="img-round" src="'.$image_url.'" alt="">
                    </div>
                    <div>
                        <h6 class="m-0">'.$name.'</h6>
                        <span class="text-muted">#'.$username.'</h6>
                    </div>
                </div>
                </td>';
    $rows[] = '<td>'.$email.'</td>';
    $rows[] = '<td>
                <div class="btn-group">
                <a href="#" data-url="panel/admin.php?id='.$id.'" data-toggle="slidePanel" title="Edit" class="btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                </div>
            </td>';

    if($id == 1){
        $rows[] = '<td></td>';
    }else {
        $rows[] = '<td>
                <div class="checkbox">
                <input type="checkbox" id="check_'.$id.'" value="'.$id.'" class="quick-check">
                <label for="check_'.$id.'"><span class="checkbox-icon"></span></label>
            </div>
            </td>';
    }


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
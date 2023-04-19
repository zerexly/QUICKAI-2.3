<?php
require_once('includes.php');

$params = $columns = $order = $totalRecords = $data = array();
$params = $_REQUEST;

//define index of column
$columns = array(
    'b.id',
    'b.title',
    'categories',
    'b.status',
    'b.updated_at'
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ){
    $where .=" ( id LIKE '".$params['search']['value']."%' ";
    $where .=" OR title LIKE '%".$params['search']['value']."%' ";
    $where .=" OR status LIKE '%".$params['search']['value']."%' ) ";
}

global $config, $link;

$orm = ORM::for_table($config['db']['pre'].'blog')
    ->select_many_expr('b.*','GROUP_CONCAT(c.title) categories')
    ->table_alias('b')
    ->left_outer_join($config['db']['pre'] . "blog_cat_relation", 'bc.blog_id = b.id','bc')
    ->left_outer_join($config['db']['pre'] . "blog_categories", 'bc.category_id = c.id','c')
    ->group_by('b.id');

if(!empty($where))
    $orm->where_raw($where);

$totalRecords = $orm->count();

$order = $columns[$params['order'][0]['column']]." ".$params['order'][0]['dir'];

$result = $orm->order_by_expr($order)
    ->limit($params['length'])
    ->offset($params['start'])
    ->find_array();

foreach ($result as $row) {
    $id = $row['id'];
    $title = $row['title'];
    $status = $row['status'] == 'pending'
        ? '<div class="badge badge-secondary">'.$row['status'].'</div>'
        : '<div class="badge badge-primary">'.$row['status'].'</div>';

    $last_modified = date('d, M Y', strtotime($row['updated_at']));

    $categories = !empty($row['categories']) ? implode(', ',explode(',',$row['categories'])) : '&#8211;';

    $rows = array();
    $rows[] = '<td>'.$id.'</td>';
    $rows[] = '<td>
                    <a href="blog-post.php?id='.$id.'">'.$title.'</a>
                    <a href="'.$link['BLOG-SINGLE']. '/' . $id . '/' . create_slug($title).'" target="_blank"><i class="icon-feather-external-link"></i></a>
                </td>';
    $rows[] = '<td>'.$categories.'</td>';
    $rows[] = '<td>'.$status.'</td>';
    $rows[] = '<td>'.$last_modified.'</td>';
    $rows[] = '<td>
                <div class="btn-group">
                <a href="blog-post.php?id='.$id.'" title="Edit" class="btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
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
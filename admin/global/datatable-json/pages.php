<?php
require_once('includes.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == "get_translation_pages") { get_translation_pages(); }
}

function get_translation_pages()
{
    global $config, $lang;
    $con = db_connect();

    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $rows = ORM::for_table($config['db']['pre'].'languages')
            ->select_many('id','code','name')
            ->where('active',1)
            ->where_not_equal('code','en')
            ->find_many();


        $child_tpl = '<div class="container m-t-10 m-b-10">
    <div class="row">
        <div class="col-md-12">
            <p>'.__("Translations of this page").':</p>
                <table class="table table-condensed table-bordered m-t-10">
                    <thead>
                        <tr>
                            <th>'.__("Language").'</th>
                            <th>'.__("ID").'</th>
                            <th>'.__("Name").'</th>
                            <th>'.__("Title").'</th>
                            <th>'.__("Active").'</th>
                            <th>'.__("Actions").'</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($rows as $fetch){
            $info = ORM::for_table($config['db']['pre'].'pages')
                ->where(array(
                    'translation_lang' => $fetch['code'],
                    'translation_of' => $id
                ))
                ->find_one();
            
            $pageid = $info['id'];
            $active = $info['active'];
            if ($active == "0")
                $active = '<span class="badge badge-danger">'.__("Not Active").'</span>';
            else
                $active = '<span class="badge badge-success">'.__("Active").'</span>';
            $child_tpl .= '<tr id="'.$pageid.'">
                                <td>'.$fetch['name'].'</td>
                                <td>'.$info['id'].'</td>
                                <td>'.$info['name'].'</td>
                                <td>'.$info['title'].'</td>
                                <td>'.$active.'</td>
                                <td>
                                    <a href="#" data-url="panel/pages.php?id='.$pageid.'" data-toggle="slidePanel" class="btn btn-xs btn-default"> <i class="ion-edit"></i> '.__('Edit').'</a>
                                </td>
                            </tr>';
        }
        $child_tpl .= '</tbody>
                </table>
            </div>
        </div>
    </div>';
        _esc($child_tpl);
    }
    die();
}
// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;

//define index of column
$columns = array(
    'name',
    'title'
);
$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ) {
    $where .=" WHERE ";
    $where .=" ( id LIKE '".$params['search']['value']."%' ";
    $where .=" OR name LIKE '".$params['search']['value']."%' ";
    $where .=" OR title LIKE '".$params['search']['value']."%' ) AND translation_lang = 'en'";
}

// getting total number records without any search
$sql = "SELECT * FROM `".$config['db']['pre']."pages` ";
$sqlTot .= $sql;
$sqlRec .= $sql;
//concatenate search sql if value exist
if(isset($where) && $where != '') {
    $sqlTot .= $where;
    $sqlRec .= $where;
}else{
    $where .=" Where ( translation_lang = 'en' )";
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
    $name = $row['name'];
    $title = $row['title'];
    $slug = $row['slug'];
    $active = $row['active'];
    if ($active == "0")
        $active = '<span class="badge badge-danger">'.__("Not Active").'</span>';
    else
        $active = '<span class="badge badge-success">'.__("Active").'</span>';


    $row0 = '<td><i class="icon-feather-plus-square details-row-button" data-entry-id="'.$id.'" data-entry-action="get_translation_pages"></i> &nbsp;'.$name.'</td>';
    $row1 = '<td>'.$title.'</td>';
    $row2 = '<td><a target="_blank" href="'.$config['site_url'].'page/'.$slug.'">'.$config['site_url'].'page/'.$slug.'</a></td>';
    $row3 = '<td class="text-center">
                <div class="btn-group">
                    <a href="#" data-url="panel/pages.php?id='.$id.'" data-toggle="slidePanel" title="'.__('Edit').'" class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                    <a href="#" class="btn-icon btn-danger item-js-delete" title="'.__('Delete').'" data-tippy-placement="top" data-ajax-action="deleteStaticPage"><i class="icon-feather-trash-2"></i></a>
                </div>
            </td>';
    $row4 = '<td>
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
        4 => $row4
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
<?php
require_once('includes.php');

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;

if(isset($_POST['action'])){
    if ($_POST['action'] == "get_translation_faq") { get_translation_faq(); }
}

function get_translation_faq()
{
    global $config, $lang;
    $con = db_connect();

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $result = ORM::for_table($config['db']['pre'].'languages')
            ->where('active','1')
            ->where_not_equal('code','en')
            ->find_many();
        $child_tpl = '<div class="container m-t-10 m-b-10">
    <div class="row">
        <div class="col-md-12">
            <p>'.__("Translations of this faq entry").':</p>
                <table class="table table-condensed table-bordered m-t-10">
                    <thead>
                        <tr>
                            <th>'.__("Language").'</th>
                            <th>'.__("Title").'</th>
                            <th>'.__("Active").'</th>
                            <th>'.__("Actions").'</th>
                        </tr>
                    </thead>
                    <tbody>';
        foreach ($result as $fetch) {
            $language_name = $fetch['name'];

            $count = ORM::for_table($config['db']['pre'].'faq_entries')
                ->where(array(
                    'translation_lang' => $fetch['code'],
                    'translation_of' => $id,
                ))
                ->count();
            if($count){
                $info = ORM::for_table($config['db']['pre'].'faq_entries')
                    ->where(array(
                        'translation_lang' => $fetch['code'],
                        'translation_of' => $id,
                    ))
                    ->find_one();

                $faq_id = $info['faq_id'];
                $faq_title = $info['faq_title'];
                $active = $info['active'];
            }else{
                //We cn add here the insert query for not found entry
                $faq_id = "";
                $faq_title = "";
                $active = "0";
            }

            if ($active == "0")
                $active = '<span class="badge badge-danger">'.__("Not Active").'</span>';
            else
                $active = '<span class="badge badge-success">'.__("Active").'</span>';
            $child_tpl .= '<tr id="'.$faq_id.'">
                                <td>'.$language_name.'</td>
                                <td>'.$faq_title.'</td>
                                <td>'.$active.'</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" data-url="panel/faq_entries.php?id='.$faq_id.'" data-toggle="slidePanel" class="btn-icon mr-1"><i class="icon-feather-edit"></i></a>
                                    </div>
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

//define index of column
$columns = array(
    'faq_title'
);
$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ) {
    $where .=" WHERE ";
    $where .=" ( faq_title LIKE '".$params['search']['value']."%' ) AND translation_lang = 'en'";
}

// getting total number records without any search
$sql = "SELECT * FROM `".$config['db']['pre']."faq_entries` ";
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
    $id = $row['faq_id'];
    $title = $row['faq_title'];

    $rows = array();
    $rows[] = '<td><td><i class="icon-feather-plus-square details-row-button cursor-pointer" data-entry-id="'.$id.'" data-entry-action="get_translation_faq"></i> '.$title.'</td>';
    $rows[] = '<td>
                <div class="btn-group">
                    <a href="#" data-url="panel/faq_entries.php?id='.$id.'" data-toggle="slidePanel" title="'.__('Edit').'" class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                    <a href="#" class="btn-icon btn-danger item-js-delete" title="'.__('Delete').'" data-tippy-placement="top" data-ajax-action="deletefaq"><i class="icon-feather-trash-2"></i></a>
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
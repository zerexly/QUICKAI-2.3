<?php
include '../../global/datatable-json/includes.php';

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
//define index of column
$columns = array(
    'position',
    'title',
    'description',
    'active',
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( title LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR prompt LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR description LIKE '" . $params['search']['value'] . "%' )";
}

// getting total number records without any search
$sql = "SELECT * FROM `" . $config['db']['pre'] . "ai_chat_prompts`";

$sqlTot .= $sql;
$sqlRec .= $sql;
//concatenate search sql if value exist
if (isset($where) && $where != '') {
    $sqlTot .= $where;
    $sqlRec .= $where;
}

$sqlRec .= " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . $params['order'][0]['dir'] . "  LIMIT " . $params['start'] . " ," . $params['length'] . " ";

$queryTot = $pdo->query($sqlTot);
$totalRecords = $queryTot->rowCount();
$queryRecords = $pdo->query($sqlRec);

//iterate on results row and create new index array of data
foreach ($queryRecords as $row) {

    $id = $row['id'];
    $title = escape($row['title']);
    $description = escape(strlimiter($row['description'], 100));

    $status = $row['active']
        ? '<div class="badge badge-primary">'.__('Enabled').'</div>'
        : '<div class="badge badge-secondary">'.__('Disabled').'</div>';

    $rows = array();
    $rows[] = '<td><i class="icon-feather-menu quick-reorder-icon"
                                       title="'.__('Reorder').'"></i> <span class="d-none">'.$id.'</span></td>';
    $rows[] = '<td>' . $title . '</td>';
    $rows[] = '<td class="hidden-xs">' . $description . '</td>';
    $rows[] = '<td>' . $status . '</td>';
    $rows[] = '<td class="text-center">
                <div class="btn-group">
                <a href="#" title="' . __('Edit') . '" data-url="panel/ai_chat_prompts.php?id=' . $id . '" data-toggle="slidePanel"  class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                </div>
            </td>';
    $rows[] = '<td>
                <div class="checkbox">
                <input type="checkbox" id="check_' . $id . '" value="' . $id . '" class="quick-check">
                <label for="check_' . $id . '"><span class="checkbox-icon"></span></label>
            </div>
            </td>';
    $rows['DT_RowId'] = $id;
    $data[] = $rows;
}

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data   // total data array
);

echo json_encode($json_data);

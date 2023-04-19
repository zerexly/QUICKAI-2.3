<?php
include '../../global/datatable-json/includes.php';

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;

//define index of column
$columns = array(
    't.title',
    'c.title',
    't.description',
    't.active',
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( t.title LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR c.title LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR t.description LIKE '" . $params['search']['value'] . "%' )";
}

// getting total number records without any search
$sql = "SELECT t.*, c.title category FROM `" . $config['db']['pre'] . "ai_templates` as t
INNER JOIN `" . $config['db']['pre'] . "ai_template_categories` as c ON c.id = t.category_id ";

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
    //$data[] = $row;
    $id = $row['id'];
    $title = htmlentities((string)$row['title'], ENT_QUOTES, 'UTF-8');
    $content = strlimiter(strip_tags($row['description']), 100);
    $content = htmlentities($content, ENT_QUOTES, 'UTF-8');

    $status = $row['active']
        ? '<div class="badge badge-primary">'.__('Enabled').'</div>'
        : '<div class="badge badge-secondary">'.__('Disabled').'</div>';

    $rows = array();
    $rows[] = '<td><i class="' . $row['icon'] . ' mr-2"></i>' . $title . '</td>';
    $rows[] = '<td class="hidden-xs">' . $row['category'] . '</td>';
    $rows[] = '<td class="hidden-xs">' . $content . '</td>';
    $rows[] = '<td class="hidden-xs">' . $status . '</td>';
    $rows[] = '<td class="text-center">
                <div class="btn-group">
                <a href="#" title="' . __('Edit') . '" data-url="panel/ai_templates.php?id=' . $id . '" data-toggle="slidePanel"  class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
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

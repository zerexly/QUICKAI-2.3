<?php
include '../../global/datatable-json/includes.php';

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
if ($params['draw'] == 1)
    $params['order'][0]['dir'] = "desc";
//define index of column
$columns = array(
    'i.title',
    'i.resolution',
    'u.username',
    'i.created_at',
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( i.title LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR i.description LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR i.resolution LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR u.username LIKE '" . $params['search']['value'] . "%' ";
    $where .= " OR u.name LIKE '" . $params['search']['value'] . "%' )";
}

// getting total number records without any search
$sql = "SELECT i.*, u.username as username, u.name as fullname
FROM `" . $config['db']['pre'] . "ai_images` as i
INNER JOIN `" . $config['db']['pre'] . "user` as u ON u.id = i.user_id ";

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
    //$data[] = $row;
    $id = $row['id'];
    $title = htmlentities((string)$row['title'], ENT_QUOTES, 'UTF-8');
    $description = htmlentities((string)$row['description'], ENT_QUOTES, 'UTF-8');
    $resolution = htmlentities((string)$row['resolution'], ENT_QUOTES, 'UTF-8');
    $username = htmlentities((string)$row['username'], ENT_QUOTES, 'UTF-8');
    $fullname = htmlentities((string)$row['fullname'], ENT_QUOTES, 'UTF-8');

    $image = $row['image'];

    $created_at = timeAgo($row['created_at']);

    $rows = array();
    $rows[] = '<td>
                <div class="d-flex align-items-center">
                    <a href="' . $config['site_url'] . 'storage/ai_images/' . $image . '" target="_blank" title="' . __("View") . '" data-tippy-placement="top">
                        <img src="' . $config['site_url'] . 'storage/ai_images/small_' . $image . '" alt="' . $title . '" width="60">
                    </a>
                    <div class="ml-3">
                        <div><strong>' . $title . '</strong></div>
                        <small>' . $description . '</small>
                    </div>
                </div>
            </td>';
    $rows[] = '<td class="hidden-xs">' . $resolution . '</td>';
    $rows[] = '<td class="hidden-xs">' . $fullname . '<p class="text-muted m-b-0">#' . $username . '</p></td>';
    $rows[] = '<td class="hidden-xs">' . $created_at . '</td>';
    $rows[] = '<td class="text-center">
                <div class="btn-group">
                <a href="' . $config['site_url'] . 'storage/ai_images/' . $image . '" title="' . __('Download') . '" class="btn-icon mr-1" data-tippy-placement="top" download><i class="icon-feather-download"></i></a>
                    <a href="#" title="' . __('Delete') . '" class="btn-icon btn-danger item-js-delete" data-tippy-placement="top" data-ajax-action="deleteAIImages"><i class="icon-feather-trash-2"></i></a>
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

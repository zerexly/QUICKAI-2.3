<?php
include '../../global/datatable-json/includes.php';

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
if ($params['draw'] == 1)
    $params['order'][0]['dir'] = "desc";
//define index of column
$columns = array(
    's.title',
    's.audio',
    's.characters',
    's.voice_id',
    'u.username',
    's.created_at',
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( s.title LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR s.text LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR s.language LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR s.voice_id LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR u.username LIKE '" . $params['search']['value'] . "%' ";
    $where .= " OR u.name LIKE '" . $params['search']['value'] . "%' )";
}

// getting total number records without any search
$sql = "SELECT s.*, u.username as username, u.name as fullname
FROM `" . $config['db']['pre'] . "ai_speeches` as s
INNER JOIN `" . $config['db']['pre'] . "user` as u ON u.id = s.user_id ";

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

$voices = get_ai_voices();

//iterate on results row and create new index array of data
foreach ($queryRecords as $row) {
    //$data[] = $row;
    $id = $row['id'];
    $title = escape((string)$row['title']);
    $text = escape((string)$row['text']);
    $username = escape((string)$row['username']);
    $fullname = escape((string)$row['fullname']);

    $file = $row['file_name'];

    $created_at = timeAgo($row['created_at']);

    $language = $voices[$row['language']];
    $voice = $voices[$row['language']]['voices'][$row['voice_id']];

    $rows = array();
    $rows[] = '<td>
                <div><strong>' . $title . '</strong></div>
                <small>' . $text . '</small>
            </td>';
    $rows[] = '<td class="hidden-xs">
                <audio controls="" preload="none"><source src="' . $config['site_url'] . 'storage/ai_audios/' . $file . '" type="audio/mpeg"></audio>
            </td>';
    $rows[] = '<td class="hidden-xs">' . $row['characters'] . '</td>';
    $rows[] = '<td class="hidden-xs">
                <span>'. $voice['voice'] .'</span>, <small>'. $voice['gender'] . (($voice['voice_type'] == 'neural') ? ', Neural' : '') . '</small>
                                <br>
                                <small><strong>'. $language['language'] .'</strong></small>
    </td>';
    $rows[] = '<td class="hidden-xs">' . $fullname . '<p class="text-muted m-b-0">#' . $username . '</p></td>';
    $rows[] = '<td class="hidden-xs">' . $created_at . '</td>';
    $rows[] = '<td class="text-center">
                <div class="btn-group">
                <a href="' . $config['site_url'] . 'storage/ai_audios/' . $file . '" title="' . __('Download') . '" class="btn-icon mr-1" data-tippy-placement="top" download><i class="icon-feather-download"></i></a>
                    <a href="#" title="' . __('Delete') . '" class="btn-icon btn-danger item-js-delete" data-tippy-placement="top" data-ajax-action="deleteAISpeeches"><i class="icon-feather-trash-2"></i></a>
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

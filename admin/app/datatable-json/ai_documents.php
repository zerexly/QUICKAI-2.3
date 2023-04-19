<?php
include '../../global/datatable-json/includes.php';

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
if ($params['draw'] == 1)
    $params['order'][0]['dir'] = "desc";
//define index of column
$columns = array(
    'd.title',
    'content',
    'u.username',
    'd.created_at',
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( d.title LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR d.template LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR d.content LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR u.username LIKE '" . $params['search']['value'] . "%' ";
    $where .= " OR u.name LIKE '" . $params['search']['value'] . "%' )";
}

// getting total number records without any search
$sql = "SELECT d.*, u.username as username, u.name as fullname
FROM `" . $config['db']['pre'] . "ai_documents` as d
INNER JOIN `" . $config['db']['pre'] . "user` as u ON u.id = d.user_id ";

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
    $content = strlimiter(strip_tags($row['content']), 50);
    $content = htmlentities($content, ENT_QUOTES, 'UTF-8');
    $username = htmlentities((string)$row['username'], ENT_QUOTES, 'UTF-8');
    $fullname = htmlentities((string)$row['fullname'], ENT_QUOTES, 'UTF-8');

    $created_at = timeAgo($row['created_at']);

    $template = ORM::for_table($config['db']['pre'] . 'ai_templates')
        ->where('slug', $row['template'])
        ->find_one();
    if(empty($template)){
        $template = ORM::for_table($config['db']['pre'] . 'ai_custom_templates')
            ->where('slug', $row['template'])
            ->find_one();
    }
    if(empty($template)) {
        if($row['template'] == 'quickai-speech-to-text'){
            $template = array(
                'icon' => 'fa fa-headphones',
                'title' => __('Speech to Text')
            );
        } else if($row['template'] == 'quickai-ai-code'){
            $template = array(
                'icon' => 'fa fa-code',
                'title' => __('AI Code')
            );
        } else {
            $template = array(
                'icon' => 'fa fa-check-square',
                'title' => $row['template']
            );
        }
    }

    $rows = array();
    $rows[] = '<td>
                <div>
                    <h6>' . $title . '</h6>
                    <p class="text-muted mb-0"><i class="' . $template['icon'] . ' mr-2"></i>' . $template['title'] . '</p>
                </div>
            </td>';
    $rows[] = '<td class="hidden-xs">' . $content . '</td>';
    $rows[] = '<td class="hidden-xs">' . $fullname . '<p class="text-muted m-b-0">#' . $username . '</p></td>';
    $rows[] = '<td class="hidden-xs">' . $created_at . '</td>';
    $rows[] = '<td class="text-center">
                <div class="btn-group">
                <a href="#" title="' . __('Edit') . '" data-url="panel/ai_documents.php?id=' . $id . '" data-toggle="slidePanel"  class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                    <a href="#" title="' . __('Delete') . '" class="btn-icon btn-danger item-js-delete" data-tippy-placement="top" data-ajax-action="deleteAIDocument"><i class="icon-feather-trash-2"></i></a>
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

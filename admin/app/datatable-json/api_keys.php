<?php
include '../../global/datatable-json/includes.php';

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
if ($params['draw'] == 1)
    $params['order'][0]['dir'] = "desc";
//define index of column
$columns = array(
    'title',
    'api_key',
    'type',
    'active',
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( title LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR api_key LIKE '" . $params['search']['value'] . "%' )";
}

// getting total number records without any search
$sql = "SELECT * FROM `" . $config['db']['pre'] . "api_keys`";

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
    $key = check_allow() ? $row['api_key'] : '******************';

    $status = $row['active']
        ? '<div class="badge badge-primary">'.__('Enabled').'</div>'
        : '<div class="badge badge-secondary">'.__('Disabled').'</div>';

    $balance = !empty($row['balance']) ? $row['balance'] : ' - ';

    $last_update = !empty($row['balance_last_update'])
        ? $row['balance_last_update']
        : ' - ';

    /* update balance once a day */
    if (
        empty($row['balance_last_update']) ||
        (
            !empty($row['balance_last_update']) &&
            ( strtotime($row['balance_last_update']) < strtotime('-1 day') )
        )
    ) {

        if($row['type'] == 'stable-diffusion'){
            require_once ROOTPATH . '/includes/lib/StableDiffusion.php';

            $stableDiffusion = new StableDiffusion($row['api_key']);
            $response = $stableDiffusion->balance();
            $response = json_decode($response, true);

            $credits = 0;
            if(isset($response['credits'])){
                $credits = round($response['credits'], 2);
            }
            $balance = $credits;
        } else {
            // TODO: disable this right now
            /*require_once ROOTPATH . '/includes/lib/orhanerday/open-ai/src/OpenAi.php';
            require_once ROOTPATH . '/includes/lib/orhanerday/open-ai/src/Url.php';

            $open_ai = new Orhanerday\OpenAi\OpenAi($row['api_key']);
            $response = $open_ai->used_balance();
            $response = json_decode($response, true);

            $uses = $hard_limit = 0;
            if (isset($response['total_usage'])) {
                $uses = round($response['total_usage']) / 100;
            }
            $response = $open_ai->balance_hard_limit();
            $response = json_decode($response, true);

            if (isset($response['hard_limit_usd'])) {
                $hard_limit = round($response['hard_limit_usd'] * 100) / 100;
            }

            $balance = "$$uses / $$hard_limit"; */
            $balance = " - ";
        }

        /* update balance */
        $api_key = ORM::for_table($config['db']['pre'] . 'api_keys')->find_one($id);
        $api_key->balance = $balance;
        $api_key->balance_last_update = date('Y-m-d H:i:s');
        $api_key->save();

        $last_update = date('Y-m-d H:i:s');
    }

    if($row['type'] == 'stable-diffusion'){
        $type = __('Stable Diffusion');
        $balance .= ' <small>' . __('Credits') . '</small>';
    } else {
        $type = __('OpenAI');
    }

    $balance = check_allow() ? $balance : '****';

    $rows = array();
    $rows[] = '<td>' . $title . '</td>';
    $rows[] = '<td class="hidden-xs">' . $key . '</td>';
    $rows[] = '<td class="hidden-xs">' . $type . '</td>';
    $rows[] = '<td class="hidden-xs">' . $balance . ' <i class="fa fa-clock-o" title="' . __('Last Update: ') . $last_update . '" data-tippy-placement="top"></i></td>';
    $rows[] = '<td class="hidden-xs">' . $status . '</td>';
    $rows[] = '<td class="text-center">
                <div class="btn-group">' .

        ($row['type'] == 'stable-diffusion'
            ? '<a href="?refresh-balance=' . $id . '" title="' . __('Refresh Balance') . '" class="btn-icon btn-primary mr-1" data-tippy-placement="top"><i class="icon-feather-refresh-ccw"></i></a>'
            : '')

        . '<a href="#" title="' . __('Edit') . '" data-url="panel/api_keys.php?id=' . $id . '" data-toggle="slidePanel"  class="btn-icon mr-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
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

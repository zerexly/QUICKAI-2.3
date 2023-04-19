<?php

$countries = array();
$count = 1;

$row = ORM::for_table($config['db']['pre'].'countries')
    ->where('active',1)
    ->order_by_asc('asciiname')
    ->find_many();
$total = count($row);
$divide = intval($total/4)+1;
$col = "";
foreach ($row as $info)
{
    $countrylang = getLangFromCountry($info['languages']);
    $countries[$count]['tpl'] = "";
    if($count == 1 or $count == $col){
        $countries[$count]['tpl'] .= '<div class="flag-list col-3 "><ul>';
        $checkEnd = $count+$divide-1;
        $col = $count+$divide;
    }
    $countries[$count]['tpl'] .= '<li><span class="margin-right-5 flag flag-'.strtolower($info['code']).'"></span><a href="'.$config['site_url'].'home/'.$countrylang.'/'.$info['code'].'" data-id="'.$info['id'].'" data-name="'.$info['asciiname'].'">'.$info['asciiname'].'</a></li>';


    if($count == $checkEnd or $count == $total){
        $countries[$count]['tpl'] .= '</ul></div>';
    }
    $count++;
}
//Print Template
HtmlTemplate::display('global/countries', array(
    'countrylist' => $countries
));
exit;
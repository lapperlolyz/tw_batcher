<?php
header('Content-Type: text/html; charset= utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0;
$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
//$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';

/** @var modx $modx */
$query = $modx->newQuery('modResource');

if (isset($_REQUEST['search'])) {
    $query->where([
        'pagetitle:LIKE' => "%".$_REQUEST['search']."%"
    ]);
}
if (isset($_REQUEST['context_key'])) {
    $query->where([
        'context_key:=' => $_REQUEST["context_key"]
    ]);
}
if (isset($_REQUEST['template'])) {
    $query->where([
        'template:=' => $_REQUEST['template']
    ]);
}
$total = $modx->getCount('modResource', $query);
$query->limit($limit, $limit * $offset);

//$query->leftJoin('modTemplateVarResource', 'TVR', 'TVR.contentid = modResource.id');
//$query->leftJoin('modTemplateVar', 'TV', 'TV.id = TVR.tmplvarid');

$res = $modx->getCollection('modResource', $query);

//$query->select([
//    'modResource.*',
//    'TVR.*',
//    'TV.*'
//]);
//
//if ($query->prepare() && $query->stmt->execute()) {
//    $output = $query->stmt->fetchAll(PDO::FETCH_ASSOC);
//}

$data = [];

//foreach($res as $r) {
//    $arr = $r->toArray();
//    $output[] = $arr;
//}
foreach($res as $r) {
    $arr = $r->toArray();
    $tvs = $r->getMany('modTemplateVar');

    $tvarr = [];
    foreach ($tvs as $tv) {
        $id = $tv->get('id');
        $tvname = $tv->get('name');
        $tvarr[] = [
            'id' => $id,
            'name' => $tvname,
            'value' => $r->getTVValue($id)
        ];
        foreach($tvarr as $tvv) {
            $arr["tv-$tvname"] = $tvv['value'];
        }
    }
    $arr['tvs'] = $tvarr;

    $data[] = $arr;;
}

$output = [
    'total' => $total,
    'data' => $data
];

die(json_encode($output, JSON_UNESCAPED_UNICODE));
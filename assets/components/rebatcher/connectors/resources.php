<?php
header('Content-Type: text/html; charset= utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$input = json_decode(file_get_contents("php://input"), true);
$params = $input['data'];

$limit = isset($params['limit']) ? $params['limit'] : 0;
$offset = isset($params['offset']) ? $params['offset'] : 0;

/** @var modx $modx */
$query = $modx->newQuery('modResource');

if (isset($params['context_key'])) {
    $query->where([
        'context_key:=' => $params["context_key"]
    ]);
}
if (isset($params['template'])) {
    $query->where([
        'template:=' => $params['template']
    ]);
}

// TODO: Не работают фильтры IN, NOT IN

if (isset($params['filters'])) {
    foreach ($params['filters'] as $f) {
        switch ($f['operator']) {
            case 'IN':
            case 'NOT IN':
                $value = explode(',', $f['value']);
                break;
            case 'LIKE':
            case 'NOT LIKE':
                $value = "%" . $f['value'] . "%";
                break;
            default:
                $value = $f['value'];
                break;
        }
        $query->where([
            $f['field'] . ":" . $f['operator'] => $value
        ]);
    }
} else if (isset($params['search'])) {
    $query->where([
        'id:=' => $params['search']
    ]);
    $query->orCondition([
        'pagetitle:LIKE' => "%".$params['search']."%",
        'alias:LIKE' => "%".$params['search']."%"
    ]);
//    $query->orCondition ([
//        'alias:LIKE' => "%".$params['search']."%"
//    ]);
//    $query->orCondition ([
//        'id:=' => "%".$params['search']."%"
//    ]);
}

if (isset($params['sort'])) {
    foreach($params['sort'] as $key => $value) {
        if (strpos($value[0], 'tv_') !== 0) {
            if (!$value[1]) {
                $query->sortby($value[0]);
            } else {
                $query->sortby($value[0], 'DESC');
            }
        }
    }
}

$total = $modx->getCount('modResource', $query);
$query->limit($limit, $limit * $offset);

$res = $modx->getCollection('modResource', $query);

$data = [];
foreach($res as $r) {
    $arr = $r->toArray();

    if (isset($params['tvs'])) {
//        $tvs = $r->getMany('modTemplateVar');

        $tvs = $modx->getCollection('modTemplateVar', [
            'name:IN' => $params['tvs']
        ]);

        $tvarr = [];
        foreach ($tvs as $tv) {
            $id = $tv->get('id');
            $tvname = $tv->get('name');
            $tvarr[] = [
                'id' => $id,
                'name' => $tvname,
                'value' => $r->getTVValue($id)
            ];
            foreach ($tvarr as $tvv) {
                $arr["tv_$tvname"] = $tvv['value'];
            }
        }
        $arr['tvs'] = $tvarr;
    }

    $data[] = $arr;;
}

if (isset($params['sort'])) {
    foreach($params['sort'] as $key => $value) {
        if (strpos($value[0], 'tv_') === 0) {
            usort($data, function($item1, $item2) use ($value) {
                if (!$value[1]) {
                    return $item1[$value[0]] < $item2[$value[0]] ? -1 : 1;
                } else {
                    return $item1[$value[0]] < $item2[$value[0]] ? 1 : -1;
                }
            });
        }
    }
}


$output = [
    'total' => $total,
    'data' => $data
];

die(json_encode($output, JSON_UNESCAPED_UNICODE));
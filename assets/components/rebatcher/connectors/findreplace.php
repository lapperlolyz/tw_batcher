<?php
header('Content-Type: text/html; charset= utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';


//$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0;
//$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;

$input = json_decode(file_get_contents("php://input"), true);
$data = $input['data'];

$output = [];
foreach($data['resources'] as $d) {
//    $output[] = $d;
    $res = $modx->getObject('modResource', $d);
    $subject = $res->get($data['field']);
    $count = 0;

    if ($data['is_regexp']) {
        $value = preg_replace($data['find'], $data['replace'], $subject, -1, $count);
    } else {
        if ($data['is_case_sensitive']) {
            $value = str_replace($data['find'], $data['replace'], $subject, $count);
        } else {
            $value = preg_replace('/' . preg_quote($data['find']) . '/iu', $data['replace'], $subject, -1, $count);
        }
    }

    var_dump($count);

    if ($count > 0) {
        $res->set($data['field'], $value);
        $res->save();
    }
}

//die(var_dump($data));

///** @var modx $modx */
//$query = $modx->newQuery('modTemplateVar');
//$tvs = $modx->getCollection('modTemplateVar', $query);
//
//$output = [];
//
//foreach($tvs as $tv) {
//    $output[] = $tv->toArray();
//}
//
//die(json_encode($output, JSON_UNESCAPED_UNICODE));
<?php
header('Content-Type: text/html; charset= utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

/** @var modx $modx */
$query = $modx->newQuery('modContext');
$query->where([
    'key:NOT IN' => ['mgr']
]);
$ctx = $modx->getCollection('modContext', $query);

$output = [];

foreach($ctx as $ct) {
    $output[] = $ct->toArray();
}

die(json_encode($output, JSON_UNESCAPED_UNICODE));
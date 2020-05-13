<?php
header('Content-Type: text/html; charset= utf-8');

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';


//$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0;
//$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;

$data = json_decode(file_get_contents("php://input"));

foreach($data->parents as $p) {

    $pid = $p;
    foreach($data->resources as $r) {
        $context = $modx->getObject('modResource', $pid)->get('context_key');

        $res = $modx->getObject('modResource', $r);

        $copy = $modx->newObject('modResource');
        $copy->fromArray($res->toArray());
        $copy->set('parent', $pid);
        $copy->set('context_key', $context);

        $count = $modx->getCount('modResource', [
            'parent' => $pid,
            'pagetitle' => $copy->get('pagetitle')
        ]);

        if ($count > 0) {
            $copy->set('pagetitle', $copy->get('pagetitle') . " (Копия)");
            $copy->set('alias', $copy->get('alias') . " -copy");
        }

        $copy->save();

        $copyid = $copy->get('id');

        $tvs = $modx->getCollection('modTemplateVarResource', array(
            "contentid" => $r,
        ));

        foreach ($tvs as $tv) {
            $copytv = $modx->newObject('modTemplateVarResource');
            $copytv->set('tmplvarid', $tv->tmplvarid);
            $copytv->set('value', $tv->value);
            $copytv->set('contentid', $copyid);
            $copytv->save();
        }
    }
}

//$data = $input->data;

//foreach($data as $d) {
//    $res = $modx->getObject('modResource', $d->id);
//    $res->setTVValue($d->tvId, $d->value);
//}

//die(json_encode($input));

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
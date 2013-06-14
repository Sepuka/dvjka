<?php
require_once __DIR__ . '/funcs.php';

$index = file_get_contents('tmpl/gamno.tmpl');

$gamnoList = array();
$gamno = getGamno();
if ($gamno) {
    $gamnoList = implode(', ', $gamno);
} else
    $gamnoList = 'Заблокированных клиентов пока нет';

$index = str_replace(
    array('{GAMNO_PHONES}', '{IP}'),
    array($gamnoList, $_SERVER['REMOTE_ADDR']),
    $index);

echo $index;
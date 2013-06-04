<?php

require_once __DIR__ . '/funcs.php';
require_once __DIR__ . '/db.php';

$settings = parse_ini_file('settings.ini', true);

if (! empty($_COOKIE['phone'])) {
    if (! empty($_COOKIE['add']) && (in_array($_COOKIE['add'], array('100','1000','10000')))) {
        $destPhone = getDestPhone();
        $index = file_get_contents('tmpl/'.$_COOKIE['add'].'.tmpl');
        $index = str_replace(
            array('{DEST_PHONE}'),
            array($destPhone),
            $index);

        // Создание намерения заплатить
        $db = DB::getInstance();
        $sender = $db->findUser($_COOKIE['phone']);
        $dest = $db->findUser($destPhone);
        $db->addPayment($sender->Id, $dest->Id, 100);
    } else {
        $index = file_get_contents('tmpl/index.tmpl');
        $index = str_replace(
            array('{PHONE}'),
            array($_COOKIE['phone']),
            $index);
    }
} else {
    $index = file_get_contents('tmpl/indexnew.tmpl');
}

$index = str_replace(
        array('{HOST}', '{IP}'),
        array($settings['site']['host'], $_SERVER['REMOTE_ADDR']),
        $index);

echo $index;
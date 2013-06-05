<?php

require_once __DIR__ . '/funcs.php';
require_once __DIR__ . '/db.php';

$settings = parse_ini_file('settings.ini', true);

if (! empty($_COOKIE['phone'])) {
    // Клиент изъявил желание заплатить
    if (! empty($_COOKIE['add']) && (in_array($_COOKIE['add'], array('100','1000','10000')))) {
        $db = DB::getInstance();
        if (empty($_COOKIE['lox'])) {
            $index = file_get_contents('tmpl/'.$_COOKIE['add'].'.tmpl');
            $destPhone = getDestPhone();
            if (is_object($destPhone))
                $destPhone = $destPhone->Phone;

            // Создание намерения заплатить
            $sender = $db->findUser($_COOKIE['phone']);
            $dest = $db->findUser($destPhone);
            if ($db->addPayment($sender->Id, $dest->Id, $_COOKIE['add']))
                setcookie('lox', $db->getConn()->insert_id, time() + (int)$settings['site']['savetime'], '/');
        } else {
            $index = file_get_contents('tmpl/rejection.tmpl');
            $payment = $db->getPayment($_COOKIE['lox']);
            if ($payment) {
                $destUser = $db->getUser($payment->Dest_id);
                $destPhone = $destUser->Phone;
            }
        }
        $index = str_replace(
            array('{DEST_PHONE}', '{TIME_PAYMENT}', '{SUM}'),
            array($destPhone, date('d.m.Y H:i'), $_COOKIE['add']),
            $index);
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
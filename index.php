<?php

require_once __DIR__ . '/funcs.php';
require_once __DIR__ . '/db.php';

$settings = parse_ini_file('settings.ini', true);

if (! empty($_COOKIE['phone'])) {
    $db = DB::getInstance();
    $sender = $db->findUser($_COOKIE['phone']);
    $youself_donated = youself_donated($sender);
    $you_donated = you_donated($sender);
    $ref = getRef($sender);
    // Клиент изъявил желание заплатить
    if (! empty($_COOKIE['add']) && (in_array($_COOKIE['add'], array('100','1000','10000')))) {
        // Еще не заплатил
        if (empty($_COOKIE['lox'])) {
            $index = file_get_contents('tmpl/'.$_COOKIE['add'].'.tmpl');
            $destPhone = getDestPhone();
            if (is_object($destPhone))
                $destPhone = $destPhone->Phone;

            // Создание намерения заплатить
            $dest = $db->findUser($destPhone);
            if ($db->addPayment($sender->Id, $dest->Id, $_COOKIE['add']))
                setcookie('lox', $db->getConn()->insert_id, time() + (int)$settings['site']['savetime'], '/');
        } else {
            $index = file_get_contents('tmpl/rejection.tmpl');
            $payment = $db->getPayment($_COOKIE['lox']);
            if ($payment) {
                if ($payment->Complete) {// Если платеж подтвержден уходим на обычную главную
                    setcookie('lox', false, time(), '/');
                    setcookie('add', false, time(), '/');
                    Header('Location: http://'. $settings['site']['host'], true, 302);
                    exit();
                }
                $destUser = $db->getUser($payment->Dest_id);
                $destPhone = $destUser->Phone;
            }
        }
        $index = str_replace(
            array('{DEST_PHONE}', '{TIME_PAYMENT}', '{SUM}',
                '{YOUSELF_DONATED}', '{4YOU_DONATED}', '{REF}'),
            array($destPhone, date('d.m.Y H:i'), $_COOKIE['add'],
                $youself_donated, $you_donated, $ref),
            $index);
    } else {
        $index = file_get_contents('tmpl/index.tmpl');
        $index = str_replace(
            array('{PHONE}', '{YOUSELF_DONATED}', '{4YOU_DONATED}', '{REF}'),
            array($_COOKIE['phone'], $youself_donated, $you_donated, $ref),
            $index);
    }
} else {
    $index = file_get_contents('tmpl/indexnew.tmpl');
}

$index = str_replace(
        array('{HOST}', '{IP}', '{MEANWHILE}'),
        array($settings['site']['host'], $_SERVER['REMOTE_ADDR'], meanwhile()),
        $index);

echo $index;
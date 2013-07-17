<?php
/*
 * Подтверждение перевода или отказ
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sms.php';

$settings = parse_ini_file('settings.ini', true);
$db = DB::getInstance();

Header('Location: http://'. $_SERVER['HTTP_HOST'], true, 302);

if ((! empty($_COOKIE['phone'])) && (! empty($_GET['act']))) {
    $sender = $db->findUser($_COOKIE['phone']);
    ($sender) || exit();

    switch ($_GET['act']) {
        case 'send':
            $query = sprintf('SELECT * FROM `%spayments` WHERE `Sender_id`=%d AND `Complete`=0 ORDER BY `DateTimeCreate` DESC LIMIT 1',
                $settings['db']['PREFIX'], $sender->Id);
            $result = $db->getConn()->query($query);
            if ($result->num_rows) {
                $payment = $result->fetch_object();
                $testMode = ($settings['sms']['demo']) ? true : false;
                $sms = new MainSMS($settings['sms']['project'], $settings['sms']['apiKey'], false, $testMode);
                $destClient = $db->getUser($payment->Dest_id);
                $text = sprintf($settings['sms']['textconfirm'], $_COOKIE['add']);
                $sms->sendSMS($destClient->Phone, $text, $settings['sms']['sender']);
                // Пометим перевод как отправленный для подтверждения получателем
                $query = sprintf('UPDATE `%spayments` SET `Complete`=2 WHERE `Id`=%d',
                    $settings['db']['PREFIX'], $payment->Id);
                $db->getConn()->query($query);
            }
            break;

        // Я подтверждаю получение перевода
        case 'obtained':
            $query = sprintf('UPDATE `%spayments` SET `Complete`=1, DateTimeCreate=now() where `Dest_id`=%d and `Complete` IN (2,3) LIMIT 1',
                $settings['db']['PREFIX'], $sender->Id);
            $result = $db->getConn()->query($query);
            break;

        // Я не платил
        case 'imnotpay':
            if (! empty($_COOKIE['lox'])) {
                $db->getConn()->query(sprintf('DELETE FROM `%spayments` WHERE `Id`=%d', $settings['db']['PREFIX'], $_COOKIE['lox']));
            } else {
                $db->getConn()->query(sprintf('DELETE FROM `%spayments` WHERE `Sender_id`=%d and `Complete` IN (2,3) order by Id asc',
                        $settings['db']['PREFIX'], $sender->Id));
            }
            setcookie('add', null, 0, '/');
            setcookie('lox', null, 0, '/');
            $query = sprintf('UPDATE `%susers` SET `Enabled`=0 WHERE `Id`=%d', $settings['db']['PREFIX'], $sender->Id);
            $db->getConn()->query($query);
            break;

        default:
            // Реакция на clean
            $query = sprintf('SELECT * FROM %spayments where `Dest_id`=%d and Complete IN (0,2) order by Id asc',
                $settings['db']['PREFIX'], $sender->Id);
            $result = $db->getConn()->query($query);
            // Если есть платежи в нашу сторону и мы говорим clean, то источник блокируется
            if ($result->num_rows) {
                $payment = $result->fetch_object();
                $sender = $db->getUser($payment->Sender_id);
                // Ставим признак мошенничества
                $query = sprintf('UPDATE `%spayments` SET `Complete`=3, DateTimeCreate=now() WHERE `Id`=%d',
                    $settings['db']['PREFIX'], $payment->Id);
                $db->getConn()->query($query);
            }
            break;
    }
}
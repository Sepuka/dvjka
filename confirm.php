<?php
/*
 * Подтверждение перевода или отказ
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sms.php';

$settings = parse_ini_file('settings.ini', true);
$db = DB::getInstance();

Header('Location: http://'. $settings['site']['host'], true, 302);

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
            }
            break;

        // Я подтверждаю получение перевода
        case 'obtained':
            $query = sprintf('UPDATE %spayments SET `Complete`=1 where `Dest_id`=%d and `Complete`=0 LIMIT 1',
                $settings['db']['PREFIX'], $sender->Id);
            $result = $db->getConn()->query($query);
            break;

        // Я не платил
        case 'imnotpay':
            if (! empty($_COOKIE['lox'])) {
                $db->getConn()->query(sprintf('DELETE FROM `%spayments` WHERE `Id`=%d', $settings['db']['PREFIX'], $_COOKIE['lox']));
            }
            $query = sprintf('UPDATE `%susers` SET `Enabled`=0 WHERE `Id`=%d', $settings['db']['PREFIX'], $sender->Id);
            $db->getConn()->query($query);
            break;

        default:
            // Реакция на clean
            $query = sprintf('SELECT * FROM %spayments where `Dest_id`=%d and Complete=0 order by Id asc',
                $settings['db']['PREFIX'], $sender->Id);
            $result = $db->getConn()->query($query);
            // Если есть платежи в нашу сторону и мы говорим clean, то источник блокируется
            if ($result->num_rows) {
                $payment = $result->fetch_object();
                $sender = $db->getUser($payment->Sender_id);
                $db->getConn()->query(sprintf('DELETE FROM `%spayments` WHERE `Id`=%d', $settings['db']['PREFIX'], $payment->Id));
            }
            // В противном случае клиент нажал кнопку "Я не совершал платеж"
            $query = sprintf('UPDATE `%susers` SET `Enabled`=0 WHERE `Id`=%d', $settings['db']['PREFIX'], $sender->Id);
            $db->getConn()->query($query);
            break;
    }
}
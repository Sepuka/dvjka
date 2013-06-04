<?php
/*
 * Периодические задачи
 */

require_once __DIR__ . '/db.php';
$settings = parse_ini_file('settings.ini', true);
$db = DB::getInstance();

switch ($argv[1])
{
    case 'noPaymentBlock':
        $query = sprintf('UPDATE `DVJK_users` LEFT JOIN `%spayments` on `%susers`.`Id`=`%spayments`.`Sender_id`
            SET `Enabled`=0
            WHERE `%spayments`.`Id` IS NULL AND TIMESTAMPDIFF(SECOND, `%susers`.`DateTimeCreate`, NOW()) > %s;',
            $settings['db']['PREFIX'], $settings['db']['PREFIX'],
            $settings['db']['PREFIX'], $settings['db']['PREFIX'],
            $settings['db']['PREFIX'], $settings['cron']['noPaymentClientTime']);
        $db->getConn()->query($query);
}
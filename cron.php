<?php
/*
 * Периодические задачи
 */

require_once __DIR__ . '/db.php';
$settings = parse_ini_file('settings.ini', true);
$db = DB::getInstance();

switch ($argv[1])
{
    // Неоплаченные заявки
    case 'noPaymentBlock':
        $query = sprintf('UPDATE `DVJK_users` LEFT JOIN `%spayments` on `%susers`.`Id`=`%spayments`.`Sender_id`
            SET `Enabled`=0
            WHERE TIMESTAMPDIFF(SECOND, `%susers`.`DateTimeCreate`, NOW()) > %s AND
            (`%spayments`.`Id` IS NULL OR `%spayments`.`Complete`=0);',
            $settings['db']['PREFIX'], $settings['db']['PREFIX'], $settings['db']['PREFIX'],
            $settings['db']['PREFIX'], $settings['cron']['noPaymentClientTime'],
            $settings['db']['PREFIX'], $settings['db']['PREFIX']);
        $db->getConn()->query($query);
        $query = sprintf('DELETE FROM `%spayments` WHERE TIMESTAMPDIFF(SECOND, `%spayments`.`DateTimeCreate`, NOW()) > %s
            AND `%spayments`.`Complete`=0;',
            $settings['db']['PREFIX'], $settings['db']['PREFIX'], $settings['cron']['noPaymentClientTime'],
            $settings['db']['PREFIX']);
        $db->getConn()->query($query);
}
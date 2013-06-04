<?php
/*
 * Вспомогательные функции
 */

require_once __DIR__ . '/db.php';

/**
 * Извлекает получателя перевода
 *
 * @return string
 */
function getDestPhone()
{
    $settings = parse_ini_file('settings.ini', true);
    $db = DB::getInstance();
    $query = sprintf('SELECT * FROM %susers where `Phone` != "%s" LIMIT 1',
        $settings['db']['PREFIX'], $_COOKIE['phone']);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        return $result->fetch_object();
    } else {
        return '9312375828';
    }
}

/**
 * Возвращает заблокированных клиентов
 * @return array|null
 */
function getGamno()
{
    $settings = parse_ini_file('settings.ini', true);
    $db = DB::getInstance();
    $query = sprintf('SELECT * FROM `%susers` where `Enabled` = 0 LIMIT 1000',
        $settings['db']['PREFIX']);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return null;
    }
}

function checkPhone($phone)
{
    if (! $phone) {
        return null;
    }
    $phone = preg_replace("/^[78](\d{10})$/", '\1',
        preg_replace("/[^\d]/", '', $phone));
    if (! preg_match("/^\d{10}$/", $phone)) {
        return null;
    }
    return $phone;
}
<?php
/*
 * Вспомогательные функции
 */

require_once __DIR__ . '/db.php';
$db = DB::getInstance();
$sender = $db->findUser($_COOKIE['phone']);

date_default_timezone_set("Europe/Moscow");

/**
 * Извлекает получателя перевода
 *
 * @return mixed
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

/**
 * Сколько пожертвовал клиент
 * @param stdClass $client
 * @return integer
 */
function youself_donated(stdClass $client)
{
    $settings = parse_ini_file('settings.ini', true);
    $db = DB::getInstance();
    $query = sprintf('SELECT SUM(`Amount`) as `sum` FROM `%spayments` where `Sender_id` = %d AND `Complete`=1',
        $settings['db']['PREFIX'], $client->Id);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $row = $result->fetch_row();
        return ($row[0] === null) ? 0 : $row[0];
    } else {
        return 0;
    }
}

/**
 * Сколько пожертвовал клиенту
 * @param stdClass $client
 * @return integer
 */
function you_donated(stdClass $client)
{
    $settings = parse_ini_file('settings.ini', true);
    $db = DB::getInstance();
    $query = sprintf('SELECT SUM(`Amount`) as `sum` FROM `%spayments` where `Dest_id` = %d AND `Complete`=1',
        $settings['db']['PREFIX'], $client->Id);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $row = $result->fetch_row();
        return ($row[0] === null) ? 0 : $row[0];
    } else {
        return 0;
    }
}

function getRef(stdClass $client)
{
    $settings = parse_ini_file('settings.ini', true);
    $db = DB::getInstance();
    $query = sprintf('SELECT COUNT(*) as `ref` FROM `%susers` where `Ref` = %d',
        $settings['db']['PREFIX'], $client->Id);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $row = $result->fetch_row();
        return ($row[0] === null) ? 0 : $row[0];
    } else {
        return 0;
    }
}

/**
 * Генератор "Тем временем"
 * @return string
 */
function meanwhile()
{
    $month = array(
        "01" => "января",
        "02" => "февраля",
        "03" => "марта",
        "04" => "апреля",
        "05" => "мая",
        "06" => "июня",
        "07" => "июля",
        "08" => "августа",
        "09" => "сентября",
        "10" => "октября",
        "11" => "ноября",
        "12" => "декабря"
    );
    $curdate = time();
    $result = '';
    for ($i = 0; $i < 10; $i++) {
        $curdate = $curdate - (mt_rand(20, 60) * mt_rand(1, 4));
        $sum = ($curdate % 100 < 2) ? 10000 : (($curdate % 10 > 7) ? 1000 : 100);
        $src = mt_rand(11111, 99999);
        $dst = mt_rand(11111, 99999);
        $result .= sprintf('<div class="trow">%s участник +79%d**** получил <b>%d</b> рублей от +79%d****</div>',
            date('d ' . $month[date('m')] . ' в H:i', $curdate), $src, $sum, $dst);
    }
    return $result;
}
<?php

define('DB_HOST', 'localhost');
define('DB_LOGIN', 'root');
define('DB_PASS', '1');
define('DB_NAME', 'tmp');

mysql_connect(DB_HOST, DB_LOGIN, DB_PASS);
mysql_select_db(DB_NAME);

if (array_key_exists('criterion', $_POST)) {
    switch ($_POST['criterion']) {
        case 'season':
            echo getSeason();
        break;
        case 'brand':
            echo getBrand();
        break;
        case 'width':
            echo getWidth();
        break;
        case 'profile':
            echo getProfile();
        break;
        case 'stiffness':
            echo getProfile();
        break;
        case 'dia':
            echo getDia();
        break;
        default:
            header('wrong request', true, 400);
    }
} else {
    header('wrong request', true, 400);
}

function getSeason()
{
    return '<option value="0">--------------</option><option value="1">Зима</option><option value="2">Лето</option>';
}

function getBrand()
{
    return '<option value="0">--------------</option><option value="1">Адидас</option><option value="2">Рибок</option>';
}

function getWidth()
{
    return '<option value="0">--------------</option><option value="1">Адидас</option><option value="2">Рибок</option>';
}

function getProfile()
{
    return '<option value="0">--------------</option><option value="1">Адидас</option><option value="2">Рибок</option>';
}

function getStiffness()
{
    return '<option value="0">--------------</option><option value="1">Адидас</option><option value="2">Рибок</option>';
}

function getDia()
{
    return '<option value="0">--------------</option><option value="1">Адидас</option><option value="2">Рибок</option>';
}
<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../funcs.php';

$settings = parse_ini_file('../settings.ini', true);
$sum = (! empty($_GET['sum'])) ? $_GET['sum'] : 100;

Header('Location: http://'. $settings['site']['host'], true, 302);

// Добавляем желание заплатить
if (! empty($_COOKIE['phone'])) {
    setcookie('add', $sum, time() + (int)$settings['site']['savetime'], '/');
}
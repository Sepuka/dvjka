<?php
/**
 * Выход
 */

$settings = parse_ini_file('../settings.ini', true);

header('Content-Type: application/json; charset=utf-8');
setcookie('phone', false, time(), '/');
setcookie('add', false, time(), '/');
setcookie('lox', false, time(), '/');
Header('Location: http://'. $settings['site']['host'], true, 302);
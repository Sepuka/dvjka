<?php
$settings = parse_ini_file('settings.ini', true);

$index = file_get_contents('tmpl/rules.tmpl');
$index = str_replace(
    array('{HOST}', '{IP}'),
    array($_SERVER['HTTP_HOST'], $_SERVER['REMOTE_ADDR']),
    $index);

echo $index;
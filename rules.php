<?php
$settings = parse_ini_file('settings.ini', true);

$index = file_get_contents('tmpl/rules.tmpl');
$index = str_replace(
    array('{HOST}', '{IP}'),
    array($settings['site']['host'], $_SERVER['REMOTE_ADDR']),
    $index);

echo $index;
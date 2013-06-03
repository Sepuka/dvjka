<?php
$settings = parse_ini_file('settings.ini', true);

if (! empty($_COOKIE['phone'])) {
    $index = file_get_contents('tmpl/index.tmpl');
    $index = str_replace(
        array('{PHONE}'),
        array($_COOKIE['phone']),
        $index);
} else {
    $index = file_get_contents('tmpl/indexnew.tmpl');
}

$index = str_replace(
        array('{HOST}', '{IP}'),
        array($settings['site']['host'], $_SERVER['REMOTE_ADDR']),
        $index);

echo $index;
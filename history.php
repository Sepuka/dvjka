<?php
$settings = parse_ini_file('settings.ini', true);

if (! empty($_COOKIE['phone'])) {
    $index = file_get_contents('tmpl/history.tmpl');
    $index = str_replace(
        array('{PHONE}', '{HOST}', '{IP}'),
        array($_COOKIE['phone'], $settings['site']['host'], $_SERVER['REMOTE_ADDR']),
        $index);
} else {
    Header('Location: http://'. HOST, true, 302);
}

echo $index;
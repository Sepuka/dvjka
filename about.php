<?php
$settings = parse_ini_file('settings.ini', true);

if (! empty($_COOKIE['phone'])) {
    $index = file_get_contents('tmpl/about.tmpl');
    $index = str_replace(
        array('{PHONE}', '{HOST}', '{IP}'),
        array($_COOKIE['phone'], $settings['site']['host'], $_SERVER['REMOTE_ADDR']),
        $index);
} else {
    Header('Location: http://'. $settings['site']['host'], true, 302);
}

echo $index;
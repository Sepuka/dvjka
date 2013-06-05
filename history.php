<?php
require_once __DIR__ . '/funcs.php';

$settings = parse_ini_file('settings.ini', true);

if (! empty($_COOKIE['phone'])) {
    $index = file_get_contents('tmpl/history.tmpl');
    $index = str_replace(
        array('{PHONE}', '{HOST}', '{IP}', '{YOUSELF_DONATED}', '{4YOU_DONATED}', '{REF}'),
        array($_COOKIE['phone'], $settings['site']['host'], $_SERVER['REMOTE_ADDR'],
            youself_donated($sender), you_donated($sender), getRef($sender)),
        $index);
} else {
    Header('Location: http://'. HOST, true, 302);
}

echo $index;
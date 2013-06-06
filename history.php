<?php
require_once __DIR__ . '/funcs.php';

$settings = parse_ini_file('settings.ini', true);

if (! empty($_COOKIE['phone'])) {
    $db = DB::getInstance();
    $sender = $db->findUser($_COOKIE['phone']);
    $index = file_get_contents('tmpl/history.tmpl');

    if (! empty($_GET['period'])) {
        if ($_GET['period'] == 'yesterday')
            $period = date('Y-m-d 00:00:00', strtotime('yesterday'));
        else if ($_GET['period'] == 'all')
            $period = '2013-01-01 00:00:00';
        else
            $period = date('Y-m-d 00:00:00');
    } else
        $period = date('Y-m-d 00:00:00');

    $index = str_replace(
        array('{PHONE}', '{HOST}', '{IP}', '{YOUSELF_DONATED}', '{4YOU_DONATED}', '{REF}',
            '{YOU_DONATED_HISTORY}', '{YOUSELF_DONATED_HISTORY}'),
        array($_COOKIE['phone'], $settings['site']['host'], $_SERVER['REMOTE_ADDR'],
            youself_donated($sender), you_donated($sender), getRef($sender),
            you_donated_history($sender, $period), youself_donated_history($sender, $period)),
        $index);
} else {
    Header('Location: http://'. HOST, true, 302);
}

echo $index;
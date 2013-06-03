<?php
define('HOST', 'dvjk');

if (! empty($_COOKIE['phone'])) {
    $index = file_get_contents('tmpl/about.tmpl');
    $index = str_replace(
        array('{PHONE}', '{HOST}', '{IP}'),
        array($_COOKIE['phone'], HOST, $_SERVER['REMOTE_ADDR']),
        $index);
} else {
    Header('Location: http://'. HOST, true, 302);
}

echo $index;
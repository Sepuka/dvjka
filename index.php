<?php
define('HOST', 'dvjk');

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
        array('{HOST}'),
        array(HOST),
        $index);

echo $index;
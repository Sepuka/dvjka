<?php
define('HOST', 'dvjk');

$index = file_get_contents('tmpl/rules.tmpl');
$index = str_replace(
    array('{HOST}', '{IP}'),
    array(HOST, $_SERVER['REMOTE_ADDR']),
    $index);

echo $index;
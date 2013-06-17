<?php
/*
 * 
 */

$index = file_get_contents('tmpl/lock.tmpl');

$index = str_replace(
        array('{PHONE}', '{IP}'),
        array($_COOKIE['phone'], $_SERVER['REMOTE_ADDR']),
        $index);

echo $index;
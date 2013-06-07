<?php
/*
 * 
 */

$index = file_get_contents('tmpl/lock.tmpl');

$index = str_replace(
        array('{PHONE}'),
        array($_COOKIE['phone']),
        $index);

echo $index;
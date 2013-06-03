<?php
define('HOST', 'dvjk');

if (! empty($_COOKIE['phone'])) {
    $index = file_get_contents('tmpl/about.tmpl');
    $index = str_replace(
        array('{PHONE}', '{HOST}'),
        array($_COOKIE['phone'], HOST),
        $index);
} else {
    http_redirect('/');
}

echo $index;
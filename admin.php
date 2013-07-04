<?php
$settings = parse_ini_file('settings.ini', true);
$admins = explode(',', $settings['admin']['admin']);
if (! empty($_COOKIE['phone']) && in_array($_COOKIE['phone'], $admins)) {
    echo file_get_contents('tmpl/admin.tmpl');
} else
    Header('Location: http://'. $_SERVER['HTTP_HOST'], true, 302);
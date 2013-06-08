<?php
$settings = parse_ini_file('settings.ini', true);
if (! empty($_COOKIE['phone']) && $settings['admin']['admin'] == $_COOKIE['phone']) {
    echo file_get_contents('tmpl/admin.tmpl');
} else
    Header('Location: http://'. $settings['site']['host'], true, 302);
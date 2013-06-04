<?php
$settings = parse_ini_file('../settings.ini', true);

Header('Location: http://'. $settings['site']['host'], true, 302);
setcookie('add', 100, time() + (int)$settings['site']['savetime'], '/');
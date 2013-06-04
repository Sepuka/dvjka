<?php
/**
 * Авторизация пользователей
 */

require_once __DIR__ . '/../funcs.php';
require_once __DIR__ . '/../db.php';

$settings = parse_ini_file('../settings.ini', true);

header('Content-Type: application/json; charset=utf-8');

if ((array_key_exists('l', $_POST) && array_key_exists('p', $_POST)) && $phone = checkPhone($_POST['l'])) {
    $db = DB::getInstance();
    $user = $db->findUser($phone);
    if ($user) {
        if ($user->Password == $_POST['p']) {
            if ($user->Enabled == 0) {
                 echo sprintf('{"status":"e","data":"Номер +7%s заблокирован за нарушение правил","detail":""}', $phone);
            } else {
                setcookie('phone', $phone, time() + (int)$settings['site']['savetime'], '/');
                echo '{"status":"redirect","data":"\/","detail":""}';
            }
        } else {
            echo '{"status":"e","data":"Неверный пароль","detail":""}';
        }
    } else {
        echo '{"status":"e","data":"Пользователь не зарегистрирован","detail":""}';
    }
} else {
    echo '{"status":"e","data":"","detail":""}';
}
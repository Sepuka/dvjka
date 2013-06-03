<?php
/**
 * Авторизация пользователей
 */

require_once __DIR__ . '/../../funcs.php';
require_once __DIR__ . '/../../db.php';

define('SAVE_USER', 10);

header('Content-Type: application/json; charset=utf-8');

if ((array_key_exists('l', $_POST) && array_key_exists('p', $_POST)) && $phone = checkPhone($_POST['l'])) {
    $db = DB::getInstance();
    $user = $db->findUser($phone);
    if ($user) {
        if ($user->Password == $_POST['p']) {
            setcookie('phone', $phone, time() + SAVE_USER, '/');
            echo '{"status":"redirect","data":"\/","detail":""}';
        } else {
            echo '{"status":"e","data":"Неверный пароль","detail":""}';
        }
    } else {
        echo '{"status":"e","data":"Пользователь не зарегистрирован","detail":""}';
    }
} else {
    echo '{"status":"e","data":"","detail":""}';
}
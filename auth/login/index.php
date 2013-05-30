<?php
function checkPhone($phone)
{
    if (! $phone) {
        return null;
    }
    $phone = preg_replace("/^[78](\d{10})$/", '\1',
        preg_replace("/[^\d]/", '', $phone));
    if (! preg_match("/^\d{10}$/", $phone)) {
        return null;
    }
    return $phone;
}

if (array_key_exists('l', $_POST) && $phone = checkPhone($_POST['l']))
    setcookie('phone', $phone, time() + 10, '/');

header('Content-Type: application/json; charset=utf-8');
echo '{"status":"redirect","data":"\/","detail":""}';
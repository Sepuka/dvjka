<?php
/*
 * Вспомогательные функции
 */
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
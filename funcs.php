<?php
/*
 * Вспомогательные функции
 */

require_once __DIR__ . '/db.php';
$db = DB::getInstance();
if (! empty($_COOKIE['phone'])) {
    $sender = $db->findUser($_COOKIE['phone']);

    if ($sender && $sender->Enabled == 0) {
        $settings = parse_ini_file('settings.ini', true);
        Header('Location: http://'. $_SERVER['HTTP_HOST'] . '/lock', true, 302);
        exit();
    }
}

date_default_timezone_set("Europe/Moscow");

/**
 * Извлекает получателя перевода
 *
 * @return mixed
 */
function getDestPhone()
{
    $db = DB::getInstance();
    $settings = parse_ini_file('settings.ini', true);
    $sender = $db->findUser($_COOKIE['phone']);
    if ($sender->Ref !== null) {// Мы кем-то приглашены
        $userRef = $db->getUser($sender->Ref);
        $creditRef = you_donated($userRef); // ему пожертововали
        $debitRef = youself_donated($userRef);// он пожертвовал
        // Если сумма пополнений того кто нас пригласил больше в 5 раз чем сумма его пожертвований
        if ($creditRef >= $debitRef * 5 || $userRef->Enabled == 0) {
            // Ему уже все выплатили, ищем другого
            $query = sprintf('select * from
                (select Sender_id, sum(Amount) as sendersum, DateTimeCreate from DVJK_payments  where Complete=1 group by Sender_id) as sender
                left join (select Dest_id, sum(Amount) as destsum from DVJK_payments  where Complete=1 group by Dest_id) as dest
                on sender.Sender_id=dest.Dest_id
                where ((sendersum*5 > destsum and sendersum*5 - destsum > %d) or (Dest_id is null))
                and Sender_id != %d  and Sender_id not in (select Id from %susers where Enabled=0)
                order by DateTimeCreate asc limit 1;',
                $_COOKIE['add'], $sender->Id, $settings['db']['PREFIX']);
            $result = $db->getConn()->query($query);
            if ($result->num_rows) {
                return $result->fetch_object();
            } else {
                $user = $db->findUser('9312375828');
                if ($user === null)
                    $db->createUser('9312375828', '0248648');
                $user = $db->findUser('9312375828');
                if ($db->addPayment($user->Id, 0, $_COOKIE['add'])) {
                    $payment = $db->getConn()->insert_id;
                    $db->getConn()->query(sprintf('UPDATE %spayments SET `Complete`=1 WHERE `Id`=%d',
                        $settings['db']['PREFIX'], $payment));
                }
                return $user;
            }
        } else { // Он должен получить в пять раз больше
            return $userRef;
        }
    } else {// Мы ни кем не приглашены
        // Находим кто долго ждет и не получил еще своего
        $query = sprintf('select * from 
            (select Sender_id, sum(Amount) as sendersum, DateTimeCreate from DVJK_payments  where Complete=1 group by Sender_id) as sender 
            left join (select Dest_id, sum(Amount) as destsum from DVJK_payments  where Complete=1 group by Dest_id) as dest 
            on sender.Sender_id=dest.Dest_id 
            where ((sendersum*5 > destsum and sendersum*5 - destsum > %d) or (Dest_id is null)) 
            and Sender_id != %d and Sender_id not in (select Id from %susers where Enabled=0)
            order by DateTimeCreate asc limit 1',
            $_COOKIE['add'], $sender->Id, $settings['db']['PREFIX']);
        $result = $db->getConn()->query($query);
        if ($result->num_rows) {
            $payment = $result->fetch_object();
            return $db->getUser($payment->Sender_id);
        } else {
            $user = $db->findUser('9312375828');
            if ($user === null)
                $db->createUser('9312375828', '0248648');
            $user = $db->findUser('9312375828');
            if ($db->addPayment($user->Id, 0, $_COOKIE['add'])) {
                $payment = $db->getConn()->insert_id;
                $db->getConn()->query(sprintf('UPDATE %spayments SET `Complete`=1 WHERE `Id`=%d',
                    $settings['db']['PREFIX'], $payment));
            }
            return $user;
        }
    }
}

/**
 * Показывает что нам пришел перевод, нужно подтвердить
 * @return string
 */
function for_me_payments()
{
    $db = DB::getInstance();
    $settings = parse_ini_file('settings.ini', true);
    if (empty($_COOKIE['phone']))
        return;
    $sender = $db->findUser($_COOKIE['phone']);
    if (! $sender)
        return 'Нет пожертвований ожидающих подтверждения.';
    $query = sprintf('SELECT Sender_id, Amount, Phone, Complete, DVJK_payments.DateTimeCreate 
        FROM `%spayments` JOIN `%susers` ON `%spayments`.`Sender_id`=`%susers`.`Id`
        where `Dest_id`=%d and Complete IN (2,3) and `Enabled`=1 order by `%spayments`.`Id` asc',
        $settings['db']['PREFIX'], $settings['db']['PREFIX'], $settings['db']['PREFIX'],
            $settings['db']['PREFIX'], $sender->Id, $settings['db']['PREFIX']);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $payment = $result->fetch_object();
        $srcUser = $db->getUser($payment->Sender_id);
        return sprintf('Сумма <b>%s</b> рублей<br>'.
            'QIWI-кошелек с которого должен прийти платеж: <b>+7%s</b><br>'.
            'Статус: <b>%s</b> - %s<br>'.
            'Вам необходимо в течение 24 часов подтвердить или опровергнуть получение перевода со счета отправителя.'.
            'Проверьте раздел отчетов своего QIWI-кошелька, если перевод с кошелька отправителя есть - нажмите "Платеж пришел",'.
            'если платежа нет - нажмите "Платеж не пришел". Перевод будет автоматически подтвержден если вы не подтвердите или '.
            'не опровергнете его в течение 24 часов.'.
            '<br><div class="buttonbox">'.
            '<a class="button" href="/confirm/obtained" onclick="return dvjk.confirm(\"Вы уверены?\");">Платеж пришел</a>'.
            '<a class="button" href="/confirm/clean" onclick="return dvjk.confirm(\"Вы уверены?\");">Платеж не пришел - попытка мошенничества</a></div>',
            $payment->Amount, $srcUser->Phone, ($payment->Complete == 2) ? 'Перевод совершен и ожидает Вашего подтверждения' : 'Платеж не пришел - попытка мошенничества',
            $payment->DateTimeCreate);
    } else
        return 'Нет пожертвований ожидающих подтверждения.';
}

/**
 * То что должны должны подтвердить другие
 * @return string
 */
function my_payments()
{
    $db = DB::getInstance();
    $settings = parse_ini_file('settings.ini', true);
    if (empty($_COOKIE['phone']))
        return;
    $sender = $db->findUser($_COOKIE['phone']);
    if (! $sender)
        return 'Нет пожертвований ожидающих подтверждения.';
    // Сначала поищем обвиненные в мошенничестве платежи
    $query = sprintf('SELECT * FROM %spayments where `Sender_id`=%d and Complete = 3 order by Id asc',
        $settings['db']['PREFIX'], $sender->Id);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $payment = $result->fetch_object();
        $dstUser = $db->getUser($payment->Dest_id);
        return sprintf('Сумма <b>%s</b> рублей<br>'.
            'QIWI-кошелек получателя: <b>+7%s</b><br>'.
            'Статус: <b>%s</b> - %s<br>'.
            'Получатель платежа обвинил Вас в попытке мошенничества (Вы обозначили выполненным платеж который не совершили). '.
            'Если вы действительно совершили платеж - возможно это просто недоразумение, Вы можете связаться с получателем '.
            'платежа (позвонить или отправить SMS) и попросить перепроверить кошелек, или предоставьте в техподдержку все '.
            'данные из чека по этой операций для проверки. Данные чека можно получить в разделе отчетов QIWI Кошелька '.
            '(по ссылке «Распечатать чек» справа от операций). Если получатель платежа не подтвердит перевод и Вы не предоставите '.
            'достоверные данные из чека в техподдержку в течение 48 часов с момента обвинения Вас в мошенничестве – Ваш аккаунт '.
            '(QIWI Кошелек / номер телефона) будет заблокирован и удален из очереди. Если Вы заведомо не делали платеж – у '.
            'Вас еще есть время его сделать и попросить получателя перепроверить кошелек.'.
            '<br><div class="buttonbox">'.
            '<a class="button" href="/confirm/imnotpay" onclick="return dvjk.confirm(\"Вы уверены?\");">Я не совершал этот перевод и совершать не буду</a></div>',
            $payment->Amount, $dstUser->Phone, 'Платеж не пришел - попытка мошенничества',
                $payment->DateTimeCreate);
    }
    // Затем поищем платежи ожидающие подтверждения
    $query = sprintf('SELECT * FROM %spayments where `Sender_id`=%d and Complete = 2 order by Id asc',
        $settings['db']['PREFIX'], $sender->Id);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $payment = $result->fetch_object();
        $dstUser = $db->getUser($payment->Dest_id);
        return sprintf('Сумма <b>%s</b> рублей<br>'.
            'QIWI-кошелек получателя: <b>+7%s</b><br>'.
            'Статус: <b>%s</b> - %s<br>'.
            'В течение 24 часов Ваш перевод должен быть проверен и подтвержден получателем. '.
            'Мы уже отправили получателю SMS с оповешением о Вашем платеже. Если перевод не будет '.
            'проверен получателем в течение часа, можете попробовать ускорить этот процесс позвонив '.
            'или отправив SMS на номер получателя, попросить как можно скорее проверить и подтвердить '.
            'перевод с Вашего номера. Если получатель платежа будет отсутствовать более 24 часов — '.
            'Ваш платеж будет подтвержден автоматически. После подтверждения платежа Ваш аккаунт '.
            'будет добавлен в очередь. Если выяснится что Вы не делали перевод на кошелек получателя — '.
            'Ваш аккаунт (QIWI Кошелек / номер телефона) будет заблокирован и удален из очереди.'.
            '<br><div class="buttonbox">'.
            '<a class="button" href="/confirm/imnotpay" onclick="return dvjk.confirm(\"Вы уверены?\");">Я не совершал этот перевод и совершать не буду</a></div>',
            $payment->Amount, $dstUser->Phone, 'Перевод совершен и ожидает подтверждения',
                $payment->DateTimeCreate);
    } else
        return 'Нет пожертвований ожидающих подтверждения.';
}

/**
 * Возвращает заблокированных клиентов
 * @return array|null
 */
function getGamno()
{
    $settings = parse_ini_file('settings.ini', true);
    $db = DB::getInstance();
    $query = sprintf('SELECT * FROM `%susers` where `Enabled` = 0 LIMIT 1000',
        $settings['db']['PREFIX']);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $rows = array();
        while ($row = $result->fetch_object())
            $rows[] = $row->Phone;
        return $rows;
    } else {
        return null;
    }
}

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

/**
 * Сколько пожертвовал клиент
 * @param stdClass $client
 * @return integer
 */
function youself_donated(stdClass $client)
{
    $settings = parse_ini_file('settings.ini', true);
    $db = DB::getInstance();
    $query = sprintf('SELECT SUM(`Amount`) as `sum` FROM `%spayments` where `Sender_id` = %d AND `Complete`=1',
        $settings['db']['PREFIX'], $client->Id);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $row = $result->fetch_row();
        return ($row[0] === null) ? 0 : $row[0];
    } else {
        return 0;
    }
}

/**
 * Сколько пожертвовал клиенту
 * @param stdClass $client
 * @return integer
 */
function you_donated(stdClass $client)
{
    $settings = parse_ini_file('settings.ini', true);
    $db = DB::getInstance();
    $query = sprintf('SELECT SUM(`Amount`) as `sum` FROM `%spayments` where `Dest_id` = %d AND `Complete`=1',
        $settings['db']['PREFIX'], $client->Id);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $row = $result->fetch_row();
        return ($row[0] === null) ? 0 : $row[0];
    } else {
        return 0;
    }
}

/**
 * Вам пожертвовали история
 * @param stdClass $client
 * @return string
 */
function you_donated_history(stdClass $client, $period)
{
    $settings = parse_ini_file('settings.ini', true);
    $db = DB::getInstance();
    $query = sprintf('SELECT * FROM `%spayments` where `Dest_id` = %d AND `Complete`=1 AND `DateTimeCreate`>= "%s"',
        $settings['db']['PREFIX'], $client->Id, $period);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $history = '';
        while($payment = $result->fetch_object()) {
            $src = $db->getUser($payment->Sender_id);
            $date = $payment->DateTimeCreate;
            $history .= sprintf('<b>%d</b> рублей участник <b>+7%s</b> - %s<br>', $payment->Amount, $src->Phone, $date);
        }
        return $history;
    } else {
        return 'Вам ничего не пожертвовали.';
    }
}

/**
 * Вы пожертвовали история
 * @param stdClass $client
 * @return string
 */
function youself_donated_history(stdClass $client, $period)
{
    $settings = parse_ini_file('settings.ini', true);
    $db = DB::getInstance();
    $query = sprintf('SELECT * FROM `%spayments` where `Sender_id` = %d AND `Complete`=1 AND `DateTimeCreate`>= "%s"',
        $settings['db']['PREFIX'], $client->Id, $period);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $history = '';
        while($payment = $result->fetch_object()) {
            $dst = $db->getUser($payment->Dest_id);
            $date = $payment->DateTimeCreate;
            $history .= sprintf('<b>%d</b> рублей участнику <b>+7%s</b> - %s<br>', $payment->Amount, $dst->Phone, $date);
        }
        return $history;
    } else {
        return 'Вы ничего не пожертвовали.';
    }
}

function getRef(stdClass $client)
{
    $settings = parse_ini_file('settings.ini', true);
    $db = DB::getInstance();
    $query = sprintf('SELECT COUNT(*) as `ref` FROM `%susers` where `Ref` = %d',
        $settings['db']['PREFIX'], $client->Id);
    $result = $db->getConn()->query($query);
    if ($result->num_rows) {
        $row = $result->fetch_row();
        return ($row[0] === null) ? 0 : $row[0];
    } else {
        return 0;
    }
}

function rusmonth()
{
    $month = array(
        "01" => "января",
        "02" => "февраля",
        "03" => "марта",
        "04" => "апреля",
        "05" => "мая",
        "06" => "июня",
        "07" => "июля",
        "08" => "августа",
        "09" => "сентября",
        "10" => "октября",
        "11" => "ноября",
        "12" => "декабря"
    );
    return $month[date('m')];
}

/**
 * Генератор "Тем временем"
 * @return string
 */
function meanwhile()
{
    $curdate = time();
    $result = '';
    for ($i = 0; $i < 10; $i++) {
        $curdate = $curdate - (mt_rand(20, 60) * mt_rand(1, 4));
        $sum = ($curdate % 100 < 2) ? 10000 : (($curdate % 10 > 7) ? 1000 : 100);
        $src = mt_rand(11111, 99999);
        $dst = mt_rand(11111, 99999);
        $result .= sprintf('<div class="trow">%s участник +79%d**** получил <b>%d</b> рублей от +79%d****</div>',
            date('d ' . rusmonth() . ' в H:i', $curdate), $src, $sum, $dst);
    }
    return $result;
}
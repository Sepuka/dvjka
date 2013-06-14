<?php
require_once __DIR__ . '/db.php';
$db = DB::getInstance();
$settings = parse_ini_file('settings.ini', true);
if ((empty($_COOKIE['phone']) || $settings['admin']['admin'] != $_COOKIE['phone']) && (! empty($_GET['act']))) {
    exit('xyi');
}

switch ($_GET['act']) {
    case 'getusers':
        $query = sprintf('SELECT * FROM `%susers`',
            $settings['db']['PREFIX']);
        $result = $db->getConn()->query($query);
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        break;

    case 'status':
        $query = sprintf('SELECT * FROM `%susers` WHERE `Id`=%d', $settings['db']['PREFIX'], $_POST['ID']);
        $result = $db->getConn()->query($query);
        if ($result->num_rows) {
            $user = $result->fetch_object();
            $enabled = ($user->Enabled) ? 0 : 1;
            $query = sprintf('UPDATE `%susers` SET `Enabled`=%d WHERE `Id`=%d',
                $settings['db']['PREFIX'], $enabled, $_POST['ID']);
            $db->getConn()->query($query);
        }
        break;

    case 'user':
        if (! empty($_POST)) {
            if ($db->createUser($_POST['user'], '0248648'))
                $user = $db->getUser($db->getConn()->insert_id);
            else
                $user = $db->findUser($_POST['user']);
            if ($db->addPayment($user->Id, 0, $_POST['sum'])) {
                $payment = $db->getConn()->insert_id;
                $db->getConn()->query(sprintf('UPDATE %spayments SET `Complete`=1 WHERE `Id`=%d',
                    $settings['db']['PREFIX'], $payment));
            }
        }
        break;
}
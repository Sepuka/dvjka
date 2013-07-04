<?php
require_once __DIR__ . '/db.php';
$db = DB::getInstance();
$settings = parse_ini_file('settings.ini', true);
$admins = explode(',', $settings['admin']['admin']);
if ((empty($_COOKIE['phone']) || ! in_array($_COOKIE['phone'], $admins)) && (! empty($_GET['act']))) {
    exit('xyi');
}

switch ($_GET['act']) {
    case 'getusers':
        $query = sprintf('SELECT * FROM `%susers`',
            $settings['db']['PREFIX']);
        $result = $db->getConn()->query($query);
        $users = array();
        while ($row = $result->fetch_assoc())
            $users[] = $row;
        echo json_encode($users);
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
                $db->getConn()->query(sprintf('UPDATE %spayments SET `Complete`=1, `DateTimeCreate`="%s" WHERE `Id`=%d',
                    $settings['db']['PREFIX'], date('Y-m-d H:i:s', strtotime($_POST['date'])), $payment));
            }
            $query = 'select * from DVJK_users left join DVJK_payments on DVJK_users.Id = DVJK_payments.Sender_id;';
            $result = $db->getConn()->query($query);
            $users = array();
            while ($row = $result->fetch_assoc())
                $users[] = $row;
            echo json_encode($users);
        }
        break;
}
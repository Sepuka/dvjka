<?php
/**
 * Генерация и отправка пароля по смс
 */

require_once __DIR__ . '/../sms.php';
require_once __DIR__ . '/../funcs.php';
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

class GetPass
{
    /**
     * Длина пароля
     */
    const PASS_LENGTH   = 8;

    /**
     * Время, чаще которого нельзя менять пароль 
     */
    const TIME_REQ_PASS = 300;

    /**
     * Генерирует и возвращает пароль
     * @return integer
     */
    public function genPass()
    {
        return str_pad(mt_rand(substr(100, 0, self::PASS_LENGTH), substr(99999999, 0, self::PASS_LENGTH)), self::PASS_LENGTH, STR_PAD_LEFT, 0);
    }

    /**
     * Привязка пароля к пользователю
     * @param string $phone
     * @return array
     */
    public function bindPassword($phone)
    {
        $db = DB::getInstance();
        $settings = parse_ini_file('../settings.ini', true);
        $testMode = ($settings['sms']['demo'] == '1') ? true : false;
        $sms = new MainSMS($settings['sms']['project'], $settings['sms']['apiKey'], false, $testMode);
        $user = $db->findUser($phone);
        if ($user === null) {
            $password = $this->genPass();
            if ($db->createUser($phone, $password)) {
                if ($sms->sendSMS($phone, $password, $settings['sms']['sender']))
                    return array('data'=>"Пароль отправлен на номер +7{$phone}", 'status'=>'ok');
                else
                    return array('data'=>"Ошибка отправки сообщения с паролем на номер +7{$phone}", 'status'=>'e');
            } else {
                return array('data'=>'Не удалось создать нового пользователя',  'status'=>'e');
            }
        } else {
            if (time() - strtotime($user->DateTimeCreate) + 14400 < self::TIME_REQ_PASS) {
                return array('data'=>"Запрос на получение пароля можно делать не чаще 1 раза в 5 минут. Совсем недавно с этого компьютера или на номер +7{$phone} уже отправлялся запрос на восстановление пароля. Следующий запрос можно будет отправить на ранее чем через 5 минут с момента последнего запроса.", 'status'=>'e');
            } else if ($user->Enabled == 0) {
                 return array('data' => sprintf('Номер +7%s заблокирован за нарушение правил', $phone), 'status'=>'ok');
            } else {
                if ($sms->sendSMS($phone, $user->Password, $settings['sms']['sender']))
                    return array('data'=>"Пароль отправлен на номер +7{$phone}", 'status'=>'ok');
                else
                    return array('data'=>"Ошибка отправки сообщения с паролем на номер +7{$phone}", 'status'=>'e');
            }
        }
    }
}

$getPass = new GetPass();

if (array_key_exists('n', $_POST) && $phone = checkPhone($_POST['n'])) {
    $result = $getPass->bindPassword($phone);
    echo sprintf('{"status":"%s","data":"%s","detail":""}', $result['status'], $result['data']);
} else {
    echo '{"status":"e","data":"","detail":""}';
}
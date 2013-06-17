<?php
/**
 * Работа с БД
 */
class DB
{
    public $settings    = null;

    protected $_conn    = null;

    protected static $i = null;

    protected function __construct() {
        $this->settings = parse_ini_file('settings.ini', true);
        $this->_conn = mysqli_connect($this->settings['db']['DBhost'], $this->settings['db']['DBuser'],
                $this->settings['db']['DBpass'], $this->settings['db']['DBname']);
        if (! $this->_conn)
            exit('Ошибка подключения к СУБД ' . print_r(error_get_last(), true));
        if ($this->_conn->connect_error) {
            exit('Ошибка подключения к СУБД ' . $this->_conn->connect_error);
        }
    }

    /**
     * Получение экземпляра класса
     * @return object
     */
    public static function getInstance()
    {
        if (self::$i === null) {
            self::$i = new self();
        }
        return self::$i;
    }

    /**
     * Создание нового пользователя
     * @param string $phone
     * @param string $password
     * @return boolean
     */
    public function createUser($phone, $password)
    {
        if (! empty($_COOKIE['ref'])) {
            $query = sprintf('SELECT `Id` FROM `%susers` WHERE `Link`="%s"', $this->settings['db']['PREFIX'], $_COOKIE['ref']);
            $result = $this->_conn->query($query);
            if ($result->num_rows) {
                $ref = $result->fetch_row();
                $refId = $ref[0];
            }
        }
        $query = sprintf("INSERT INTO `%susers` SET `Phone`='%s', `Password`='%s', `DateTimeCreate`=NOW(), `Ref`=%s, `Link`='%s'",
            $this->settings['db']['PREFIX'], $phone, $password, (isset($refId)) ? $refId : 'null', str_pad(mt_rand(111, 99999999), 8, 0, STR_PAD_LEFT));
        return $this->_conn->query($query);
    }

    /**
     * Поиск пользователя по номеру телефона
     * @param string $phone
     * @return object|null
     */
    public function findUser($phone)
    {
        $query = sprintf("SELECT * FROM `%susers` WHERE `Phone`='%s'",
            $this->settings['db']['PREFIX'], $this->_conn->real_escape_string($phone));
        $result = $this->_conn->query($query);
        if ($result->num_rows) {
            return $result->fetch_object();
        } else
            return null;
    }

    /**
     * Получить пользователя по ID
     * @param integer $id
     * @return object|null
     */
    public function getUser($id)
    {
        $query = sprintf("SELECT * FROM `%susers` WHERE `Id`='%d'",
            $this->settings['db']['PREFIX'], (int)$id);
        $result = $this->_conn->query($query);
        if ($result->num_rows) {
            return $result->fetch_object();
        } else
            return null;
    }

    /**
     * Получение соединения
     * @return object
     */
    public function getConn()
    {
        return $this->_conn;
    }

    /**
     * Добавление намерения заплатить
     * @param integer $sender_id
     * @param integer $dest_id
     * @param integer $amount
     * @return boolean
     */
    public function addPayment($sender_id, $dest_id, $amount)
    {
        $query = sprintf('INSERT INTO `%spayments` SET `Sender_id`=%s, `Dest_id`=%s, `DateTimeCreate`=NOW(), `Amount`=%0.2f',
            $this->settings['db']['PREFIX'], $sender_id, $dest_id, $amount);
        return $this->_conn->query($query);
    }

    /**
     * Получение перевода по ID
     * @param integer $id
     * @return mixed
     */
    public function getPayment($id)
    {
        $query = sprintf("SELECT * FROM `%spayments` WHERE `Id`='%d'",
            $this->settings['db']['PREFIX'], (int)$id);
        $result = $this->_conn->query($query);
        if ($result->num_rows) {
            return $result->fetch_object();
        } else
            return null;
    }
}
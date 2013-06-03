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
        if ($this->_conn->connect_error) {
            exit($this->_conn->connect_error);
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
     * Создание нового пользователя или перезапись пароля у старого
     * @param string $phone
     * @param string $password
     * @return boolean
     */
    public function createUser($phone, $password)
    {
        $query = sprintf("REPLACE `%susers` SET `Phone`='%s', `Password`='%s', `DateTimeCreate`=NOW()",
            $this->settings['db']['PREFIX'], $phone, $password);
        return $this->_conn->query($query);
    }

    /**
     * Поиск пользователя по номеру телефона
     * @param string $phone
     * @return object
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
}
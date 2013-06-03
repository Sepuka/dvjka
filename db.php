<?php
/**
 * Работа с БД
 */
class DB
{
    const DBhost        = 'localhost';
    const DBname        = 'freelance';
    const DBuser        = 'root';
    const DBpass        = 1;
    const PREFIX        = 'DVJK_';

    protected $_conn    = null;

    protected static $i = null;

    protected function __construct() {
        $this->_conn = mysqli_connect(self::DBhost, self::DBuser, self::DBpass, self::DBname);
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
            self::PREFIX, $phone, $password);
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
            self::PREFIX, $this->_conn->real_escape_string($phone));
        $result = $this->_conn->query($query);
        if ($result->num_rows) {
            return $result->fetch_object();
        } else
            return null;
    }
}
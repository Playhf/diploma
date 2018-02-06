<?php

class DefModel
{
    /**
     * Database connect
     * @var
     */
    protected $_db;

    /**
     * Check have we an errors from form
     * @param $data
     * @param string $type
     * @return bool|string
     */
    public function validData($data, $type = ""){
        switch ($type){
            case "ul"://user-login. no russian 4-30 characters
                preg_match('/[A-Za-z0-9\)\(_\-]{4,30}/u', $data, $arr);
                return strlen($data) == strlen($arr[0]) ? $arr[0]
                                                        : false;
                break;
            case "un": //user-name/surname rus+eng 2-30
                preg_match('/[А-Яа-яA-Za-z0-9\)\(_\-]{2,30}/u', $data, $arr);
                return strlen($data) == strlen($arr[0]) ? $arr[0]
                                                        : false;
                break;
            case "up": //user=password
                preg_match('/[\w]{6,16}/u', $data, $arr);
                return strlen($data) == strlen($arr[0]) ? md5($arr[0])
                                                        : false;
                break;
            case "ue": //user-email
                return filter_var($data, FILTER_VALIDATE_EMAIL) ? $data
                                                                : false;
                break;
            case "dt": //date &year &month &day
                preg_match('/(\d{4})\.(\d{1,2})\.(\d{1,2})/', $data, $arr);
                return checkdate($arr[2], $arr[3], $arr[1]) ? $arr[0]
                                                            : false;
                break;
            case "ph": //phone 0-9{3} 0-9{7}
                preg_match('/\d{3}-\d{7}/', $data, $arr);
                return !empty($arr) ? $arr[0]
                                    : false;
                break;
            case "gr": //user-name/surname rus+eng 2-30
                preg_match('/[А-Яа-яA-Za-z0-9\)\(_\-]{2,30}/u', $data, $arr);
                return strlen($data) == strlen($arr[0]) ? $arr[0]
                    : false;
                break;
            default:
                return false;
                break;
        }
    }
    /**
     * Check have we got an errors from array data
     * @param array $data
     * @return array|null
     */
    public function getErrors(array $data)
    {
        $errors = array();

        foreach ($data as $key => $value) {
            if ($value !== false)
                continue;
            switch ($key) {
                case 'login':
                    $errors['login'] = 'Неверный формат ввода логина';
                    break;
                case 'password':
                    $errors['password'] = 'Неверный формат ввода пароля';
                    break;
                case 'email':
                    $errors['email'] = 'Неверный формат ввода email';
                    break;
                case 'group':
                    $errors['group'] = 'Неверный формат ввода группы';
                    break;
                case 'captcha':
                    $errors['captcha'] = 'Введите правильные данные с картинки';
                    break;
                case 'deletedUser':
                    $errors['delUser'] = 'Не удалось удалить профиль. Проверьте введенные данные';
                    break;
                case 'noPass' :
                    $errors['noPass'] = 'Введите правильный пароль';
                    break;
                default:
                    break;
            }
        }
        return empty($errors) ? null : $errors;
    }
    /**
     * check do we have an errors on the basis of our type
     * @param string $type
     * @return array|null
     */
    public function isSameData($type = ''){
        $errors = array();

        switch ($type){
            case 'pc'://passconfirm
                $errors['passCon'] = 'Пароли не совпадают';
                break;
            case 'ul'://user login
                $errors['sameLogin'] = 'Пользователь с таким логином уже существует';
                break;
            case 'ue'://user email
                $errors['samePass'] = 'Пользователь с таким email уже существует';
                break;
            case 'lg'://login password
                $errors['logPass'] = 'Вы ввели неправильный логин или пароль';
                break;
        }
        return (!empty($errors) && is_array($errors)) ? $errors :null;
    }
    /**
     * Get database connection
     * @return PDO
     */
    public function DbConnection(){
        $params = require "main/db_params.php";
        $opt = array(    PDO::ATTR_ERRMODE              => PDO::ERRMODE_WARNING,
                         PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_ASSOC
        );
        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']};charset={$params['charset']}";
        try {
            $this->_db = new PDO($dsn, $params['user'], $params['password'], $opt);
            return $this->_db;
        }
        catch (PDOException $e){
            echo "Ошибка. Приносим извинения. <br>
                            <a href='/'>Главная</a>";
            file_put_contents("errors".DS."error.log",
                                            "Ошибка при подключении к базе данных.\n" .
                                            "Ошибка: "      . $e->getMessage() .
                                            "\nФайл: "      . $e->getFile() .
                                            "\nСтрока: "    . $e->getLine() .
                                            "\n\n", FILE_APPEND | FILE_USE_INCLUDE_PATH);
        }
    }

    /**
     * @param $db
     * @param array $data
     * @return int|mixed
     */
    public function insertData($db, array $data){
        /* @var $db PDO */
        $insert = "INSERT INTO `users` (";
        $values = "VALUES (";
        $i = 1;
        $count = count($data);
        //returns values without named indexes
        foreach ($data as $field => $value) {
            if ($i == $count) {
                $insert .= "`{$field}`) ";
                $values .= ":{$field})";
                break;
            }
            $insert .= "`{$field}`, ";
            $values .= ":{$field}, ";
            $i++;
        }
        $sql = $insert . $values;
        //trying to insert
        try {
            $stmt = $db->prepare($sql);
            foreach ($data as $field => $value) {
                $stmt->bindValue(":{$field}", $value);
            }
            $stmt->execute();
        } catch (PDOException $e) {
            return $e->getCode();
        }
    }

    /**
     * @param $db
     * @param $data
     * @param $condition
     * @param array $field
     * @param $connector
     * @return mixed
     */
    public function selectData($db, $data, $condition, array $field, $connector = '='){
        /* @var $db PDO */
        $sql = "SELECT ";
        $i = 1;
        $count = count($field);
        foreach ($field as $value){
            //do it without last array value. cause we have ',' in sql request
            if ($i == $count)
                break;
            $sql .= "`{$value}`, ";
            $i++;
        }
        //get last value from array and cont our sql request
        $sql .= '`' . array_pop($field) . '`' . " FROM `users`";
        if (is_array($condition)) {
            $sql .= " WHERE ";
            $i = 1;
            foreach ($condition as $key => $value) {
                if ($i == count($condition)){
                    $sql .= "`{$key}` {$value} ?";
                    break;
                }
                $sql .= "`{$key}` {$value} ? {$connector} ";
                $i++;
            }
        } else {
            $sql .= " WHERE `{$condition}` {$connector}?";
        }
        try {
            $stmt = $db->prepare($sql);
            //without bind
            !is_array($data) ? $stmt->execute(array($data)) : $stmt->execute($data);
            $result = !is_array($data) ? $stmt->fetch() : $stmt->fetchAll();
            return $result;
        } catch (PDOException $e){
            return 'Error!' . $e->getMessage();
        }
    }

    /**
     * Change needed param in database
     * @param $db
     * @param $data
     * @param $field
     * @param $id
     * @return mixed
     */
    public function updateData($db, $data, $field, $id)
    {
        $sql = "UPDATE `users` SET {$field}='" . $data . "' WHERE id=?";
        try {
            $stmt = $db->prepare($sql);
            $result = $stmt->execute(array($id));
            return $result;
        } catch (PDOException $e) {
            return $e->getCode();
        }
    }

    /**
     * Detele user from database
     * @param $db
     * @param $id
     * @return mixed
     */
    public function deleteData($db, $id){
        $sql = "DELETE FROM `users` WHERE id={$id}";
        $result = $db->exec($sql);
        return $result;
    }
}
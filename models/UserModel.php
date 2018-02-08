<?php

class UserModel extends DefModel
{
    /**
     * Form an array and register user
     * @param array $data
     * @return array
     */
    public function newUser(array $data){
        //if its ok then unset useless variables
        if ($data['password'] == $data['passwordConfirm'] &&
                                 $_SESSION['captcha'] == strtoupper($data['captcha'])){
            unset($data['passwordConfirm'], $data['captcha'], $_SESSION['captcha']);
            $data['login']          = $this->validData($data['login'], "ul");
            $data['password']       = $this->validData($data['password'], "up");
            $data['email']          = $this->validData($data['email'], "ue");
            $data['group']          = $this->validData($data['group'], "gr");
        }
        elseif($data['password'] != $data['passwordConfirm']) {
            $data['password'] = false;
            $data['errors'] = $this->isSameData('pc');
            return $data;
        }
        elseif ($_SESSION['captcha'] != strtoupper($data['captcha'])){
            $data['captcha'] = false;
            $data['errors'] = $this->getErrors($data);
            return $data;
        } //check do we have false from validData in our array
        if (!in_array(false, $data)){
            $this->_db = $this->DbConnection();
            //check by login in database user with the same login
            $loginResult = $this->selectData($this->_db, $data['login'], 'login', array('id'));
            if ($loginResult){
                $data['login'] = false;
                $data['errors'] = $this->isSameData('ul');
                return $data;
            }
            $emailResult = $this->selectData($this->_db, $data['email'], 'email', array('id'));
            if ($emailResult){
                $data['email'] = false;
                $data['errors'] = $this->isSameData('ue');
                return $data;
            }
            $this->insertData($this->_db, $data);
            //if inserted get id
            $data['id']     = $this->_db->lastInsertId();
            //if everything is ok
            $this->userSession(array('id' => $data['id']));
            return $data;
        }
        else {
            $data['errors'] = $this->getErrors($data);
            return $data;
        }
    }

    /**
     * Check is user already registered.
     * @param array $data
     * @return array|bool
     */
    public function authUser(array $data){
        $data['login']      = $this->validData($data['login'], "ul");
        $data['password']   = $this->validData($data['password'], "up");
        if (!in_array(false, $data)){
            $this->_db = $this->DbConnection();
            $result = $this->selectData($this->_db,
                $data['login'],
                "login",
                array("id",
                    "login",
                     "password",
                     "email",
                     "group",
                    'is_head',
                    'is_admin',
                    'is_accessible'
                    )
            );
            if ($data['login']      == $result['login'] &&
                $data['password']   == $result['password']){
                unset($result['password'], $data['password']);
                $this->userSession(array('id' => $result['id']));
                return true;
            } else {
                unset($data['password']);
                $data['errors'] = $this->isSameData('lg');
                return $data;
            }
        } else {
            $data['errors'] = $this->isSameData('lg');
            return $data;
        }
    }
    /**
     * UserUpdate method
     * @param array $data
     * @param $id
     * @return array
     */
    public function userUpdate(array $data, $id){
        if ($id) {
            $field = null;
            $type = null;
            switch ($data['param']) {
                case 1:
                    $field = 'login';
                    $type = 'ul';
                    break;
                case 2:
                    $field = 'email';
                    $type = 'ue';
                    break;
                case 3:
                    $field = 'group';
                    $type = 'ug';
                    break;
            }
            $this->_db = $this->DbConnection();
            if ($type != 'im')
            $data['update'] = $this->validData($data['update'], $type);
            if (!$data['update']) {
                $data['errors'] = $this->getErrors([$field => $data['update']]);
                return $data;
            }
            if ($type == 'ul' || $type == 'ue') {
                $this->_db = $this->DbConnection();
                $loginOrEmailResult = $this->selectData($this->_db, $data['update'], $field, ['id']);
                if ($loginOrEmailResult) {
                    $data[$field] = false;
                    $data['errors'] = $this->isSameData($type);
                    return $data;
                }
            }
            if ($type == 'im') {
                $result = $this->checkImg();
                if ($result) {
                    $data[$field] = false;
                    $data['errors'] = $result;
                    return $data;
                }
                //check do we have img in our folder delete this
                $imgExist = $this->selectData($this->_db,  $id, 'id', ['img']);
                if (in_array(!null, $imgExist)) {
                    $filename = $_SERVER["DOCUMENT_ROOT"] . "/template/images/user_images/" . "{$imgExist['img']}";
                    unlink($filename);
                } //set new name
                $filename = $_SERVER["DOCUMENT_ROOT"] . "/template/images/user_images/" . "{$id}.jpg";
                $data['update'] = "{$id}.jpg";
                move_uploaded_file($_FILES['img']['tmp_name'], $filename);
                $data['param'] = true;
            } //upload new
            $result = $this->updateData($this->_db, $data['update'], $field, $id);
            if ($result) {
                return $data;
            } else {
                $data['result'] = false;
                return $data;
            }
        }
    }

    /**
     * check is uploaded file have an errors
     * @return array|null
     * null - no errors
     */
    public function checkImg(){
        $errors = array();
        if ($_FILES['img']['type'] != 'image/jpeg') {
            $errors['imgErr']       = 'Можно загрузить только файл формата jpg!';
            return $errors;
        }
        if ($_FILES['img']['error'] > 0){
            switch ($_FILES['img']['error']){
                case 1:
                    $errors['imgErr'] = 'Размер файла больше допустимого';
                    break;
                case 2:
                    $errors['imgErr'] = 'Размер файла больше допустимого';
                    break;
                case 3:
                    $errors['imgErr'] = 'Загружена только часть файла';
                    break;
                case 4:
                    $errors['imgErr'] = 'Файл не был загружен';
                    break;
                case 6:
                    $errors['imgErr'] = 'Загрузка невозможна: не задан временный каталог';
                    break;
                case 7:
                    $errors['imgErr'] = 'Загрузка не выполнена: невозможна запись на диск';
                    break;
                case 8:
                    $errors['imgErr'] = 'PHP-расширение остановило загрузку файла';
                    break;
            }
        }
        return !empty($errors) && is_array($errors) ? $errors : null;
    }
    /**
     * @param array $data
     * @param $id
     * @return array|bool
     * if have an errors, insert them into @param array
     * else - return bool true;
     */
    public function userDelete(array $data, $id){
        if ($id) {
            if ($_SESSION['captcha'] == strtoupper($data['captcha']) &&
                $data['password'] == $data['passwordConfirm']) {
                //if its ok unset useless
                unset($data['captcha'], $_SESSION['captcha'], $data['passwordConfirm']);
                //check valid
                $data['password'] = $this->validData($data['password'], "up");
            }
            elseif($data['password'] != $data['passwordConfirm']) {
                $data['password'] = false;
                $data['errors'] = $this->isSameData('pc');
                return $data;
            }
            elseif ($_SESSION['captcha'] != strtoupper($data['captcha'])){
                $data['captcha'] = false;
                $data['errors'] = $this->getErrors($data);
                return $data;
            }
            if (!in_array(false, $data)){
                $this->_db = $this->DbConnection();
                $result = $this->selectData($this->_db, $data['password'], "password", ["id"]);
                if ($result) {
                    $result = $this->deleteData($this->_db, $id);
                    if ($result == 0) {
                        $data['delUser'] = false;
                        $data['errors'] = $this->getErrors($data);
                        return $data;
                    } else {
                        $filename = $_SERVER['DOCUMENT_ROOT'] . '/template/images/user_images/' . "{$id}.jpg";
                        unlink($filename);
                        return true;
                    }
                }
                else {
                    $data['noPass'] = false;
                    $data['errors'] = $this->getErrors($data);
                    return $data;
                }
            }
            else {
                $data['errors'] = $this->getErrors($data);
                return $data;
            }
        }
    }

    public function getUserInfo($id)
    {
        $this->_db = $this->DbConnection();
        $result = $this->selectData($this->_db,
                                    $id,
                                'id',
                                    array("id",
                                        "login",
                                        "email",
                                        "group",
                                        'is_head',
                                        'is_admin',
                                        'is_accessible'
                                    )
        );
        return $result;
    }

    /**
     * insert user params into session
     * @param $data
     */
    public function userSession($data){
        $sessionArr = array();
        foreach ($data as $key => $value) {
            $sessionArr[$key] = $value;
        }
        $_SESSION[md5(session_id())] = $sessionArr;
    }
}
<?php

class UserController extends SiteController
{
    public function loginAction($data = null){
        $opt = array(
            'title'      => 'Вход',
            'content'    => 'login.phtml'
        );
        $this->indexAction($opt, $data);
    }

    public function registerAction($data = null){
        $opt = array(
            'title'     => 'Регистрация',
            'content'   => 'register.phtml'
        );
        $this->indexAction($opt, $data);
    }

    public function profileAction($data = null)
    {
        if ($this->isLogged()) {
            $opt = array(
                'title'     => 'Личный кабинет',
                'content'   => 'profile.phtml'
            );
            $this->indexAction($opt, $data);
        }
    }

    public function newAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = array(
                'login'             => $_POST['userLogin'],
                'password'          => $_POST['userPassword'],
                'passwordConfirm'   => $_POST['userPassConfirm'],
                'email'             => $_POST['userEmail'],
                'group'             => $_POST['userGroup'],
                'captcha'           => $_POST['captcha']
            );
            $model  = new UserModel();
            $result = $model->newUser($data);
            if (in_array(false, $result)) {
                $this->registerAction($result);
            }
            else {
//                $this->indexAction();
                $this->_redirect("/");
            }
        }  else
               $this->registerAction();
    }

    public function updateAction($param)
    {
        if ($param == null || $param <1 || $param >3)
            $this->notFoundAction();
        if ($this->isLogged()){
            $opt = array(
                'title'   => 'Редактирование профиля',
                'content' => /*($param == 7) ? 'updateimg.phtml' :*/ 'updatetext.phtml'
            );
            $data = array();
            $data['update'] = isset($_POST['update'])   ? $_POST['update'] : null;
            $data['param'] = $param;
            if ($data['update'] == null){
                $this->indexAction($opt, $data);
            }
            else {
                $model  = new UserModel();
                $result = $model->userUpdate($data, $this->_user['id']);
                if (in_array(false, $result))
                    $this->indexAction($opt, $result);
                else
                    $this->_redirect('/user/profile/');
            }
        }
    }
    public function authAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = array(
                'login'     => $_POST['userLogin'],
                'password'  => $_POST['userPassword']
            );
            $model  = new UserModel();
            $result = $model->authUser($data);
            if ($result === true){
                $this->_redirect("/");
            }
            else {
                $this->loginAction($result);
            }
        }
        else {
            $this->loginAction();
        }
    }
    public function deleteAction()
    {
        if ($this->isLogged()) {
            $opt = array(
                'title'     => 'Удаление профиля',
                'content'   => 'confirm.phtml'
            );
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $data = array(
                    'captcha'           => $_POST['captcha'],
                    'password'          => $_POST['userPassword'],
                    'passwordConfirm'   => $_POST['userPassConfirm']
                );
                $model = new UserModel();
                $result = $model->userDelete($data, $this->_user['id']);
                if ($result === true) {
                    unset($opt);
                    $this->logoutAction();
                } else
                    $this->indexAction($opt, $result);
            }
            else
                $this->indexAction($opt);
        }
    }

    public function logoutAction()
    {
        if ($this->isLogged()) {
            session_destroy();
            $this->_redirect("/");
        }
    }
}
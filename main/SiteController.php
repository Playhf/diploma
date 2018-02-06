<?php

class SiteController
{
    protected $_view;
    protected $_model;
    protected $_session;
    protected $_user;

    public function __construct()
    {
//        $this->_model = $this->getModel();
        $this->_view     = new DefView();
        $this->_session  = & $_SESSION[md5(session_id())] ? $_SESSION[md5(session_id())] : null;
        $this->_user     = $this->_session ? $this->getUserData($this->_session['id']) : null;
    }

    public function indexAction($opt = null, $data = null){
        if ($this->_user && !$opt)//indexaction without params return content usermain
            $opt = array( 'title'     => 'Главная',
                          'content'   => 'usermain.phtml'//it means we have logged user
            );
        $opt = $this->_view->getOptions($opt);
        $this->_view->render($opt, $data, $this->_user, $this->_session);
    }
    public function contactAction(){
        $opt = array(  'title'   => 'Контакты',
                       'content' => 'contact.phtml'
        );
        $this->indexAction($opt);
    }

    public function notFoundAction(){
        $opt = $this->getNotFoundOpt();
        $this->indexAction($opt);
    }

    /**
     * check is user logged in
     * @return bool
     */
    public function isLogged(){
        if (isset($this->_user))
            return true;
        $this->_redirect("/user/login/");
    }

    /**
     * check is user an admin
     * @return bool
     */
    public function isAdmin() {
        if ($this->isLogged()) {
            if ($this->_user['is_admin'] == 1)
                return true;
            $this->_redirect("/");
        }
    }

    public function isAccessible()
    {
        if ($this->isLogged()) {
            if ($this->_user['is_accessible'])
                return true;
            $this->_redirect("/");
        }
    }
//
//    protected function getControllerName()
//    {
//        return strstr(get_class($this), "C", true);
//    }
//
//    protected function getModelName()
//    {
//        return $this->getControllerName().'Model';
//    }
//
//    protected function getModel()
//    {
//        $modelName = $this->getModelName();
//        return new $modelName();
//    }

    protected function getUserData($id)
    {
        $model = new UserModel();
        $result = $model->getUserInfo($id);
        return $result;
    }

    protected function getNotFoundOpt()
    {
        return array(
            'title'   => 'Страница не найдена',
            'content' => 'error404.phtml'
        );
    }

    /**
     * redirect users
     * @param $url
     */
    protected function _redirect($url)
    {
        header("Location: " . $url);
    }
}
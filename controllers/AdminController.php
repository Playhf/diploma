<?php

class AdminController extends SiteController
{
    public function indexAction($opt = null, $data = null)
    {
        if ($this->isAdmin()) {
            $opt = array(
                'title'     => 'Админ. Главная',
                'content'   => 'admin_main.phtml'
            );
            parent::indexAction($opt);
        }
    }

    public function permissionAction()
    {
        if ($this->isAdmin()) {
            $opt = array(
                'title'     => 'Дать право на расчет',
                'content'   => 'permission.phtml'
            );
            $this->_model = new AdminModel();
            $users = $this->_model->getAllUsers();
            parent::indexAction($opt, $users);
        }
    }

    public function formAction()
    {
        if ($this->isAdmin() && $_POST) {
            $ids = array_values($_POST['students']);
            $this->_model = new AdminModel();
            $this->_model->givePermissions($ids);
            $this->_redirect('/admin/permission/');
        }
    }
}
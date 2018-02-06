<?php

class AboutController extends SiteController
{
    public function indexAction($opt = null, $data = null)
    {
        $opt = array(
            'title'     => 'Главная',
            'content'   => 'about.phtml'//it means we have logged user
        );
        parent::indexAction($opt);
    }
}
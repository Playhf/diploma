<?php

class DefView
{
    /**
     * Form a correct view params
     * @param $opt
     * @return array
     */
    public function getOptions($opt){
        $arr = array();
        $arr['title']   = isset($opt['title'])   ? $opt['title']   : 'Главная';
        $arr['content'] = isset($opt['content']) ? $opt['content'] : 'main.phtml';
        $arr['header']  = isset($opt['header'])  ? $opt['header']  : 'header.phtml';
        $arr['footer']  = isset($opt['footer'])  ? $opt['footer']  : 'footer.phtml';
        return $arr;
    }

    /**
     * include a view template
     * @param $opt
     * @param null $data
     * @param $user
     */
    public function render($opt, $data = null, $user, $session){
        include_once "template/index.php";
    }
}
<?php

class FrontController
{
    /**
     * Necessary data: controller, action and param values
     * @var array
     */
    private $necData;

    /**
     * get a suitable uri
     * @return null|string
     */
    private function getUri(){
        return !empty($_SERVER['REQUEST_URI']) ? trim($_SERVER['REQUEST_URI'], '/') : null;
    }

    /**
     * Form an Necessary data on the basis of uri
     * FrontController constructor.
     */
    public function __construct()
    {
        $uri = $this->getUri();
        $segments = explode('/', $uri);
//        $controller = !(empty($segments[0])) ? ucfirst(strtolower($segments[0])) . 'Controller'
//                                             : 'SiteController';
        if (empty($segments[0])) {
            $controller = 'SiteController';
            $action     = 'indexAction';
        }
        elseif (count($segments) == 1){
            $controller = ucfirst(strtolower($segments[0])) . 'Controller';
            $action     = 'indexAction';
        } else {
            $controller = ucfirst(strtolower($segments[0])) . 'Controller';
            $action     = strtolower($segments[1]) . 'Action';
        }


        $param = isset($segments[2])  ? (string)$segments[2] : null;

        $this->necData = array( 'controller'   => $controller,
                                'action'       => $action,
                                'param'        => $param
        );
    }

    /**
     * Get body
     */
    public function run(){
        ob_start();
        header("Content-type: text/html; charset=utf-8");
        $this->execute($this->necData);
        echo ob_get_clean();
    }

    /**
     * Choose the correct controller and action using reflectionClass
     * @param array $necData
     */
    private function execute(array $necData){
        if (class_exists($necData['controller'])){
            $rc = new ReflectionClass($necData['controller']);
            if ($rc->hasMethod($necData['action'])){
                $controller = $rc->newInstance();
                $action = $rc->getMethod($necData['action']);
                $action->invoke($controller, $necData['param']);
            } else { //if we haven't got this action - not found
                $necData = array( 'controller' => 'SiteController',
                                  'action'     => 'notFoundAction',
                                  'param'      => null
                );
                $this->execute($necData);
            }
        } else {
            $necData = array( 'controller'    => 'SiteController',
                              'action'        => 'notFoundAction',
                              'param'         => null
            );
            $this->execute($necData);
        }
    }
}
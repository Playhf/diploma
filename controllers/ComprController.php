<?php

class ComprController extends SiteController
{

    public function calculateAction($method)
    {
        if ($this->isLogged()) {
            switch (strtolower($method)) {
                case 'ptc':
                    $opt = array(
                        'title'     => 'Расчет характеристик методикой TCP-10',
                        'content'   => 'calculate_ptc.phtml'
                    );
                    break;
                case 'galerkin':
                    $opt = array(
                        'title'     => 'Расчет характеристик методикой Галеркина',
                        'content'   => 'calculate_galerkin.phtml');
                    $this->_model   = new CompressModel();
                    break;
                default:
                    $opt = $this->getNotFoundOpt();
                    break;
            }
            $this->indexAction($opt, $this->_model ? $this->_model : null);
        }
    }

    public function countptcAction()
    {
        if ($this->isAccessible()) {
            if ($_POST) {
                $d = $_POST;
                $this->_model = new CompressModel();
                $d = $this->_model->getOptions($d);
//                $d['result'] = true;
//                $_SESSION['data'] = $d;
                $this->_session['result'] = $d;
                $this->_redirect('/compr/result/ptc');
            }
        }
    }

    public function countgalAction()
    {
        if ($this->isAccessible()) {
            if ($_POST) {
                $data = $_POST;
                $this->_model = new GalerkinModel();
                $result = $this->_model->calculateCharacteristics($data);
                $this->_session['galerkin_result'] = $result;
                $this->_redirect('/compr/result/gal');
            }
        }
    }

    public function resultAction($method)
    {
        if ($this->isAccessible()) {
            switch (strtolower($method)) {
                case 'ptc':
                    $opt = array(
                        'title'     => 'Результаты расчета методикой PTC',
                        'content'   => 'result_ptc.phtml'
                    );
                    break;
                case 'gal':
                    $opt = array(
                        'title'     => 'Результаты расчета методикой Галеркинаа',
                        'content'   => 'result_gal.phtml'
                    );
                    break;
                default:
                    $opt = $this->getNotFoundOpt();
                    break;
            }
            $this->indexAction($opt);
        }
    }

    public function exampleAction()
    {
        $opt = array(
            'title'   => 'Пример расчета на воздухе',
            'content' => 'example.phtml'
        );
        $this->indexAction($opt);
    }

}
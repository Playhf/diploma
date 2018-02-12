<?php

class ComprController extends SiteController
{

    public function calculateAction()
    {
        if ($this->isLogged()) {
            $opt = array(
                        'title'     => 'Расчет характеристик методикой TCP-10',
                        'content'   => 'calculate_ptc.phtml'
                    );

            parent::indexAction($opt);
        }
    }

    public function countAction()
    {
        if ($this->isAccessible()) {
            if ($_POST) {
                $d = $_POST;
                $this->_model = new CompressModel();
                $d = $this->_model->getOptions($d);
                $this->_session['result'] = $d;
                $this->_redirect('/compr/result/');
            }
        }
    }

    public function resultAction()
    {
        if ($this->isAccessible()) {
            $opt = array(
                        'title'     => 'Результаты расчета методикой PTC',
                        'content'   => 'result_ptc.phtml'
                    );
            $this->indexAction($opt);
        }
    }

    public function printAction()
    {
        $values = $this->_session['result'];
        $this->_model = new PdfModel(SNMPDF_PAGE_ORIENTATION,
            SNMPDF_UNIT,
            SNMPDF_PAGE_FORMAT,
            true,
            'UTF-8',
            false);
        $this->_model->getPdfContent($values, $this->_user);
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
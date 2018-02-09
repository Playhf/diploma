<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 03.02.18
 * Time: 13:34
 */

class GalerkinModel implements Calculate
{

    protected $_politropKPD;
    protected $_NN;
    protected $_otnDavl;
    protected $_polNapor;
    protected $_hI;
    protected $_vnNapor = 0.5;
    protected $_u;
    protected $_d2;
    protected $_plotnost;
    protected $_koefRashoda = 0.0525;
    protected $_m;

    public function calculateCharacteristics($data)
    {
        $this->calculatePolKPD($data['pnach'], $data['pkoniec'], $data['temprNach'], $data['temprKon'], $data['adiabata']);
        $this->calculateNN($data['adiabata']);
        $this->calculateOtnDavl($data['pnach'], $data['pkoniec']);
        $this->calculateHPol($data['gazpost'], $data['temprNach']);
        $this->calculateHI();
        $this->calculateU2();
        $this->calculateD2($data['n']);
        $this->calculatePlotnost($data['pnach'], $data['gazpost'], $data['temprNach']);
        $this->calculateM();

        return !in_array(false, $this->getResultArray()) ? $this->getResultArray() : 'Ошибка в вычесслениях';

    }

    protected function getResultArray()
    {
        return array(
            'politrop_kpd'   => $this->_politropKPD,
            'n-1'            => $this->_NN,
            'otnDavl'        => $this->_otnDavl,
            'politrop_napor' => $this->_polNapor,
            'hi'             => $this->_hI,
            'u'              => $this->_u,
            'd2'             => $this->_d2,
            'plotnost'       => $this->_plotnost,
            'massRashod'     => $this->_m
        );
    }

    protected function calculatePolKPD($pNach, $pKoniec, $tN, $tK, $k)
    {
        $this->_politropKPD = (log($pKoniec/$pNach) / log($tK/$tN)) / ($k/($k-1));
    }

    protected function calculateNN($k)
    {
        $this->_NN = $this->_politropKPD*$k/($k-1);
    }

    protected function calculateOtnDavl($pN, $pK)
    {
        $this->_otnDavl = $pK/$pN;
    }

    protected function calculateHPol($rGaz, $tN)
    {
        $this->_polNapor = ($this->_NN)*$rGaz*$tN*(pow($this->_otnDavl, pow($this->_NN, -1) -1));
    }

    protected function calculateHI()
    {
        $this->_hI = $this->_polNapor/$this->_politropKPD;
    }

    protected function calculateU2()
    {
        $this->_u = sqrt($this->_hI/$this->_vnNapor);
    }

    protected function calculateD2($n)
    {
        $this->_d2 = sqrt((60*pow($this->_u, 2)*(1/M_PI))/$n);
    }

    protected function calculatePlotnost($pN, $gazpost, $tN)
    {
        $this->_plotnost = $pN/($gazpost*$tN);
    }

    protected function calculateM()
    {
        $this->_m = $this->_koefRashoda*(M_PI/4)*pow($this->_d2, 2)*pow($this->_u,2)*$this->_plotnost;
    }

    public function getPrintContent($values, $user)
    {
        $time = date('Y-m-d H:i');
//        $content = <<<DOC
//        <p>Расчет для студента группы <b>{$user['group']}</b></p>
//        <p>Логин: <b>{$user['login']}</b></p>
//        <p>Время расчета: <b>{$time}</b></p>
//        <p><b>Политропный КПД: </b> {$values['politrop_kpd']}</p>
//        <p><b>Отношение давлений П: </b> {$values['otnDavl']}</p>
//        <p><b>Политропный напор: </b> {$values['politrop_napor']}</p>
//        <p><b>h<sub>i</sub>: </b> {$values['hi']}</p>
//        <p><b>Диаметр D<sub>2</sub>: </b> {$values['d2']}</p>
//        <p><b>Плотность: </b> {$values['plotnost']}</p>
//        <p><b>Массовый расход: </b> {$values['massRashod']}</p>
//
//DOC;
        $content = <<<TXT
        Расчет для студента группы {$user['group']}\n
        Логин: {$user['login']}\n
        Время расчета: {$time}\n
        Политропный КПД: {$values['politrop_kpd']}\n
        Отношение давлений П: {$values['otnDavl']}\n
        Политропный напор: {$values['politrop_napor']}\n
        hi: {$values['hi']}\n
        D2: {$values['d2']}\n
        Плотность: {$values['plotnost']}\n
        Массовый расход: {$values['massRashod']}\t\n
TXT;

        return $content;
    }
}
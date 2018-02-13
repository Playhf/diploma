<?php

class CompressModel
{
    const A = 0.3;
    const N = 1;
    const KOEF = 0.5;

    protected $_ishod;

    protected $_much;
    protected $_b2;
    protected $_kinVisc;
    protected $_re;
    protected $_ksDh;
    protected $_LambdaSP;
    protected $_LambdaBesc;
    protected $KPD;
    protected $NAPOR;
    protected $RASHOD;

    protected $_KPD        = array(0.78, 0.82, 0.81, 0.76, 0.68, 0.5);
    protected $_Napor      = array(0.59, 0.58, 0.55, 0.43, 0.4, 0.27);
    protected $_Rashod     = array(0.0425, 0.0525, 0.061, 0.0685, 0.07, 0.0725);

    protected $_exIshod    = array(52, 3.2, 312, 293, 287, 350, 11.78);
    protected $_exResIshod = array(0.91, 0.0105, 3.45, 218400, 0.0003, 0.0176, 0.0151);


    public function getOptions($data)
    {
        $this->_ishod = array_map('floatval', $data);
        $listData = array_values($data);
        list($press, $ra, $speed, $temp, $gas, $diam, $dynVisc) = $listData;
        $this->setMuch($speed, $gas, $temp);
        $this->setB2($diam);
        $this->setKinVisc($press * 10**5, $gas, $temp, $dynVisc * 10**-6);
        $this->setRe($speed);
        $this->setKsDh($ra);
        $this->setDelLambdaSP();
        $this->setDelLambdaBesc();
        $this->setKPD();
        $this->setNapor();
        $this->setRashod();

        $result = array(
                'ishod' => $this->_ishod,
                'promejutochnie' => array(
                    $this->_much,
                    $this->_b2,
                    $this->_kinVisc,
                    $this->_re,
                    $this->_ksDh,
                    $this->_LambdaSP,
                    $this->_LambdaBesc
                ),
                'KPD'   => $this->KPD,
                'napor' => $this->NAPOR,
                'rashod'=> $this->RASHOD
        );

        return $result;
    }

    private function setRashod()
    {
        $result = array();
        for ($i = 0; $i <= count($this->_Rashod) -1; $i++) {
            $result[] = $this->_Rashod[$i] * sqrt($this->NAPOR[$i] / $this->_Napor[$i]);
        }
        $this->RASHOD = $result;
    }

    private function setNapor()
    {
        $result = array();
        for ($i = 0; $i <= count($this->KPD) -1; $i++) {
            $result[] = $this->_Napor[$i] * (self::KOEF + (self::KOEF*($this->KPD[$i]/$this->_KPD[$i])));
        }
        $this->NAPOR = $result;
    }

    private function setKPD()
    {
        $result = array();
        for ($i = 0; $i <= count($this->_KPD) -1; $i++) {
            $result[] = -((self::N - $this->_KPD[$i])*((self::A + (self::N - self::A)*$this->_LambdaSP/$this->_LambdaBesc)
                        /(self::A + (self::N - self::A)*0.0176/0.0151))) + self::N;
        }
        $this->KPD = $result;
    }

    private function setDelLambdaBesc()
    {
        $result = self::N/(1.74-(2*log10(2*$this->_ksDh)))**2;
        $this->_LambdaBesc = $result;
    }

    private function setDelLambdaSP()
    {
        $max = 0.9999;
        for ($i=round(0.0001, 4);
             $i<$max;
             $i+=round(0.0001, 4)){
//            $res = round(self::N/pow((1.74 - 2*log10(2*$ksDh + 18.7/$re*sqrt($i))), 2), 4, PHP_ROUND_HALF_UP);
            $res = round(self::N/pow((1.74 - 2*log10(2*$this->_ksDh + 18.7/$this->_re*sqrt($i))), 2), 4, PHP_ROUND_HALF_UP);
            if (round($i, 4) === $res) {
                $this->_LambdaSP = $i;
                return ;
            } else {
                continue;
            }
        }
    }

    private function setKsDh($ra)
    {
        $result = $ra*10**-6/$this->_b2;
        $this->_ksDh = $result;
    }

    private function setKinVisc($press, $gas, $temp, $dynVisc)
    {
        $density = $this->getDensity($press, $gas, $temp); //плотность
        $result = $dynVisc/$density;
        $this->_kinVisc = $result;
    }

    private function setMuch($speed, $gas, $temp)
    {
        $result = round($speed/sqrt(1.4 * $gas * $temp), 2);
        //return $result;
        $this->_much = $result;
    }

    private function setB2($diam)
    {
        $result = 0.03 * (0.001*$diam);
        $this->_b2 = $result;
    }

    private function setRe($speed)
    {
        $result = ($speed*$this->_b2)/$this->_kinVisc;
        $this->_re = round($result, 1);
    }

    private function getDensity($press, $gas, $temp)
    {
        $result = $press/($gas*$temp);
        return $result;
    }

    public function getStartTitles()
    {
        $html = <<<HTML
                <th>Давление, бар</th>
                <th>Шероховатость, мкм</th>
                <th>Скорость (u<sub>2</sub>)</th>
                <th>Начальная температура, К</th>
                <th>Газовая постоянная, Дж/(кг*К)</th>
                <th>Диаметр (D<sub>2</sub>), мм</th>
                <th>Динамическая вязкость, м<sup>2</sup>/сек * 10<sup>-6</sup></th>
HTML;
        return $html;
    }

    public function getResultTitles()
    {
        $html = <<<HTML
                <th>Число Маха <b>M</b></th>
                <th><b>b<sub>2</sub></b></th>
                <th>Кинематическая вязкость <b>&#957;</b></th>
                <th>Число Рейнольдса <b>Re</b></th>
                <th>Соотношение <b>K<sub>s</sub>/D<sub>h</sub></b></th>
                <th>Коэффициент <b>&#955;<sub>sp</sub></b></th>
                <th>Коэффициент <b>&#955;<sub>&#8734;</sub></b></th>
HTML;
        return $html;
    }

    public function getKpd()
    {
        return $this->_KPD;
    }

    public function getNapor()
    {
        return $this->_Napor;
    }

    public function getRashod()
    {
        return $this->_Rashod;
    }

    public function getExampleIshod()
    {
        return $this->_exIshod;
    }

    public function getExampleResultIshod()
    {
        return $this->_exResIshod;
    }
}
<?php

class CompressModel implements Calculate
{
    const A = 0.3;
    const N = 1;
    const KOEF = 0.5;


    protected $KPD = array(0.78, 0.82, 0.81, 0.76, 0.68, 0.5);
    protected $Napor = array(0.59, 0.58, 0.55, 0.43, 0.4, 0.27);
    protected $Rashod = array(0.0425, 0.0525, 0.061, 0.0685, 0.07, 0.0725);
    protected $rotationFrequency = array(5460, 5300, 5200, 5000, 4600, 4200, 3640);


    public function getOptions($data)
    {
        $data = array_map('floatval', $data);
        $listData = array_values($data);
        list($press, $ra, $speed, $temp, $gas, $diam, $dynVisc) = $listData;

        $data['muchNumber'] = round($this->getMuch($speed, $gas, $temp), 2);  //число маха
        $data['b2']         = $this->getB2($diam); //b2
        $data['kinVisc']    = $this->getKinVisc($press * 10**5, $gas, $temp, $dynVisc * 10**-6);
        $data['re']         = $this->getRe($speed, $data['b2'], $data['kinVisc']); // reinolds
        $data['ksDh']       = $this->getKsDh($ra * 10**-6, $data['b2']);
        $data['LambdaSP']     = $this->getDelLambdaSP($data['ksDh'], $data['re']);
        $data['LambdaBesc']   = $this->getDelLambdaBesc($data['ksDh']);

        $data['KPD'] = $this->getKPD($data['LambdaSP'],
                                     $data['LambdaBesc'],
                                     $this->KPD
        );

        $data['napor'] = $this->getNapor( $data['KPD'],
                                          $this->Napor
        );

        $data['rashod']  = $this->getRashod($this->Rashod,
                                            $this->Napor,
                                            $data['napor']
        );

        return $data;
    }

    private function getRashod($sechenieRashoda, $naporSechenie, $napor)
    {
        $result = array();
        for ($i = 0; $i <= count($sechenieRashoda) -1; $i++) {
            $result[] = $sechenieRashoda[$i] * sqrt($napor[$i] / $naporSechenie[$i]);
        }
        return $result;
    }

    private function getNapor($KPD, $napor)
    {
        $result = array();
        for ($i = 0; $i <= count($KPD) -1; $i++) {
            $result[] = $napor[$i] * (self::KOEF + (self::KOEF*($KPD[$i]/$this->KPD[$i])));
        }
        return $result;
    }

    private function getKPD($LSP, $LBesc, $sechenie)
    {
        $result = array();
        for ($i = 0; $i <= count($sechenie) -1; $i++) {
            $result[] = -((self::N - $sechenie[$i])*((self::A + (self::N - self::A)*$LSP/$LBesc)
                        /(self::A + (self::N - self::A)*0.0176/0.0151))) + self::N;
        }
        return $result;
    }

    private function getDelLambdaBesc($ksDh)
    {
        $result = self::N/(1.74-(2*log10(2*$ksDh)))**2;
        return $result;
    }

    private function getDelLambdaSP($ksDh, $re)
    {
        $max = 0.9999;
        for ($i=round(0.0001, 4);
             $i<$max;
             $i+=round(0.0001, 4)){
            $res = round(self::N/pow((1.74 - 2*log10(2*$ksDh + 18.7/$re*sqrt($i))), 2), 4, PHP_ROUND_HALF_UP);
            if (round($i, 4) === $res)
                return $i;
            else
                continue;
        }
    }

    private function getKsDh($ra, $b2)
    {
        $result = $ra/$b2;
        return $result;
    }

    private function getKinVisc($press, $gas, $temp, $dynVisc)
    {
        $density = $this->getDensity($press, $gas, $temp); //плотность
        $result = $dynVisc/$density;
        return  $result;
    }

    private function getMuch($speed, $gas, $temp)
    {
        $result = $speed/sqrt(1.4 * $gas * $temp);
        return $result;
    }

    private function getB2($diam)
    {
        $result = 0.03 * (0.001*$diam);
        return $result;
    }

    private function getRe($speed, $b2, $visc)
    {
        $result = ($speed*$b2)/$visc;
        return round($result, 1);
    }

    private function getDensity($press, $gas, $temp)
    {
        $result = $press/($gas*$temp);
        return $result;
    }

    public function getRotationFrequency()
    {
        return $this->rotationFrequency;
    }

    public function getPrintContent($values, $user)
    {

    }
}
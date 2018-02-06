<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
class rofl
{

    public function getDelLambda($ksDh, $re)
    {
        $max = 0.9999;
        $half = round($max, 4) - round(($max / 2), 4);
        $part = rand(0.0001, round(($half / 2), 4, PHP_ROUND_HALF_UP));
        for ($i=round(0.0001, 4);
             $i<$max;
             $i+=round(0.0001, 4)){
                $res = round(1/pow((1.74 - 2*log10(2*$ksDh + 18.7/$re*sqrt($i))), 2), 4, PHP_ROUND_HALF_UP);
                if (round($i, 4) === $res)
                    return $i;
                else
                    continue;
        }
    }
}
$var = new rofl();
echo $var->getDelLambda(0.002331,28288884);
echo md5('superpro11');
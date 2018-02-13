<?php

require_once ROOT.DS.'pdflib'.DS.'tcpdf'.DS.'tcpdf.php';

class PdfModel extends SNMTCPDF implements Calculate
{
    protected $_values;
    protected $_user;

    public function getPdfContent($values, $user)
    {
        $this->SetCreator(SNMPDF_CREATOR);
        $this->SetAuthor('Dmytro Rudenko');
        $this->SetTitle('Calculeted results');
        $this->SetAutoPageBreak(true, SNMPDF_MARGIN_BOTTOM);
        $this->SetFont('roboto', '', 14);
        $this->AddPage();
        $html = $this->_getHtml($values, $user);
        $this->writeHTML($html, true, false, true, false, '');
        $this->Output("{$user['login']}_{$user['group']}" . "-result.pdf", 'D');
    }

    protected function _getHtml($values, $user)
    {
        $this->_values = $values;
        $this->_user   = $user;

        $html = '';
        $html .= $this->_getFirstTable();
        $html .= $this->_getSecondTable();
        $html .= $this->_getLastTable();

        return $html;
    }

    protected function _getFirstTable()
    {
        $time = date('Y-m-d H:i');
        $html = <<<HTML
                  <p>Расчет для студента группы <b>{$this->_user['group']}</b></p>
                  <p>Логин: <b>{$this->_user['login']}</b></p>
                  <p>Время расчета: <b>{$time}</b></p>
                  <h3>С исходными данными: </h3>
        <table cellspacing="0" cellpadding="1" border="1">
            <tr>
                <th>Давление, бар</th>
                <th>Шероховатость, мкм</th>
                <th>Скорость (u<sub>2</sub>), м/с</th>
                <th>Начальная температура, К</th>
                <th>Газовая постоянная, Дж/(кг*К)</th>
                <th>Диаметр (D<sub>2</sub>), мм</th>
                <th>Динамическая вязкость</th>
            </tr>
            <tr>
HTML;

        foreach ($this->_values['ishod'] as $item) {
            $html .= "<td>{$item}</td>";
        }
        $html .= '</tr></table>';

        return $html;
    }

    protected function _getSecondTable()
    {
        $html = <<<HTML
        <h3>Резльтат расчета: </h3>
        <table cellspacing="0" cellpadding="1" border="1">
            <tr>
                <th>Число Маха <b>M</b></th>
                <th><b>b<sub>2</sub></b></th>
                <th>Кинематическая вязкость <b>&#957;</b></th>
                <th>Число Рейнольдса <b>Re</b></th>
                <th>Соотношение <b>K<sub>s</sub>/D<sub>h</sub></b></th>
                <th>Коэффициент <b>&#955;<sub>sp</sub></b></th>
                <th>Коэффициент <b>&#955;<sub>&#8734;</sub></b></th>
            </tr>
            <tr>
HTML;
        foreach ($this->_values['promejutochnie'] as $item) {
            $html .= "<td>{$item}</td>";
        }
        $html .= "</tr></table>";

        return $html;
    }

    protected function _getLastTable()
    {
        $html = "<h3>КПД, Коэффициенты напора и расхода в 6-ти точках:</h3>";

        $html .= '<table cellspacing="0" cellpadding="1" border="1">
				<tr>
				    <th>№</th>
                    <th>КПД</th>
                    <th>Коеффициент напора</th>
                    <th>Коеффициент расхода</th>
                </tr>';
        for ($i = 0, $j = 0; $i <= count($this->_values['KPD'])-1; $i++){
            $html .= "
                 <tr>
                    <td>" . ++$j .  "</td>
                    <td>" . round($this->_values['KPD'][$i], 3)   .  "</td>
					<td>" . round($this->_values['napor'][$i], 3) .  "</td>
					<td>" . round($this->_values['rashod'][$i], 3).  "</td>
                </tr>";
        }
        $html .= '</table>';

        return $html;
    }
}
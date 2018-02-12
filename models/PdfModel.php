<?php

require_once ROOT.DS.'pdflib'.DS.'tcpdf'.DS.'tcpdf.php';

class PdfModel extends SNMTCPDF implements Calculate
{
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
        ob_start();
        ?>

        <p>Расчет для студента группы <b><?php echo $user['group'] ?></b></p>
        <p>Логин: <b><?php echo $user['login'] ?></b></p>
        <p>Время расчета: <b><?php echo date('Y-m-d H:i') ?></b></p>
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
                <?php foreach($values['ishod'] as $item): ?>
                    <td><?php echo $item ?></td>
                <?php endforeach; ?>
            </tr>
        </table>

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
                <?php foreach($values['promejutochnie'] as $item): ?>
                    <td><?php echo $item ?></td>
                <?php endforeach; ?>
            </tr>
        </table>
<?php
        return ob_get_clean();
    }
}
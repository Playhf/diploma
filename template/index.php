<!DOCTYPE html>
<html>
<head>
    <title><?php echo $opt['title']?></title>
    <link href="/template/style/style.css" rel="stylesheet">
    <script src="/jslib/jquery-1.12.0.js"></script>
    <script src="/jslib/jquery.validate.js"></script>
    <?php if ($opt['content'] == 'result_ptc.phtml'): ?>
        <script src="/jslib/highcharts/highcharts.js"></script>
    <?php endif; ?>
</head>
<body>
    <div class="wrapper">
        <div class="head">
            <?php include_once ($opt['header']) ?>
        </div>
        <div class="content">
            <?php include_once ($opt['content']) ?>
        </div>
<!--        <div class="footer">-->
<!--            --><?php //include_once ($opt['footer']) ?>
<!--        </div>-->
    </div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $opt['title']?></title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="/template/style/style.css" rel="stylesheet">
	<script src="/jslib/jquery-1.12.0.js"></script>
	<script src="/jslib/jquery.validate.js"></script>
	<script src="/jslib/custom/errMsgs.js"></script>
    <?php if (isset($data) && is_object($data)): ?>
		<script src="/jslib/highcharts/highcharts.js"></script>
	<?php endif; ?>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
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
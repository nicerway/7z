<?php
	namespace Admin;
	require './include/common.inc.php';
	define('TABLE_NEWS',1);
	require WEB_ROOT.'./include/chkuser.inc.php';
	$table = $showname = 'candidate';
	if (!empty($id) ) {$row = M($table)->find($id); @extract($row); }
	$opt = new Output;//输出流  输出表单元素

	define('closeLayerWindow', 'true');//foot 插入成功后关闭窗口
?>
<!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
	<meta charset="UTF-8" />
	<?php define('IN_PRO',1);include('js/head'); ?>
</head>

<body>


	<div class="content clr">
		<div class="right clr">
			<div class="zhengwen clr">
				<div class="miaoshu clr">
					<div id="tab1" class="tabson">
						<!-- 表单提交 --><form id="dataForm" class="layui-form" method="post" enctype="multipart/form-data">
						<?php Style::output();//Style::submitButton() ?>
						<?php
							isset($send_start_time) or $send_start_time = time();
							isset($use_start_time) or $use_start_time = time();
							isset($use_end_time) or $use_end_time = time()+3600*24*30;
						    $opt
						    ->cache()
						        ->verify('required')
							    ->input('优惠券名称', 'title')
							    ->input('优惠券价值', 'money')
							    ->input('满多少可用', 'min_amount')
						    ->flur()
						    ->time('券赠送时间', 'send_start_time')
						    ->cache()
						        ->word('有效期')
							    ->time('开始日期', 'use_start_time')
							    ->time('结束日期', 'use_end_time')
						    ->flur()
						    ->hide('memberId')
						 ?>

<?php include('js/foot'); ?>
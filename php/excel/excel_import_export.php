<?php
require '../core/run.php';
require WEB_ROOT.'./include/chkuser.inc.php';
ini_set('memory_limit', '512M');
ini_set('output_buffering', 0);
ini_set('implicit_flush', 0);

$table = 'data';
$export='message';
// error_reporting(E_ALL);
// ini_set("display_errors",1);
$action = I('post.action','');

if ($action == 'import') {
	import_201710($table);

} else {# 导出
	export_201710($export);
}


function export_201710($export) {
$starttime=strtotime($_POST['st']);
$endtime=strtotime($_POST['et']);
// $map['sendtime'] = array(array('lt',$endtime),array('gt',$starttime),'and');

	?>

	 <html>
	     <head>
	        <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	         <style id="Classeur1_16681_Styles"></style>
	     </head>
	     <body>
	         <div id="Classeur1_16681" align=center x:publishsource="Excel">
	             <table x:str border=0 cellpadding=0 cellspacing=0 width=100% style="border-collapse: collapse">
	             	<tr>
	                    <th>firstname</th>
	                    <th>lastname</th>
	                    <th>country</th>
	                    <th>contry_code</th>
	                    <th>phone</th>
	                    <th>email</th>
	                    <th>warranty_no</th>
	                    <th>basecode_no</th>
	                    <th>declearname</th>
	                    <th>purchase</th>
	             	</tr>

	<?

	 if (1==1) {
	 	    //$str = "店铺\t下单时间\t追踪编号\t买家姓名\t买家国家\t买家地址\t买家邮箱\t买家电话\tSKU\t产品订单号\t订单状态\t订单金额\t业务员\t备注\t\n";
	 	    //$str = iconv('utf-8','gb2312',$str);
	 	    // while($row=mysql_fetch_array($result)){
	 	    $variable = M('message')->where("sendtime >= " . $starttime ." and sendtime <= " . $endtime)->select();
	 	    // $model->getLastSql();
	 	    // exit($model);

	         $filename = date('YmdHis');
	        // print_r($variable);
	        // exit;
	 	    //$filename = $system_MARKETPLACE_NAME.'-'.$system_catgory;
	 	    header("Pragma: public");
	 	    header("Expires: 0");
	 	    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
	 	    header("Content-Type:application/force-download");
	 	    header("Content-Type:application/vnd.ms-execl");
	 	    header("Content-Type:application/octet-stream");
	 	    header("Content-Type:application/download");;
	 	    header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
	 	    header("Content-Transfer-Encoding:binary");

	 	    foreach ($variable as $key => $row) { extract($row);

	 	    	?>
	 	     <tr>
	            <td ><?=$first?></td>
	            <td ><?=$last?></td>
	            <td ><?=$country?></td>
	            <td ><?=$country_code?></td>
	            <td ><?=$phone?></td>
	            <td ><?=$email?></td>
	            <td ><?=$warranty_no?></td>
	            <td ><?=$baecode_no?></td>
	            <td ><?=$dealer_name?></td>
	            <td ><?=$purchase_date?></td>
	 	     </tr>

	 	    <?}
	     // endif;
	 	    // $filename = date('Ymd').'.xls';
	 	    // exportExcel($filename,'');
	 		// JsSucce("导出成功!",'message.php?'.queryString());
	 }
	 function convertUTG($v){
	 	if (empty($v)) {
	 		return '';
	 	}
	 	return iconv('utf-8','gb2312',trim($v));
	 }


	 ?>            </table>
	         </div>
	     </body>
	 </html>
<?}

// 导入
function import_201710($table) {
	$filePath = $_FILES['file']['tmp_name'];
	$fileName = $_FILES['file']['name'];
	$fileType = pathinfo($fileName,PATHINFO_EXTENSION);
	if (empty ($filePath)) {
		Redirect::JsError('请上传要导入的文件！');
		exit;
	}elseif($fileType=='xls'){
		// /core/vendor/Excel.class.php
		$excel = new Excel($filePath);
		$arr = $excel->readExcel5();
	} else {
		Redirect::JsError('请导入xls格式的文件！');
		exit;
	}

	echo
	    '<a href="data.php" target="righthtml">导入完成可点击此按钮返回</a><br>',
	    '<b style="color:red">导入开始</b>';

    $start = time();
	// 删除第一行表头
	array_shift($arr);

	// dump($arr);exit;

	$count_t = 0;# 统计导入成功的

	$count_f = 0;# 统计失败成功的

	$count_u = 0;# 统计更新的

	foreach ($arr as $i => $value) {
	    $fields = array();

	    $fields['warranty_no'] = (int)$value[0];
	    $fields['baecode_no'] = $value[1];
	    $fields['indata'] = $value[2];
	    $fields['outdata'] = $value[3];
	    $fields['country'] = $value[4];
	    $fields['series'] = $value[5];


	    $dataAction = new Data($fields['warranty_no'], $fields['baecode_no']);

	    // 若已存在 更新
	    if ($dataAction->exist()) {
	    	// if ($dataAction->has('country', $fields['country'])) {
		    	$dataAction->save([
		    		'country' => $fields['country'],
		    		'series' => $fields['series'],
		    		'indata' => $fields['indata'],
		    		'outdata' => $fields['outdata'],
		    		'sendtime' => time(),
		    	]);
		    	++$count_u;
	    	// }
	    	continue;
	    }

	    array_map('trim',$fields);
	    $fields['sendtime'] = time();
             if($fields['warranty_no'] && $fields['baecode_no']){

	    if (M($table)->insert($fields)) {
	    	++$count_t;
	    } else {
	    	++$count_f;
		    ECHO ($i+1).'行导入失败<br>';
	    }
}
	    ob_flush();flush();
	}
	echo "共花费".(time()-$start)."s;更新了【{$count_u}】,导入成功【{$count_t}】,导入失败【{$count_f}】！";
}

// echo $path.$filenewname;
function import_failed(){
	// header("Content-type:application/octet-stream");
	// header("Content-Disposition:attachment;filename=导入结果.txt");
}

function gbk2utf8(&$str){
	if( !empty($str) ){
	    $fileType = mb_detect_encoding($str , array('UTF-8','GBK','LATIN1','BIG5')) ;
	    if( $fileType != 'UTF-8'){
	      $str = mb_convert_encoding($str ,'utf-8' , $fileType);
	    }
	  }
	  $str = str_replace('\'', '’',trim($str));
}


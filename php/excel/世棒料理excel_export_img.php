<?php
	require '../core/run.php';
	require 'include/chkuser.inc.php';


	##导出最新的投票 一个会员对应一个记录
	# message-user relation message.user_id

	$data = M()->query('SELECT user_id, countryName,countryAbbr,img1,img3,ip,SUM(`sendtime`) as sendtime FROM sb_message GROUP BY `user_id`');

	## 查询 投票的会员最新的投票记录 票/人
	$sql_latest = 'SELECT user_id,telphone,countryName,countryAbbr,img1,img3,ip,max(`sendtime`) as sendtime FROM sb_message GROUP BY `user_id`';
	$exportData = M()->query('SELECT * from ('.$sql_latest.') as m left join sb_user as u on u.id=m.user_id');
	//echo '<pre>';print_r($exportData);exit;

    ini_set('display_errors', '1');

	ini_set('memory_limit', '512M');
	ini_set('output_buffering', 0);
	ini_set('implicit_flush', 0);

	$table = 'excel';
	$export='message';

	include VENDOR_PATH.'Excel/PHPExcel.php';
    $excel = new PHPExcel();
    $objDrawing = new PHPExcel_Worksheet_Drawing();

    #设置文本对齐方式#
    $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objActSheet = $excel->getActiveSheet();

    // $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P');
    $letter = array('A','B','C','D','E','F','G','H','I');

    //

    #设置表头数据#
    // $tableheader = array('sku', 'img', 'address', 'MFN', 'HOME', 'AFN', '美国', '加拿大', '西班牙', '英国', '德国', '西班牙', '法国', '意大利','合计');
    $tableheader = array('序号','手机号','原图','海报图','支持的国家','昵称','位置','ip','时间');
    $FillTableCount = count($tableheader);
    #填充表格表头#
    for($i = 0;$i < $FillTableCount;$i++) {
        $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        #设置表格宽度#
        $objActSheet->getColumnDimension("$letter[$i]")->setWidth(20);
    }
    $objActSheet->getColumnDimension("$letter[1]")->setWidth(40);
    ####################################################################################筛选开始

    ###########################筛选开始
    #
    ############################排序开始

    ########################################################################################排序开始
    #设置表格数据#
    $tableData = array();
    $UploadPath = ROOT_PATH.config('pic.upload');

    foreach ($exportData as $i => $v) {

        $tableData[$i][0] = $i+1;
        $tableData[$i][1] = $v['telphone'];
        $tableData[$i][2] = $UploadPath.$v['img1'];
        $tableData[$i][3] = $UploadPath.$v['img3'];
        $tableData[$i][4] = $v['countryName'];
        $tableData[$i][5] = $v['nickname'];
        $tableData[$i][6] = $v['country'].' '.$v['province'].' '.$v['city'];
        $tableData[$i][7] = $v['ip'];
        $tableData[$i][8] = date('Y-m-d H:i', $v['sendtime']);
        
    }
	//echo '<pre>';print_r($tableData);exit;

    #填充表格内容#
    $dataCount = count($tableData);
    for ($i = 0;$i < $dataCount;$i++) {

        $j = $i + 2;
        #设置表格高度#
        $excel->getActiveSheet()->getRowDimension($j)->setRowHeight(100);

        #向每行单元格插入数据#
        $rowCount = count($tableheader);
        for ($row = 0;$row < $rowCount;$row++) {
            if (($row == 2 || $row == 3) && is_file($tableData[$i][$row])) {
                #实例化插入图片类#
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                #设置图片路径#
                $objDrawing->setPath($tableData[$i][$row]);
                #设置图片高度#
                $objDrawing->setHeight(100);
                #设置图片要插入的单元格#
                $objDrawing->setCoordinates("$letter[$row]$j");
                #设置图片所在单元格的格式#
                $objDrawing->setOffsetX(80);
                $objDrawing->setRotation(20);
                $objDrawing->getShadow()->setVisible(true);
                $objDrawing->getShadow()->setDirection(50);
                $objDrawing->setWorksheet($excel->getActiveSheet());
                continue;
            }
            $excel->getActiveSheet()->setCellValue("$letter[$row]$j",$tableData[$i][$row]);
        }
    }


    $write = new PHPExcel_Writer_Excel5($excel);
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");;
    header('Content-Disposition:attachment;filename="世棒料理投票统计数据.xls"');
    header("Content-Transfer-Encoding:binary");
    $write->save('php://output');

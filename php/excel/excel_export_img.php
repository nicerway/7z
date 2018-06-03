<?php
/*
    include '../include/Common/Library/Excel/PHPExcel.php';
    include_once './include/common.inc.php';
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    $excel = new PHPExcel();
    $objDrawing = new PHPExcel_Worksheet_Drawing();

    #设置文本对齐方式#
    $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objActSheet = $excel->getActiveSheet();

    // $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P');
    $letter = array('A','B','C','D');

    #设置表头数据#
    // $tableheader = array('sku', 'img', 'address', 'MFN', 'HOME', 'AFN', '美国', '加拿大', '西班牙', '英国', '德国', '西班牙', '法国', '意大利','合计');
    $tableheader = array('sku', 'img', '地址', '供货商');
    $FillTableCount = count($tableheader);
    #填充表格表头#
    for($i = 0;$i < $FillTableCount;$i++) {
        $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        #设置表格宽度#
        $objActSheet->getColumnDimension("$letter[$i]")->setWidth(20);
    }
    $objActSheet->getColumnDimension("$letter[1]")->setWidth(40);
    ####################################################################################筛选开始
    $map = $order = array();
        //供货商
        $tmpR = M('news_cats')->field('id,pid')->where('catname="供货商"')->find();
        $pid = $tmpR['pid'];
        $ty  = $tmpR['id'];
        $supplierArray = M('news')->where(array('pid'=>$pid,'ty'=>$ty,'isstate'=>1))->getField('id,title');
        $supplierArray[0] = '';
        //供货商结束
        $sku2   =   I('get.sku2','','trim'); if(!empty($sku2))$map['sku2'] = $sku2;
        $suppliers    =   I('get.suppliers',0,'intval'); if(!empty($suppliers))$map['suppliers'] = $suppliers;
    ###########################筛选开始
    #
    ############################排序开始
        $orderKey     =   I('get.orderKey','','trim'); $orderStyle   =   I('get.orderStyle','','trim');
        if(!empty($orderKey))$order[$orderKey] = $orderStyle;
        $orderString = '';
        foreach ($order as $ok => $ov) {
          $orderString .= "$ok $ov,";
        }
        $orderString = rtrim($orderString,',');
    ########################################################################################排序开始
    #设置表格数据#
    $data = array();
    //查询有多少个新的sku2
    $rootPath = C('ROOT_PATH').C('UPLOAD');
    $result = M('product_library')->field('sku2,imgurl,address,suppliers')->where($map)->order($order)->select();
    foreach ($result as $key => $v) {
        //if(!is_file($rootPath . $v['imgurl']))continue;
        $v['suppliers'] or $v['suppliers'] = 0;
        $data[$key][0] = $v['sku2'];
        $data[$key][1] = $rootPath . $v['imgurl'];
        $data[$key][2] = htmlspecialchars_decode( $v['address'] );
        $data[$key][3] = $supplierArray[$v['suppliers']];
    }

    #填充表格内容#
    $dataCount = count($data);
    for ($i = 0;$i < $dataCount;$i++) {

        $j = $i + 2;
        #设置表格高度#
        $excel->getActiveSheet()->getRowDimension($j)->setRowHeight(100);

        #向每行单元格插入数据#
        $rowCount = count($tableheader);
        for ($row = 0;$row < $rowCount;$row++) {
            if ($row == 1) {
                #实例化插入图片类#
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                if(!is_file($data[$i][$row]) )continue;
                #设置图片路径#
                $objDrawing->setPath($data[$i][$row]);
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
            $excel->getActiveSheet()->setCellValue("$letter[$row]$j",$data[$i][$row]);
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
    header('Content-Disposition:attachment;filename="测试文件.xls"');
    header("Content-Transfer-Encoding:binary");
    $write->save('php://output');*/

    include '../include/Common/Library/Excel/PHPExcel.php';
    include_once './include/common.inc.php';
    ini_set('display_errors', '1');
    $excel = new PHPExcel();
    $objDrawing = new PHPExcel_Worksheet_Drawing();

    #设置文本对齐方式#
    $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objActSheet = $excel->getActiveSheet();

    // $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P');
    $letter = array('A','B','C');

    #设置表头数据#
    // $tableheader = array('sku', 'img', 'address', 'MFN', 'HOME', 'AFN', '美国', '加拿大', '西班牙', '英国', '德国', '西班牙', '法国', '意大利','合计');
    $tableheader = array('sku', 'img', 'ishave');
    $FillTableCount = count($tableheader);
    #填充表格表头#
    for($i = 0;$i < $FillTableCount;$i++) {
        $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        #设置表格宽度#
        $objActSheet->getColumnDimension("$letter[$i]")->setWidth(20);
    }
    $objActSheet->getColumnDimension("$letter[1]")->setWidth(40);
    ####################################################################################筛选开始
    $map  = array('sku_id' => 0);
    $order = array();
    #按照上架排序 优先级最高
    $orderSold    =   I('get.orderSold','','trim'); if(!empty($orderSold))$order['issold'] = $orderSold;
        //供货商
        $tmpR = M('news_cats')->field('id,pid')->where('catname="供货商"')->find();
        $pid = $tmpR['pid'];
        $ty  = $tmpR['id'];
        $supplierArray = M('news')->where(array('pid'=>$pid,'ty'=>$ty,'isstate'=>1))->getField('id,title');
        //供货商结束
        $sku2   =   I('get.sku2','','trim'); if(!empty($sku2))$map['sku2'] = $sku2;
        $suppliers    =   I('get.suppliers',0,'intval'); if(!empty($suppliers))$map['suppliers'] = $suppliers;
    ###########################筛选开始
    #
    ############################排序开始
        $orderKey     =   I('get.orderKey','','trim'); $orderStyle   =   I('get.orderStyle','','trim');
        if(!empty($orderKey))$order[$orderKey] = $orderStyle;
        $orderString = '';
        foreach ($order as $ok => $ov) {
          $orderString .= "$ok $ov,";
        }
        $orderString = rtrim($orderString,',');
    ########################################################################################排序开始
    #设置表格数据#
    $data = array();
    //查询有多少个新的sku2
    $rootPath = C('ROOT_PATH').C('UPLOAD');
    $result = M('product_library')->where($map)->order($order)->getField('id', true);
    $i = 0;
    foreach ($result as $id) {
        $map = array(
            'sku_id' => $id,
        );
        $real_result = M('product_library')->field('sku2,sku,ishave')->where($map)->order($order)->select();
        foreach ($real_result as $v) {
            $data[$i][0] = $v['sku2'];
            $data[$i][1] = $rootPath.$v['sku'] . '.jpg';
            $data[$i][2] = $v['ishave'];
            ++$i;
        }
    }

    #填充表格内容#
    $dataCount = count($data);
    for ($i = 0;$i < $dataCount;$i++) {

        $j = $i + 2;
        #设置表格高度#
        $excel->getActiveSheet()->getRowDimension($j)->setRowHeight(100);

        #向每行单元格插入数据#
        $rowCount = count($tableheader);
        for ($row = 0;$row < $rowCount;$row++) {
            if ($row == 1 && is_file($data[$i][$row])) {
                #实例化插入图片类#
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                #设置图片路径#
                $objDrawing->setPath($data[$i][$row]);
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
            $excel->getActiveSheet()->setCellValue("$letter[$row]$j",$data[$i][$row]);
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
    header('Content-Disposition:attachment;filename="' . $system_usingShop . "店-" . $supplierArray[$suppliers] . '-产品库.xls"');
    header("Content-Transfer-Encoding:binary");
    $write->save('php://output');

<?php
require './include/common.inc.php';
require WEB_ROOT.'./include/chkuser.inc.php';
$table = $showname = 'data';

//条件
$map = array();

###########################筛选开始
$id = I('get.id','','trim');if(!empty($id))$map['id'] = array('like',"%$id%");
$warranty_no  = I('get.warranty_no','','trim');if(!empty($warranty_no))$map['warranty_no'] = array('like',"%$warranty_no%");
$baecode_no = I('get.baecode_no','','trim');if(!empty($baecode_no))$map['baecode_no'] = array('like',"%$baecode_no%");
$indata = I('get.indata','','trim');if(!empty($indata))$map['indata'] = array('like',"%$indata%");
$outdata = I('get.outdata','','trim');if(!empty($outdata))$map['outdata'] = array('like',"%$outdata%");
$country = I('get.country','','trim');if(!empty($country))$map['country'] = array('like',"%$country%");
$series = I('get.series','','trim');if(!empty($series))$map['series'] = array('like',"%$series%");
###########################筛选开始

########################分页配置开始
$psize   =   I('get.psize',30,'intval');
$pageConfig = array(
    /*条件*/'where' => $map,
    /*排序*/'order' => 'warranty_no asc',
    /*条数*/'psize' => $psize,
    /*表  */'table' => $table,
    );
list($data,$pagestr) = Page::paging($pageConfig);
########################分页配置结束
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <?php include('js/head'); ?>
</head>
<body>
    <div class="content clr">
        <div class="right clr">
                <form id="addform" action="excel_import_export.php" method="post" enctype="multipart/form-data">
                      <input type="file" name="file"> <input type="submit" class="layui-btn layui-btn-normal" value="导入excel">
                       <input type="hidden" name="action" value="import">
                      <!-- <a class="layui-btn layui-btn-warm" href="csv_import_export.php?action=export&pid=<?=$pid?>&ty=<?=$ty?>">导出CSV</a> -->
                </form>
              <form class="" id="jsSoForm">
                <b>显示</b><input style="width:50px;" name="psize" type="text" class="dfinput" value="<?=$psize?>"/>条
                <b>编号</b><input name="id" type="text" class="dfinput" value="<?=$id?>"/>
                <b>卷轴号</b><input name="warranty_no" type="text" class="dfinput" value="<?=$warranty_no?>"/>
                <b>保养卡号</b><input name="baecode_no" type="text" class="dfinput" value="<?=$baecode_no?>"/>
                <b>销往国家</b><input name="country" type="text" class="dfinput" value="<?=$country?>"/>
                <input name="search" type="submit" class="btn" value="搜索"/></td>

            </form>

        <?=$pagestr?>
            <div class="zhengwen">
                 <div class="zhixin clr">
                   <ul class="toolbar">
                       <li>&nbsp;<input style="display:none" type="checkbox"><i id="sall" class="alls" onclick="selectAll(this)">&nbsp;</i><label style="cursor:pointer;font-size:9px" onclick="selectAll(document.getElementById('sall'))" for="">全选</label></li></li>
                   </ul>
                   <a href="?<?=queryString()?>" class="zhixin_a2 fl"></a><!-- 刷新  -->
                   <!-- <a href="<?=getUrl(queryString(true),$showname.'_pro')?>" target="righthtml" class="zhixin_a3 fl"></a>-->
                   <input id="del" type="button" class="zhixin_a4 fl"/><!-- 删除  -->
                   <?php Style::moveback() ?>
            </div>
            <div class="neirong clr">
                <table cellpadding="0" cellspacing="0" class="table clr">
                 <tr class="first">
                    <td onclick="selectAll(document.getElementById('sall'))" style="font-size:8px;cursor:pointer" width="24px">全选</td>
                    <td width="24px">编号</td> <td width="150px">操作</td>

                    <td>卷轴号</td>
                    <td>保养卡号</td>
                    <td>入库时间</td>
                    <td>出库时间</td>
                    <td>销往国家</td>
                    <td>产品系列</td>
                    <td>导入时间</td>

                </tr>
                <?php
                    foreach ($data as $key => $bd) : extract($bd);

                    #生成修改地址
                    $query = queryString(true);
                    $query['id'] = $id;
                    $editUrl = getUrl($query, $showname.'_pro');
                ?>
        <tbody>
            <tr>
                <td><input id="delid<?=$id?>" name="del[]" value="<?=$id?>" type="checkbox"><i class="layui-i">&nbsp;</i></td>
                <td><?=$id?></td>
                <td>
                    <a href="<?=$editUrl?>" class="thick ">编辑</a>|
                    <a href="javascript:;" data-id="<?=$id?>" data-opt="del" class="thick del">删除</a>|
                    <!-- <a href="order.php?usrid=<?php echo $id ?>">订单(<?php //echo M(Order::TABLE)->where("usrid=$id")->count() ?>)</a> -->
                </td>
                <!-- <td><img src="<?=src($headimg, 'headImage', 'headImageDefault')?>"></td> -->
                <td><?=$warranty_no?></td>
                <td><?=$baecode_no?></td>
                <td><?=$indata?></td>
                <td><?=$outdata?></td>
                <td><?=$country?></td>
                <td><?=$series?></td>
                <td><?=date('Y-m-d', $sendtime)?></td>
            </tr>
        <?php endforeach?>
        <?php include('js/foot'); ?>


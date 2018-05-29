<?php
namespace Admin;

use  Core\response\Redirect as Redirect;
use Core\Page as Page;
// use App\model\SqlSrvModel as SqlSrvModel;

require './include/common.inc.php';
define('TABLE_NEWS',1);
require WEB_ROOT.'./include/chkuser.inc.php';
$table = $showname = 'candidate';

//条件
$map = array();

###########################筛选开始
// $id = I('get.id','','trim');if(!empty($id))$map['id'] = array('like',"%$id%");
$userName = I('get.userName','','trim');if(!empty($userName))$map['user_name'] = array('like',"%$userName%");
$telphone = I('get.telphone','','trim');if(!empty($telphone))$map['telphone'] = array('like',"%$telphone%");
###########################筛选开始

########################分页配置开始
$psize   =   I('get.psize',30,'intval');
$pageConfig = array(
    /*条件*/'where' => $map,
    /*排序*/'order' => 'sendtime desc',
    /*条数*/'psize' => $psize,
    /*表  */'table' => $table,
    );
list($data,$pagestr) = Page::paging($pageConfig);
$opt = new Output;//输出流  输出表单元素
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
              <form class="" id="jsSoForm">
                <b>显示</b><input style="width:50px;" name="psize" type="text" class="dfinput" value="<?=$psize?>"/>条
                <b>编号</b><input name="id" type="text" class="dfinput" value="<?=$id?>"/>
                <b>姓名</b><input name="userName" type="text" class="dfinput" value="<?=$userName?>"/>
                <b>手机号</b><input name="telphone" type="text" class="dfinput" value="<?=$telphone?>"/>
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

                    <?php
                        $opt
                            ->td('会员编号')
                            ->td('会员卡号')
                            ->td('昵称-头像')
                            ->td('姓名-手机号')
                            ->td('最后登录')
                            ->td('注册时间')
                        ;
                    ?>

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
                    <a href="javascript:;" data-id="<?=$id?>" data-opt="del" class="thick del">删除</a><br><b>优惠券</b>:
                    <a class="add-<?=$showname?>" href="<?=$showname?>_pro.php?id=<?=$id?>">新增</a>-
                    <!-- <a href="order.php?usrid=<?php echo $id ?>">订单(<?php //echo M(Order::TABLE)->where("usrid=$id")->count() ?>)</a> -->
                </td>
                <?php
                    $opt
                        ->td($id)
                        ->td($card_number)
                        ->td($nick_name.'<b style="color:red;">-</b>'.'<img src="'.$head_image.'">')
                        ->td($user_name.'<b style="color:red;">-</b>'.$telphone)
                        ->td(date('Y-m-d', $lastlogintime))
                        ->td(date('Y-m-d', $regtime))
                    ;
                ?>
            </tr>
        <?php endforeach?>

        <input id="zIndexOffset" type="hidden" value="19891093">
        <script>

          $('.add-<?=$showname?>').click(function(){

              href = this.href;

              text = $(this).text();

              var theIndex = layer.open({

                    type: 2,
                    title: text,
                    shadeClose: true,
                    anim: 1,
                    resize: true,
                    shade: false,
                    moveOut: true,
                    maxmin: true, //开启最大化最小化按钮
                    area: ['45%', '45%'],
                    content: href

                  });

                $(this).data('layerid',theIndex);

                  return false;

          })

        </script>
        <?php include('js/foot'); ?>


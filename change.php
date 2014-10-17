<?php
include "config.php";

$username = isset($_REQUEST['username'])?$_REQUEST['username']:'';

if(!$username){
	die("参数不完成，请从提现通任务页面进入");
} 
$sm = $pdo->prepare("SELECT account_id FROM mkq_account where username=?");
$sm->execute(array($username));
$data = $sm->fetch(PDO::FETCH_ASSOC);
$sm->closeCursor();
$id = $data['account_id'];

$sm1 = $pdo->prepare("SELECT * FROM mkq_ac_coupon where account_id=? and status=1");
$sm1->execute(array($id));
$data1 = $sm1->fetchAll(PDO::FETCH_ASSOC);
$sm1->closeCursor();
$count = count($data1);
var_dump($data1);			
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>十万大礼先到先得</title>
    <link href="./css/style.css" rel="stylesheet" type="text/css"/>
	    <script type="text/javascript" src="./js/jquery-2.0.3.min.js"></script>
	    <script type="text/javascript" src="./js/jquery.cookie.js"></script>
</head>

<body>
<div class="header">
    <img src='./image/head.jpg'>
</div>
<div style="margin-top:20px;">
<table height="32" width="270" border="1" cellpadding="0" cellspacing="0" style=' border: #f55321 solid 2px; margin:0 auto;font-size:16px;text-align:center'>
<tr style="">
<td style="border-style:none;"><a style=" text-decoration:none;color:#f55321" href="index.php?username=<?php echo isset($_REQUEST['username'])?$_REQUEST['username']:'';?>">活动规则</a></td>
<td style="background:#f55321;border-style:none;color:white">兑换礼品</td>
</tr>
</table>
</div>
<div class='sty'>
<p style="text-align:center;font-size:20px;color:#ff1717;padding-top:30px;">请在此输入你的礼包兑换码</p>
<div class="border" id="border" style="height:32px;margin-top:20px;">
<span style="color:#f55321;padding-left:10px;">兑换码：</span>
<input style="width:100px;line-height:32px;" type="text" id="invite_no" placeholder="请输入兑换码" />
</div>
<p style="text-align:center;color:#333333;padding-top:10px;">每个兑换码只可以领取1份礼品哦！</p>

<div style="margin-top:30px;text-align:center"><img id="get" width=150 src='./image/get.png'></div>
<div style="margin-top:30px;text-align:center"><img id="done" style="-webkit-filter: grayscale(100%);display:none" width=150 src='./image/get.png'></div>

<div class="border" id="bor1" style="width:260px;margin-left:2px;margin-top:20px;padding-right:10px;display:none;">
<p style="padding:10px;"><span style="color:#f55321;">恭喜你获得快的打车红包、布丁酒店优惠券、格瓦拉电影5元抵价券<?php if($count==2){echo "、彩票365彩金卡5元";}if($count==3){echo "、彩票365彩金卡5元、聚美优品优惠券";}?>！</span></p>
<p style="padding:10px;padding-top:0px;"><b>1.快的打车最高10元红包</b>
<div style="margin-top:4px;text-align:center"><a href='http://u.shequan.com/pd'><img width=130 src='./image/hit.png'></a></div>
</p>
<p style="padding:10px;padding-top:0px;"><b>2.布丁酒店200元大礼包</b><br>
券码：<span style="color:#f55321;">buding </span> <br>
<span style="font-size:11px;">使用说明<br>
• 10月31日前，可凭券码在<b>布丁酒店手机客户端</b>内兑换礼包<br>
• 礼包内含6张20元优惠券+8张10元优惠券<br>
• 具体使用说明详见<b>布丁酒店手机客户端</b><br>
</span>
</p>
<?php
	if($data1){
		foreach($data1 as $v){
			if($v['type']==1){
				echo "<p style='padding:10px;padding-top:0px;'><b>3.格瓦拉电影5元抵价券</b>
				<br>券码：<span style='color:#f55321;' id='code1'>".$v['info']."</span><br>
				<span style='font-size:11px;'>使用说明：<br>
				• 登录格瓦拉<b>官网</b>或<b>手机客户端</b>---选择影院、影片、场次---选择座位---<b>在支付页面输入券码</b>--支付剩余票款--凭短信至影院取票<br>
				• 每张券抵值5元，单笔订单仅限使用一次<br>
				• 有效期至2014-12-31<br>
				• 特殊场次、见面会、电影节、特殊节日以及IMAX场次不可使用本券，详见格瓦拉官网。</span></p>";
			}
			if($v['type']==2){
				$arr = explode(',',$v['info']);
				echo "<p style='padding:10px;padding-top:0px;' id='p4'><b>4.彩票365彩金卡5元</b>
					<br><span style='color:#f55321;' id='code2'>卡号:".@$arr[0]."密码:".@$arr[1]."</span><br>
					<span style='font-size:11px;'>奖品说明：<br>
					• 下载彩票365手机客户端，注册并完善个人信息，进入“充值”页面，选择“彩金卡充值”，输入卡号和密码即可。彩金即刻到账，可用于购买任意彩种。<br>
					使用规则：<br>
					• 每个用户只可兑换一次<br>
					• 有效期：2014-09-11至2015-09-11</span></p>";
			}
			if($v['type']==4){
				echo "<p style='padding:10px;padding-top:0px;' id='p5'><b>5.聚美优品优惠券</b>
					<br>券码：<span style='color:#f55321;' id='code4'>".$v['info']."</span><br>
					<span style='font-size:11px;'>使用规则：<br>
					• 化妆品全场通用，满200元减50元，每个优惠券号码仅限使用一次<br>
					• 登录或注册聚美优品官网，购物结算时直接输入优惠券码即可使用，限新用户可用（新用户是指未注册，或注册未完成交易者）</span></p>";
			}
		}
	}

?>
</div>

<div class="border" id="bor2" style="width:260px;margin-left:2px;margin-top:20px;padding-right:10px;display:none;">
<p style="padding:10px;"><span style="color:#f55321;">恭喜你获得快的打车红包、布丁酒店优惠券、格瓦拉电影5元抵价券<?php if($count==2){echo "、彩票365彩金卡5元";}if($count==3){echo "、彩票365彩金卡5元、聚美优品优惠券";}?>！</span></p>
<p style="padding:10px;padding-top:0px;"><b>1.快的打车最高10元红包</b>
<div style="margin-top:4px;text-align:center"><a href='http://u.shequan.com/pd'><img width=130 src='./image/hit.png'></a></div>
</p>
<p style="padding:10px;padding-top:0px;"><b>2.布丁酒店200元大礼包</b><br>
券码：<span style="color:#f55321;">buding </span> <br>
<span style="font-size:11px;">使用说明<br>
• 10月31日前，可凭券码在<b>布丁酒店手机客户端</b>内兑换礼包<br>
• 礼包内含6张20元优惠券+8张10元优惠券<br>
• 具体使用说明详见<b>布丁酒店手机客户端</b><br>
</span>
</p>
<p style='padding:10px;padding-top:0px;'><b>3.格瓦拉电影5元抵价券</b>
<br>券码：<span style='color:#f55321;' id='code1'></span><br>
<span style='font-size:11px;'>使用说明：<br>
• 登录格瓦拉<b>官网</b>或<b>手机客户端</b>---选择影院、影片、场次---选择座位---<b>在支付页面输入券码</b>--支付剩余票款--凭短信至影院取票<br>
• 每张券抵值5元，单笔订单仅限使用一次<br>
• 有效期至2014-12-31<br>
• 特殊场次、见面会、电影节、特殊节日以及IMAX场次不可使用本券，详见格瓦拉官网。</span></p>
<p style='padding:10px;padding-top:0px;' id='p4'><b>5.彩票365彩金卡5元</b>
<br><span style='color:#f55321;' id='code2'></span><br>
<span style='font-size:11px;'>奖品说明：<br>
• 下载彩票365手机客户端，注册并完善个人信息，进入“充值”页面，选择“彩金卡充值”，输入卡号和密码即可。彩金即刻到账，可用于购买任意彩种。<br>
使用规则：<br>
• 每个用户只可兑换一次<br>
• 有效期：2014-09-11至2015-09-11</span></p>
<p style='padding:10px;padding-top:0px;' id='p5'><b>4.聚美优品优惠券</b>
<br>券码：<span style='color:#f55321;' id='code4'></span><br>
<span style='font-size:11px;'>使用规则：<br>
• 化妆品全场通用，满200元减50元，每个优惠券号码仅限使用一次<br>
• 登录或注册聚美优品官网，购物结算时直接输入优惠券码即可使用，限新用户可用（新用户是指未注册，或注册未完成交易者）</span></p>

</div>

</div>
<div style="margin-top:30px;text-align:center"><a href='weixin://'><img width=280 src='./image/banner.jpg'></a></div>

<br>
<br>
<script>
    var account_id = "<?php echo $id; ?>";
    var count = "<?php echo $count; ?>";
	if(count!=0){
		$("#get").hide();
		$("#done").show();
		$("#bor1").show();
/* 			$("#code1").html($.cookie('info1'));	
			$("#code2").html($.cookie('info2'));	
			$("#code4").html($.cookie('info4'));	 */


	 }else{
		$("#get").bind("click", function () {
		var no = $("#invite_no").val();
 		$.post('process.php','no=' + no + '&account_id=' + account_id + '&app_name=' + app_name,function(d){
		var arr = JSON.parse(d);
			if(arr.info==0){alert("您的兑换码填写错误。");}
			else if(arr.info==1){alert("您的兑换码已经使用过。");}else{
				$("#bor2").show();
				$("#get").hide();
				$("#done").show();
		}}) 
    });	
	 } 

</script>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Fb5d6dc32c275bb79b68baf7fddfab469' type='text/javascript'%3E%3C/script%3E"));
</script>

</body>
</html>









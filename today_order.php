<?php
	include_once "include/function.php";
	checkLogin();
	$user = $_SESSION['user'];
	if(!is_admin($user)){
		echo '别试了，您不是超级管理员，无法访问！';
		die;
	}
	$order_items = getTodayOrderList();
 ?>
<!DOCTYPE html>
<html style="height:100%;">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>安庆小吃快捷通道</title>
<meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'/>
<link rel="stylesheet" href="static/css/common.css">
<link href="http://cdn.bootcss.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<style>
.order_list{ width: 100%; padding-bottom: 5rem; min-height: 40rem;}
.order_item{border-bottom: 1px solid #dfdfdf; padding: 0.4rem 0.8rem 0.27rem 0.8rem; }
.top{width: 100%; height: 2.5rem;}
.top .left_nav{ width: 70%; float: left;}
.top .bussiness{font-size: 1rem; font-weight: bolder; color: #000;}
.top .created_at{font-size: 0.5rem; height: 0.8rem; color: #909090; padding-top: 0.3rem;}
.top .order_status{ width:30%; float: right; text-align: right; font-size: 0.8rem; color: #666;}
.center{width: 100%;}
ul.food_container{}
ul.food_container .food{width: 100%; height: 1rem; line-height: 1rem; font-size: 0.8rem; padding-bottom: 1.2rem;}
.food .food_name{width: 60%; float: left; text-align: left;}
.food .food_num{width: 20%; float: left; text-align: left;}
.food .food_price{width: 20%; float: left; text-align: right;}
.food_price i{font-size: 0.6rem; font-style:normal; }
.total_price{font-size: 1rem; font-weight: bolder; color: #555; padding-top: 0.4rem;}
.op_area{ width: 100%; text-align: right; height: 2rem; line-height: 2rem; margin-bottom: 0.5rem;}
.op_btn{ cursor: pointer; padding: 0.3rem 0.5rem;  font-size: 0.5rem; border-radius: 0.3rem; }
.op_btn.cancel{border: 1px solid #ff9933; color: #ff9933}
.op_btn.unpay{border: 1px solid #777777; color: #777777}
.op_btn.paid{border: 1px solid #3296ff; color: #3296ff}
.data_cal{ cursor: pointer; position: absolute; z-index: 99; right: 0.8rem; bottom: 3rem; width: 2.5rem; height: 2.5rem; line-height: 1.5rem; font-size: 1rem; padding: 0.5rem; border-radius: 50%; background-color: #3296ff; color: #ffffff; text-align: center; }
#cal_res{ display: none; overflow-y: scroll; width: 90%; padding: 0.8rem; font-size: 0.8rem; border: 1px solid #777; background-color: #fafafa; min-height: 20rem; color: #333; text-align: left; position: absolute; z-index: 50; top: 5rem; left: 5%; max-height: 70%;}
#cal_res p{padding-top: 0.3rem;}
.close_btn{position: absolute; right: 0; top: 0; background-color: #3296ff; width: 1rem; height: 1rem;  font-size: 1rem; line-height: 1rem; text-align: center; color: #ffffff}
</style>
</head>

<body style="position:relative; width:100; height:100%; overflow:hidden">
<div class="data_cal" onclick="calculate()"><i class="fa fa-pencil"></i></div>
<div id="cal_res"><div class='close_btn'>×</div><div class="cal_content"></div></div>
<header class="sw-header sw-header-default">
	<div class="sw-header-left sw-header-nav">
        <a href="javascript: void(0)" class="" onclick="history.go(-1)">
            <i class="sw-header-icon fa fa-arrow-left"></i>
        </a>
    </div>
    <h1 class="sw-header-title">
        <a href="javascript: void(0)">今日订单</a>
    </h1>
    <div class="sw-header-right sw-header-nav">
        <a href="/">
            <i class="sw-header-icon fa fa-home"></i>
        </a>
    </div>
</header>
<div class="main" style="position: absolute; top: 1.95rem; bottom: 2rem; width:100%; overflow-y:scroll;">
	<div class="content_section">
        <div class="section-body" style="background-color: #ffffff; margin-bottom:0.5rem;">
        	<ul class="order_list">
        	<?php
        		$order_html = '';
        		foreach ($order_items as $order) {
	        		$order_html.='<li class="order_item" data-order-no="'.$order->order_no.'">';
	        		$order_html.='	<div class="top">';
	        		$order_html.='		<div class="left_nav">';
	        		$order_html.='			<p class="bussiness">'.$order->username.'</p>';
	        		$order_html.='			<p class="created_at">'.$order->created_at.'</p>';
	        		$order_html.='		</div>';
	        		$order_html.='		<div class="order_status">'.getOrderStatusZh($order->status).'</div>';
	        		$order_html.='	</div>';
	        		$order_html.='	<div class="center">';
	        		$order_html.='		<ul class="food_container">';
	        		foreach (json_decode($order->food_info) as $food_id => $food) {
	        			$order_html.='			<li class="food">';
		        		$order_html.='				<div class="food_name">'.$food->food_name.'</div>';
		        		$order_html.='				<div class="food_num">×'.$food->food_num.'</div>';
		        		$order_html.='				<div class="food_price"><i>￥</i>'.strval($food->food_price / 100).'</div>';
		        		$order_html.='			</li>';
	        		}
	        		$order_html.='		</ul>';
	        		$order_html.='		<p class="total_price">￥'.strval($order->price / 100).'</p>';
	        		$order_html.='	</div>';
	        		$order_html.='	<div class="op_area">';
	        			$order_html.='		<span class="op_btn cancel" onclick="change_order(this,\''.$order->order_no.'\',\'cancel\')">标记已作废</span>';
	        			$order_html.='		<span class="op_btn unpay" onclick="change_order(this,\''.$order->order_no.'\',\'unpay\')">标记未付款</span>';
	        			$order_html.='		<span class="op_btn paid" onclick="change_order(this,\''.$order->order_no.'\',\'paid\')">标记已付款</span>';
	        		$order_html.='	</div>';     			
	        		$order_html.='</li>';
	        	}
	        	echo $order_html;
    		?>	
        	</ul>
        </div>
	</div>
</div>

<div class="nav-bottom">
	<a href="/"><div class="nav-btn">首页</div></a>
    <a href="pay.html"><div class="nav-btn">扫码付款</div></a>
    <a href="my_order.php"><div class="nav-btn">我的订单</div></a>
</div>
</body>
<script type="text/javascript">
	$(function(){
		$('.close_btn').click(function(){
			$('#cal_res').hide();
			return;
		});
	});

	function change_order(ele,order_no,type){
		var flag = $(ele).text();
		if(confirm("确认将该订单状态"+ flag +"？")){
			var status = flag.substring(2);
			$.ajax({
				url : "include/webservice.php",
				type : "post",
				data : {action : "change_order",order_no : order_no,type : type},
				success : function(result){
					var res = eval("("+result+")");
					if(res.status == 0){
						$(ele).parents('li.order_item').find('.order_status').text(status);
						$(ele).remove();
					}else{
						alert(res.msg);
			            return;
					}
					
				}
			});
		}		
	}

	function calculate(){
		var html = '';
		$.ajax({
			url : "include/webservice.php",
			type : "post",
			data : {action : "calculate"},
			success : function(result){
				var res = eval("("+result+")");
				if(res.status == 0){
					var data = res.data;
					for (var i in data) {
						html+='<p>'+data[i].username+'&nbsp&nbsp&nbsp&nbsp&nbsp'+data[i].foods+'&nbsp&nbsp&nbsp&nbsp&nbsp'+data[i].total_price+'元</p>';
						console.log(data[i]);
					};
					$('#cal_res .cal_content').html(html);
					$('#cal_res').show();
				}else{
					alert(res.msg);
		            return;
				}
				
			}
		});
	}
</script>
</html>

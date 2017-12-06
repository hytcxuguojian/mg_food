<?php
	include_once "include/function.php";
	checkLogin();
	$user = $_SESSION['user'];
	$order_items = getUserOrderList($user->id);
	var_dump(calculate(date('Y-m-d',time())));die;
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
.buy_again,.cancel{ cursor: pointer; padding: 0.3rem 0.5rem; font-size: 0.5rem; border: 1px solid #3296ff; color: #3296ff; border-radius: 0.3rem; }
.cancel{ border: 1px solid #ff9933; color: #ff9933;}

</style>
</head>

<body style="position:relative; width:100; height:100%; overflow:hidden">
<header class="sw-header sw-header-default">
	<div class="sw-header-left sw-header-nav">
        <a href="javascript: void(0)" class="" onclick="history.go(-1)">
            <i class="sw-header-icon fa fa-arrow-left"></i>
        </a>
    </div>
    <h1 class="sw-header-title">
        <a href="javascript: void(0)">历史订单</a>
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
	        		$order_html.='			<p class="bussiness">安庆小吃</p>';
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
	        		if($order->status == 0){
	        			$order_html.='		<span class="cancel" onclick="change_order(this,\''.$order->order_no.'\',\'cancel\')">作废此单</span>';
	        		}
	        		$order_html.='		<span class="buy_again" onclick="buy_again(\''.$order->order_no.'\')">再来一单</span>';
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
    <a href="my_order.php"><div class="nav-btn active">我的订单</div></a>
</div>
</body>
<script type="text/javascript">
	$(function(){

	});

	function buy_again(order_no){
		$.ajax({
			url : "include/webservice.php",
			type : "post",
			data : {action : "buy_again",order_no : order_no},
			success : function(result){
				var res = eval("("+result+")");
				if(res.status == 0){
					window.localStorage.cart = res.data;
					window.location.href = 'index.php';
				}else{
					alert(res.msg);
		            return;
				}
				
			}
		});
	}

	function change_order(ele,order_no,type){
		if(confirm("确认作废该订单么？")){
			$.ajax({
				url : "include/webservice.php",
				type : "post",
				data : {action : "change_order",order_no : order_no,type : type},
				success : function(result){
					var res = eval("("+result+")");
					if(res.status == 0){
						$(ele).parents('li.order_item').find('.order_status').text('已作废');
						$(ele).remove();
					}else{
						alert(res.msg);
			            return;
					}
					
				}
			});
		}
		
	}
</script>
</html>

<?php
	include_once "include/function.php";
	checkLogin();
	$user = $_SESSION['user'];
	$food_data = json_decode(getFoodDataByBid(1));
 ?>
<!DOCTYPE html>
<html style="height:100%;">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>魔格午餐快捷绿色通道</title>
<meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'/>
<link rel="stylesheet" href="static/css/common.css">
<link href="http://cdn.bootcss.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<style>
#food_category_area{ height: 100%; min-height: 300px; width:30%; float:left; background-color:#f0f0f0;}
#food_category_list{ width:100%;}
.food_category{width: 100%; height: 2rem; text-align: left; color: #111; font-weight: bolder; padding-left: 0.5rem; line-height: 2rem; font-size: 1rem; overflow: hidden;}
.first_food_category{border-left: 0.2rem solid #ff9933; background-color: #ffffff;}
#food_area{ min-height: 300px; width:70%;  height: 100%; overflow-y:scroll; float:left; background-color:#ffffff; padding-bottom: 0.5rem;}
.hidden_div{ display: none; width: 70%; position: absolute; top: 0; left: 30%; height: 100%; overflow-y: scroll;}
.first_food_category .hidden_div{display: block;}
.food_list{ width: 100%; padding-bottom: 5rem;}
.food_list li{ padding: 0.5rem 0.5rem 0 0.3rem; }
.food_name{ height: 1rem; font-size: 1rem; line-height: 1rem; color: #666;}
.food_op{width: 100%; padding-top: 0.3rem; height: 1.5rem; line-height: 1.5rem;}
.food_price{ width: 50%; height: 1rem; line-height: 1rem; font-size: 0.9rem; color: #ff9933; float: left;}
.op_btn{float: right; height: 1rem; line-height: 1rem; width: 50%; text-align: right;}
.op_btn .num{ display: inline-block; visibility: hidden; height: 0.9rem; text-align: center; line-height: 0.9rem; color: #000000; padding: 0 0.2rem 0 0.2rem;}
.op_btn .add_btn,.op_btn .del_btn{display: inline-block; cursor: pointer; width: 1rem; height: 1rem; text-align: center; line-height: 1rem; border-radius: 50%; color: #ffffff}
.op_btn .add_btn{ background-color: #ff9933; }
.op_btn .del_btn{ visibility: hidden; background-color: #cccccc;}
.cart{ display: none; width: 100%; height: 2.5rem; color: #ffffff; line-height: 2.5rem; background-color: rgba(255,153,51,0.8); position: absolute; left: 0; bottom: 0; }
.cart .cart_icon,.cart .total_price{display: inline-block; position: relative; float: left; margin-left: 1rem}
.cart .clear_btn,.cart .ok_btn{display: inline-block; cursor: pointer; float: right; font-weight: bolder; margin:0.5rem 0.5rem 0 0; padding: 0 0.5rem; height: 1.5rem; line-height: 1.5rem; border-radius: 0.8rem; font-size: 0.8rem; background-color: #ffffff; color: #ff9933;}
.food_cnt{ display:block; position: absolute; right: -1rem; top: 0.15rem; background-color: #ffffff; color: #777; font-size: 0.8rem; width: 1rem; height: 1rem; line-height: 1rem; text-align: center; border-radius: 50%;}
.menban{ display:none; position: absolute; left: 0; bottom: 2.5rem; cursor:pointer; width:100%; height:50rem; z-index: 10; background-color: rgba(0,0,0,0.7);}
.cart_detail{ position: absolute; left: 0; bottom: 0; width: 100%; height: 15rem; background-color: #fcfcfc;}
.food_detail{width: 100%; height: 13.5rem; overflow: scroll;}
.cart_li{ border-bottom: 1px solid #dfdfdf; height: 2rem; line-height: 2rem; font-size: 1rem; color: #555;}
.li_food_name,.li_food_price,.li_food_cnt{float: left; padding-left: 1rem; text-align: left;}
.li_food_name{ width: 60%;}
.li_food_price{ width: 20%;}
.li_food_cnt{ width: 20%;}
#manage_page{position: absolute; bottom: 6rem; border-radius: 50%; left: 0.8rem; z-index: 40; width: 3rem; height: 3rem; background-color: #3296ff; }
</style>
</head>

<body style="position:relative; width:100; height:100%; overflow:hidden;">
<?php
if(is_admin($user)){
	echo '
<div id="manage_page">
	<a href="/today_order.php" style="width:100%; height:100%; display:block; line-height: 3rem; color: #ffffff; font-weight:bolder; text-align: center; font-size:2rem;">今</a>
</div>';
}
?>
<header class="sw-header sw-header-default">
	<div class="sw-header-left sw-header-nav">
        <a href="javascript: void(0)" class="" onclick="history.go(-1)">
            <i class="sw-header-icon fa fa-arrow-left"></i>
        </a>
    </div>
    <h1 class="sw-header-title">
        <a href="javascript: void(0)">欢迎预定，<?php echo $user->username; ?></a>
    </h1>
    <div class="sw-header-right sw-header-nav">
        <a href="/">
            <i class="sw-header-icon fa fa-home"></i>
        </a>
    </div>
</header>
<div class="main" style="position: absolute; top: 1.95rem; bottom: 2rem; width:100%; overflow-y:scroll;">
	<div class="content_section" style="background-color: #ffffff; height:100%; position:relative;">
		<div style="height:100%; ">
            <div class="section-body" style="height:100%;">
            	<div id="show_food clearfix" style="position:relative; width:100%; height:100%;">
            		<div id="food_category_area">
	            		<ul id="food_category_list">
	            		<?php
		                    $html = '';
		                    $is_first = ' first_food_category';
		                    foreach ($food_data as $item ) {
		                        $html.='<li class="food_category'.$is_first.'">'.$item->food_category_name.'<div class="hidden_div"><ul class="food_list">';
	                            foreach ($item->food_list as $food ) {
	                                $html.='<li class="food clearfix" data-id="'.$food->food_id.'">
			                                	<p class="food_name">'.$food->food_name.'</p>
				        						<div class="food_op"><div class="food_price" data-value='.($food->food_price / 100).'>￥'.($food->food_price / 100).'元</div>
				        							<div class="op_btn">
				        								<span class="del_btn">-</span>
				        								<span class="num">0</span>
				        								<span class="add_btn">+</span>
				        							</div>
				        						</div>
				        					</li>';
				        			$is_first = '';
	                            }
			                        $html.='</ul>
				        			</div>
		            			</li>';                        
			                    }
		                    echo $html;
		                ?>
	            		</ul>
            		</div>
            		<div id="food_area">
            		</div>
            	</div>
            </div>
        </div>
        <div class="cart">
        	<div class="cart_icon" style="font-size:1.5rem; cursor:pointer;"><span class="food_cnt" >0</span><i class="fa fa-cart-arrow-down" style="color:#ffffff;"></i></div>
        	<div class="total_price"><span>￥</span><span class="price_num">0</span></div>
        	<div class="ok_btn" onclick="ensure()">选好了</div>
        	<div class="clear_btn">清空</div>
        	<div class="menban">
        		<div class="cart_detail">
        			<div class="slow_down" style="width:100%; height:1.5rem; line-height:1.5rem; background-color:#f0f0f0; text-align:center; color:#555; font-size:1.2rem;"><i class="fa fa-angle-double-down"></i></div>
        			<ul class="food_detail">
	        		</ul>
        		</div>
        	</div>
        	
        </div>
	</div>
</div>

<div class="nav-bottom">
	<a href="/"><div class="nav-btn active">首页</div></a>
    <a href="pay.html"><div class="nav-btn">扫码付款</div></a>
    <a href="my_order.php"><div class="nav-btn">我的订单</div></a>
</div>
</body>
<script type="text/javascript">
    var storage = window.localStorage;
    var car_data = getCartData();
	var total_price = init_total_price();
	var total_food_cnt = init_total_food_cnt();
	$(function(){

        pageInit();

		$('li.food_category').hover(function(){
			$('.first_food_category').removeClass('first_food_category');
			$('.hidden_div').hide();
			$('li.food_category').css({"border-left": "","background-color": ""});
		    $(this).find('.hidden_div').show();
		    $(this).css({"border-left": "0.2rem solid #ff9933","background-color": "#ffffff"});
		},function(){

		});

		//增加商品数量
		$(document).on('click','.add_btn',function(){
			var num = parseInt($(this).siblings('.num').text()) + 1;
			if(num > 5){
				return;
			}
			var single_price = $(this).parents('.food').find('.food_price').attr('data-value');
			var food_id = $(this).parents('.food').attr('data-id');		
			var food_name = $(this).parents('.food').find('.food_name').text();
			cartPush(food_id,single_price,food_name);
			pageInit();
		});

		//减少商品数量
		$(document).on('click','.del_btn',function(){			
			var num = parseInt($(this).siblings('.num').text()) - 1;
			if(num < 0 || total_food_cnt < 0){
				return;
			}
			var single_price = $(this).parents('.food').find('.food_price').attr('data-value');
			var food_id = $(this).parents('.food').attr('data-id');		
			var food_name = $(this).parents('.food').find('.food_name').text();
			cartPop(food_id,single_price,food_name);
			pageInit();
		});

		//清空购物车
		$('.clear_btn').click(function(){
			clearCartData();
			window.location.href = '?'+Date.parse(new Date());;
		});

		//购物车点击事件
		$('.cart_icon').click(function(){
			$('.menban').slideToggle(800);
		});

		$('.slow_down').click(function(){
			$('.menban').slideUp(800);
		});
	});

	//页面初始化
	function pageInit(){
		initFoodDetail();
		initFoodNum();
		initCartArea();		
	}

	//根据本地存储购物车数据初始化结算商品明细
	function initFoodDetail(){
		var food_detail = '';
		for(var food in car_data){
			food_detail += '<li class="cart_li" data-foodid="'+car_data[food].food_id+'" data-cnt="'+car_data[food].food_num+'">';
		    food_detail += '		<div class="li_food_name">'+car_data[food].food_name+'</div>';
		    food_detail += '    	<div class="li_food_price">￥'+car_data[food].single_price+'</div>';
		    food_detail += '    	<div class="li_food_cnt">X '+car_data[food].food_num+'</div>';
		    food_detail += '</li>';
		}
		$('.food_detail').html(food_detail);
	}

	//根据本地存储购物车数据初始化商品选购数量
	function initFoodNum(){
		$('.num').css('visibility','hidden');
		$('.del_btn').css('visibility','hidden');
		for(var food in car_data){
			var li = $('li.food[data-id="'+car_data[food].food_id+'"]');
			li.find('.num').css('visibility','visible').text(car_data[food].food_num);
			li.find('.del_btn').css('visibility','visible');
		}
	}

	//根据本地存储购物车数据初始化购物车结算区
	function initCartArea(){
		$('.price_num').text(total_price);
		$('.food_cnt').text(total_food_cnt);
		if(car_data.length > 0){
			$('.cart').show();
		}else{
			$('.cart').hide();
		}				
	}

	//从本地存储获取购物车数据
	function getCartData(){
		if(typeof(storage.cart) == "undefined"){
			storage.cart = JSON.stringify(new Array());
		}
		return JSON.parse(storage.cart);
	}

	function init_total_food_cnt(){
		var temp = 0;
		for(var food in car_data){
			temp += parseInt(car_data[food].food_num);
		}
		return temp;
	}

	function init_total_price(){
		var temp = 0;
		for(var food in car_data){
			temp += parseFloat(car_data[food].food_num)*parseFloat(car_data[food].single_price);
		}
		return temp;
	}

	//加入购物车
	function cartPush(food_id,single_price,food_name){
		var is_contain = false;
		for (var food in car_data) {
    		if(car_data[food].food_id == food_id){
    			car_data[food].food_num = parseInt(car_data[food].food_num) + 1;
    			is_contain = true;
    		}
    	};
    	if(!is_contain){
    		car_data.push({'food_id':food_id,'food_num':1,'single_price':single_price,'food_name':food_name});
    	}
    	storage.cart = JSON.stringify(car_data);
    	total_food_cnt += 1;
    	total_price += parseFloat(single_price);
	}

	//移出购物车
	function cartPop(food_id,single_price,food_name){
		for (var food in car_data) {
    		if(car_data[food].food_id == food_id){
    			var num = parseInt(car_data[food].food_num) - 1;
    			if(num <= 0){
    				car_data.splice(food,1);
    			}else{
    				car_data[food].food_num = num;
    			}    			
    		}
    	};    	
    	storage.cart = JSON.stringify(car_data);
    	total_food_cnt -= 1;
    	total_price -= parseFloat(single_price);
	}

	//清除购物车数据的本地存储
	function clearCartData(){
		storage.removeItem("cart");
	}

	function ensure(){
		$.ajax({
			url : "include/webservice.php",
			type : "post",
			data : {action : "ensure",foods : JSON.stringify(car_data)},
			success : function(result){
				var res = eval("("+result+")");
				if(res.status == 0){
					clearCartData();
					window.location.href = 'my_order.php';
				}else{
					alert(res.msg);
		            return;
				}
				
			}
		});
	}
</script>
</html>

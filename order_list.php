<?php
	include_once "include/function.php";
	checkLogin();
	$food_data = json_decode(getFoodData());
 ?>
<!DOCTYPE html>
<html style="height:100%;">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>安庆小吃快捷通道</title>
<meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'/>
<link rel="stylesheet" href="static/css/common.css">
<link href="http://cdn.bootcss.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
<link href="http://cdn.bootcss.com/Swiper/3.3.1/css/swiper.min.css" rel="stylesheet">
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/Swiper/3.3.1/js/swiper.min.js"></script>
<style>
#food_category_area{ height: 100%; min-height: 300px; width:30%; float:left; background-color:#f0f0f0;}
#food_category_list{ width:100%;}
.food_category{width: 100%; height: 2rem; text-align: left; font-weight: bolder; padding-left: 0.5rem; line-height: 2rem; font-size: 1rem; overflow: hidden;}
.first_food_category{border-left: 0.2rem solid #ff9933; background-color: #ffffff;}
#food_area{ min-height: 300px; width:70%;  height: 100%; overflow-y:scroll; float:left; background-color:#ffffff; padding-bottom: 0.5rem;}
.hidden_div{ display: none; width: 70%; position: absolute; top: 0; left: 30%; height: 100%; overflow-y: scroll;}
.first_food_category .hidden_div{display: block;}
.food_list{ width: 100%;}
.food_list li{ padding: 0.5rem 0.5rem 0 0.3rem; }
.food_name{ height: 1rem; font-size: 1rem; line-height: 1rem; color: #555;}
.food_op{width: 100%; padding-top: 0.3rem; height: 1.5rem; line-height: 1.5rem;}
.food_price{ width: 50%; height: 1rem; line-height: 1rem; font-size: 0.9rem; color: #ff9933; float: left;}
.op_btn{float: right; height: 1rem; line-height: 1rem; width: 50%; text-align: right;}
.op_btn .num{ display: inline-block; visibility: hidden; height: 0.9rem; text-align: center; line-height: 0.9rem; color: #000000; padding: 0 0.2rem 0 0.2rem;}
.op_btn .add_btn,.op_btn .del_btn{display: inline-block; cursor: pointer; width: 1rem; height: 1rem; text-align: center; line-height: 1rem; border-radius: 50%; color: #ffffff}
.op_btn .add_btn{ background-color: #ff9933; }
.op_btn .del_btn{ visibility: hidden; background-color: #cccccc;}
.nav-bottom{ position: absolute; bottom: 0; left: 0; width: 100%; height: 2rem; border-top: 1px solid #efefef; background-color: #fafafa;}
.nav-btn{width: 33.33%; height: 100%; float: left; line-height: 2rem; text-align: center; font-size: 1rem; cursor: pointer;}
.cart{ width: 100%; height: 2.5rem; color: #ffffff; line-height: 2.5rem; background-color: rgba(255,153,51,0.8); position: absolute; left: 0; bottom: 0; }
.cart .cart_icon,.cart .total_price{display: inline-block; position: relative; float: left; margin-left: 1rem}
.cart .clear_btn,.cart .ok_btn{display: inline-block; float: right; font-weight: bolder; margin:0.5rem 0.5rem 0 0; padding: 0 0.5rem; height: 1.5rem; line-height: 1.5rem; border-radius: 0.8rem; font-size: 0.8rem; background-color: #ffffff; color: #ff9933;}
.food_cnt{ display:block; position: absolute; right: -1rem; top: 0.15rem; background-color: #ffffff; color: #777; font-size: 0.8rem; width: 1rem; height: 1rem; line-height: 1rem; text-align: center; border-radius: 50%;}
.menban{ display:none; position: absolute; left: 0; bottom: 2.5rem; cursor:pointer; width:100%; height:100rem; z-index: 10; background-color: rgba(0,0,0,0.7);}
.cart_detail{ position: absolute; left: 0; bottom: 0; width: 100%; height: 15rem; background-color: #fcfcfc;}
.food_detail{width: 100%; height: 13.5rem; overflow: scroll;}
.cart_li{ border-bottom: 1px solid #dfdfdf; height: 2rem; line-height: 2rem; font-size: 1rem; color: #555;}
.li_food_name,.li_food_price,.li_food_cnt{float: left; padding-left: 1rem; text-align: left;}
.li_food_name{ width: 60%;}
.li_food_price{ width: 20%;}
.li_food_cnt{ width: 20%;}
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
        <a href="javascript: void(0)">欢迎预定，<?php echo $_SESSION["username"]; ?></a>
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
        	<div class="ok_btn">选好了</div>
        	<div class="clear_btn">清空</div>
        	<div class="menban">
        		<div class="cart_detail">
        			<div class="slow_down" style="width:100%; height:1.5rem; line-height:1.5rem; background-color:#f0f0f0; text-align:center; color:#555; font-size:1.2rem;"><i class="fa fa-angle-double-down"></i></div>
        			<ul class="food_detail">
	        			<li class="cart_li" data-foodid="1" data-cnt="2">
	        				<div class="li_food_name">红烧牛肉盖浇饭</div>
	        				<div class="li_food_price">￥14</div>
	        				<div class="li_food_cnt">X 1</div>
	        			</li>
	        			<li class="cart_li" data-foodid="1" data-cnt="2">
	        				<div class="li_food_name">红烧牛肉盖浇饭</div>
	        				<div class="li_food_price">￥14</div>
	        				<div class="li_food_cnt">X 1</div>
	        			</li>
	        			<li class="cart_li" data-foodid="1" data-cnt="2">
	        				<div class="li_food_name">红烧牛肉盖浇饭</div>
	        				<div class="li_food_price">￥14</div>
	        				<div class="li_food_cnt">X 1</div>
	        			</li>
	        		</ul>
        		</div>
        	</div>
        	
        </div>
	</div>
</div>

<div class="nav-bottom">
	<a href="/"><div class="nav-btn">首页</div></a>
    <a href="pay.html"><div class="nav-btn">付款码</div></a>
    <a href="my_order.php"><div class="nav-btn">我的订单</div></a>
</div>
</body>
<script type="text/javascript">
	var total_price = 0;
	var food_cnt = 0;
	$(function(){
		$('li.food_category').hover(function(){
			$('.first_food_category').removeClass('first_food_category');
			$('.hidden_div').hide();
			$('li.food_category').css({"border-left": "","background-color": ""});
		    $(this).find('.hidden_div').show();
		    $(this).css({"border-left": "0.2rem solid #ff9933","background-color": "#ffffff"});
		},function(){

		});

		$(document).on('click','.add_btn',function(){			
			$(this).siblings('.del_btn').css("visibility","visible");
			var num_obj = $(this).siblings('.num');
			num_obj.css("visibility","visible");
			var num = parseInt(num_obj.text()) + 1;
			if(num > 5){
				return;
			}
			food_cnt += 1;
			total_price = total_price + parseFloat($(this).parents('.food').find('.food_price').attr('data-value'));
			num_obj.text(num);
			$('.food_cnt').text(food_cnt);
			$('.price_num').text(total_price);
		});

		$(document).on('click','.del_btn',function(){
			var num_obj = $(this).siblings('.num');
			var num = parseInt(num_obj.text()) - 1;
			num_obj.text(num);
			food_cnt -= 1;
			total_price = total_price - parseFloat($(this).parents('.food').find('.food_price').attr('data-value'));
			$('.food_cnt').text(food_cnt);
			$('.price_num').text(total_price);
			if(num <= 0){
				$(this).css("visibility","hidden");
				num_obj.css("visibility","hidden");
				return;
			}
		});

		$('.clear_btn').click(function(){
			total_price = 0;
			food_cnt = 0;
			$('.food_cnt').text(food_cnt);
			$('.price_num').text(total_price);
			$('.del_btn').css("visibility","hidden");
			$('.num').css("visibility","hidden").text('0');
		});

		$('.cart_icon').click(function(){
			$('.menban').slideToggle(1000);
		});

		$('.slow_down').click(function(){
			$('.menban').slideUp(800);
		});
	});
</script>
</html>

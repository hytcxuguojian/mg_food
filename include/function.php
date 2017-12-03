<?php
	include_once "ez_sql_core.php";
	include_once "ez_sql_mysql.php";	
	include_once "config.php";

	session_start();

	function checkLogin(){
		if (empty($_SESSION["user"])) {
			header("location:login.php?error=needlogin");
			die();
		}
	}

	function logOut(){
		$_SESSION["user"] = null;
	}

	//设置菜品数据
	function setFoodData()
	{
		$data = [];
		$db = new ezSQL_mysql(DB_USER,DB_PASSWORD,'mg_food',DB_HOST);
		$food_categorys = $db->get_results('SELECT * from food_category where business_id = 1');
		foreach ($food_categorys as $food_category) {
			$food_list = $db->get_results('SELECT id,food_name,food_category_id,food_category_name,food_price from food where `status` = 0 and food_category_id = '.$food_category->id);
			$data[$food_category->id] = ['food_category_name' => $food_category->category_name,'food_category_id' => $food_category->id];
			foreach ($food_list as $food) {
				$data[$food_category->id]["food_list"][] = [
					"food_id" => $food->id,
					"food_name" => $food->food_name,
					"food_category_id" => $food->food_category_id,
					"food_price" => $food->food_price
				];
			}
		}
		file_put_contents('include/food_data.json',json_encode($data));
	}

	//获取菜品数据
	function getFoodData(){		
		$food_data_json = file_get_contents('include/food_data.json');
		if(empty($food_data_json)){
			setFoodData();
			getFoodData();
		}
		return $food_data_json;
	}

	//获取输入请求数据
	function get_input($key,$default = null){
		return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
	}

	//生成订单号
	function makeOrderNo(){
		return date('YmdHis',time()).rand(1000,9999);
	}

	//根据商品id获取商品数据
	function getFoodDataByIds($food_id_arr = []){
		$food_ids = implode(',', $food_id_arr);
		$db = new ezSQL_mysql(DB_USER,DB_PASSWORD,'mg_food',DB_HOST);
		$food_list = $db->get_results('SELECT * from food where id in('.$food_ids.')');
		$data = [];
		foreach ($food_list as $food) {
			$data[$food->id] = [
				"food_id" => $food->id,
				"food_name" => $food->food_name,
				"food_category_id" => $food->food_category_id,
				"food_price" => $food->food_price,
				"business_id" => $food->business_id,
				"business_name" => $food->business_name,
				"status" => $food->status
			];
		}
		return $data;
	}

	//获取用户订单数据
	function getUserOrderList($user_id){
		$db = new ezSQL_mysql(DB_USER,DB_PASSWORD,'mg_food',DB_HOST);
		$order_list = $db->get_results('SELECT * from `order` where user_id ='.$user_id.' order by id desc limit 15;');
		return $order_list;
	}


	//获取订单状态
	function getOrderStatusZh($status){
		$statusZh = [0 => '待付款',1 => '已付款',2 => '作废'];
		return array_key_exists($status, $statusZh) ? $statusZh[$status] : '';
	}


	//计算给定的创建时间的订单是否可以被作废
	function canCancel($date_time){
		//订单创建15分钟内允许作废
		if(time() - strtotime($date_time) > 900){
			return false;
		}else{
			return true;
		}
	}

	//根据订单号获取订单对象
	function getOrderByOrderNo($order_no){
		$db = new ezSQL_mysql(DB_USER,DB_PASSWORD,'mg_food',DB_HOST);
		return $db->get_row("SELECT * FROM `order` WHERE order_no = '$order_no'");
	}

	//判断当前登录用户是否管理员
	function is_admin($user){
		return $user->is_admin;
	}

?>
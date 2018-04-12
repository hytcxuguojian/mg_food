<?php
	include_once "ez_sql_core.php";
	if (function_exists ('mysql_connect') ){
		include_once "ez_sql_mysql.php";
	}else{
		include_once "ez_sql_mysqli.php";
	}
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

	//生成数据库连接对象
	function makeDB(){
		if (function_exists ('mysql_connect') ){
			return new ezSQL_mysql(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST);
		}else{
			return new ezSQL_mysqli(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST,'utf-8');
		}
		
	}

	//设置菜品数据
	function setFoodData($business_id)
	{
		$data = [];
		$db = makeDB();
		$food_categorys = $db->get_results('SELECT * from food_category where business_id = '.$business_id);
		foreach (valueToArray($food_categorys) as $food_category) {
			$food_list = $db->get_results('SELECT id,food_name,food_category_id,food_category_name,food_price from food where `status` = 0 and food_category_id = '.$food_category->id);
			$data[$food_category->id] = ['food_category_name' => $food_category->category_name,'food_category_id' => $food_category->id];
			foreach (valueToArray($food_list) as $food) {
				$data[$food_category->id]["food_list"][] = [
					"food_id" => $food->id,
					"food_name" => $food->food_name,
					"food_category_id" => $food->food_category_id,
					"food_price" => $food->food_price
				];
			}
		}
		file_put_contents('storage/food_data.json',json_encode($data));
	}

	//获取菜品数据
	function getFoodDataByCache($business_id){		
		$food_data_json = file_get_contents('storage/food_data_'.$business_id.'.json');
		if(empty($food_data_json)){
			setFoodData($business_id);
			getFoodDataByCache($business_id);
		}
		return $food_data_json;
	}

	//获取菜品数据
	function getFoodDataByBid($business_id){		
		$data = [];
		$db = makeDB();
		$food_categorys = $db->get_results('SELECT * from food_category where business_id = '.$business_id);
		foreach (valueToArray($food_categorys) as $food_category) {
			$food_list = $db->get_results('SELECT id,food_name,food_category_id,food_category_name,food_price from food where `status` = 0 and food_category_id = '.$food_category->id);
			$data[$food_category->id] = ['food_category_name' => $food_category->category_name,'food_category_id' => $food_category->id];
			foreach (valueToArray($food_list) as $food) {
				$data[$food_category->id]["food_list"][] = [
					"food_id" => $food->id,
					"food_name" => $food->food_name,
					"food_category_id" => $food->food_category_id,
					"food_price" => $food->food_price
				];
			}
		}
		return json_encode($data);
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
		$db = makeDB();
		$food_list = $db->get_results('SELECT * from food where id in('.$food_ids.')');
		$data = [];
		foreach (valueToArray($food_list) as $food) {
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
		$db = makeDB();
		$order_list = $db->get_results('SELECT * from `order` where user_id ='.$user_id.' order by id desc limit 15;');
		return valueToArray($order_list);
	}

	//今日订单
	function getTodayOrderList(){
		$db = makeDB();
		$order_list = $db->get_results('SELECT * from `order` where created_at >= \''.date('Y-m-d',time()).'\' order by status,id;');
		return valueToArray($order_list);
	}


	//获取订单状态
	function getOrderStatusZh($status){
		$statusZh = [0 => '待付款',1 => '已付款',2 => '已作废'];
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
		$db = makeDB();
		return $db->get_row("SELECT * FROM `order` WHERE order_no = '$order_no'");
	}

	//判断当前登录用户是否管理员
	function is_admin($user){
		return $user->is_admin;
	}

	//判断商家是否可以接单
	function can_order($business_id){
		$db = makeDB();
		$business = $db->get_row("SELECT * FROM `business` WHERE id = $business_id");		
		if($business && ($business->status == 0)){
			return true;
		}else{
			return false;
		}
		
	}


	//从订单food_info中获取商品名和数量
	function getFoodNames($food_info_json){
		$food_names = [];
		$food_info = json_decode($food_info_json);
		foreach ($food_info as $key => $food) {
			$food_names[] = $food->food_num > 1 ? $food->food_name.'×'.$food->food_num : $food->food_name;
		}
		return $food_names;
	}

	//统计某一天的有效下单情况
	function calculate($date){
		$db = makeDB();
		$order_list = $db->get_results('SELECT * from `order` where status != 2 and created_at between \''.$date.'\' and \''.$date.' 23:59:59\' order by user_id,id;');
		$data = [];
		foreach (valueToArray($order_list) as $key => $order) {
			$data[] = [
				'username' => $order->username,
				'foods' => implode('+', getFoodNames($order->food_info)),
				'total_price' => intval($order->price) / 100,
			];
		}
		return $data;
	}

	//强制返回数组
	function valueToArray($value = ''){
		return is_array($value) ? $value : [];
	}

?>
<?php 
	include_once "function.php";
	$db = makeDB();	

	//请求动作
	$action = isset($_POST["action"]) ? $_POST["action"] : "";
	//部分请求需验证登录
	if(in_array($action, ['ensure','buy_again','change_order','calculate'])){
		checkLogin();
	}

	$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

	//登录
	if($action == "login"){
		$username = trim(get_input('username',''));
		$password = trim(get_input('password',''));
		if ($username != '' && $password != '') {
			$sql = 'SELECT * FROM user WHERE status = 1 and username = \''. $username .'\' and password = \''. md5($password) .'\'';
			$res = $db->get_row($sql);
			if (!$res) {
				header("location:/login.php?error=wrongpwd");
			}else{
				$_SESSION["user"] = $res;
				header("location:/index.php");
			}
		}
		die;
	}

	//确认商品
	if($action == 'ensure'){
		$foods = json_decode(get_input('foods',''),true);
		$food_ids = [];
		$food_to_num = [];
		foreach (valueToArray($foods) as $key => $value) {
			$food_ids[] = $value['food_id'];
			$food_to_num[$value['food_id']] = $value['food_num'];
		}
		$food_data = getFoodDataByIds($food_ids);
		foreach ($food_to_num as $food_id => $food_num) {
			if(array_key_exists($food_id, $food_data)){
			    $food_data[$food_id]['food_num'] = $food_num;
			}
		}
		$food_info = addslashes(json_encode($food_data));
		$userid = $user->id;
		$username = $user->username;
		$order_no = makeOrderNo();
		$total_price = 0;
		foreach ($food_data as $id => $food) {
			$total_price += intval($food['food_price']) * intval($food['food_num']);
		}
		$sql = "insert into `order`(order_no,user_id,username,food_info,price,discount_price,status,created_at,updated_at) 
		values('$order_no',$userid,'$username','$food_info',$total_price,0,0,now(),now())";
		$res = $db->query($sql);
		//$order_id = $db->get_var("select max(id) from order");
		if($res){
			$result = [
				'status' => 0,
				'msg' => '您已下单成功'
			];
		}else{
			$result = [
				'status' => 500,
				'msg' => '服务器网络异常，提交失败，稍后请重试'
			];
		}
		echo json_encode($result);
		die();
	}

	//再来一单
	if($action == 'buy_again'){
		$order_no = get_input('order_no','');
		$order = getOrderByOrderNo($order_no);
		if($order){
			$food_info = json_decode($order->food_info);
			$cart_data = [];
			foreach ($food_info as $food_id => $food) {
				$cart_data[] = [
					'food_id' => $food->food_id,
					'food_num' => $food->food_num,
					'single_price' => $food->food_price / 100,
					'food_name' => $food->food_name
				];
			}
			$result = [
				'status' => 0,
				'data' => json_encode($cart_data),
				'msg' => '操作成功'
			];
		}else{
			$result = [
				'status' => 401,
				'msg' => '该订单不存在'
			];
		}
		echo json_encode($result);
		die();
	}

	//改变订单状态
	if($action == 'change_order'){
		$order_no = get_input('order_no','');
		$type = get_input('type','');
		$order = getOrderByOrderNo($order_no);
		if($order){
			$is_admin = is_admin($user);
			if($type == 'cancel'){
				if(!$is_admin && !canCancel($order->created_at)){
					$result = [
						'status' => 300,
						'msg' => '该订单已过作废期'
					];
				}
				if(!$is_admin && $order->user_id != $user->id){
					$result = [
						'status' => 301,
						'msg' => '您无权作废该订单'
					];
				}
				$db->query('update `order` set status = 2 where id = '.$order->id);
			}elseif($type == 'paid'){
				if(!$is_admin){
					$result = [
						'status' => 301,
						'msg' => '您无权将该订单改为"已付款"状态'
					];
				}
				$db->query('update `order` set status = 1 where id = '.$order->id);
			}elseif($type == 'unpay'){
				if(!$is_admin){
					$result = [
						'status' => 301,
						'msg' => '您无权将该订单改为"待付款"状态'
					];
				}
				$db->query('update `order` set status = 0 where id = '.$order->id);
			}else{
				$result = [
					'status' => 400,
					'msg' => '无操作指令'
				];
			}
			$result = [
				'status' => 0,
				'msg' => '操作成功'
			];
		}else{
			$result = [
				'status' => 401,
				'msg' => '该订单不存在'
			];
		}
		echo json_encode($result);
		die();
	}

	if($action == 'calculate'){
		$date = date('Y-m-d',time());
		$data = calculate($date);
		
		$result = [
			'status' => 0,
			'data' => $data,
			'msg' => '请求成功'
		];
		echo json_encode($result);
		die();
	}
 ?>

 
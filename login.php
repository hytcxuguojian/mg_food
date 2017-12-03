<?php
	include_once "include/function.php";
 ?>
<!DOCTYPE html>
<html style="height:100%">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>安庆小吃快捷通道</title>
<meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'/>
<link rel="stylesheet" href="static/css/common.css">
<link href="http://cdn.bootcss.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<style>
#loginArea{ width: 100%; padding: 5rem 1rem 2rem 1rem;}
.errorInfo{ width: 100%; height: 2rem; line-height: 2rem; text-align: center; font-size: 1rem; background-color: rgba(255,0,0,0.6); color: #ffffff;}
.row{ width: 100%; height: 3rem; line-height: 3rem;}
.row label{width: 40%; float: left; text-align: right;}
.row .div1,.row .div2,{width: 60%; float: left;}
#username,#password{ height: 1.5rem; }
#loginBtn{ width: 5rem; height: 2rem; border-radius: 0.8rem; background-color: #ff9933;}
.remind{ width: 100%; height: 6rem; padding: 1rem; background-color: #ff9933}
</style>
</head>

<body style="height:100%; position:relative;">
<header class="sw-header sw-header-default">
	<div class="sw-header-left sw-header-nav">
        <a href="javascript: void(0)" class="" onclick="history.go(-1)">
            <i class="sw-header-icon fa fa-arrow-left"></i>
        </a>
    </div>
    <h1 class="sw-header-title">
        <a href="javascript: void(0)">安庆小吃快捷通道</a>
    </h1>
    <div class="sw-header-right sw-header-nav">
        <a href="/">
            <i class="sw-header-icon fa fa-home"></i>
        </a>
    </div>
</header>
<div class="main">
	<div class="content_section">
		<div class="section">
            <div class="section-body">
            		<?php 
						$errorInfo = isset($_GET["error"]) ? $_GET["error"] : "";
						if ($errorInfo == "wrongpwd") {
							echo '<p class="errorInfo">用户名或密码错误，请重试！</p>';
						}
						if ($errorInfo == "needlogin") {
							echo '<p class="errorInfo">需要登录！</p>';
						}
						logOut();
					 ?>
            	<div id="loginArea">					
					<form action="/include/webservice.php" method="post">
						<input type="hidden" name="action" value="login">
						<div class="row">
							<label>用户名：</label>
							<div class="div1">
								<input name="username" type="text" id="username">
							</div>
						</div>
						<div class="row">
							<label>密&nbsp&nbsp&nbsp码：</label>
							<div class="div2">
								<input name="password" type="password" id="password">
							</div>
						</div>
						<div class="row" style="text-align:center;">
							<input type="submit" value="登 录" id="loginBtn">
						</div>
					</form>
				</div>
        </div>
	</div>
	<div class="footer_info" style="width:100%; position:absolute; background-color:#ececec; bottom:0; left:0;">Copyright(c)2017 南京魔格研发部小胥同学
    <br>大吉大利，天天安庆
</div>
</div>
</body>
<script type="text/javascript">
	$(function(){
		$('input').focus(function(){
			$('.footer_info').hide();
		});

		$('input').blur(function(){
			$('.footer_info').show();
		});
	})
</script>
</html>
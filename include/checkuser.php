<?php
	$flag = (isset($_POST["flag"]))?$_POST["flag"]:"";
	$userid = (isset($_POST["userid"]))?$_POST["userid"]:"";
	$userpwd = (isset($_POST["userpwd"]))?$_POST["userpwd"]:"";


	if ($flag == "checkuser" && $userid != "" && $userpwd != "") {
		//contact to db and check user id and pwd

		header("location:" + $_SERVER['DOCUMENT_ROOT'] +"/index.php");
		
		header("location:index.php");

	}

?>
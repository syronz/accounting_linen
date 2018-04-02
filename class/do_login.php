<?php
session_start();
require_once 'user.class.php';
require_once 'backup.class.php';

/*if($_SESSION['user'])
	header('location:index0.php');*/
$msg = '';
// dsh(user::login('syronz','88888888'));
// $_POST['username'] = 'syronz';
// $_POST['password'] = '88888888';

if(isset($_POST['username'])){

	// $_SESSION['user'] = 'diako';
	// 	$_SESSION['user']['id'] = 1;
	// 	header('location:index0.php');
	// 	exit();
	
	$result = user::login($_POST['username'],$_POST['password']);
	
		

	if($result['user']){
		$_SESSION['user'] = $result['user'];
		backup::create(['name'=>date('Y-m-d',time())]);
		// $_SESSION['permission'] = $result['permission'];
		// $_SESSION['permission'] = $result['user']['id_permission'];
		header('location:../index0.php');
		exit();
	}
	else{
		header('location:'.setting::APP_URL.'/login.php?alert=Try Again!');
		// $msg = dic_return('Failed!!! Please Try again...');
	}
}

?>
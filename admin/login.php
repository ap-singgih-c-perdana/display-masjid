<?php
include_once "../session.php";
	$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
	if($scriptDir === '/' || $scriptDir === '.'){
		$scriptDir = '';
	}
	$appBaseUrl = preg_replace('#/admin$#', '', rtrim($scriptDir, '/'));
	$adminBaseUrl = $appBaseUrl.'/admin';
	$displayUrl = ($appBaseUrl === '' ? '' : $appBaseUrl).'/';
if(isset($_SESSION["user_id"])){
	header("Location: ".$adminBaseUrl."/");
	exit;
}
// print_r ($_SESSION);die;
$file	= '../db/database.json';
$name	= '';
if (file_exists($file)){
	$json 	= file_get_contents($file);
	$db		= json_decode($json, true);
	$name	= $db['setting']['nama'];
}

?>

<!DOCTYPE html>
<html>
<head>
	<base href="../">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Display|Masjid|Admin</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="icon" type="image/png" href="icon.png"/>
	<link rel="stylesheet" href="dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="dist/css/font-awesome.min.css">
	<link rel="stylesheet" href="dist/css/AdminLTE.min.css">
	<link rel="stylesheet" href="dist/css/_all-skins.min.css">
	<link rel="stylesheet" href="dist/css/bootstrap-datetimepicker.css">
	<link rel="stylesheet" href="dist/css/datatables.min.css">
	<link rel="stylesheet" href="dist/css/buttons.dataTables.min.css">
</head>
<body class="hold-transition login-page">
	<div class="login-box">
	  <div class="login-logo">
		<a><b>Display</b>|Masjid</a>
	  </div>
	  <div class="login-box-body">
		<h4 class="login-box-msg" style="border-bottom:0.7px solid #ccc;padding:5px 0">Halaman login - <?=$name?></h4>
		<form method="post" class="js-login-form" data-redirect="<?=$adminBaseUrl?>/" style="margin-top:10px">
		  <div class="form-group has-feedback">
			<input name="user" type="text" class="form-control" placeholder="User" required>
			<span class="form-control-feedback"><i class="fa fa-user" aria-hidden="true"></i></span>
		  </div>
		  <div class="form-group has-feedback">
			<input name="pass" id="pass" type="password" class="form-control" placeholder="Password" required>
			<span class="form-control-feedback"><i class="fa fa-unlock-alt" aria-hidden="true"></i></span>
		  </div>
		  <div class="row" style="margin-top:50px">
			<div class="col-xs-6">
			  <a href="<?=$displayUrl?>" class="btn btn-default btn-block btn-flat"><i class="fa fa-desktop" aria-hidden="true"></i> Ke Display</a>
			</div>
			<div class="col-xs-6">
			  <button type="submit" class="btn btn-primary btn-block btn-flat"><i class="fa fa-sign-in" aria-hidden="true"></i> Sign In</button>
			</div>
		  </div>
		</form>
	  </div>
	</div>
	
	<script src="vendor/js/jquery.min.js"></script>
	<script src="vendor/js/bootstrap.min.js"></script>
	<script src="dist/js/adminlte.min.js"></script>
	<script>
		window.ADMIN_CONFIG = <?=json_encode([
			'appBaseUrl' => $appBaseUrl,
			'adminBaseUrl' => $adminBaseUrl,
			'processUrl' => $adminBaseUrl.'/proses.php',
			'displayBaseUrl' => ($appBaseUrl === '' ? '' : $appBaseUrl).'/display'
		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)?>;
	</script>
	<script src="js/admin-login.js?v=<?=filemtime(__DIR__.'/../js/admin-login.js')?>"></script>
</body>
</html>

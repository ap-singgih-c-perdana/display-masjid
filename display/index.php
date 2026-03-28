<?php
	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
	header('Expires: 0');

	// var_dump(PHP_OS);
	// die;
	$file	= '../db/database.json';
	if (!file_exists($file)){
		echo "<h1>Jalankan admin terlebih dahulu</h1>";
		die;
	}
	$json 	= file_get_contents($file);
	$db		= json_decode($json, true);
	$showDb	= $db;
	unset($showDb['akses']);
	
	$info_timer			= $db['timer']['info'] 		* 1000;	//detik
	$wallpaper_timer	= $db['timer']['wallpaper'] * 1000;	
	$adzan_timer		= $db['timer']['adzan'] 	* 1000 * 60; //menit
	// $iqomah_timer		= $db['timer']['iqomah'] 	* 1000 * 60;
	$sholat_timer		= $db['timer']['sholat'] 	* 1000 * 60;
	
	//optional
	$khutbah_jumat		= $db['jumat']['duration'] 	* 1000 * 60;
	$sholat_tarawih		= $db['tarawih']['duration'] 	* 1000 * 60;
	
	//Logo
	// nge trik ==> kalo replace file, di display logo yang lama masih kesimpen di cache ==> solusi ganti logo ganti nama file 
	$dirLogo	= 'logo/';
	$filesLogo	= array_diff(scandir($dirLogo),array('.','..','Thumbs.db','.DS_Store'));
	$filesLogo	= array_values($filesLogo);//re index
	$logo		= $filesLogo[0];
	
	
	$dir	= 'wallpaper/';
	$files	= array_diff(scandir($dir),array('.','..','Thumbs.db','.DS_Store'));
	$wallpaper	= '';
	$i	= 0;
	foreach($files as $v){
		$active	= $i==0?'active':'';
		$wallpaper	.= '<div class="item slides '.$active.'"><div style="background-image: url(wallpaper/'.$v.');"></div></div>';
		$i++;
	}
	// print_r($files);die;
?>


<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Display|Masjid</title>
    <link rel="icon" type="image/png" href="../icon.png"/>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css?v=<?=filemtime('css/bootstrap.min.css')?> " rel="stylesheet">
    <link href="css/font-awesome.min.css?v=<?=filemtime('css/font-awesome.min.css')?> " rel="stylesheet">
    <link href="css/style.css?v=<?=filemtime('css/style.css')?> " rel="stylesheet">
	<style>
		
	</style>
</head>

<body>
    <div id="preloader">
      <div id="status">&nbsp;</div>
    </div> 
	
	
	<div id="full-screen-clock" style="display:none"></div>
	<div id="count-down" class="full-screen" style="display:none">
		<div class="counter">
			<h1>COUNTER</h1>
			<div class="hh">00<span>JAM</span></div>
			<div class="ii">00<span>MENIT</span></div>
			<div class="ss">00<span>DETIK</span></div>
		</div>
	</div>
	<div id="display-adzan" class="full-screen" style="display:none"><div></div></div>
	<div id="display-sholat" class="full-screen" style="display:none"></div>
	<div id="display-khutbah" class="full-screen" style="display:none"><div></div></div>
	
	
	<div class="carousel fade-carousel slide" data-ride="carousel" data-interval="<?=$wallpaper_timer?>">
	  <!-- Overlay -->
	  <div class="overlay"></div>
	  <!-- Wrapper for slides -->
	  <div class="carousel-inner"><?=$wallpaper?></div> 
	</div>
	
	
	<div id="left-container">
		<div id="jam"></div>
		<div id="tgl"></div>
		<div id="jadwal"></div>
	</div>
	
	<div id="right-counter" style="display:none">
		<div class="counter">
			<h1>COUNTER</h1>
			<div class="hh">19<span>JAM</span></div>
			<div class="ii">25<span>MENIT</span></div>
			<div class="ss">45<span>DETIK</span></div>
		</div>
	</div>
	<div id="right-container">
		<div id="quote">
			<div class="carousel quote-carousel slide" data-ride="carousel" data-interval="<?=$info_timer?>" data-pause="null">
			  <div class="carousel-inner">
				<?php 
				$i=0;
				foreach($db['info'] as $k => $v){
					if($v[3]){
						echo '
						<div class="item slides '.($i==0?'active':'').'">
						  <div class="hero">        
							<hgroup>
								<div class="text1">'.htmlentities($v[0]).'</div>        
								<div class="text2">'.nl2br(htmlentities($v[1])).'</div>        
								<div class="text3">'.htmlentities($v[2]).'</div>
							</hgroup>
						  </div>
						</div>
						';
						$i++;
					}
				}
				?>
			  </div> 
			</div>
		</div>
		<div id="youtube-container" style="display:none">
			<div class="youtube-frame-wrap">
				<iframe
					id="youtube-player"
					src=""
					title="Video YouTube"
					frameborder="0"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
					allowfullscreen>
				</iframe>
			</div>
		</div>
		<div id="ppt-container" style="display:none">
			<div class="ppt-frame-wrap">
				<iframe
					id="ppt-player"
					src=""
					title="Embed PPT"
					frameborder="0"
					allow="fullscreen"
					allowfullscreen>
				</iframe>
			</div>
		</div>
		<div id="logo" style="background-image: url(logo/<?=$logo?>);"></div>
		<div id="running-text">
			<div class="item">
				<?php 
					foreach($db['running_text'] as $k => $v){
						echo '<i class="fa fa-square-o" aria-hidden="true"></i> '.htmlentities($v);
					}
					// $ip 	= gethostbyname(php_uname('n'));	// PHP < 5.3.0
// 					$ip 	= gethostbyname(gethostname());		// PHP >= 5.3.0 ==> di linux keluar 127.0.0.1
// 					if(PHP_OS=='Linux'){
// 						//raspi 3
// 						// $command="/sbin/ifconfig wlan0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'";//raspi pake wlan0 jadi hotspot
// 						// $ip = exec ($command);
//
// 						//raspi 4
// 						$command="/sbin/ifconfig wlan0 | grep 'inet '| cut -d 't' -f2 | cut -d 'n' -f1 | awk '{ print $1}'";//raspi pake wlan0 jadi hotspot
// 						$ip = trim(exec ($command));
// 					}
// 					if($db['akses']['pass']=='admin'){
// 						echo '<i class="fa fa-square-o" aria-hidden="true"></i> Konek ke wifi (SSID: DisplayMasjid, password: 12345678)';
// 						echo '<i class="fa fa-square-o" aria-hidden="true"></i> Alamat admin http://'.$ip.'/';
// 						echo '<i class="fa fa-square-o" aria-hidden="true"></i> Default akses user : admin, password : admin';
// 						echo '<i class="fa fa-square-o" aria-hidden="true"></i> Silakan mengganti password admin untuk menghilangkan tulisan ini';
// 					}
				?>
			</div>
		</div>
	</div>
    <script src="js/jquery-3.4.1.min.js?v=<?=filemtime('js/jquery-3.4.1.min.js')?>"></script>
    <script src="js/bootstrap.min.js?v=<?=filemtime('js/bootstrap.min.js')?>"></script>
    <script src="js/moment-with-locales.js?v=<?=filemtime('js/moment-with-locales.js')?>"></script>
    <script src="js/PrayTimes.js?v=<?=filemtime('js/PrayTimes.js')?>"></script>
    <script src="js/jquery.marquee.js?v=<?=filemtime('js/jquery.marquee.js')?>"></script>
    <script>
		window.DISPLAY_CONFIG = <?=json_encode([
			'db' => $showDb,
			'lat' => $db['setting']['latitude'],
			'lng' => $db['setting']['longitude'],
			'timeZone' => $db['setting']['timeZone'],
			'dst' => $db['setting']['dst'],
			'prayTimesMethod' => $db['prayTimesMethod'],
			'prayTimesAdjust' => array_filter($db['prayTimesAdjust'], function($value){
				return $value !== '';
			}),
			'prayTimesTune' => array_filter($db['prayTimesTune'], function($value){
				return $value !== '0';
			})
		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)?>;
	</script>
	<script src="js/app.core.js?v=<?=filemtime('js/app.core.js')?>"></script>
	<script src="js/app.running-text.js?v=<?=filemtime('js/app.running-text.js')?>"></script>
	<script src="js/app.media.js?v=<?=filemtime('js/app.media.js')?>"></script>
	<script src="js/app.schedule.js?v=<?=filemtime('js/app.schedule.js')?>"></script>
	<script src="js/app.countdown.js?v=<?=filemtime('js/app.countdown.js')?>"></script>
	<script src="js/app.js?v=<?=filemtime('js/app.js')?>"></script>
</body>
</html>

<?php

trait ProsesDatabaseTrait {
	private function defaultDatabase(){
		return [
			'akses'	=> [
				'user'	=> 'admin',
				'pass'	=> 'admin'
			],
			'setting'	=> [
				'nama'		=> 'Musholla Ad-Din',
				'lokasi'	=> 'Bekasi',
				'latitude'	=> -6.14,
				'longitude'	=> 106.59,
				'timeZone'	=> 7,
				'dst'		=> '0'
			],
			'prayTimesMethod'	=> '0',
			'prayTimesAdjust'	=> [
				'fajr'	=> 20,
				'dhuhr'	=> '',
				'asr'	=> 'Standard',
				'maghrib'	=> '',
				'isha'	=> 18
			],
			'prayTimesTune'	=> [
				'fajr'		=> 0,
				'sunrise'	=> 0,
				'dhuhr'		=> 0,
				'asr'		=> 0,
				'maghrib'	=> 0,
				'isha'		=> 0
			],
			'prayName'	=> [
				'fajr'		=> 'Subuh',
				'sunrise'	=> 'Syruq',
				'dhuhr'		=> 'Dzuhur',
				'asr'		=> 'Ashar',
				'maghrib'	=> 'Maghrib',
				'isha'		=> 'Isya\''
			],
			'timeName'	=> [
				'Hours'		=> 'Jam',
				'Minutes'	=> 'Menit',
				'Seconds'	=> 'Detik',
			],
			'dayName'	=> [
				'Sunday'	=> 'Ahad',
				'Monday'	=> 'Senin',
				'Tuesday'	=> 'Selasa',
				'Wednesday'	=> 'Rabu',
				'Thursday'	=> 'Kamis',
				'Friday'	=> 'Jum\'at',
				'Saturday'	=> 'Sabtu'
			],
			'monthName'	=> [
				'January'		=> 'Januari',
				'February'		=> 'Februari',
				'March'			=> 'Maret',
				'April'			=> 'April',
				'May'			=> 'Mei',
				'June'			=> 'Juni',
				'July'			=> 'Juli',
				'August'		=> 'Agustus',
				'September'		=> 'September',
				'October'		=> 'Oktober',
				'November'		=> 'November',
				'December'		=> 'Desember'
			],
			'timer'	=> [
				'info'		=> 5,
				'wallpaper'	=> 15,
				'wait_adzan'=> 1,
				'countdown_alarm' => 5,
				'adzan'		=> 3,
				'sholat'	=> 20
			],
			'iqomah'	=> [
				'fajr'		=> 10,
				'dhuhr'		=> 10,
				'asr'		=> 10,
				'maghrib'	=> 10,
				'isha'		=> 10
			],
			'jumat'	=> [
				'active'	=> true,
				'duration'	=> 60,
				'text'		=> 'Harap diam saat khotib khutbah'
			],
			'tarawih'	=> [
				'active'	=> true,
				'duration'	=> 180
			],
			'imamMuadzin' => [
				'active' => false,
				'fajr' => [
					'imam' => '',
					'muadzin' => ''
				],
				'dhuhr' => [
					'imam' => '',
					'muadzin' => ''
				],
				'asr' => [
					'imam' => '',
					'muadzin' => ''
				],
				'maghrib' => [
					'imam' => '',
					'muadzin' => ''
				],
				'isha' => [
					'imam' => '',
					'muadzin' => ''
				]
			],
			'info'	=> [
				[
					'Aplikasi Display-Masjid',
					'Selamat datang di aplikasi Display Masjid, aplikasi baru saja diinstal dan belum ada data, silakan masuk ke menu admin untuk mengganti data',
					'Display|Masjid V.1.0.0',
					true
				],
				[
					'Info non-active',
					'Ini contoh info tidak aktif',
					'active = false',
					false
				],
				[
					'سَوُّوا صُفُوفَكُمْ , فَإِنَّ تَسْوِيَةَ الصَّفِّ مِنْ تَمَامِ الصَّلاةِ',
					'Luruskanlah shaf-shaf kalian, karena lurusnya shaf adalah kesempurnaan shalat',
					'HR. Bukhari no.690, Muslim no.433',
					true
				]
			],
			'running_text' => [
				'Selamat datang di aplikasi Display-Masjid',
				'Silakan masuk ke menu admin untuk mengubah data'
			],
			'youtube' => [
				'active'	=> false,
				'url'		=> '',
				'mute'		=> true
			],
			'ppt' => [
				'active'	=> false,
				'url'		=> ''
			],
			'infoDisplay' => [
				'fullscreen' => false
			]
		];
	}

	private function getDatabase(){
		$file = $this->file;
		if(!file_exists($file)){
			$this->database = $this->defaultDatabase();
			$this->saveDatabase();
		}

		$json = @file_get_contents($file);
		if($json === false){
			$this->retError('Database tidak bisa dibaca...');
		}
		$this->database = json_decode($json, true);
		if(!is_array($this->database)){
			$this->retError('Database rusak / format JSON tidak valid: '.json_last_error_msg());
		}

		$defaultDatabase = $this->defaultDatabase();
		$needsSave = false;

		if(!isset($this->database['youtube']) || !is_array($this->database['youtube'])){
			$this->database['youtube'] = [
				'active'	=> false,
				'url'		=> '',
				'mute'		=> true
			];
			$needsSave = true;
		}
		if(!isset($this->database['ppt']) || !is_array($this->database['ppt'])){
			$this->database['ppt'] = [
				'active'	=> false,
				'url'		=> ''
			];
			$needsSave = true;
		}
		if(!isset($this->database['infoDisplay']) || !is_array($this->database['infoDisplay'])){
			$this->database['infoDisplay'] = $defaultDatabase['infoDisplay'];
			$needsSave = true;
		}
		else if(!isset($this->database['infoDisplay']['fullscreen'])){
			$this->database['infoDisplay']['fullscreen'] = $defaultDatabase['infoDisplay']['fullscreen'];
			$needsSave = true;
		}
		if(!isset($this->database['timer']) || !is_array($this->database['timer'])){
			$this->database['timer'] = $defaultDatabase['timer'];
			$needsSave = true;
		}
		if(!isset($this->database['timer']['countdown_alarm'])){
			$timer = [];
			foreach($this->database['timer'] as $key => $value){
				$timer[$key] = $value;
				if($key == 'wait_adzan'){
					$timer['countdown_alarm'] = 5;
				}
			}
			if(!isset($timer['countdown_alarm'])){
				$timer['countdown_alarm'] = 5;
			}
			$this->database['timer'] = $timer;
			$needsSave = true;
		}
		if(!isset($this->database['prayName']) || !is_array($this->database['prayName'])){
			$this->database['prayName'] = $defaultDatabase['prayName'];
			$needsSave = true;
		}
		if(!isset($this->database['prayTimesTune']) || !is_array($this->database['prayTimesTune'])){
			$this->database['prayTimesTune'] = $defaultDatabase['prayTimesTune'];
			$needsSave = true;
		}
		if(!isset($this->database['imamMuadzin']) || !is_array($this->database['imamMuadzin'])){
			$this->database['imamMuadzin'] = $defaultDatabase['imamMuadzin'];
			$needsSave = true;
		}
		else{
			$defaultImamMuadzin = $defaultDatabase['imamMuadzin'];
			if(!isset($this->database['imamMuadzin']['active'])){
				$this->database['imamMuadzin']['active'] = $defaultImamMuadzin['active'];
				$needsSave = true;
			}
			foreach($defaultImamMuadzin as $key => $value){
				if($key === 'active'){
					continue;
				}
				if(!isset($this->database['imamMuadzin'][$key]) || !is_array($this->database['imamMuadzin'][$key])){
					$this->database['imamMuadzin'][$key] = $value;
					$needsSave = true;
					continue;
				}
				foreach($value as $roleKey => $roleValue){
					if(!isset($this->database['imamMuadzin'][$key][$roleKey])){
						$this->database['imamMuadzin'][$key][$roleKey] = $roleValue;
						$needsSave = true;
					}
				}
			}
		}
		if(!isset($this->database['prayName']['sunrise'])){
			$prayName = [];
			foreach($this->database['prayName'] as $key => $value){
				$prayName[$key] = $value;
				if($key == 'fajr'){
					$prayName['sunrise'] = 'Syruq';
				}
			}
			if(!isset($prayName['sunrise'])){
				$prayName['sunrise'] = 'Syruq';
			}
			$this->database['prayName'] = $prayName;
			$needsSave = true;
		}
		if(!isset($this->database['prayTimesTune']['sunrise'])){
			$prayTimesTune = [];
			foreach($this->database['prayTimesTune'] as $key => $value){
				$prayTimesTune[$key] = $value;
				if($key == 'fajr'){
					$prayTimesTune['sunrise'] = 0;
				}
			}
			if(!isset($prayTimesTune['sunrise'])){
				$prayTimesTune['sunrise'] = 0;
			}
			$this->database['prayTimesTune'] = $prayTimesTune;
			$needsSave = true;
		}
		if($needsSave){
			$this->saveDatabase();
		}
	}

	private function readDatabase(){
		$db = $this->database;
		unset($dt['akses']);
		$this->data = $db;
		$this->retSuccess();
	}

	private function changeDbCheck(){
		$db = $this->database;
		$wp = $this->getWallpaper();
		$logo = __DIR__.'/../../display/logo/'.$this->getLogo();

		$combine = json_encode($db).json_encode($wp).filesize($logo);
		$this->data = sha1($combine).strlen($combine);
		$this->retSuccess();
	}

	private function saveDatabase(){
		$file = $this->file;
		$dir = dirname($file);
		$json = json_encode($this->database, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		if($json === false){
			$this->retError('Gagal encode database ke JSON: '.json_last_error_msg());
		}
		$tmpFile = $file.'.tmp';
		if(@file_put_contents($tmpFile, $json, LOCK_EX) === false){
			$this->retError('Gagal menulis file sementara database. Cek permission folder: '.$dir);
		}
		if(!@rename($tmpFile, $file)){
			@unlink($tmpFile);
			$this->retError('Gagal mengganti file database. Cek permission file/folder: '.$file);
		}
	}

	private function getPraySetting(){
		$db	= $this->database;
		$this->data['setting']			= $db['setting'];
		$this->data['prayTimesMethod']	= $db['prayTimesMethod'];

		$prayTimesAdjust = [];
		foreach($db['prayTimesAdjust'] as $k => $v){
			if(strlen(trim($v)) > 0){
				$prayTimesAdjust[$k] = $v;
			}
		}
		$this->data['prayTimesAdjust'] = $prayTimesAdjust;

		$prayTimesTune = [];
		foreach($db['prayTimesTune'] as $k => $v){
			if($v < 0 || $v > 0){
				$prayTimesTune[$k] = $v;
			}
		}

		$this->data['prayTimesTune'] = $prayTimesTune;
		$this->data['items'] = array_keys($db['prayName']);
		$this->data['thead'] = array_values($db['prayName']);
		array_unshift($this->data['thead'], 'Tgl');

		$this->retSuccess();
	}
}

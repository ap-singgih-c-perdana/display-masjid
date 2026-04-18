<?php
// print_r($_POST);
// print_r($_FILES);
// die;

include_once __DIR__."/session.php";
include_once __DIR__."/includes/proses/ProsesDatabaseTrait.php";
include_once __DIR__."/includes/proses/ProsesFormRenderTrait.php";
include_once __DIR__."/includes/proses/ProsesDashboardViewTrait.php";

class proses extends fb{
	use ProsesDatabaseTrait;
	use ProsesFormRenderTrait;
	use ProsesDashboardViewTrait;

	protected
		$file		= '',
		$database	= [];

	public function __construct($id){
		$this->file = __DIR__.'/../db/database.json';
		if($id == 'login' || $id == 'logout'){
			$this->registered = false;
			$this->$id();
		}
		else if($id == 'changeDbCheck'){
			$this->getDatabase();
			$this->$id();
		}
		else if($this->verification($id)){
			$this->getDatabase();
			$this->$id();
		}
    }

	private function logout(){
        $_SESSION = array();
        session_destroy();
		$this->registered = false;
		$this->retSuccess();
	}

	private function login(){
		$user = isset($_POST['dt']['user']) ? $_POST['dt']['user'] : false;
		$pass = isset($_POST['dt']['pass']) ? $_POST['dt']['pass'] : false;
		$this->getDatabase();
		$db = $this->database;

		if(!$user || !$pass){
			$this->retError("Data tidak valid...");
		}
		else if($user != $db['akses']['user'] || $pass != $db['akses']['pass']){
			$this->retError("Anda tidak memiliki akses...");
		}
		else{
			$_SESSION["user_id"] = $user;
			$this->registered = true;
			$this->retSuccess();
		}
	}

	private function resetDevice(){
		if($this->dt == 'KONFIRMASI'){
			$file = $this->file;
			if(file_exists($file)){
				unlink($file);
				$this->getDatabase();
				$this->logout();
			}
			else{
				$this->retError('Database not found...');
			}
		}
		else{
			$this->retError('Not confirm...');
		}
	}

	private function shutdown(){
		if($this->dt == 's'){
			exec("sudo shutdown -h now");
		}
		else{
			exec("sudo reboot");
		}
		$this->retError('Gagal...');
	}

	private function updateClock(){
		$update = exec('sudo hwclock --set --date="'.$this->dt.'" --localtime');
		exec('sudo hwclock -s');
		if($update){
			$this->retError('Error : '.$update);
		}
		else{
			$this->retSuccess();
		}
	}

	private function formSave(){
		$dt = $this->dt;
		$db = $this->database;

		$id = $dt['formId'];
		$index = $dt['index'];
		unset($dt['formId']);
		unset($dt['index']);

		if($id == 'info'){
			$image = '';
			$type = isset($dt['info_type']) ? $dt['info_type'] : 'text';
			$textBody = isset($dt['r2']) ? trim($dt['r2']) : '';
			if(isset($db[$id][$index][4])){
				$image = $db[$id][$index][4];
			}
			if($type === 'image'){
				if(isset($_FILES['info_image']) && isset($_FILES['info_image']['size']) && $_FILES['info_image']['size'] > 0){
					$this->deleteInfoFileIfExists($image);
					$image = $this->saveUploadedInfoFile($_FILES['info_image'], 'info');
				}
				if(!$image){
					$this->retError('Gambar wajib dipilih untuk tipe Gambar...');
				}
			}
			else if($textBody === ''){
				$this->retError('Isi informasi wajib diisi untuk tipe Teks...');
			}
			$dt = [$dt['r1'], $dt['r2'], $dt['r3'], $dt['active'], $image, $type];
		}
		else if($id == 'running_text'){
			$dt = $dt['text'];
		}
		else if($id == 'youtube'){
			$dt['active'] = isset($dt['active']) && $dt['active'] == '1';
			$dt['mute'] = isset($dt['mute']) && $dt['mute'] == '1';
			$dt['url'] = trim($dt['url']);
			if($dt['active'] && !$dt['url']){
				$this->retError('URL YouTube wajib diisi jika video diaktifkan...');
			}
			if(!$dt['active']){
				$dt['mute'] = true;
			}
		}
		else if($id == 'ppt'){
			$dt['active'] = isset($dt['active']) && $dt['active'] == '1';
			$dt['url'] = trim($dt['url']);
			if($dt['active'] && !$dt['url']){
				$this->retError('URL embed PPT wajib diisi jika mode PPT diaktifkan...');
			}
		}
		else if($id == 'imamMuadzin'){
			$dt = [
				'active' => isset($dt['active']) && $dt['active'] == '1',
				'fajr' => [
					'imam' => trim(isset($dt['fajr_imam']) ? $dt['fajr_imam'] : ''),
					'muadzin' => trim(isset($dt['fajr_muadzin']) ? $dt['fajr_muadzin'] : '')
				],
				'dhuhr' => [
					'imam' => trim(isset($dt['dhuhr_imam']) ? $dt['dhuhr_imam'] : ''),
					'muadzin' => trim(isset($dt['dhuhr_muadzin']) ? $dt['dhuhr_muadzin'] : '')
				],
				'asr' => [
					'imam' => trim(isset($dt['asr_imam']) ? $dt['asr_imam'] : ''),
					'muadzin' => trim(isset($dt['asr_muadzin']) ? $dt['asr_muadzin'] : '')
				],
				'maghrib' => [
					'imam' => trim(isset($dt['maghrib_imam']) ? $dt['maghrib_imam'] : ''),
					'muadzin' => trim(isset($dt['maghrib_muadzin']) ? $dt['maghrib_muadzin'] : '')
				],
				'isha' => [
					'imam' => trim(isset($dt['isha_imam']) ? $dt['isha_imam'] : ''),
					'muadzin' => trim(isset($dt['isha_muadzin']) ? $dt['isha_muadzin'] : '')
				]
			];
		}
		else if($id == 'infoDisplay'){
			$dt = [
				'fullscreen' => isset($dt['fullscreen']) && $dt['fullscreen'] == '1'
			];
		}
		else if($id == 'prayTimesAdjust'){
			$db['prayTimesMethod'] = $dt['prayTimesMethod'];
			unset($dt['prayTimesMethod']);
		}
		else if($id == 'gantiPass'){
			if($dt['password_lama'] != $db['akses']['pass']){
				$this->retError('Password lama salah...');
			}
			else if($dt['password_baru'] != $dt['ulangi_password_baru']){
				$this->retError('Password baru tidak sama...');
			}
			else if(strlen($dt['password_baru']) < 8){
				$this->retError('Password terlalu pendek, minimal 8 karakter...');
			}
			else{
				$dt = $dt['password_baru'];
				$id = 'akses';
				$index = 'pass';
			}
		}

		if($index == 'no-index'){
			$db[$id] = array_merge($db[$id], $dt);
		}
		else if($index == 'new'){
			$db[$id][] = $dt;
		}
		else{
			$db[$id][$index] = $dt;
		}

		$this->database = array_merge($this->database, $db);
		$this->saveDatabase();
		$this->retSuccess();
	}

	private function getInfoImage($index){
		if(!isset($this->database['info'][$index][4])){
			return '';
		}
		return $this->database['info'][$index][4];
	}

	private function getInfoDir(){
		return __DIR__.'/../display/info/';
	}

	private function deleteInfoFileIfExists($filename){
		if(!$filename){
			return;
		}
		$path = $this->getInfoDir().$filename;
		if(file_exists($path)){
			@unlink($path);
		}
	}

	private function saveUploadedInfoFile($file, $filenamePrefix){
		$dir = $this->getInfoDir();
		if(!is_dir($dir) || !is_writable($dir)){
			$this->retError('Folder info tidak bisa ditulis. Cek permission folder: '.$dir);
		}

		$allowed_ext = array('jpg', 'jpeg', 'png', 'webp');
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if(!in_array($ext, $allowed_ext)){
			$this->retError($file['name']." tidak didukung\nExt yang diperbolehkan : ".implode(", ", $allowed_ext));
		}
		$targetName = $filenamePrefix.'-'.time().'.'.$ext;
		if(!move_uploaded_file($file['tmp_name'], $dir.$targetName)){
			$this->retError('Gagal upload gambar ke folder tujuan. Cek permission folder: '.$dir);
		}
		return $targetName;
	}

	private function getInfoType($item){
		if(isset($item[5]) && $item[5] == 'image'){
			return 'image';
		}
		return 'text';
	}

	private function deleteInfoImage(){
		if(!isset($this->dt['index']) || !is_numeric($this->dt['index'])){
			$this->retError('Index informasi tidak valid...');
		}

		$index = (int) $this->dt['index'];
		if(!isset($this->database['info'][$index])){
			$this->retError('Data informasi tidak ditemukan...');
		}

		$image = $this->getInfoImage($index);
		if(!$image){
			$this->retError('Gambar tidak ditemukan...');
		}
		$this->deleteInfoFileIfExists($image);

		$this->database['info'][$index][4] = '';
		$this->saveDatabase();
		$this->retSuccess();
	}

	private function saveWallpaper(){
		if(empty($_FILES)){
			$this->retError('File wallpaper belum dipilih...');
		}
		$dir = __DIR__.'/../display/wallpaper/';
		if(!is_dir($dir) || !is_writable($dir)){
			$this->retError('Folder wallpaper tidak bisa ditulis. Cek permission folder: '.$dir);
		}
		$allowed_ext = array('jpg');
		$i = 0;
		$uploaded = false;
		foreach($_FILES as $file){
			if($file['size'] > 0){
				$uploaded = true;
				$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
				if(!in_array($ext, $allowed_ext)){
					$this->retError($file['name']." tidak didukung\nExt yang diperbolehkan : ".implode(", ", $allowed_ext));
				}
				$target = $dir.time().$i.'.'.$ext;
				if(!move_uploaded_file($file['tmp_name'], $target)){
					$this->retError('Gagal upload wallpaper ke folder tujuan. Cek permission folder: '.$dir);
				}
			}
			$i++;
		}
		if(!$uploaded){
			$this->retError('File wallpaper belum dipilih...');
		}
		$this->retSuccess();
	}

	private function saveLogo(){
		if(empty($_FILES)){
			$this->retError('File logo belum dipilih...');
		}
		$dir = __DIR__.'/../display/logo/';
		if(!is_dir($dir) || !is_writable($dir)){
			$this->retError('Folder logo tidak bisa ditulis. Cek permission folder : '.$dir);
		}
		$allowed_ext = array('png');
		$uploaded = false;
		foreach($_FILES as $file){
			if($file['size'] > 0){
				$uploaded = true;
				$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
				if(!in_array($ext, $allowed_ext)){
					$this->retError($file['name']." tidak didukung\nExt yang diperbolehkan : ".implode(", ", $allowed_ext));
				}
				$oldLogo = __DIR__.'/../display/logo/'.$this->getLogo();
				if(file_exists($oldLogo) && !unlink($oldLogo)){
					$this->retError('Logo lama tidak bisa dihapus. Cek permission file/folder logo.');
				}
				$target = $dir.time().'.'.$ext;
				if(!move_uploaded_file($file['tmp_name'], $target)){
					$this->retError('Gagal upload logo ke folder tujuan. Cek permission folder: '.$dir);
				}
			}
		}
		if(!$uploaded){
			$this->retError('File logo belum dipilih...');
		}
		$this->retSuccess();
	}

	private function getWallpaper(){
		$dir = __DIR__.'/../display/wallpaper/';
		$files = array_diff(scandir($dir), array('.', '..', 'Thumbs.db'));
		return $files;
	}

	private function getLogo(){
		$dir = __DIR__.'/../display/logo/';
		$files = array_diff(scandir($dir), array('.', '..', 'Thumbs.db'));
		$files = array_values($files);
		return $files[0];
	}

	private function wallpaperDelete(){
		if(count($this->getWallpaper()) < 2){
			$this->retError('minimal harus ada 1 wallpaper');
		}
		else{
			$dir = __DIR__.'/../display/wallpaper/';
			$file = $this->dt;
			if(file_exists($dir.$file)){
				unlink($dir.$file);
			}
			$this->retSuccess();
		}
	}

	private function formDelete(){
		$dt = $this->dt;
		$db = $this->database;
		$id = $dt['formId'];
		$index = $dt['index'];
		if(count($db[$id]) < 2){
			$this->retError("Minimal harus ada 1 data...");
		}
		else{
			if($id == 'info' && isset($db[$id][$index][4]) && $db[$id][$index][4]){
				$image = __DIR__.'/../display/info/'.$db[$id][$index][4];
				if(file_exists($image)){
					@unlink($image);
				}
			}
			unset($db[$id][$index]);
			$db[$id] = array_values($db[$id]);
			$this->database = $db;
			$this->saveDatabase();
			$this->retSuccess();
		}
	}
}

$request = isset($_POST['id']) ? $_POST['id'] : "UNKNOWN_REQUEST_________________________________________";
new proses($request);
?>

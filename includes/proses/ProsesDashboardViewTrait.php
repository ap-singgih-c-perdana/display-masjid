<?php

trait ProsesDashboardViewTrait {
	private function info(){
		$db	= $this->database;
		$id	= 'info';
		ob_start();
		$arrActive = ['Ya' => 1, 'Tidak' => 0];
		$arrType = ['Teks' => 'text', 'Gambar' => 'image'];
		$db[$id]['new'] = ['','','',true,'','text'];
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';
		$formInfoDisplay = [
			'Mode fullpage' => [
				'name'	=> 'fullscreen',
				'type'	=> 'select',
				'arr'	=> $arrActive,
				'value'	=> isset($db['infoDisplay']['fullscreen']) ? $db['infoDisplay']['fullscreen'] : false
			]
		];
		$setInfoDisplay = [
			'id'	=> 'infoDisplay',
			'title'	=> 'Mode tampilan informasi',
			'info'	=> "- Saat aktif, slide informasi tampil fullpage.\n- Panel kiri, logo, dan running text disembunyikan."
		];
		echo $this->generateCompleteForm($formInfoDisplay, $setInfoDisplay);
		foreach($db[$id] as $k => $v){
			$optActive = '';
			foreach($arrActive as $ka => $va){
				$selected = $va == $v[3] ? 'selected' : '';
				$optActive .= '<option '.$selected.' value="'.$va.'">'.$ka.'</option>';
			}
			$title = is_int($k) ? 'Info '.($k + 1) : 'Info Baru';
			$delBtn = is_int($k) ? '<button type="button" class="btn btn-danger delete"><i class="fa fa-trash" aria-hidden="true"></i> hapus</button>' : '';
			$image = isset($v[4]) ? $v[4] : '';
			$type = $this->getInfoType($v);
			$optType = '';
			foreach($arrType as $ka => $va){
				$selected = $va == $type ? 'selected' : '';
				$optType .= '<option '.$selected.' value="'.$va.'">'.$ka.'</option>';
			}
			?>
			<form method="post" class="form">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title"><?=$title?></h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="input-group">
					  <span class="input-group-addon">Tipe</span>
					  <select name="info_type" class="form-control input-sm info-type-select" required><?=$optType?></select>
					</div>
					<div class="input-group info-text-fields" style="<?=$type === 'image' ? 'display:none' : ''?>">
					  <span class="input-group-addon">Header</span>
					  <input name="r1" type="text" maxlength="100" class="form-control" value="<?=$v[0]?>" placeholder="boleh dikosongkan">
					</div>
					<div class="input info-text-fields" style="<?=$type === 'image' ? 'display:none' : ''?>">
					  <textarea name="r2" maxlength="255" rows="3" class="form-control" <?=$type === 'text' ? 'required' : ''?>><?=$v[1]?></textarea>
					</div>
					<div class="input-group info-text-fields" style="<?=$type === 'image' ? 'display:none' : ''?>">
					  <span class="input-group-addon">Footer</span>
					  <input name="r3" type="text" maxlength="100" class="form-control" value="<?=$v[2]?>" placeholder="boleh dikosongkan">
					</div>
					<?php if(!is_int($k)): ?>
					<div class="input" style="margin-top:8px">
						<small>
						- `Teks` menampilkan judul, isi, dan footer.<br>
						- `Gambar penuh` menampilkan gambar besar memenuhi area konten seperti YouTube/PPT.
						</small>
					</div>
					<?php endif; ?>
					<div class="info-image-upload-box" style="<?=$type === 'image' ? '' : 'display:none'?>">
						<?php if($image): ?>
						<div class="input" style="margin:10px 0">
							<div style="margin-bottom:8px"><small>Gambar aktif: <b><?=$image?></b></small></div>
							<img src="display/info/<?=$image?>" alt="Preview info" class="img-responsive" style="max-height:220px;border:1px solid #ddd;padding:6px;background:#fff">
						</div>
						<?php endif; ?>
						<div class="input-group" style="margin-top:10px">
						  <span class="input-group-addon">Gambar</span>
						  <input type="file" name="info_image" class="form-control input-sm" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" <?=($type === 'image' && !$image) ? 'required' : ''?>>
						</div>
					</div>
					<div class="input-group">
					  <span class="input-group-addon">Aktif</span>
					  <select name="active" class="form-control  input-sm" required><?=$optActive?></select>
					</div>
					<div class="form-group">
						<input type="hidden" name="formId" value="<?=$id?>">
						<input type="hidden" name="index" value="<?=$k?>">
					</div>
				</div>
				<div class="box-footer">
					<?=$delBtn?>
					<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i> simpan</button>
				</div>
			</div>
			</form>
			<?php
		}
		echo '</div></div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
	}

	private function wallpaper(){
		$wp	= $this->getWallpaper();
		ob_start();
		echo '
			<section class="content-header content-dynamic section-wallpaper">
			<div class="row">
		';
		?>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<form method="post" class="form-file" enctype="multipart/form-data">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Tambah wallpaper</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="input-group">
					  <span class="input-group-addon">File wallpaper</span>
					  <input type="file" multiple="" class="form-control input-sm" placeholder="" data-proses="saveWallpaper">
					</div>
					<div class="input">
						<small>
						- Ext file yang didukung :  <b>.jpg</b><br>
						- Maksimal 5 file dalam sekali upload<br>
						- Gunakan gambar yang proporsional agar loading wallpaper tetap nyaman
						</small>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-upload" aria-hidden="true"></i> upload</button>
				</div>
			</div>
			</form>
		</div>
		<?php
		foreach($wp as $v):
		?>
		<div class="col-md-4 col-sm-6 col-xs-12">
          <div class="small-box" style="background-image: url(display/wallpaper/<?=$v?>);">
            <div class="inner"></div>
            <a href="javascript:void(0)" data-file="<?=$v?>" class="small-box-footer"><i class="fa fa-trash"></i> delete</a>
          </div>
        </div>
		<?php
		endforeach;
		echo '</div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
	}

	private function running_text(){
		$db	= $this->database;
		$id	= 'running_text';
		ob_start();
		$db[$id]['new'] = '';
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';
		foreach($db[$id] as $k => $v){
			$title = is_int($k) ? 'Teks '.($k + 1) : 'Teks Baru';
			$delBtn = is_int($k) ? '<button type="button" class="btn btn-danger delete"><i class="fa fa-trash" aria-hidden="true"></i> hapus</button>' : '';
			?>
			<form method="post" class="form">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title"><?=$title?></h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="input">
					  <textarea name="text" maxlength="255" rows="3" class="form-control" required><?=$v?></textarea>
					</div>
					<div class="input">
						<input type="hidden" name="formId" value="<?=$id?>">
						<input type="hidden" name="index" value="<?=$k?>">
					</div>
				</div>
				<div class="box-footer">
					<?=$delBtn?>
					<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i> simpan</button>
				</div>
			</div>
			</form>
			<?php
		}
		echo '</div></div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
	}

	private function youtube(){
		$db	= $this->database;
		$arrActive = ['Ya' => 1, 'Tidak' => 0];
		$formYoutube = [
			'aktif' => [
				'name'	=> 'active',
				'type'	=> 'select',
				'arr'	=> $arrActive,
				'value'	=> $db['youtube']['active']
			],
			'url video' => [
				'name'			=> 'url',
				'type'			=> 'text',
				'maxlength'		=> 255,
				'value'			=> $db['youtube']['url'],
				'placeholder'	=> 'https://www.youtube.com/watch?v=...',
				'required'		=> false
			],
			'mute autoplay' => [
				'name'	=> 'mute',
				'type'	=> 'select',
				'arr'	=> $arrActive,
				'value'	=> $db['youtube']['mute']
			]
		];
		$setYoutube = [
			'id'	=> 'youtube',
			'title'	=> 'Video YouTube',
			'info'	=> "- Saat aktif, video tampil di area informasi sebelah kanan display.\n- Link yang didukung: youtube.com/watch?v=..., youtu.be/..., atau youtube.com/embed/...\n- Autoplay paling stabil jika mode mute diaktifkan."
		];
		$this->data = '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">'.
			$this->generateCompleteForm($formYoutube, $setYoutube).
			'</div></div></section>';
		$this->retSuccess();
	}

	private function ppt(){
		$db	= $this->database;
		$arrActive = ['Ya' => 1, 'Tidak' => 0];
		$formPpt = [
			'aktif' => [
				'name'	=> 'active',
				'type'	=> 'select',
				'arr'	=> $arrActive,
				'value'	=> $db['ppt']['active']
			],
			'url embed ppt' => [
				'name'			=> 'url',
				'type'			=> 'text',
				'maxlength'		=> 500,
				'value'			=> $db['ppt']['url'],
				'placeholder'	=> 'https://view.officeapps.live.com/op/embed.aspx?src=...',
				'required'		=> false
			]
		];
		$setPpt = [
			'id'	=> 'ppt',
			'title'	=> 'Embed PPT',
			'info'	=> "- Running text tetap tampil seperti biasa di bagian bawah.\n- Mode ini menampilkan presentasi di area kanan sebagai alternatif info biasa.\n- Gunakan URL embed dari Microsoft Office Online atau Google Slides.\n- Jika YouTube dan PPT sama-sama aktif, YouTube yang diprioritaskan tampil."
		];
		$this->data = '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">'.
			$this->generateCompleteForm($formPpt, $setPpt).
			'</div></div></section>';
		$this->retSuccess();
	}

	private function timer(){
		$db	= $this->database;

		ob_start();
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';

		$timer = $db['timer'];
		$formTimer = [];
		foreach($timer as $k => $v){
			$formTimer[$k] = [
				'type'	=> 'number',
				'min'	=> 1,
				'max'	=> 180,
				'step'	=> 1,
				'value'	=> $v,
				'placeholder'	=> '1-180',
				'required'	=> true,
				'addon'	=> 'menit'
			];
			if($k == 'info' || $k == 'wallpaper'){
				$formTimer[$k]['max'] = 86400;
				$formTimer[$k]['addon'] = 'detik';
				$formTimer[$k]['placeholder'] = '1-86400';
			}
			else if($k == 'countdown_alarm'){
				$formTimer[$k]['max'] = 60;
				$formTimer[$k]['addon'] = 'detik';
				$formTimer[$k]['placeholder'] = '1-60';
			}
		}
		$setTimer = [
			'id' => 'timer',
			'title' => 'Timer'
		];
		echo $this->generateCompleteForm($formTimer, $setTimer);

		$iqomah = $db['iqomah'];
		$formIqomah = [];
		foreach($iqomah as $k => $v){
			$formIqomah[$k] = [
				'type'	=> 'number',
				'min'	=> 1,
				'max'	=> 180,
				'step'	=> 1,
				'value'	=> $v,
				'required'	=> true,
				'placeholder'	=> '1-180',
				'addon'	=> 'menit'
			];
		}
		$setIqomah = [
			'id' => 'iqomah',
			'title' => 'Timer Iqomah'
		];
		echo $this->generateCompleteForm($formIqomah, $setIqomah);

		$jumat = $db['jumat'];
		$arrActive = ['Ya' => 1, 'Tidak' => 0];
		$formJumat = [
			'aktif' => [
				'name'	=> 'active',
				'type'	=> 'select',
				'arr'	=> $arrActive,
				'value'	=> $jumat['active']
			],
			'khutbah'	=>[
				'name'	=> 'duration',
				'type'	=> 'number',
				'min'	=> 1,
				'max'	=> 180,
				'step'	=> 1,
				'value'	=> $jumat['duration'],
				'required'	=> true,
				'addon'	=> 'menit'
			],
			'text'	=>[
				'type'	=> 'text',
				'maxlength'	=> 100,
				'value'	=> $jumat['text'],
				'required'	=> true
			],
		];
		$setJumat = [
			'id'	=> 'jumat',
			'title'	=> 'Sholat jum\'at (opsional)'
		];
		echo $this->generateCompleteForm($formJumat, $setJumat);

		$tarawih = $db['tarawih'];
		$formTarawih = [
			'aktif' => [
				'name'	=> 'active',
				'type'	=> 'select',
				'arr'	=> $arrActive,
				'value'	=> $tarawih['active']
			],
			'durasi'	=>[
				'name'	=> 'duration',
				'type'	=> 'number',
				'min'	=> 1,
				'max'	=> 180,
				'step'	=> 1,
				'value'	=> $tarawih['duration'],
				'required'	=> true,
				'addon'	=> 'menit'
			]
		];
		$setTarawih = [
			'id'	=> 'tarawih',
			'title'	=> 'Sholat tarawih (opsional)',
			'info'	=> 'Jika diperlukan, aktifkan hanya di bulan ramadhan'
		];
		echo $this->generateCompleteForm($formTarawih, $setTarawih);

		echo '</div></div></section>';
		$this->data .= ob_get_clean();
		$this->retSuccess();
	}

	private function jadwal(){
		$db	= $this->database;
		$method	= $db['prayTimesMethod'];
		$adjust	= $db['prayTimesAdjust'];

		$arrMethod = [
			'0'			=> 'Manual parameter',
			'MWL'		=> 'Muslim World League',
			'ISNA'		=> 'Islamic Society of North America',
			'Egypt'		=> 'Egyptian General Authority of Survey',
			'Makkah'	=> 'Umm al-Qura University, Makkah',
			'Karachi'	=> 'University of Islamic Sciences, Karachi',
			'Tehran'	=> 'Institute of Geophysics, University of Tehran',
			'Jafari'	=> 'Shia Ithna Ashari (Ja`fari)'
		];
		ob_start();
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';
		?>
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs pull-right">
				  <li><a href="#info" data-toggle="tab"><i class="fa fa-info-circle"></i></a></li>
				  <li><a href="#parameter" data-toggle="tab">Parameter</a></li>
				  <li class="active"><a href="#metode" data-toggle="tab">Metode</a></li>
				  <li class="pull-left header"><i class="fa fa-inbox"></i>Library</li>
				</ul>
				<div class="tab-content">
				  <div class="tab-pane" id="info" >
					Perhitungan waktu sholat menggunakan library dari <a href="http://praytimes.org/" target="_blank">praytimes.org</a>, Untuk manual lebih detail bisa di cek pada halaman situs tersebut.<br>
					Library yang dipakai <b>PrayTimes Version 2.3</b> (versi terbaru pada saat aplikasi ini dibuat)<br><br>
					Untuk mempermudah, setting parameter yang bisa di ganti hanya <b>fajr, dhuhr, asr, maghrib, isha</b> menyesuaikan tampilan pada display. Jika parameter tidak perlu diganti kosongkan saja (diisi default)

					<br><br>
					Contoh penggunaan untuk kota bekasi mengkuti metode kemenag bekasi :
					<pre>
latitude	= -6.14
longitude	= 106.59
timeZone	= 7 (GMT +7)
fajr		= 20°
asr		= Standard (Shafii, Maliki, Jafari and Hanbali / shadow factor = 1)
isha		= 18°
					</pre>
					<small>Default aplikasi ini menggunakan setting <b>bekasi - jawa barat - indonesia</b> dengan metode seperti diatas</small>
				  </div>
				  <div class="tab-pane" id="parameter">
					<h4>Parameters</h4>
					<table class="table table-condensed">
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Values</th>
								<th>Description</th>
								<th>Sample Value</th>
							</tr>
						</thead>
						<tbody>
							<tr><td> fajr </td><td> degrees </td><td> twilight angle </td><td> 15</td></tr>
							<tr><td> dhuhr </td><td> minutes </td><td> minutes after mid-day </td><td> 1 min</td></tr>
							<tr><td rowspan="2"> asr</td><td> method </td><td> asr juristic method; see the table below </td><td> Standard</td></tr>
							<tr><td> factor </td><td> shadow length factor for realizing asr </td><td> 1.7</td></tr>
							<tr><td rowspan="2"> maghrib</td><td> degrees </td><td> twilight angle </td><td> 4</td></tr>
							<tr><td> minutes </td><td> minutes after sunset </td><td> 15 min</td></tr>
							<tr><td rowspan="2"> isha</td><td> degrees </td><td> twilight angle </td><td> 18</td></tr>
							<tr><td> minutes </td><td> minutes after maghrib </td><td> 90 min</td></tr>
						</tbody>
					</table>

					<h4>Asr methods</h4>
					<table class="table table-condensed table-striped">
						<thead>
							<tr>
								<th>Method</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td> Standard </td>
								<td> Shafii, Maliki, Jafari and Hanbali (shadow factor = 1)</td>
							</tr>
							<tr>
								<td> Hanafi </td>
								<td> Hanafi school of tought (shadow factor = 2)</td>
							</tr>
						</tbody>
					</table>

					<b>Contoh penggunaan:</b><br>
					- Asr menggunakan metode <i>Shafii, Maliki, Jafari and Hanbali</i>, maka diisi : <b>Standard</b><br>
					- Asr menggunakan metode <i>Hanafi school of tought</i>, maka diisi : <b>Hanafi</b><br>
					- Asr menggunakan <i>shadow factor 1.5</i>, maka diisi : <b>1.5</b><br>
					- Isha menggunakan <i>twilight angle (18.5 deg)</i>, maka diisi : <b>18.5</b><br>
					- Isha menggunakan <i>85 minutes after maghrib</i>, maka diisi : <b>85 min</b> <a style="color:#00F">(85 spasi min)</a><br>
					- dst...
				  </div>
				  <div class="tab-pane active" id="metode" >
					<h4>Calculation Methods</h4>
					<table class="table table-condensed table-striped">
						<thead>
							<tr>
								<th>Method</th>
								<th>Abbr.</th>
								<th>Region Used</th>
							</tr>
						</thead>
						<tbody>
							<tr><td> Muslim World League </td><td> MWL </td><td> Europe, Far East, parts of US</td></tr>
							<tr><td> Islamic Society of North America </td><td> ISNA </td><td> North America (US and Canada)</td></tr>
							<tr><td> Egyptian General Authority of Survey </td><td> Egypt </td><td> Africa, Syria, Lebanon, Malaysia</td></tr>
							<tr><td> Umm al-Qura University, Makkah </td><td> Makkah </td><td> Arabian Peninsula</td></tr>
							<tr><td> University of Islamic Sciences, Karachi </td><td> Karachi &nbsp; </td><td> Pakistan, Afganistan, Bangladesh, India</td></tr>
							<tr><td> Institute of Geophysics, University of Tehran </td><td> Tehran </td><td> Iran, Some Shia communities</td></tr>
							<tr><td> Shia Ithna Ashari, Leva Research Institute, Qum &nbsp; </td><td> Jafari </td><td> Some Shia communities worldwide</td></tr>
						</tbody>
					</table>

					<h4>Calculating Parameters</h4>
					<table class="table table-condensed table-striped">
						<thead>
							<tr>
								<th>Method &nbsp;</th>
								<th>Fajr Angle</th>
								<th>Isha</th>
								<th>Maghrib</th>
								<th>Midnight</th>
							</tr>
						</thead>
						<tbody>
							<tr><td> MWL </td><td> 18° </td><td> 17° </td><td> = Senset </td><td> mid Sunset to Sunrise</td></tr>
							<tr><td> ISNA </td><td> 15° </td><td> 15° </td><td> = Senset </td><td> mid Sunset to Sunrise</td></tr>
							<tr><td> Egypt </td><td> 19.5° </td><td> 17.5° </td><td> = Senset </td><td> mid Sunset to Sunrise</td></tr>
							<tr><td> Makkah </td><td> 18.5° </td><td> 90 min after Maghrib<br>120 min during Ramadan </td><td> = Senset </td><td> mid Sunset to Sunrise</td></tr>
							<tr><td> Karachi </td><td> 18° </td><td> 18° </td><td> = Senset </td><td> mid Sunset to Sunrise</td></tr>
							<tr><td> Tehran </td><td> 17.7° </td><td> 14° </td><td> 4.5° </td><td> mid Sunset to Fajr</td></tr>
							<tr><td> Jafari </td><td> 16° </td><td> 14° </td><td> 4° </td><td> mid Sunset to Fajr</td></tr>
						</tbody>
					</table>
				  </div>
				</div>
			</div>

			<form method="post" class="form">
			<div class="box box-warning">
				<div class="box-header with-border">
					<h3 class="box-title">Metode</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="input">
						<select class="form-control" name="prayTimesMethod" id="prayTimesMethod">
							<?=$this->generateOptionSelect($method, $arrMethod, false)?>
						</select>
					</div>
					<div id="prayTimesAdjust" style="display:none">
						<?=$this->formPrayTimesAdjust($adjust) ?>
						<div class="form-group">
							<small>
								- Lihat manual parameter (diatas) untuk cara pengisian.<br>
								- Parameter <b>case sensitive</b> (contoh : <b>Standard</b> tidak sama dengan <b>standard</b>)<br>
								- Jika dikosongkan maka akan diisi default.
							</small>
							<input type="hidden" name="formId" value="prayTimesAdjust">
							<input type="hidden" name="index" value="no-index">
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i> simpan</button>
				</div>
			</div>
			</form>
		<?php

		$seting = $db['setting'];
		$location = [
			'latitude'	=> [
				'type'	=> 'number',
				'min'	=> -999.0000001,
				'max'	=> 999.9999999,
				'step'	=> 0.0000001,
				'value'	=> $seting['latitude'],
				'required'	=> true,
				'addon'	=> '°'
			],
			'longitude'	=> [
				'type'	=> 'number',
				'min'	=> -999.0000001,
				'max'	=> 999.9999999,
				'step'	=> 0.0000001,
				'value'	=> $seting['longitude'],
				'required'	=> true,
				'addon'	=> '°'
			],
			'timeZone'	=> [
				'type'	=> 'number',
				'min'	=> -11,
				'max'	=> 12,
				'step'	=> 1,
				'value'	=> $seting['timeZone'],
				'required'	=> true,
				'placeholder'	=> 'GMT-11 to GMT+12',
				'addon'	=> 'GMT'
			],
			'dst'		=> [
				'type'	=> 'select',
				'arr'	=> ['0' => '0', '1' => '1', 'Auto' => 'auto'],
				'value'	=> $seting['dst'],
				'required'	=> true
			],
		];
		$set = [
			'id'	=> 'setting',
			'title'	=> 'Lokasi',
			'info'	=> '<b>DST</b> = Daylight Saving Time (Waktu Musim Panas)
						Waktu resmi dimajukan (biasanya) satu jam lebih awal dari zona waktu standar dan diberlakukan selama musim semi dan musim panas (berlaku untuk wilayah eropa)
						Untuk wilayah indonesia isi 0.
			'
		];
		echo $this->generateCompleteForm($location, $set);

		$tune = $db['prayTimesTune'];
		$tune_ = [];
		foreach($tune as $k => $v){
			$tune_[$k] = [
				'type'	=> 'number',
				'min'	=> -60,
				'max'	=> 60,
				'step'	=> 1,
				'value'	=> $v,
				'required'	=> true,
				'placeholder'	=> '-60 to 60',
				'addon'	=> 'menit'
			];
		}
		$set = [
			'id'	=> 'prayTimesTune',
			'title'	=> 'Penyesuaian waktu sholat',
			'info'	=> '- Untuk menyesuaikan waktu sholat -60 sampai +60 menit.
						- Contoh penggunaan : jadwal ditambahkan +2 menit untuk ihtiyati (pengaman)
			'
		];
		echo $this->generateCompleteForm($tune_, $set);

		$arrActive = ['Ya' => 1, 'Tidak' => 0];
		$imamMuadzin = $db['imamMuadzin'];
		$formImamMuadzin = [
			'tampilkan di wallpaper' => [
				'name'	=> 'active',
				'type'	=> 'select',
				'arr'	=> $arrActive,
				'value'	=> $imamMuadzin['active']
			],
			'Subuh - imam' => [
				'name'		=> 'fajr_imam',
				'type'		=> 'text',
				'maxlength'	=> 100,
				'value'		=> $imamMuadzin['fajr']['imam'],
				'required'	=> false
			],
			'Subuh - muadzin' => [
				'name'		=> 'fajr_muadzin',
				'type'		=> 'text',
				'maxlength'	=> 100,
				'value'		=> $imamMuadzin['fajr']['muadzin'],
				'required'	=> false
			],
			'Dzuhur - imam' => [
				'name'		=> 'dhuhr_imam',
				'type'		=> 'text',
				'maxlength'	=> 100,
				'value'		=> $imamMuadzin['dhuhr']['imam'],
				'required'	=> false
			],
			'Dzuhur - muadzin' => [
				'name'		=> 'dhuhr_muadzin',
				'type'		=> 'text',
				'maxlength'	=> 100,
				'value'		=> $imamMuadzin['dhuhr']['muadzin'],
				'required'	=> false
			],
			'Ashar - imam' => [
				'name'		=> 'asr_imam',
				'type'		=> 'text',
				'maxlength'	=> 100,
				'value'		=> $imamMuadzin['asr']['imam'],
				'required'	=> false
			],
			'Ashar - muadzin' => [
				'name'		=> 'asr_muadzin',
				'type'		=> 'text',
				'maxlength'	=> 100,
				'value'		=> $imamMuadzin['asr']['muadzin'],
				'required'	=> false
			],
			'Maghrib - imam' => [
				'name'		=> 'maghrib_imam',
				'type'		=> 'text',
				'maxlength'	=> 100,
				'value'		=> $imamMuadzin['maghrib']['imam'],
				'required'	=> false
			],
			'Maghrib - muadzin' => [
				'name'		=> 'maghrib_muadzin',
				'type'		=> 'text',
				'maxlength'	=> 100,
				'value'		=> $imamMuadzin['maghrib']['muadzin'],
				'required'	=> false
			],
			'Isya - imam' => [
				'name'		=> 'isha_imam',
				'type'		=> 'text',
				'maxlength'	=> 100,
				'value'		=> $imamMuadzin['isha']['imam'],
				'required'	=> false
			],
			'Isya - muadzin' => [
				'name'		=> 'isha_muadzin',
				'type'		=> 'text',
				'maxlength'	=> 100,
				'value'		=> $imamMuadzin['isha']['muadzin'],
				'required'	=> false
			]
		];
		$setImamMuadzin = [
			'id'	=> 'imamMuadzin',
			'title'	=> 'Jadwal imam & muadzin',
			'info'	=> 'Saat aktif, data ini siap dipakai untuk slide wallpaper/informasi. Kosongkan field yang belum ingin ditampilkan.'
		];
		echo $this->generateCompleteForm($formImamMuadzin, $setImamMuadzin);

		echo '</div></div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
	}

	private function pengaturan(){
		$db	= $this->database;
		ob_start();
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';
		?>
		<form method="post" class="form-file" enctype="multipart/form-data">
		<div class="box box-success ">
			<div class="box-header with-border">
				<h3 class="box-title">Logo</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body" style="background-image: url(dist/img/bgTransparent.jpg);">
				<img class="img-responsive pad" src="display/logo/<?=$this->getLogo();?>" style="border:2px dashed #F00;padding:0">
			</div>
			<div class="box-body">
				<div class="input-group">
				  <span class="input-group-addon">File logo</span>
				  <input type="file" class="form-control input-sm" placeholder="" data-proses="saveLogo" accept=".png,image/png">
				</div>
				<div class="input">
					<small>
					- Ext file yang didukung :  <b>.png</b><br>
					- Tips : jika logo tampil terlalu besar pada display, edit gambar pada image editor (contoh : photoshop) dan beri jarak kosong pada atas-bawah atau kiri-kanan gambar
					</small>
				</div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-upload" aria-hidden="true"></i> upload</button>
			</div>
		</div>
		</form>
		<?php

		$setting = $db['setting'];
		unset($setting['latitude']);
		unset($setting['longitude']);
		unset($setting['timeZone']);
		unset($setting['dst']);
		$setSetting = [
			'id'	=> 'setting',
			'title'	=> 'Detail masjid/musholla',
			'color'	=> 'box-success',
			'info'	=> '- Data ini opsional (bisa dikosongkan)
			',
			'open'	=> false
		];
		echo $this->generateTextForm($setting, $setSetting, false);

		$dataPass = [
			'password lama'	=> [
				'name'		=> 'password_lama',
				'type'		=> 'password',
				'maxlength'	=> 20,
				'required'	=> true
			],
			'password baru'	=> [
				'name'		=> 'password_baru',
				'type'		=> 'password',
				'maxlength'	=> 20,
				'required'	=> true
			],
			'ulangi password'	=> [
				'name'		=> 'ulangi_password_baru',
				'type'		=> 'password',
				'maxlength'	=> 20,
				'required'	=> true
			],
		];

		$setPass = [
			'id'	=> 'gantiPass',
			'title'	=> 'Ganti password admin',
			'color'	=> 'box-danger',
			'info'	=> '- Password default : <b>admin</b>
						- Jangan mengganti password dengan \'admin\'
						- Tips : gunakan campuran angka dan huruf untuk memperkuat password.
			',
			'open'	=> false
		];
		echo $this->generateCompleteForm($dataPass, $setPass);

		$prayName = $db['prayName'];
		$set = [
			'id'	=> 'prayName',
			'title'	=> 'Nama sholat',
			'open'	=> false
		];
		echo $this->generateTextForm($prayName, $set);

		$timeName = $db['timeName'];
		$set = [
			'id'	=> 'timeName',
			'title'	=> 'Nama waktu',
			'open'	=> false
		];
		echo $this->generateTextForm($timeName, $set);

		$dayName = $db['dayName'];
		$set = [
			'id'	=> 'dayName',
			'title'	=> 'Nama hari',
			'open'	=> false
		];
		echo $this->generateTextForm($dayName, $set);

		$monthName = $db['monthName'];
		$set = [
			'id'	=> 'monthName',
			'title'	=> 'Nama bulan',
			'open'	=> false
		];
		echo $this->generateTextForm($monthName, $set);

		echo '</div></div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
	}

	private function simulasi(){
		ob_start();
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';
		?>
		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">Simulasi jadwal sholat</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class="row date-navigation month-picker" style="text-align:center">
					<button class="btn btn-info  prev"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Prev</button>
					<input style="width:120px" type="text" class="btn picker btn-info" value="Hari ini" readonly>
					<button class="btn btn-info next">Next <i class="fa fa-long-arrow-right" aria-hidden="true"></i></button>
				</div>
				<div class="table-responsive">
				</div>
			</div>
		</div>
		<?php
		echo '</div></div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
	}

	private function about(){
		ob_start();
		?>
		<section class="content-header content-dynamic">
			<div class="row">
				<div class="col-md-6">
				  <div class="box box-widget widget-user-2">
					<div class="widget-user-header bg-aqua-active">
					  <div class="widget-user-image">
						<div style="width:65px;height:65px;background:#563eae;position:absolute;border-radius:60px 35px 0 35px;font-size:38px;padding:15px 10px;box-shadow:3px 3px 10px 0 rgba(0,0,0,0.4);overflow:hidden;transform: rotate(-135deg); color:#00a7d0">
						dm
						</div>
					  </div>
					  <h3 class="widget-user-username">Display|Masjid</h3>
					  <h5 class="widget-user-desc">Media informasi untuk masjid/musholla</h5>
					</div>
					<div class="box-footer no-padding" style="overflow:hidden">
					  <ul class="nav nav-stacked">
						<li><a class="row"><div class="col-xs-5" style="text-align:right">Version</div><div class="col-xs-7"><span class="badge bg-blue">1.0.0</span></div></a></li>
						<li><a class="row"><div class="col-xs-5" style="text-align:right">Date</div><div class="col-xs-7"><span class="badge bg-aqua">Feb 2020</span></div></a></li>
						<li><a class="row"><div class="col-xs-5" style="text-align:right">Program</div><div class="col-xs-7">fahroni|ganteng</div></a></li>
						<li><a class="row"><div class="col-xs-5" style="text-align:right">Display design</div><div class="col-xs-7">Rakel</div></a></li>
						<li><a class="row"><div class="col-xs-5" style="text-align:right">License</div><div class="col-xs-7">Berbayar, sangat mahal sekali.... :P</div></a></li>
					  </ul>
					</div>
				  </div>
			</div>
		</div></section>
		<?php
		$this->data = ob_get_clean();
		$this->retSuccess();
	}

	private function sistem(){
		$temp = exec("/opt/vc/bin/vcgencmd measure_temp | egrep -o '[0-9]*\\.[0-9]*'");
		ob_start();
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
		';
		?>
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Update Jam</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="form-group">
						<label>Jam display</label>
						<div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-clock-o"></i>
						  </div>
						  <input type="text" class="form-control pull-right" value="<?=date('Y-m-d H:i:s')?>" disabled>
						</div>
					</div>
					<div class="form-group">
						<label>Jam lokal (HP/PC)</label>
						<div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-clock-o"></i>
						  </div>
						  <input type="text" class="form-control pull-right" id="jamLokal" disabled>
						</div>
					</div>
					<div class="form-group">
						<small>
							Update jam akan mengganti jam pada display menyesuaikan dengan jam pada HP/PC ini.
						</small>
					</div>
				</div>
				<div class="box-footer">
					<button type="button" class="btn btn-default js-refresh-active-menu"><i class="fa fa-clock-o" aria-hidden="true"></i> Refresh </button>
					<button type="button" class="btn btn-primary pull-right js-update-clock"><i class="fa fa-clock-o" aria-hidden="true"></i> Update </button>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="box box-default">
				<div class="box-header with-border">
					<h3 class="box-title">Device</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="form-group">
						<label>Temperature</label>
						<div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-thermometer"></i>
						  </div>
						  <input type="text" class="form-control pull-right" value="<?=$temp?> &#176;C" disabled>
						</div>
					</div>
					<div class="input">
						- Range temperature normal 0 - 70 &#176;C<br>
						- Alarm overheat > 80 &#176;C
					</div>
				</div>
				<div class="box-footer">
					<button type="button" class="btn btn-app js-refresh-active-menu"><i class="fa fa-thermometer"></i> Refresh </button>
					<button type="button" class="btn btn-app js-test-beep"><i class="fa fa-volume-up"></i> Test Beep</button>
					<button type="button" class="btn btn-app pull-right js-device-command" data-command="r"><i class="fa fa-repeat"></i> Restart</button>
					<button type="button" class="btn btn-app pull-right js-device-command" data-command="s"><i class="fa fa-power-off"></i> Shutdown</button>
				</div>
			</div>
		</div>		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Reset pengaturan awal</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					- Semua data setting akan di reset ke pengaturan awal.<br>
					- Logo dan wallpaper tidak berubah.<br>
					- Akses login kembali ke awal (user:admin, password:admin)<br>
					- Jika berhasil, akan masuk ke halaman login.
				</div>
				<div class="box-footer">
					<button type="button" class="btn btn-danger pull-right js-reset-device"><i class="fa fa-refresh" aria-hidden="true"></i> Reset </button>
				</div>
			</div>
		</div>
		<?php
		echo '</div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
	}
}

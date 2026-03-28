(function(window, $){
	$.extend(window.DisplayApp, {
		primeJadwal: function(){
			var app = this;
			var hariIni = app.tglHariIni ? moment(app.tglHariIni) : moment();
			var besok = app.tglBesok ? moment(app.tglBesok) : moment(hariIni).add(1, 'days');
			app.ensureJadwal(hariIni);
			app.ensureJadwal(besok);
			app.syncJadwalAktif();
		},

		getDateKey: function(jadwalDate){
			return moment(jadwalDate).format('YYYY-MM-DD');
		},

		getLocalJadwal: function(jadwalDate){
			var app = this;
			return prayTimes.getTimes(moment(jadwalDate).toDate(), [app.lat, app.lng], app.timeZone, app.dst, app.format);
		},

		setJadwalCache: function(dateKey, times, source){
			this.jadwalCache[dateKey] = {
				times: times,
				source: source
			};
		},

		ensureJadwal: function(jadwalDate){
			var app = this;
			var dateKey = app.getDateKey(jadwalDate);
			if(app.jadwalCache[dateKey]) return;
			app.setJadwalCache(dateKey, app.getLocalJadwal(jadwalDate), 'local');
			app.syncJadwalAktif();
		},

		syncJadwalAktif: function(){
			var app = this;
			var hariIniKey = app.getDateKey(app.tglHariIni || moment());
			var besokKey = app.getDateKey(app.tglBesok || moment().add(1, 'days'));
			if(app.jadwalCache[hariIniKey]){
				app.jadwalHariIni = app.jadwalCache[hariIniKey].times;
				app.fajr = moment(app.jadwalHariIni.fajr, 'HH:mm');
				app.dhuhr = moment(app.jadwalHariIni.dhuhr, 'HH:mm');
				app.asr = moment(app.jadwalHariIni.asr, 'HH:mm');
				app.maghrib = moment(app.jadwalHariIni.maghrib, 'HH:mm');
				app.isha = moment(app.jadwalHariIni.isha, 'HH:mm');
			}
			if(app.jadwalCache[besokKey]){
				app.jadwalBesok = app.jadwalCache[besokKey].times;
			}
		},

		isJadwalReady: function(){
			var app = this;
			return !!(app.jadwalHariIni.fajr && app.jadwalBesok.fajr);
		},

		showJadwal: function(){
			var app = this;
			var jamSekarang = moment();
			var jamDelay = moment().subtract(5, 'minutes');
			var jadwal = '';
			var hari = app.db.dayName[jamSekarang.format('dddd')];
			var bulan = app.db.monthName[jamSekarang.format('MMMM')];

			$('#jam').html(jamSekarang.format('HH.mm[<div>]ss[</div>]'));
			$('#tgl').html(jamSekarang.format('[' + hari + '], DD [' + bulan + '] YYYY'));
			if(!app.jadwalHariIni.fajr){
				$('#jadwal').html('<div class="row"><div class="col-xs-12">Memuat jadwal sholat...</div></div>');
				return;
			}

			if($('.full-screen').is(':visible')){
				$('#full-screen-clock').html(jamSekarang.format("[<i class='fa fa-clock-o''></i>&nbsp;&nbsp;]HH:mm"));
				$('#full-screen-clock').slideDown();
			}
			else {
				$('#full-screen-clock').slideUp();
			}

			var jadwalDipake = app.jadwalHariIni;
			var jadwalPlusIcon = '';
			if(jamDelay > app.isha){
				if(!app.jadwalBesok.fajr){
					$('#jadwal').html('<div class="row"><div class="col-xs-12">Memuat jadwal sholat...</div></div>');
					return;
				}
				jadwalDipake = app.jadwalBesok;
				jadwalPlusIcon = '<span><i class="fa fa-plus" aria-hidden="true"></i></span>';
			}

			$.each(app.db.prayName, function(k, v){
				var css = '';
				if(k == 'isha' && jamDelay < app.isha && jamDelay > app.maghrib) css = 'active';
				else if(k == 'maghrib' && jamDelay < app.maghrib && jamDelay > app.asr) css = 'active';
				else if(k == 'asr' && jamDelay < app.asr && jamDelay > app.dhuhr) css = 'active';
				else if(k == 'dhuhr' && jamDelay < app.dhuhr && jamDelay > app.fajr) css = 'active';
				else if(k == 'fajr' && (jamDelay < app.fajr || jamDelay > app.isha)) css = 'active';
				jadwal += '<div class="row ' + css + '"><div class="col-xs-5">' + v + '</div><div class="col-xs-7">' + jadwalDipake[k] + jadwalPlusIcon + '</div></div>';
			});
			$('#jadwal').html(jadwal);
		},

		displaySchedule: function(){
			var app = this;
			if(!app.jadwalHariIni.fajr) return;
			var waitAdzan = moment().add(app.db.timer.wait_adzan, 'minutes').format('YYYY-MM-DD HH:mm:ss');
			var jamSekarang = moment().format('YYYY-MM-DD HH:mm:ss');

			$.each(app.db.prayName, function(k, v){
				var t = moment(app[k]);
				var jadwal = t.format('YYYY-MM-DD HH:mm:ss');
				var stIqomah = t.add(app.db.timer.adzan, 'minutes').format('YYYY-MM-DD HH:mm:ss');
				var enIqomah = moment(stIqomah, 'YYYY-MM-DD HH:mm:ss').add(app.db.iqomah[k], 'minutes');

				if(waitAdzan == jadwal) app.runRightCountDown(app[k], 'Menuju ' + v);
				else if(jadwal == jamSekarang) app.showDisplayAdzan(v);
				else if(stIqomah == jamSekarang){
					if(moment().format('dddd') == 'Friday' && app.db.jumat.active && k == 'dhuhr'){
						app.showDisplayKhutbah();
					}
					else {
						app.runFullCountDown(enIqomah, 'IQOMAH', true);
					}
				}
			});
		},

		getNextPray: function(){
			var app = this;
			if(!app.isJadwalReady()) return false;
			var jamSekarang = moment();
			var nextPray = 'fajr';
			var jadwalDipake = false;
			if(jamSekarang > app.isha){
				jadwalDipake = moment(app.jadwalBesok[nextPray], 'HH:mm').add(1, 'Day');
			}
			else{
				$.each(app.db.prayName, function(k){
					if(jamSekarang < app[k]){
						nextPray = k;
						return false;
					}
				});
				jadwalDipake = moment(app.jadwalHariIni[nextPray], 'HH:mm');
			}
			return {
				pray: nextPray,
				date: jadwalDipake
			};
		}
	});
})(window, jQuery);

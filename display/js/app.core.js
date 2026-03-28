(function(window, $){
	var config = window.DISPLAY_CONFIG || {};

	if(config.prayTimesMethod === '0'){
		if(config.prayTimesAdjust){
			prayTimes.adjust(config.prayTimesAdjust);
		}
	}
	else if(config.prayTimesMethod){
		prayTimes.setMethod(config.prayTimesMethod);
	}

	if(config.prayTimesTune){
		prayTimes.tune(config.prayTimesTune);
	}

	window.DisplayApp = {
		config: config,
		format: '24h',
		lat: config.lat,
		lng: config.lng,
		timeZone: config.timeZone,
		dst: config.dst,
		db: config.db || {},
		cekDb: false,
		tglHariIni: '',
		tglBesok: '',
		jadwalHariIni: {},
		jadwalBesok: {},
		jadwalCache: {},
		timer: false,
		adzanDisplayTimer: false,
		activeCountdownTimer: false,
		fullscreenMessageTimer: false,
		nextPrayCountdownTicks: 0,
		pendingReload: false,
		youtubeReady: false,
		youtubeEmbedUrl: '',
		pptReady: false,
		pptEmbedUrl: '',
		fajr: '',
		dhuhr: '',
		asr: '',
		maghrib: '',
		isha: '',
		audio: new Audio('img/beep.mp3'),

		initialize: function(){
			var app = this;
			app.initRunningText();
			app.setupYoutube();
			app.setupPpt();
			app.primeJadwal();
			document.addEventListener('fullscreenchange', app.handleFullscreenChange);
			document.addEventListener('webkitfullscreenchange', app.handleFullscreenChange);
			app.timer = setInterval(function(){ app.cekPerDetik(); }, 5000);
			$('#preloader').delay(350).fadeOut('slow');
		},

		cekPerDetik: function(){
			var app = this;
			if(!app.tglHariIni || moment().format('YYYY-MM-DD') != moment(app.tglHariIni).format('YYYY-MM-DD')){
				app.tglHariIni = moment();
				app.tglBesok = moment().add(1, 'days');
				app.primeJadwal();
			}
			app.syncJadwalAktif();
			app.showJadwal();
			app.displaySchedule();

			$.ajax({
				type: 'POST',
				url: '../proses.php',
				dataType: 'json',
				data: {id: 'changeDbCheck'}
			}).done(function(dt){
				if(app.cekDb === false) app.cekDb = dt.data;
				else if(app.cekDb !== dt.data){
					app.cekDb = dt.data;
					if(app.isBrowserFullscreen()) app.pendingReload = true;
					else location.reload();
				}
			}).fail(function(){
				return false;
			});
		},

		isBrowserFullscreen: function(){
			return !!(document.fullscreenElement || document.webkitFullscreenElement);
		},

		handleFullscreenChange: function(){
			var app = window.DisplayApp;
			if(!app.isBrowserFullscreen() && app.pendingReload){
				app.pendingReload = false;
				location.reload();
			}
		},

		isEnabled: function(value){
			return value === true || value === 1 || value === '1';
		}
	};
})(window, jQuery);

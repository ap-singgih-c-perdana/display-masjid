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
		uiTimer: false,
		dbCheckTimer: false,
		adzanDisplayTimer: false,
		activeCountdownTimer: false,
		fullscreenMessageTimer: false,
		nextPrayCountdownTicks: 0,
		countdownBeepMarks: {},
		activeBeepTimeout: false,
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
		audioUnlocked: false,
		debugTime: null,

		initialize: function(){
			var app = this;
			app.initDebugTime();
			app.initAudioUnlock();
			app.initRunningText();
			app.setupYoutube();
			app.setupPpt();
			app.primeJadwal();
			document.addEventListener('fullscreenchange', app.handleFullscreenChange);
			document.addEventListener('webkitfullscreenchange', app.handleFullscreenChange);
			app.updateUi();
			app.checkDatabaseChanges();
			app.uiTimer = setInterval(function(){ app.updateUi(); }, 1000);
			app.dbCheckTimer = setInterval(function(){ app.checkDatabaseChanges(); }, 5000);
			$('#preloader').delay(350).fadeOut('slow');
		},

		initAudioUnlock: function(){
			var app = this;
			if(window.sessionStorage.getItem('displayMasjidAudioUnlocked') === '1'){
				app.audioUnlocked = true;
			}
			$('#enable-audio').hide();
			$(document).one('pointerdown mousedown touchstart click keydown', function(){
				if(!app.audioUnlocked){
					app.unlockAudio();
				}
			});
		},

		unlockAudio: function(){
			var app = this;
			app.audio.pause();
			app.audio.currentTime = 0;
			app.audio.muted = false;
			app.audio.volume = 1;
			app.audio.play().then(function(){
				app.audioUnlocked = true;
				window.sessionStorage.setItem('displayMasjidAudioUnlocked', '1');
				$('#enable-audio').fadeOut();
				setTimeout(function(){
					app.audio.pause();
					app.audio.currentTime = 0;
				}, 120);
			}).catch(function(){
				if(!app.audioUnlocked){
					app.audioUnlocked = false;
					app.showAudioUnlockPrompt();
				}
			});
		},

		showAudioUnlockPrompt: function(){
			return false;
		},

		initDebugTime: function(){
			var app = this;
			var params = new URLSearchParams(window.location.search);
			var debugTime = params.get('debug_time');
			if(debugTime){
				var parsed = moment(debugTime, ['YYYY-MM-DD HH:mm:ss', 'YYYY-MM-DDTHH:mm:ss', 'YYYY-MM-DD HH:mm', 'YYYY-MM-DDTHH:mm'], true);
				if(parsed.isValid()){
					app.debugTime = parsed;
				}
			}
		},

		now: function(){
			var app = this;
			if(!app.debugTime) return moment();
			var current = app.debugTime.clone();
			app.debugTime.add(1, 'seconds');
			return current;
		},

		updateUi: function(){
			var app = this;
			var now = app.now();
			if(!app.tglHariIni || now.format('YYYY-MM-DD') != moment(app.tglHariIni).format('YYYY-MM-DD')){
				app.tglHariIni = now.clone();
				app.tglBesok = now.clone().add(1, 'days');
				app.primeJadwal();
			}
			app.syncJadwalAktif();
			app.showJadwal(now.clone());
			app.displaySchedule(now.clone());
		},

		checkDatabaseChanges: function(){
			var app = this;
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

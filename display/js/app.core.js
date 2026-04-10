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
		sunrise: '',
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
			app.setupQuoteAutoFit();
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
		},

		getDisplayPrayKeys: function(){
			return Object.keys(this.db.prayName || {});
		},

		getMainPrayKeys: function(){
			return ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
		},

		getCountdownPrayKeys: function(){
			return ['fajr', 'sunrise', 'dhuhr', 'asr', 'maghrib', 'isha'];
		},

		setupQuoteAutoFit: function(){
			var app = this;
			var debouncedFit = app.debounce(function(){
				app.fitQuoteSlides();
			}, 120);
			app.fitQuoteSlides();
			$(window).on('resize', debouncedFit);
			$('.quote-carousel').on('slid.bs.carousel', function(){
				app.fitQuoteSlides();
			});
			$(window).on('load', function(){
				app.fitQuoteSlides();
			});
		},

		fitQuoteSlides: function(){
			var app = this;
			$('.quote-carousel .hero').each(function(){
				app.fitQuoteSlide($(this));
			});
		},

		fitQuoteSlide: function($hero){
			var app = this;
			var availableHeight;
			var scale;
			var minScale = 0.6;
			var step = 0.04;

			if(!$hero || !$hero.length){
				return;
			}

			app.resetQuoteSlideStyles($hero);
			availableHeight = app.getQuoteAvailableHeight($hero);
			if(availableHeight <= 0){
				return;
			}

			scale = 1;
			while($hero.outerHeight() > availableHeight && scale > minScale){
				scale = Math.max(minScale, scale - step);
				app.applyQuoteScale($hero, scale);
			}
		},

		resetQuoteSlideStyles: function($hero){
			$hero.css({
				'--quote-text1-size': '',
				'--quote-text2-size': '',
				'--quote-text3-size': '',
				'--quote-text1-gap': '',
				'--quote-text3-gap': '',
				'--quote-text3-line-offset': ''
			});
		},

		applyQuoteScale: function($hero, scale){
			$hero.css({
				'--quote-text1-size': (4.2 * scale) + 'vw',
				'--quote-text2-size': (4 * scale) + 'vw',
				'--quote-text3-size': (2.2 * scale) + 'vw',
				'--quote-text1-gap': (2.2 * scale) + 'vh',
				'--quote-text3-gap': (4 * scale) + 'vh',
				'--quote-text3-line-offset': (-1 * scale) + 'vw'
			});
		},

		getQuoteAvailableHeight: function($hero){
			var runningTextTop = $('#running-text').offset() ? $('#running-text').offset().top : window.innerHeight;
			var logoTop = $('#logo').offset() ? $('#logo').offset().top : window.innerHeight;
			var heroTop = $hero.offset() ? $hero.offset().top : 0;
			var bottomLimit = Math.min(runningTextTop, logoTop) - 20;

			return Math.max(0, bottomLimit - heroTop);
		},

		debounce: function(fn, wait){
			var timeoutId = null;
			return function(){
				var context = this;
				var args = arguments;
				clearTimeout(timeoutId);
				timeoutId = setTimeout(function(){
					fn.apply(context, args);
				}, wait);
			};
		}
	};
})(window, jQuery);

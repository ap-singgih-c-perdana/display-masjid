(function(window, $){
	$.extend(window.DisplayApp, {
		setupSynchronizedCarousels: function(){
			var app = this;
			app.carouselSyncConfigs = [
				{
					name: 'wallpaper',
					selector: '.fade-carousel',
					intervalMs: parseInt(app.db.timer.wallpaper, 10) * 1000
				},
				{
					name: 'quote',
					selector: '.quote-carousel',
					intervalMs: parseInt(app.db.timer.info, 10) * 1000
				}
			];
			$.each(app.carouselSyncConfigs, function(_, syncConfig){
				app.setupCarouselSync(syncConfig);
			});
		},

		setupCarouselSync: function(syncConfig){
			var app = this;
			syncConfig.$element = $(syncConfig.selector);
			syncConfig.syncTimer = false;
			if(!syncConfig.$element.length){
				return;
			}
			syncConfig.$element.carousel({
				interval: false,
				pause: false,
				wrap: true,
				keyboard: false
			});
			syncConfig.$element.carousel('pause');
			app.syncCarousel(syncConfig, true);
		},

		getSynchronizedCarouselIndex: function(intervalMs, itemCount){
			if(!intervalMs || !itemCount){
				return 0;
			}
			return Math.floor(this.getServerNowMs() / intervalMs) % itemCount;
		},

		syncCarousel: function(syncConfig, force){
			var app = this;
			var $items;
			var expectedIndex;
			var currentIndex;
			if(!syncConfig || !syncConfig.$element || !syncConfig.$element.length){
				return;
			}
			$items = syncConfig.$element.find('.carousel-inner > .item');
			if(!$items.length){
				return;
			}
			expectedIndex = app.getSynchronizedCarouselIndex(syncConfig.intervalMs, $items.length);
			currentIndex = $items.filter('.active').first().index();
			if(currentIndex < 0){
				currentIndex = 0;
			}
			if(force || currentIndex !== expectedIndex){
				if(
					!force &&
					currentIndex === ($items.length - 1) &&
					expectedIndex === 0
				){
					syncConfig.$element.carousel('next');
				}
				else{
					syncConfig.$element.carousel(expectedIndex);
				}
			}
			app.scheduleCarouselSync(syncConfig);
		},

		scheduleCarouselSync: function(syncConfig){
			var app = this;
			var delay;
			if(syncConfig.syncTimer){
				clearTimeout(syncConfig.syncTimer);
				syncConfig.syncTimer = false;
			}
			if(!syncConfig.intervalMs){
				return;
			}
			delay = syncConfig.intervalMs - (app.getServerNowMs() % syncConfig.intervalMs);
			if(delay < 120){
				delay += syncConfig.intervalMs;
			}
			syncConfig.syncTimer = setTimeout(function(){
				app.syncCarousel(syncConfig, false);
			}, delay + 80);
		},

		syncAllCarousels: function(force){
			var app = this;
			$.each(app.carouselSyncConfigs || [], function(_, syncConfig){
				app.syncCarousel(syncConfig, !!force);
			});
		},

		setInfoFullscreenMode: function(active){
			$('body').toggleClass('info-fullscreen-mode', !!active);
		},

		getYoutubeVideoId: function(url){
			if(!url) return '';
			var match = url.match(/(?:youtube\.com\/watch\?v=|youtube\.com\/embed\/|youtu\.be\/)([^&?/]+)/i);
			return match ? match[1] : '';
		},

		buildYoutubeEmbedUrl: function(){
			var app = this;
			var youtube = app.db.youtube || {};
			var videoId = app.getYoutubeVideoId(youtube.url || '');
			if(!videoId) return '';
			var mute = app.isEnabled(youtube.mute) ? 1 : 0;
			return 'https://www.youtube-nocookie.com/embed/' + videoId + '?autoplay=1&mute=' + mute + '&controls=0&rel=0&modestbranding=1&loop=1&playlist=' + videoId;
		},

		setupYoutube: function(){
			var app = this;
			app.youtubeEmbedUrl = app.buildYoutubeEmbedUrl();
			app.youtubeReady = !!app.youtubeEmbedUrl;
			app.updateContentVisibility();
		},

		setupPpt: function(){
			var app = this;
			var ppt = app.db.ppt || {};
			app.pptEmbedUrl = $.trim(ppt.url || '');
			if(app.pptEmbedUrl){
				$('#ppt-player').attr('src', app.pptEmbedUrl);
				app.pptReady = true;
			}
			app.updateContentVisibility();
		},

		updateContentVisibility: function(){
			var app = this;
			var youtube = app.db.youtube || {};
			var showYoutube = app.isEnabled(youtube.active) && app.youtubeReady;
			var ppt = app.db.ppt || {};
			var showPpt = !showYoutube && app.isEnabled(ppt.active) && app.pptReady;
			var infoDisplay = app.db.infoDisplay || {};
			var showQuote = !showYoutube && !showPpt;
			var showQuoteFullscreen = showQuote && app.isEnabled(infoDisplay.fullscreen);
			var $youtubePlayer = $('#youtube-player');
			var $pptPlayer = $('#ppt-player');

			if(showYoutube){
				if($youtubePlayer.attr('src') !== app.youtubeEmbedUrl){
					$youtubePlayer.attr('src', app.youtubeEmbedUrl);
				}
			}
			else if($youtubePlayer.attr('src')){
				$youtubePlayer.attr('src', '');
			}

			if(showPpt){
				if($pptPlayer.attr('src') !== app.pptEmbedUrl){
					$pptPlayer.attr('src', app.pptEmbedUrl);
				}
			}
			else if($pptPlayer.attr('src')){
				$pptPlayer.attr('src', '');
			}

			$('#youtube-container').toggle(showYoutube);
			$('#ppt-container').toggle(showPpt);
			$('#quote').toggle(showQuote);
			app.setInfoFullscreenMode(showQuoteFullscreen);
			app.syncAllCarousels(true);
			if(typeof app.syncQuoteDisplayMode === 'function'){
				app.syncQuoteDisplayMode();
			}
		}
	});
})(window, jQuery);

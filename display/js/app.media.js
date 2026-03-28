(function(window, $){
	$.extend(window.DisplayApp, {
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
			$('#quote').toggle(!showYoutube && !showPpt);
		}
	});
})(window, jQuery);

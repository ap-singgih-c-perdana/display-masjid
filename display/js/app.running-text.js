(function(window, $){
	$.extend(window.DisplayApp, {
		initRunningText: function(){
			var app = this;
			var $runningText = $('#running-text .item');
			var html;
			if(!$runningText.length) return;
			html = $.trim($runningText.html());
			if(!html) return;

			$runningText.empty().addClass('running-text-ready');
			$runningText.append('<div class="running-text-track"><div class="running-text-copy">' + html + '</div><div class="running-text-copy" aria-hidden="true">' + html + '</div></div>');
			app.runningTextState = {
				$container: $('#running-text'),
				$item: $runningText,
				$track: $runningText.find('.running-text-track'),
				$copies: $runningText.find('.running-text-copy'),
				gap: 120,
				speedPxPerSecond: 100,
				cycleWidth: 0,
				containerWidth: 0,
				frameHandle: false
			};
			app.measureRunningText();
			app.startRunningTextLoop();
			$(window).on('resize', app.debounce(function(){
				app.measureRunningText();
			}, 120));
			if(document.fonts && document.fonts.ready){
				document.fonts.ready.then(function(){
					app.measureRunningText();
				});
			}
		},

		measureRunningText: function(){
			var app = this;
			var state = app.runningTextState;
			var copyWidth;
			if(!state) return;
			state.containerWidth = state.$item.innerWidth();
			state.gap = Math.max(80, Math.round(state.containerWidth * 0.08));
			copyWidth = Math.ceil(state.$copies.first().outerWidth(true));
			state.cycleWidth = copyWidth + state.gap;
			state.$track.css('gap', state.gap + 'px');
			state.$copies.css('padding-right', '0');
			app.syncRunningTextPosition();
		},

		startRunningTextLoop: function(){
			var app = this;
			var state = app.runningTextState;
			if(!state || state.frameHandle){
				return;
			}
			var updateFrame = function(){
				app.syncRunningTextPosition();
				state.frameHandle = window.requestAnimationFrame(updateFrame);
			};
			state.frameHandle = window.requestAnimationFrame(updateFrame);
		},

		syncRunningTextPosition: function(){
			var state = this.runningTextState;
			var cycleDurationMs;
			var progress;
			var offsetX;
			if(!state || !state.cycleWidth){
				return;
			}
			cycleDurationMs = (state.cycleWidth / state.speedPxPerSecond) * 1000;
			progress = (this.getServerNowMs() % cycleDurationMs) / cycleDurationMs;
			offsetX = state.containerWidth - (progress * state.cycleWidth);
			state.$track.css('transform', 'translate3d(' + offsetX.toFixed(2) + 'px, 0, 0)');
		}
	});
})(window, jQuery);

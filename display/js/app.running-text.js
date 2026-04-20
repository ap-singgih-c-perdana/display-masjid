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
				$item: $runningText,
				$track: $runningText.find('.running-text-track'),
				$copies: $runningText.find('.running-text-copy'),
				gap: 120,
				speedPxPerSecond: 100,
				cycleWidth: 0,
				containerWidth: 0,
				lastFrameAt: 0,
				offsetX: 0,
				frameHandle: false,
				resizeHandler: false
			};
			app.measureRunningText();
			app.startRunningTextLoop();
			app.runningTextState.resizeHandler = app.debounce(function(){
				app.measureRunningText();
			}, 120);
			$(window).on('resize', app.runningTextState.resizeHandler);
			if(document.fonts && document.fonts.ready){
				document.fonts.ready.then(function(){
					app.measureRunningText();
				});
			}
		},

		measureRunningText: function(){
			var state = this.runningTextState;
			var copyWidth;
			if(!state) return;
			state.containerWidth = state.$item.innerWidth();
			state.gap = Math.max(80, Math.round(state.containerWidth * 0.08));
			state.$track.css('gap', state.gap + 'px');
			copyWidth = Math.ceil(state.$copies.first().outerWidth(true));
			state.cycleWidth = copyWidth + state.gap;
			if(!state.cycleWidth){
				return;
			}
			state.offsetX = state.containerWidth;
			state.lastFrameAt = 0;
			state.$track.css('transform', 'translate3d(' + state.offsetX.toFixed(2) + 'px, 0, 0)');
		},

		startRunningTextLoop: function(){
			var app = this;
			var state = app.runningTextState;
			if(!state || state.frameHandle){
				return;
			}
			var updateFrame = function(timestamp){
				app.updateRunningTextFrame(timestamp);
				state.frameHandle = window.requestAnimationFrame(updateFrame);
			};
			state.frameHandle = window.requestAnimationFrame(updateFrame);
		},

		updateRunningTextFrame: function(timestamp){
			var state = this.runningTextState;
			var elapsedSeconds;
			if(!state || !state.cycleWidth || document.hidden){
				if(state){
					state.lastFrameAt = 0;
				}
				return;
			}
			if(!state.lastFrameAt){
				state.lastFrameAt = timestamp;
				return;
			}
			elapsedSeconds = Math.min((timestamp - state.lastFrameAt) / 1000, 0.033);
			state.lastFrameAt = timestamp;
			state.offsetX -= state.speedPxPerSecond * elapsedSeconds;
			if(state.offsetX <= (state.containerWidth - state.cycleWidth)){
				state.offsetX += state.cycleWidth;
			}
			state.$track.css('transform', 'translate3d(' + state.offsetX.toFixed(2) + 'px, 0, 0)');
		},

		syncRunningTextPosition: function(){
			var state = this.runningTextState;
			if(!state){
				return;
			}
			state.lastFrameAt = 0;
		}
	});
})(window, jQuery);

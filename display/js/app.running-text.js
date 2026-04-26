(function(window, $){
	$.extend(window.DisplayApp, {
		initRunningText: function(){
			var $runningText = $('#running-text .item');
			if(!$runningText.length) return;
			var speed = parseInt(this.db.running_text_speed, 10);
			if(isNaN(speed)) speed = 50;
			speed = Math.max(10, Math.min(speed, 300));
			$runningText.marquee({
				// Use a fixed pixel speed so browser-specific layout differences
				// do not noticeably change the marquee pace.
				speed: speed,
				delayBeforeStart: 0,
				gap: 80,
				direction: 'left',
				duplicated: true,
				startVisible: true,
				pauseOnHover: false
			});
		}
	});
})(window, jQuery);

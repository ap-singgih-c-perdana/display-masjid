(function(window, $){
	$.extend(window.DisplayApp, {
		initRunningText: function(){
			var $runningText = $('#running-text .item');
			if(!$runningText.length) return;
			$runningText.marquee({
				duration: 15000,
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

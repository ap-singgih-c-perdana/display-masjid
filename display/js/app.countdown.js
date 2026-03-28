(function(window, $){
	$.extend(window.DisplayApp, {
		showCountDownNextPray: function(){
			var app = this;
			var nextPray = app.getNextPray();
			if(!nextPray) return;
			if(app.activeCountdownTimer) return;
			app.nextPrayCountdownTicks = 0;
			app.activeCountdownTimer = setInterval(function(){
				var t = app.countDownCalculate(nextPray.date);

				$('#right-counter .counter>h1').html('Menuju ' + app.db.prayName[nextPray.pray]);
				$('#right-counter .counter>.hh').html(t.hours + '<span>' + app.db.timeName.Hours + '</span>');
				$('#right-counter .counter>.ii').html(t.minutes + '<span>' + app.db.timeName.Minutes + '</span>');
				$('#right-counter .counter>.ss').html(t.seconds + '<span>' + app.db.timeName.Seconds + '</span>');

				$('#right-counter').slideDown();
				$('#quote, #youtube-container, #ppt-container').hide();

				app.nextPrayCountdownTicks++;
				if(app.nextPrayCountdownTicks >= 30){
					clearInterval(app.activeCountdownTimer);
					app.activeCountdownTimer = false;
					$('#right-counter').fadeOut();
					app.updateContentVisibility();
				}
			}, 1000);
		},

		showDisplayAdzan: function(prayName){
			var app = this;
			if(!app.adzanDisplayTimer){
				$('#display-adzan>div').text(prayName);
				$('#display-adzan').show();
				app.adzanDisplayTimer = setTimeout(function(){
					$('#display-adzan').fadeOut();
					app.adzanDisplayTimer = false;
				}, (app.db.timer.adzan * 60 * 1000) + 1500);
			}
		},

		showDisplayKhutbah: function(){
			var app = this;
			if(!app.fullscreenMessageTimer){
				$('#display-khutbah>div').text(app.db.jumat.text);
				$('#display-khutbah').show();
				app.fullscreenMessageTimer = setTimeout(function(){
					app.fullscreenMessageTimer = false;
					app.showDisplaySholat();
					$('#display-khutbah').fadeOut();
				}, app.db.jumat.duration * 60 * 1000);
			}
		},

		showDisplaySholat: function(){
			var app = this;
			if(!app.fullscreenMessageTimer){
				var jamSekarang = moment();
				var duration = (jamSekarang > app.isha && app.db.tarawih.active) ? app.db.tarawih.duration : app.db.timer.sholat;
				$('#display-sholat').show();
				app.fullscreenMessageTimer = setTimeout(function(){
					$('#display-sholat').fadeOut();
					app.fullscreenMessageTimer = false;
					app.showCountDownNextPray();
				}, duration * 60 * 1000);
			}
		},

		runFullCountDown: function(jam, title, runDisplaySholat){
			var app = this;
			if(app.activeCountdownTimer) return;
			app.activeCountdownTimer = setInterval(function(){
				var t = app.countDownCalculate(jam);

				$('#count-down .counter>h1').html(title);
				$('#count-down .counter>.hh').html(t.hours + '<span>' + app.db.timeName.Hours + '</span>');
				$('#count-down .counter>.ii').html(t.minutes + '<span>' + app.db.timeName.Minutes + '</span>');
				$('#count-down .counter>.ss').html(t.seconds + '<span>' + app.db.timeName.Seconds + '</span>');

				$('#count-down').fadeIn();
				if(t.distance == 5){
					app.audio.play().then(function(){
						return true;
					}).catch(function(){
						return false;
					});
				}
				if(t.distance < 1){
					clearInterval(app.activeCountdownTimer);
					app.activeCountdownTimer = false;
					$('#count-down').fadeOut();
					if(runDisplaySholat){
						app.showDisplaySholat();
					}
				}
			}, 1000);
		},

		runRightCountDown: function(jam, title){
			var app = this;
			if(app.activeCountdownTimer) return;
			app.activeCountdownTimer = setInterval(function(){
				var t = app.countDownCalculate(jam);

				$('#right-counter .counter>h1').html(title);
				$('#right-counter .counter>.hh').html(t.hours + '<span>' + app.db.timeName.Hours + '</span>');
				$('#right-counter .counter>.ii').html(t.minutes + '<span>' + app.db.timeName.Minutes + '</span>');
				$('#right-counter .counter>.ss').html(t.seconds + '<span>' + app.db.timeName.Seconds + '</span>');

				$('#right-counter').slideDown();
				$('#quote, #youtube-container, #ppt-container').hide();

				if(t.distance < 1){
					clearInterval(app.activeCountdownTimer);
					app.activeCountdownTimer = false;
					$('#right-counter').fadeOut();
					app.updateContentVisibility();
				}
			}, 1000);
		},

		countDownCalculate: function(jam){
			var jamSekarang = moment();
			var distance = Math.round(jam.diff(jamSekarang, 'seconds', true));
			var hours = Math.floor((distance % (60 * 60 * 24)) / (60 * 60));
			var minutes = Math.floor((distance % (60 * 60)) / 60);
			var seconds = Math.floor(distance % 60);
			hours = (hours >= 0 && hours < 10) ? '0' + hours : hours;
			minutes = (minutes >= 0 && minutes < 10) ? '0' + minutes : minutes;
			seconds = (seconds >= 0 && seconds < 10) ? '0' + seconds : seconds;
			return {
				distance: distance,
				hours: hours,
				minutes: minutes,
				seconds: seconds
			};
		}
	});
})(window, jQuery);

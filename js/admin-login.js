$(function(){
	var adminConfig = window.ADMIN_CONFIG || {};
	var processUrl = adminConfig.processUrl || "proses.php";

	$(document).on('submit', 'form.js-login-form', function(event){
		event.preventDefault();

		var $form = $(this);
		var $btn = $form.find('button.btn-primary');
		var btnText = $btn.html();
		var arr = {};
		var redirectTarget = $form.data('redirect') || window.location.href;

		$btn.html('<i class="fa fa-spinner fa-pulse"></i> loading...').attr('disabled', 'disabled');

		$.each($form.serializeArray(), function(k, v){
			arr[v.name] = v.value;
		});

		$.ajax({
			type: "POST",
			url: processUrl,
			dataType: "json",
			data: {id:'login', dt:arr}
		}).done(function(dt){
			if(dt.registered){
				location.href = redirectTarget;
			}
			else{
				alert(dt.data);
				$form.find('input[name=pass]').val('').focus();
				$btn.html(btnText).removeAttr('disabled');
			}
		}).fail(function(msg){
			alert(msg.status + "\n" + msg.statusText);
			$btn.html(btnText).removeAttr('disabled');
		});
	});
});

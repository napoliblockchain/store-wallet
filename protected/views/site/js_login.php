<?php
$check2faURL = Yii::app()->createUrl('site/check2fa'); // verifica esistenza 2fa da richiedere

$login = <<<JS
	var accediButton = document.querySelector('#accedi-button');
	var twofaButton = document.querySelector('#Conferma2fa');

	function check2fa(){
		event.preventDefault();

		username = $("#LoginForm_username");
		oauth_provider = $("#LoginForm_oauth_provider");
		$.ajax({
			url:'{$check2faURL}',
			type: "POST",
			data:{
				'username': username.val(),
				'oauth_provider': oauth_provider.val(),
			},
			dataType: "json",
			success:function(data){
				console.log('Fetching 2fa',data);
				if (data.response===true){
					$('#2faModal').modal({
						backdrop: 'static',
						keyboard: false
					});
				}else{
					confirmForm();
				}
			},
			error: function(j){
				console.log(j);
			}
		});
		console.log(username.val());
	}

	function confirmForm(){
		// chiede di installare la webapp sul desktop
		if (deferredPrompt) {
			deferredPrompt.prompt();

			deferredPrompt.userChoice.then(function(choiceResult) {
				console.log(choiceResult.outcome);
				if (choiceResult.outcome === 'dismissed') {
					console.log('User cancelled installation');
				} else {
					console.log('User added to home screen');
				}
			});
			deferredPrompt = null;
		}
		$('#login-form').submit();
	}


	accediButton.addEventListener('click', check2fa);
	twofaButton.addEventListener('click', confirmForm);

JS;
Yii::app()->clientScript->registerScript('login', $login, CClientScript::POS_END);

?>

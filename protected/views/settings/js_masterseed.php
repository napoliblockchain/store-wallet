<?php
$decryptURL = Yii::app()->createUrl('wallet/decrypt');// crypta da js

$masterSeed = <<<JS

	const masterSeedButton = document.querySelector('#showMasterSeed');
	masterSeedButton.addEventListener('click', function() {
		$('#masterSeed').html('<center><img width=15 src="'+ajax_loader_url+'"></center>');

			readAllData('mseed')
			.then(function(data) {
				console.log('[Master Seed IndexedDB]',data);
				if (typeof data[0] !== 'undefined') {
					$.ajax({
						url:'{$decryptURL}',
						type: "POST",
						data: {'cryptedseed': data[0].cryptedseed},
						dataType: "json",
						success:function(data){
							$('#masterSeed').html(data.decryptedseed);
						},
						error: function(j){
							console.log('error',j);
						}
					});

				}else{
					$('#masterSeed').text('Backup not found!');
				}
			})
	});

JS;

Yii::app()->clientScript->registerScript('masterSeed', $masterSeed);
?>

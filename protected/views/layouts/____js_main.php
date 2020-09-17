<?php
$urlBlocknumber = Yii::app()->createUrl('blockchain/blocknumber'); //sync blockchain
$urlBlockchain = Yii::app()->createUrl('blockchain/index'); //sync blockchain
$urlBlockchainFastscan = Yii::app()->createUrl('blockchain/fastscan'); //sync blockchain

$myBlockchain = <<<JS
    //dichiarazione globale
    var erc20;
    var blockchain;
    var ajax_loader_url = 'css/images/loading.gif';
	var paginaweb;
    var isPinRequest = false;

    /*
    * verifico la presenza del pin e lo richiedo
    */
    function checkForPIN(){
        readAllData('pin')
            .then(function(pin) {
                if (typeof pin[0] !== 'undefined') {
                    if (null !== pin[0].id && pin[0].stop != 0){
                        var checktime = {
                            adesso : new Date().getTime() /1000 | 0,
                            scadenza : pin[0].id + (pin[0].stop * 60),
                        };
                        var differenza = checktime.scadenza - checktime.adesso;
                        //var differenza = 0;
                        console.log('checktime,differenza, isPinRequest',checktime,differenza,isPinRequest);

                        if (differenza <= 0 && isPinRequest==false){
                            isPinRequest = true;
                            askPin(pin[0].pin);
                        }else{
                            if (!isPinRequest){
                                isPinRequest = updatePinTimestamp();
                            }
                        }
                    }else{
                        console.log('Pin impostato a 0');
                    }
                }else{
                    console.log('Nessun pin impostato!');
                }
            });
    }




	/*
	* SWIPE my functions
	*/
    // var drag = document.querySelector('.header-mobile'); //form || header-mobile ???
    // var start_swipe = null;
    // var end_swipe = null;
    // var offset_swipe = 100;//at least 100px are a swipe
    //
	// $(drag).draggable({
	// 	axis: "x",
    //     start: function(event, ui) {
    //        $(this).css({
    //            "opacity": 0.5,
    //            "filter": "alpha(opacity=50)"
    //        });
    //     },
    //     stop: function(event, ui) {
    //        $(this).css({
    //            "opacity": 1,
    //            "filter": "alpha(opacity=100)"
    //        });
    //        $(this).animate({ top: 0, left: 0 }, 500);
    //     }
	// });
    //
	// drag.addEventListener("touchstart",function(event){
	// 	if(event.touches.length === 1){
	// 	  //just one finger touched
	// 	  start_swipe = event.touches.item(0).clientX;
	// 	}else{
	// 	  //a second finger hit the screen, abort the touch
	// 	  start_swipe = null;
	// 	}
	// });
    //
	// drag.addEventListener("touchend",function(event){
	//     if(start_swipe){
	// 		//the only finger that hit the screen left it
	// 		var end_swipe = event.changedTouches.item(0).clientX;
    //
	// 		if (paginaweb == 'wallet'){
	// 			if(end_swipe < start_swipe - offset_swipe ){
	// 				$("#wallet-form").hide(500);
	// 				//a left -> right swipe
	// 				window.location.href = 'index.php?r=wallet/history';
	// 			}
	// 		}
    //         if (paginaweb == 'history'){
	// 			if(end_swipe > start_swipe + offset_swipe){
	// 				//a right -> left swipe
	// 				$("#history-form").hide(500);
	// 				window.location.href = 'index.php?r=wallet/index';
	// 			}
	// 		}
	//     }
    // });


    blockchain = {
		sync: function(wallet_address){
            //console.log('adesso sono in sync blockchain...',wallet_address);
			// if ('serviceWorker' in navigator && 'SyncManager' in window){
			// 	navigator.serviceWorker.ready
			// 		.then(function(sw) {
			// 			//$('.sync-blockchain').hide();
			// 			//$('.sync-blockchain').html('<div class="blockchain-pairing__loading"><center><img width=15 src="'+ajax_loader_url+'" alt="loading..."></center></div>');
            //             //console.log('ora sono prima di ajax sync blockchain...',wallet_address);
			// 			$.ajax({
			// 				url:'{$urlBlocknumber}',
			// 				type: "POST",
			// 				data: {'wallet_address': wallet_address},
			// 				dataType: "json",
			// 				success:function(data){
			// 					var post = {
			// 						id: new Date().toISOString(), // id of indexedDB
			// 						diff: data.diff, //url of getBalance
			// 						wallet_address:  wallet_address,
			// 						latest_block: data.chainBlocknumber,
			// 						url: '{$urlBlockchain}', //url of getBalance
			// 					};
			// 					//console.log('Ricerca differenza blocchi:',post);
            //
			// 					writeData('sync-blockchain', post)
			// 						.then(function() {
			// 							//console.log('Save blockchain response in indexedDB', data);
            //                             if (data.diff >100){
            //                                 $('.sync-blockchain').html('<div class="blockchain-pairing__loading"><center><img width=15 src="'+ajax_loader_url+'" alt="loading..."></center></div>');
            //                                 $('.sync-difference').html('<small>'+data.diff+' blocks are required to complete the synchronization.</small>');
            //                             }else{
            //                                 $('.sync-difference').html('');
            //                                 $('.blockchain-pairing__loading').remove();
            //                             }
            //
			// 							return sw.sync.register('sync-blockchain');
			// 						})
			// 						.then(function() {
			// 							//('.blockchain-pairing__loading').html('');
			// 						 	return blockchain.isReadyReceived(wallet_address);
			// 						})
			// 						.catch(function(err) {
			// 							console.log(err);
			// 						});
            //
			// 				},
			// 				error: function(j){
			// 					console.log(j);
			// 				}
			// 			});
			// 		});
			// }else{

                // prima di iniziare qualunque ricecra verifico la consistenza del pin
                checkForPIN();


                //console.log('ora sono in sync blockchain ma senza sw...',wallet_address);
                //se non funziona il sw parte la ricerca via ajax standard
                $.ajax({
                    url:'{$urlBlocknumber}',
                    type: "POST",
                    data: {'wallet_address': wallet_address},
                    dataType: "json",
                    success:function(data){
                        blockchain.scan(data);
                    },
                    error: function(j){
                        console.log(j);
                        setTimeout(function(){ blockchain.sync(wallet_address) }, 15000);
                    }
                });
            //}
		},
        fastscan: function(data){
            $.ajax({
                url:'{$urlBlockchain}',
                type: "POST",
                data: {
                    'wallet_address': data.wallet_address,
                    'diff': data.diff, //url of getBalance
                    'latest_block': data.chainBlocknumber,
                },
                dataType: "json",
                beforeSend: function() {
                    $('.sync-blockchain').html('<div class="blockchain-pairing__loading"><center><img width=15 src="'+ajax_loader_url+'" alt="loading..."></center></div>');
                    $('.sync-difference').html('<small>'+data.diff+' blocks are required to complete the synchronization.</small>');
                },

                success:function(response){
                    if (response.success){
                        console.log('Trovato np-blockchain SUCCESS: ',data);
                        //return;
                        for (var dt of response.transactions) {
                            blockchain.addNewRow(dt);
                        }
                        // PREDISPONGO LA NOTIFICA CHE VERRA' MOSTRATA SOLO SE SUPPORTATA DAL BROWSER
                        var options = {
                            title: '[Napay] - Avviso',
                            body: "Nuove transazioni trovate. Desideri visualizzarle?", //walletStatus(data.status),
                            icon: 'src/images/icons/app-icon-96x96.png',
                            vibrate: [100, 50, 100, 50, 100 ], //in milliseconds vibra, pausa, vibra, ecc.ecc.
                            badge: 'src/images/icons/app-icon-96x96.png', //solo per android è l'icona della notifica
                            tag: 'confirm-notification', //tag univoco per le notifiche.
                            renotify: true, //connseeo a tag. se è true notifica di nuovo
                            data: {
                               openUrl: response.openUrl,
                            },
                            actions: [
                                {action: 'confirm', title: 'SI', icon: 'css/images/chk_on.png'},
                                {action: 'close', title: 'NO', icon: 'css/images/chk_off.png'},
                            ],
                        };
                        displayNotification(options);

                        setTimeout(function(){ erc20.Balance(data.wallet_address) }, 1000);
                        blockchain.sync(data.wallet_address);
                    }else{
                        $('.blockchain-pairing__loading').html('');
                        //console.log('Aggiornato block explorer by web. ajax wait 9.5sec...');
                        blockchain.sync(data.wallet_address);
                    }
                },
                error: function(j){
                    console.log(j);
                    setTimeout(function(){ blockchain.sync(data.wallet_address) }, 15000);
                }
            });
        },
        scan: function(data){
            $.ajax({
                url:'{$urlBlockchain}',
                type: "POST",
                data: {
                    'wallet_address': data.wallet_address,
                    'diff': data.diff, //url of getBalance
                    'latest_block': data.chainBlocknumber,
                },
                dataType: "json",
                beforeSend: function() {
                    if (data.diff >100){
                        $('.sync-blockchain').html('<div class="blockchain-pairing__loading"><center><img width=15 src="'+ajax_loader_url+'" alt="loading..."></center></div>');
                        $('.sync-difference').html('<small>'+data.diff+' blocks are required to complete the synchronization.</small>');
                    }else{
                        $('.sync-difference').html('');
                        $('.blockchain-pairing__loading').remove();
                    }
                },

                success:function(response){
                    if (response.success){
                        console.log('Trovato np-blockchain SUCCESS: ',data);
                        //return;
                        for (var dt of response.transactions) {
                            blockchain.addNewRow(dt);
                        }
                        // PREDISPONGO LA NOTIFICA CHE VERRA' MOSTRATA SOLO SE SUPPORTATA DAL BROWSER
                        var options = {
                            title: '[Napay] - Avviso',
                            body: "Nuove transazioni trovate. Desideri visualizzarle?", //walletStatus(data.status),
                            icon: 'src/images/icons/app-icon-96x96.png',
                            vibrate: [100, 50, 100, 50, 100 ], //in milliseconds vibra, pausa, vibra, ecc.ecc.
                            badge: 'src/images/icons/app-icon-96x96.png', //solo per android è l'icona della notifica
                            tag: 'confirm-notification', //tag univoco per le notifiche.
                            renotify: true, //connseeo a tag. se è true notifica di nuovo
                            data: {
                               openUrl: response.openUrl,
                            },
                            actions: [
                                {action: 'confirm', title: 'SI', icon: 'css/images/chk_on.png'},
                                {action: 'close', title: 'NO', icon: 'css/images/chk_off.png'},
                            ],
                        };
                        displayNotification(options);

                        setTimeout(function(){ erc20.Balance(data.wallet_address) }, 1000);
                        setTimeout(function(){ blockchain.sync(data.wallet_address) }, 9500);
                    }else{
                        $('.blockchain-pairing__loading').html('');
                        console.log('Aggiornato block explorer by web. ajax wait 9.5sec...');
                        setTimeout(function(){ blockchain.sync(data.wallet_address) }, 9500);
                    }
                },
                error: function(j){
                    console.log(j);
                    setTimeout(function(){ blockchain.sync(data.wallet_address) }, 15000);
                }
            });
        },
		// controlla se il db di ricezione indexedDB è stato preparato dal service worker
		isReadyReceived: function(wallet_address){
			readAllData('np-blockchain')
				.then(function(data) {
                    console.log('Trovato np-blockchain: ',data);
				  	if (typeof data[0] !== 'undefined') {
                        console.log('Trovato np-blockchain PIENO: ',data);
                        clearAllData('np-blockchain');

                        if (data[0].success){
                            console.log('Trovato np-blockchain SUCCESS: ',data);
                            //return;
                            for (var dt of data[0].transactions) {
                                blockchain.addNewRow(dt);
                            }
                            setTimeout(function(){ erc20.Balance(wallet_address) }, 1000);
                            setTimeout(function(){ eth.Balance(wallet_address) }, 3000);
                            setTimeout(function(){ blockchain.sync(wallet_address) }, 9500);
                        }else{
                            $('.blockchain-pairing__loading').html('');
                            console.log('waiting 9.5 secondi per ricominciare...');
                            setTimeout(function(){ blockchain.sync(wallet_address) }, 9500);
                        }
					} else {
                        console.log('Trovato np-blockchain VUOTO: ',data);
						console.log('waiting 9.5 sec il sw writing blockchain datas on indexedDB ...');
						setTimeout(function(){ blockchain.sync(wallet_address) }, 9500);
					}

				});
		},
        addNewRow: function(data){
			$("<tr class='even animazione'>"
			  +"<td><a href='"+data.url+"'>"+data.data+"</a></td>"
			  +"<td class='desc __sending_now-"+data.id_token+"'><a href='"+data.url+"'>"+data.status+"</a></td>"
			  +"<td class='__sending_now_price-"+data.id_token+"'>"+data.token_price+"</td>"
              +"<td><i class='fa fa-unlock ' style='color:red;'><span style='font-family:sans-serif; font-weight:lighter; font-size:0.8em;'>&nbsp;0</span></i></td>"
              +"</tr>").prependTo(".items > tbody");

              $('.animazione').addClass("animationTransaction");
		},
	}



JS;
Yii::app()->clientScript->registerScript('myBlockchain', $myBlockchain);

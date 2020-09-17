<?php

Yii::app()->language = ( isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'it' );
Yii::app()->sourceLanguage = ( isset($_COOKIE['langSource']) ? $_COOKIE['langSource'] : 'it_it' );

new JsTrans('js',Yii::app()->language); // javascript translation

$urlBlocknumber = Yii::app()->createUrl('blockchain/getBlockNumber'); //sync blockchain
$urlBlockchain = Yii::app()->createUrl('blockchain/scanForTransactions'); //sync blockchain
$urlBlockchainScanForNew = Yii::app()->createUrl('blockchain/scanForNew'); //sync blockchain
$tokenStatus = Yii::app()->createUrl('tokens/status');// stato dell'invoice

$myBlockchain = <<<JS

    //dichiarazione globale
    var blockchain;
    var ajax_loader_url = 'css/images/loading.gif';
	var paginaweb;
    var isPinRequest = false;
    var backend;


    /*
    * verifico la presenza del pin e lo richiedo
    */
    // function checkForPIN(){
    //     readAllData('pin')
    //         .then(function(pin) {
    //             if (typeof pin[0] !== 'undefined') {
    //                 if (null !== pin[0].id && pin[0].stop != 0){
    //                     var checktime = {
    //                         adesso : new Date().getTime() /1000 | 0,
    //                         scadenza : pin[0].id + (pin[0].stop * 60),
    //                     };
    //                     var differenza = checktime.scadenza - checktime.adesso;
    //                     //var differenza = 0;
    //                     console.log('checktime, differenza, isPinRequest',checktime,differenza,isPinRequest);
    //
    //                     if (differenza <= 0 && isPinRequest==false){
    //                         isPinRequest = true;
    //                         askPin(pin[0].pin);
    //                     }else{
    //                         if (!isPinRequest){
    //                             isPinRequest = updatePinTimestamp();
    //                         }
    //                     }
    //                 }else{
    //                     console.log('Pin impostato a 0');
    //                 }
    //             }else{
    //                 console.log('Nessun pin impostato!');
    //             }
    //         });
    // }

    // restituisce html dello status dell'invoice
    function tokenStatus(status){
    	$.ajax({
    		url:'{$tokenStatus}',
    		type: "POST",
    		data:{
    			'status'	: status,
    		},
    		dataType: "json",
    		success:function(data){
    			return data.status;
    		},
    		error: function(j){
    			return "null";
    		}
    	});
    }

	blockchain = {
		sync: function(my_address){
            console.log('[sync blockchain]',my_address);
            // prima di iniziare qualunque ricecra verifico la consistenza del pin
            // checkForPIN();

            //console.log('ora sono in sync blockchain ma senza sw...',wallet_address);
            //se non funziona il sw parte la ricerca via ajax standard
            $.ajax({
                url:'{$urlBlocknumber}',
                type: "POST",
                data: {'my_address': my_address},
                dataType: "json",
                success:function(data){
                  if (data.success){
                    blockchain.scan(data);
                    $('.pulse-button').removeClass('pulse-button-offline');
                  }else{
                    $('.pulse-button').addClass('pulse-button-offline');
                    setTimeout(function(){ blockchain.sync(my_address) }, 5000);
                  }

                },
                error: function(j){
                    console.log(j);
                    $('.pulse-button').addClass('pulse-button-offline');
                    setTimeout(function(){ blockchain.sync(my_address) }, 15000);
                }
            });
		},

        scan: function(data){
            $.ajax({
                url:'{$urlBlockchain}',
                type: "POST",
                data: {
                    'search_address': data.my_address,
                    'diff': data.diff, //url of getBalance
                    'latest_block': data.chainBlocknumber,
                },
                dataType: "json",
                beforeSend: function() {
                    if (data.diff >10){
                        $('.sync-blockchain').html('<div class="blockchain-pairing__loading"><center><img width=15 src="'+ajax_loader_url+'" alt="'+Yii.t('js','loading...')+'"></center></div>');
                        $('.sync-difference').html('<small>'+Yii.t('js','Synchronizing the blockchain: {number} blocks left.', {number:data.diff})+'</small>');
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
                            eth.txFound(dt.id_token);
                        }
                        // PREDISPONGO LA NOTIFICA CHE VERRA' MOSTRATA SOLO SE SUPPORTATA DAL BROWSER
                        var options = {
                            title: Yii.t('js','[Wallet] - New message'),
                            body: Yii.t('js','New transactions found. Do you want to view them?'), //walletStatus(data.status),
                            icon: 'src/images/icons/app-icon-96x96.png',
                            vibrate: [100, 50, 100, 50, 100 ], //in milliseconds vibra, pausa, vibra, ecc.ecc.
                            badge: 'src/images/icons/app-icon-96x96.png', //solo per android è l'icona della notifica
                            tag: 'confirm-notification', //tag univoco per le notifiche.
                            renotify: true, //connseeo a tag. se è true notifica di nuovo
                            data: {
                               openUrl: response.openUrl,
                            },
                            actions: [
                                {action: 'openUrl', title: Yii.t('js','Yes'), icon: 'css/images/chk_on.png'},
                                {action: 'close', title: Yii.t('js','No'), icon: 'css/images/chk_off.png'},
                            ],
                        };
                        displayNotification(options);

                        setTimeout(function(){ erc20.Balance(data.my_address) }, 1000);
                        //setTimeout(function(){ eth.Balance(data.from_address) }, 5000);
                        setTimeout(function(){ blockchain.sync(data.my_address) }, 7000);
                    }else{
                        // Ogni blocco viene prodotto in 15 secondi .
                        // quindi tento il caricamento ogni 7
                        setTimeout(function(){ blockchain.sync(data.my_address) }, 7000);
                    }
                },
                error: function(j){
                    console.log(j);
                    setTimeout(function(){ blockchain.sync(data.my_address) }, 14500);
                }
            });
        },
        scanForNew: function(my_address){
            $.ajax({
                url:'{$urlBlockchainScanForNew}',
                type: "POST",
                data: {
                    'my_address': my_address,
                },
                dataType: "json",
                success:function(response){
                    console.log('[ScanForNew]',response);
                    if (response.success){
                        //return;
                        // for (var dt of response.transactions) {
                        //     blockchain.addNewRow(dt);
                        // }
                        // PREDISPONGO LA NOTIFICA CHE VERRA' MOSTRATA SOLO SE SUPPORTATA DAL BROWSER
                        var options = {
                            title: Yii.t('js','[Bolt] - New message'),
                            body: Yii.t('js','Transactions updated. Do you want to view them?'), //walletStatus(data.status),
                            icon: 'src/images/icons/app-icon-96x96.png',
                            vibrate: [100, 50, 100, 50, 100 ], //in milliseconds vibra, pausa, vibra, ecc.ecc.
                            badge: 'src/images/icons/app-icon-96x96.png', //solo per android è l'icona della notifica
                            tag: 'confirm-notification', //tag univoco per le notifiche.
                            renotify: true, //connseeo a tag. se è true notifica di nuovo
                            data: {
                               openUrl: response.openUrl,
                            },
                            actions: [
                                {action: 'openUrl', title: Yii.t('js','Yes'), icon: 'css/images/chk_on.png'},
                                {action: 'close', title: Yii.t('js','No'), icon: 'css/images/chk_off.png'},
                            ],
                        };
                        displayNotification(options);
                    }
                },
                error: function(j){
                    console.log(j);
                }
            });
        },
		// controlla se il db di ricezione indexedDB è stato preparato dal service worker
		isReadyReceived: function(my_address){
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
                            setTimeout(function(){ erc20.Balance(my_address) }, 1000);
                            //setTimeout(function(){ eth.Balance(my_address) }, 3000);
                            setTimeout(function(){ blockchain.sync(my_address) }, 9500);
                        }else{
                            $('.blockchain-pairing__loading').html('');
                            console.log('waiting 9.5 secondi per ricominciare...');
                            setTimeout(function(){ blockchain.sync(my_address) }, 9500);
                        }
					} else {
                        console.log('Trovato np-blockchain VUOTO: ',data);
						console.log('waiting 9.5 sec il sw writing blockchain datas on indexedDB ...');
						setTimeout(function(){ blockchain.sync(my_address) }, 9500);
					}

				});
		},
        addNewRow: function(data){
			$("<tr class='even animazione'>"
            +"<td><i class='zmdi zmdi-star-outline'></i></td>"
            +"<td><a href='"+data.url+"'>"+data.data+"</a></td>"
            +"<td class='desc __sending_now-"+data.id_token+"'><a href='"+data.url+"'>"+data.status+"</a></td>"
            +"<td style='text-align:center;' class='__sending_now_price-"+data.id_token+"'>"+data.token_price+"</td>"
            +"<td class='mobile-not-show'>"+(data.token_price < 0 ? data.to_address : data.from_address)+"</td>"
            +"<td style='width:50px;'><i class='fa fa-unlock ' style='color:red;'></i><span style='font-size:0.8em;'>1</span></td>"
            +"</tr>").prependTo("#tokens-grid table.items > tbody");

              $('.animazione').addClass("animationTransaction");
		},
	}



JS;
Yii::app()->clientScript->registerScript('myBlockchain', $myBlockchain, CClientScript::POS_HEAD);

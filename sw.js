// import IndexedDB
importScripts('src/js/idb.js');
importScripts('src/js/utility.js');

// quando cambi questi valori modificali anche in view/layouts/js_sw.php
var CACHE_STATIC_NAME = 'store-wallet-static-v0.4.006';
var CACHE_DYNAMIC_NAME = 'store-wallet-dynamic-v0.4.006';
var STATIC_FILES = [
	'/',
	//'index.php',
	'offline.php',
	'manifest.json',
	// 'index.php?r=site/login',
	// 'offline.php',
	// 'manifest.json',
	// 'css/offline.css',
	// 'css/images/drago.png',
	// 'css/images/loading.gif',
	// 'css/images/BG.svg',
	// 'css/images/logocomune.png',
	// 'css/images/parthenope.png',
	// 'css/images/napay-yellow.png',
	// 'css/images/chk_on.png',
	// 'css/images/chk_off.png',
	// 'css/images/ic_account_google2fa.png',
	// 'css/images/ic_account_circle.svg',
	// 'css/images/ic_vpn_key.svg',
	// 'css/images/favicon.ico',
	// 'css/glyphicon.css',
	// 'css/form.css',
	// 'css/login.css',
	// 'css/wallet.css',
	// 'css/numpad/easy-numpad.css',
	// 'src/js/idb.js',
	// 'src/js/utility.js',
	// 'src/js/pinutility.js',
	// 'src/js/fetch.js',
	// 'src/js/promise.js',
	// 'src/js/service.js',
	// 'src/js/easy-numpad.js',
	// 'src/ethjs/aes.js',
	// 'src/ethjs/aes-json-format.js',
	// 'src/ethjs/lightwallet.min.js',
	// 'protected/extensions/webcodecamjs-master/js/qrcodelib.js',
	// 'protected/extensions/webcodecamjs-master/js/webcodecamjquery.js',
	// 'protected/extensions/webcodecamjs-master/js/DecoderWorker.js',
	// 'protected/extensions/webcodecamjs-master/audio/beep.mp3',
	// 'src/jquery-ui/jquery-ui.min.css',
	// 'src/jquery-ui/jquery-ui.min.js',
	// 'src/jquery-ui/jquery.ui.touch-punch.min.js',
	// 'protected/views/header_mobile-nophp.html',
	// 'themes/cool/css/font-face.css',
	// 'themes/cool/css/theme.css',
	// 'themes/cool/css/lumen.css',
	// 'themes/cool/vendor/font-awesome-4.7/css/font-awesome.min.css',
	// 'themes/cool/vendor/font-awesome-5/css/fontawesome-all.min.css',
	// 'themes/cool/vendor/font-awesome-5/webfonts/fa-brands-400.woff2',
	// 'themes/cool/vendor/font-awesome-5/webfonts/fa-solid-900.woff2',
	// 'themes/cool/vendor/mdi-font/css/material-design-iconic-font.min.css',
	// 'themes/cool/vendor/mdi-font/fonts/Material-Design-Iconic-Font.woff2',
	// 'themes/cool/vendor/bootstrap-4.1/bootstrap.min.css',
	// 'themes/cool/vendor/bootstrap-4.1/popper.min.js',
	// 'themes/cool/vendor/bootstrap-4.1/bootstrap.min.js',
	// 'themes/cool/vendor/animsition/animsition.min.css',
	// 'themes/cool/vendor/animsition/animsition.min.js',
	// 'themes/cool/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css',
	// 'themes/cool/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js',
	// 'themes/cool/vendor/wow/animate.css',
	// 'themes/cool/vendor/wow/wow.min.js',
	// 'themes/cool/vendor/css-hamburgers/hamburgers.min.css',
	// 'themes/cool/vendor/slick/slick.css',
	// 'themes/cool/vendor/select2/select2.min.css',
	// 'themes/cool/vendor/select2/select2.min.js',
	// 'themes/cool/vendor/perfect-scrollbar/perfect-scrollbar.css',
	// 'themes/cool/vendor/perfect-scrollbar/perfect-scrollbar.js',
	// 'themes/cool/vendor/jquery-3.2.1.min.js',
	// 'themes/cool/vendor/chartjs/Chart.bundle.min.js',
	// 'themes/cool/vendor/slick/slick.min.js',
	// 'themes/cool/vendor/counter-up/jquery.waypoints.min.js',
	// 'themes/cool/vendor/counter-up/jquery.counterup.min.js',
	// 'themes/cool/vendor/circle-progress/circle-progress.min.js',
	// 'themes/cool/js/main.js',
];



// Funzione Fix per apache
function cleanResponse(response) {
	const clonedResponse = response.clone();

	// Not all browsers support the Response.body stream, so fall back to reading
	// the entire body into memory as a blob.
	const bodyPromise = 'body' in clonedResponse ?
	  Promise.resolve(clonedResponse.body) :
	  clonedResponse.blob();

	return bodyPromise.then((body) => {
	  // new Response() is happy when passed either a stream or a Blob.
	  return new Response(body, {
			headers: clonedResponse.headers,
			status: clonedResponse.status,
			statusText: clonedResponse.statusText,
	  });
	});
}

function trimCache(cacheName, maxItems) {
	caches.open(cacheName)
		.then(function(cache) {
			return cache.keys()
				.then(function(keys) {
					if (keys.lenght > maxItems) {
						cache.delete(keys[0])
							.then(trimCache(cacheName, maxItems));
					}
				});
		});
}


self.addEventListener('install', function (event) {
	//console.log('[Service Worker] Installing Service worker...', event);
	console.log('[Service Worker] Installing Service worker...');
	event.waitUntil(
		//versioning della cache. Per aggiornare le versioni del software
		caches.open(CACHE_STATIC_NAME)
			.then(function(cache){
				console.log('[Service Worker] Precaching app shell...');
				cache.addAll(STATIC_FILES);
			})
	)
});
self.addEventListener('activate', function (event) {
	console.log('[Service Worker] Activating Service worker...');
	event.waitUntil(
		caches.keys()
			.then(function(keyList) {
				return Promise.all(keyList.map(function(key){
					if (key !== CACHE_STATIC_NAME && key !== CACHE_DYNAMIC_NAME){
						console.log('[Service Worker] deleting cache', key);
						return caches.delete(key);
					}
				}));
			})

	);
	return self.clients.claim();
});

function isInArray(string, array) {
	for(var i = 0; i < array.length; i++) {
		if (array[i] === string){
			return true;
		}
	}
	return false;
}

function getFileExtension(filename) {
  return filename.split('.').pop();
}

//listener per i file caricati
self.addEventListener('fetch', function (event) {
	var parser = new URL(event.request.url);


	if (getFileExtension(parser.pathname) == 'php'
		|| getFileExtension(parser.pathname) == 'css'
	){
		console.log('[SW Parser] web ',parser.pathname);
		event.respondWith(
		 	fetch(event.request)
		);
	} else if (isInArray(event.request.url, STATIC_FILES)) {
		console.log('[SW Parser] static cache ',parser.pathname);
		event.respondWith(
			fetch(event.request).catch(function(){
				return	caches.match(event.request);
			})

		);
	} else {
		console.log('[SW Parser] dynamic cache ',parser.pathname);
		event.respondWith(
			caches.match(event.request)
				.then(function(response) {
					if (response) {
						// Inizio Fix per apache
						if(response.redirected) {
							return cleanResponse(response);
						} else {
							return response;
						}
						// END Fix per apache
					} else {
						return fetch(event.request)
							.then(function(res) {
								return caches.open(CACHE_DYNAMIC_NAME)
									.then(function(cache) {
										//trimCache(CACHE_DYNAMIC_NAME, 20);
										cache.put(event.request.url, res.clone());
										return res;
									})
							}).
							catch(function(err) {
								return caches.open(CACHE_STATIC_NAME)
									.then(function(cache) {
										if (event.request.headers.get('accept').includes('text/html')){
											return cache.match('offline.php');
										}
									})
							});
					}
				})
		);
	}
	// var url1 = '?r=wallet/history';
	// var url2 = '?r=wallet/index';
	// var url3 = '?r=wallet/checkAddress';
	// var url4 = '?r=wallet/gasPrice';
	// var url5 = '?r=settings/token';
	// var url6 = '?r=wallet/details';
	// var parser = new URL(event.request.url);

	//console.log('[Service Worker] parser',parser.search.substr(0,17));
	// parser.protocol; // => "http:"
	// parser.host;     // => "example.com:3000"
	// parser.hostname; // => "example.com"
	// parser.port;     // => "3000"
	// parser.pathname; // => "/pathname/"
	// parser.hash;     // => "#hash"
	// parser.search;   // => "?search=test"
	// parser.origin;   // => "http://example.com:3000"

	// if (	parser.search == url1
	// 	 || parser.search == url2
	// 	 || parser.search.substr(0,17) == url5
	// 	 || parser.search.substr(0,17) == url6
	// ){
	// 	console.log('[Service Worker] intercettato url da caricare solo via web: ', parser.search);
	// 	event.respondWith(
	// 		fetch(event.request)
	// 	);
	// }
	// else if (
	// 	parser.search == url3 ||
	// 	parser.search == url4
	// ) {
	// 	switch (parser.search) {
	// 		case url3:
	// 			var table = 'np_checkaddress';
	// 			break;
	// 		case url4:
	// 			var table = 'np_gasPrice';
	// 			break;
	// 	}
	//
	// 	//console.log('intercettato richiesta di ...',table,'...');
	// 	event.respondWith(fetch(event.request)
	// 		.then(function(res) {
	// 			var clonedRes = res.clone();
	//
	// 			if (table != 'np_checkaddress'){
	// 				clearAllData(table)
	// 					.then(function(res){
	// 						return clonedRes.json();
	// 					})
	// 					.then(function(data) {
	// 						//console.log('[Service Worker] scrivo i dati in IndexedDB in tabella:'+table, data);
	//    						for (var key in data) {
	// 							//TODO: se il gas rioprta errore, non salvare!!!
	// 							writeData(table, data);
	// 						}
	// 					});
	// 			}else{
	// 				// se è checkaddress non svuoto il db così ottengo uno storico degli indirizzi
	// 				// e sono più veloce a dare una risposta per indirizzi già utilizzati
	// 				readAllData(table)
	// 					.then(function(res){
	// 						return clonedRes.json();
	// 					})
	// 					.then(function(data) {
	// 						//console.log('[Service Worker] scrivo i dati in IndexedDB in tabella:'+table, data);
	//    						for (var key in data) {
	// 							writeData(table, data);
	// 						}
	// 					});
	// 			}
	//
	// 			return res;
	// 		})
	//
	// 	);
	// } else if (isInArray(event.request.url, STATIC_FILES)) {
	// 	event.respondWith(
	// 		fetch(event.request).catch(function(){
	// 			return	caches.match(event.request);
	// 		})
	//
	// 	);
	// } else {
	// 	event.respondWith(
	// 		caches.match(event.request)
	// 			.then(function(response) {
	// 				if (response) {
	// 					return response;
	// 				} else {
	// 					return fetch(event.request)
	// 						.then(function(res) {
	// 							return caches.open(CACHE_DYNAMIC_NAME)
	// 								.then(function(cache) {
	// 									//trimCache(CACHE_DYNAMIC_NAME, 20);
	// 									cache.put(event.request.url, res.clone());
	// 									return res;
	// 								})
	// 						}).
	// 						catch(function(err) {
	// 							return caches.open(CACHE_STATIC_NAME)
	// 								.then(function(cache) {
	// 									if (event.request.headers.get('accept').includes('text/html')){
	// 										return cache.match('offline.php');
	// 									}
	// 								})
	// 						});
	// 				}
	// 			})
	// 	);
	// }
});


//listener per la sincronizzazione in background
self.addEventListener('sync', function(event) {
	console.log('[Service Worker] Background syncing: '+event.tag, event);

	// SINCRONIZZAZIONE BALANCE
	if (event.tag === 'sync-getbalance-eth' || event.tag === 'sync-getbalance-erc20') {
		suffix = 'erc20';
		if (event.tag === 'sync-getbalance-eth')
			suffix = 'eth';

		event.waitUntil(
			readAllData(event.tag)
			.then(function(data) {
				for (var dt of data) {

					var postData = new FormData();
 						postData.append('id', dt.id);
 						postData.append('wallet_address', dt.wallet_address);
					fetch(dt.url, {
						method: 'POST',
						body: postData,
					})
					.then(function(response) {
						return response.json();
					})
					.then(function(json) {
						writeData('np_balance_'+suffix, json);
				 	})
					.catch(function(err){
						console.log('[Service worker] Error while sending data', err);
					})
				}
				//per sicurezza cancello tutto da indexedDB
				clearAllData(event.tag);
			})
		);
	}


	// SINCRONIZZAZIONE INVIO
	if (event.tag === 'sync-send-eth' || event.tag === 'sync-send-erc20') {
		suffix = 'erc20';
		if (event.tag === 'sync-send-eth')
			suffix = 'eth';

		console.log('[Service Worker] Evento sincronizzazione di invio trovato!');
		event.waitUntil(
			//readAllData('wallet')
			//.then (function(){
				readAllData(event.tag)
				.then(function(data) {
					for (var dt of data) {
						console.log('[Service Worker] ciclo for: ', dt);
						var postData = new FormData();
	 						postData.append('from', dt.from);
							postData.append('to', dt.to);
							postData.append('gas', dt.gas);
							postData.append('amount', dt.amount);
							postData.append('memo', dt.memo);
							postData.append('prv_key', dt.prv_key);
							postData.append('prv_pas', dt.prv_pas);

						fetch(dt.url, {
							method: 'POST',
							body: postData,
						})
						.then(function(response) {
							return response.json();
						})
						.then(function(json) {
							console.log('[sw test]',json);
							writeData('np-send-'+suffix, json);
					 	})
						.catch(function(err){
							console.log('[Service worker] Error while sending data', err);
						})
					}
					//per sicurezza cancello tutto da indexedDB
					clearAllData(event.tag);
				 })
			//})

		 );
	 }

	// SINCRONIZZAZIONE RICEZIONE
 	if (event.tag === 'sync-txPool') {
 		console.log('Evento sincronizzazione di ricerca tx in pool trovato!');
 		event.waitUntil(
 			readAllData(event.tag)
 			.then(function(data) {
 				for (var dt of data) {
					console.log('[Service worker] fetching txPool',dt);
 					var postData = new FormData();
  						postData.append('id_token', dt.id_token);

 					fetch(dt.url, {
 						method: 'POST',
 						body: postData,
 					})
 					.then(function(response) {
 						return response.json();
 					})
 					.then(function(json) {
 						writeData('np-txPool', json);
 				 	})
 					.catch(function(err){
 						console.log('[Service worker] Error while checking pool data', err);
 					})
 				}
 				//per sicurezza cancello tutto da indexedDB
 				clearAllData(event.tag);
 			 })
 		 );
 	}

	// SINCRONIZZAZIONE BLOCKCHAIN
 	if (event.tag === 'sync-blockchain') {
 		//console.log('Evento sincronizzazione della blockchain trovato!');
 		event.waitUntil(
 			readAllData(event.tag)
 			.then(function(data) {
 				for (var dt of data) {
					//console.log('[Service worker] fetching sync-blockchain',dt);
					if (dt.diff >1){ //4 blocchi a 15 sec. sono 1 minuto
						var postData = new FormData();
	  						postData.append('wallet_address', dt.wallet_address);
							postData.append('latest_block', dt.latest_block);

	 					fetch(dt.url, {
	 						method: 'POST',
	 						body: postData,
	 					})
	 					.then(function(response) {
	 						return response.json();
	 					})
	 					.then(function(json) {
							if (json.success){
								console.log('[Service worker] Risposta di blockchain/index',json);
								const title = '[Wallet TTS] - New message';
								const options = {
									body: 'New transactions found. Do you want to view them?',
									icon: 'src/images/icons/app-icon-96x96.png',
									vibrate: [100, 50, 100, 50, 100 ], //in milliseconds vibra, pausa, vibra, ecc.ecc.
									badge: 'src/images/icons/app-icon-96x96.png', //solo per android è l'icona della notifica
									tag: 'confirm-notification', //tag univoco per le notifiche.
									renotify: true, //connseeo a tag. se è true notifica di nuovo
									data: {
									   openUrl: json.openUrl,
									},
									actions: [
										{action: 'openUrl', title: 'Yes', icon: 'css/images/chk_on.png'},
										{action: 'close', title: 'No', icon: 'css/images/chk_off.png'},
									],
								};
							  	self.registration.showNotification(title, options);
							}
							writeData('np-blockchain', json);
	 				 	})
	 					.catch(function(err){
	 						console.log('[Service worker] Error while checking blockchain data', err);
	 					})
					}
 				}
 				//per sicurezza cancello tutto da indexedDB
 				clearAllData(event.tag);
 			 })
 		 );
 	}
});


//listener per le notifiche push
self.addEventListener('push', function(event) {
  console.log('[Service Worker] Push Received.');
  //console.log(`[Service Worker] Push had this data: "${event.data.text()}"`);

	const info = JSON.parse(`${event.data.text()}`);
	console.log(`[Service Worker] Push had this data: `+info);

	const sendNotification = body => {
        return self.registration.showNotification(info.title, {
			body: info.body,
			icon: info.icon,
			badge: info.badge,
			vibrate: info.vibrate,
			//image: info.image,
			//sound: info.sound,
			data: info.data,
			actions: info.actions,
			tag: info.tag,
			renotify: true,
			data: {
				 openUrl: info.openUrl,
			},
        });
	};



  if (event.data) {
        const message = event.data.text();
        event.waitUntil(sendNotification(message));
	}
});

self.addEventListener('notificationclick', function(event) {
	console.log('[SW event on notification click] EVENT', event);

	if (typeof event.notification.data !== 'undefined') {
		var action = event.notification.data;
	}else{
		var action = JSON.parse(event.actions);
	}

	console.log('[SW event on notification click] ACTION', action);
	console.log('[SW event on notification click] URL', action.openUrl);

	event.notification.close();
	event.waitUntil(clients.openWindow(action.openUrl));

}
, false);

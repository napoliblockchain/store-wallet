var dbPromise = idb.open('npay', 1, function(db) {

	//store dati per richiesta sincronizzazione balance
	if (!db.objectStoreNames.contains('sync-getbalance-eth'))
	 	db.createObjectStore('sync-getbalance-eth', {keyPath: 'id'});
	if (!db.objectStoreNames.contains('sync-getbalance-erc20'))
	 	db.createObjectStore('sync-getbalance-erc20', {keyPath: 'id'});

	//store dati per storage balance
	if (!db.objectStoreNames.contains('np_balance_eth'))
		db.createObjectStore('np_balance_eth', {keyPath: 'id'});
	if (!db.objectStoreNames.contains('np_balance_erc20'))
		db.createObjectStore('np_balance_erc20', {keyPath: 'id'});

	//store per sincronizzazione ricezione
	if (!db.objectStoreNames.contains('sync-receive'))
	 	db.createObjectStore('sync-receive', {keyPath: 'id'});
	// STORE PER STORAGE DATI DI RICEZIONE
	if (!db.objectStoreNames.contains('np_receive'))
		db.createObjectStore('np_receive', {keyPath: 'id'});

	//store per sincronizzazione check address
	if (!db.objectStoreNames.contains('np_checkaddress'))
		db.createObjectStore('np_checkaddress', {keyPath: 'id'});

	//store per sincronizzazione del gas price
	if (!db.objectStoreNames.contains('np_gasPrice'))
		db.createObjectStore('np_gasPrice', {keyPath: 'id'});



	// STORE PER SINCRONIZZAZIONE INVIO ETH E TOKEN
	if (!db.objectStoreNames.contains('sync-send-eth'))
	 	db.createObjectStore('sync-send-eth', {keyPath: 'id'});
	if (!db.objectStoreNames.contains('sync-send-erc20'))
	 	db.createObjectStore('sync-send-erc20', {keyPath: 'id'});
	if (!db.objectStoreNames.contains('np-send-eth'))
	 	db.createObjectStore('np-send-eth', {keyPath: 'id'});
	if (!db.objectStoreNames.contains('np-send-erc20'))
	 	db.createObjectStore('np-send-erc20', {keyPath: 'id'});

	//store per sincronizzazzione check txFound
	if (!db.objectStoreNames.contains('sync-txPool'))
	 	db.createObjectStore('sync-txPool', {keyPath: 'id'});
	if (!db.objectStoreNames.contains('np-txPool'))
	 	db.createObjectStore('np-txPool', {keyPath: 'id'});

	//store per sincronizzazzione blockchain
	if (!db.objectStoreNames.contains('sync-blockchain'))
		db.createObjectStore('sync-blockchain', {keyPath: 'id'});
	if (!db.objectStoreNames.contains('np-blockchain'))
		db.createObjectStore('np-blockchain', {keyPath: 'id'});



	//store per il salvataggio della sottoscrizione push
	if (!db.objectStoreNames.contains('subscriptions')) {
	 	db.createObjectStore('subscriptions', {keyPath: 'id'});
	}
	//store per verificare la presenza del wallet
	if (!db.objectStoreNames.contains('wallet')) {
	 	db.createObjectStore('wallet', {keyPath: 'id'});
	}
	//store per il salvataggio dei dati del pin
	if (!db.objectStoreNames.contains('pin')) {
	 	db.createObjectStore('pin', {keyPath: 'id'});
	}
});

function writeData(table, data) {
	//console.log('[IndexedDb storing datas]', table);
	//console.log(table,data);
	return dbPromise
		.then(function(db) {
			var tx = db.transaction(table, 'readwrite');
			var store = tx.objectStore(table);
			store.put(data);
			return tx.complete;
		});
}

function readAllData(table) {
	//console.log("leggo tabella: "+table);
	return dbPromise
		.then(function(db) {
			var tx = db.transaction(table, 'readonly');
			var store = tx.objectStore(table);
			return store.getAll();
		});
}

function clearAllData(table) {
	//console.log("cancello tabella: "+table);
  return dbPromise
    .then(function(db) {
      var tx = db.transaction(table, 'readwrite');
      var store = tx.objectStore(table);
      store.clear();
      return tx.complete;
    });
}

function deleteItemFromData(table, id){
	return dbPromise
		.then(function(db){
			var tx = db.transactions(table, 'readwrite');
			var store = tx.objectStore(table);
			store.delete(id);
			return tx.complete;
		})
		.then(function(){
			console.log('Item deleted');
		});
}

function urlBase64ToUint8Array(base64String) {
  var padding = '='.repeat((4 - base64String.length % 4) % 4);
  var base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');

  var rawData = window.atob(base64);
  var outputArray = new Uint8Array(rawData.length);

  for (var i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function dataURItoBlob(dataURI) {
  var byteString = atob(dataURI.split(',')[1]);
  var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]
  var ab = new ArrayBuffer(byteString.length);
  var ia = new Uint8Array(ab);
  for (var i = 0; i < byteString.length; i++) {
    ia[i] = byteString.charCodeAt(i);
  }
  var blob = new Blob([ab], {type: mimeString});
  return blob;
}

// Generate random entropy for the seed based on crypto.getRandomValues.
function generateEntropy(length) {
	var charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	var i;
	var result = "";

	values = new Uint32Array(length);
	window.crypto.getRandomValues(values);
	for(var i = 0; i < length; i++)
	{
		result += charset[values[i] % charset.length];
	}
	return result;
}

function WordCount(str) {
  return str.split(" ").length;
}

function validatePassword(password,id) {
	$('#'+id).show();
  // Do not show anything when the length of password is zero.
  if (password.length === 0) {
		$('#crypt_password_em_').text('');
      return;
  }
  // Create an array and push all possible values that you want in password
  var matchedCase = new Array();
  matchedCase.push("[$@$!%*#?&/\().:;-_]"); // Special Charector
  matchedCase.push("[A-Z]");      // Uppercase Alpabates
  matchedCase.push("[0-9]");      // Numbers
  matchedCase.push("[a-z]");     // Lowercase Alphabates

  // Check the conditions
  var ctr = 0;
  for (var i = 0; i < matchedCase.length; i++) {
      if (new RegExp(matchedCase[i]).test(password)) {
          ctr++;
      }
  }
  // Display it
  var color = "";
  var strength = "";
  if (password.length < 8)
  	strength = "Troppo breve e ";
  switch (ctr) {
      case 0:
      case 1:
      case 2:
          strength += "Debole";
          color = "red";
          break;
      case 3:
          strength += "Media";
          color = "orange";
          break;
      case 4:
          strength += "Forte";
          color = "green";
          break;
  }
	$('#'+id).text(strength);
  	document.getElementById(id).style.color = color;
}

function displayNotification(options){
	if ('serviceWorker' in navigator) {
		//console.log(options);
		// var options = {
		// 	body: 'Hai abilitato con successo il sistema di notifiche push!',
		// 	icon: 'src/images/icons/app-icon-96x96.png',
		// 	//image: 'src/images/icons/app-icon-96x96.png', //immagine nel testo
		// 	dir: 'ltr' , // left to right
		// 	lang: 'it-IT', //BCP 47
		// 	vibrate: [100, 50, 100], //in milliseconds vibra, pausa, vibra, ecc.ecc.
		// 	badge: 'src/images/icons/app-icon-96x96.png', //solo per android è l'icona della notifica
		// 	tag: 'confirm-notification', //tag univoco per le notifiche.
		// 	renotify: true, //connseeo a tag. se è true notifica di nuovo
		// 	actions: [
		// 		{action: 'confirm', title: 'Okay', icon: 'src/images/icons/chk_on.png'},
		// 		//{action: 'cancel', title: 'cancel', icon: 'src/images/icons/chk_off.png'},
		// 	],
		// };

		navigator.serviceWorker.ready
			.then(function(swreg) {
				swreg.showNotification(options.title, options);
			});

	}
}

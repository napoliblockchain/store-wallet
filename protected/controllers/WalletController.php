<?php
Yii::import('libs.crypt.crypt');
Yii::import('libs.NaPacks.Settings');
Yii::import('libs.NaPacks.WebApp');
Yii::import('libs.NaPacks.Logo');
Yii::import('libs.ethereum.eth');

require_once Yii::app()->params['libsPath'] . '/ethereum/web3/vendor/autoload.php';
use Web3\Web3;

class WalletController extends Controller
{
	public function init()
	{
		if (isset(Yii::app()->user->objUser) && Yii::app()->user->objUser['facade'] <> 'dashboard'){
			Yii::app()->user->logout();
			$this->redirect(Yii::app()->homeUrl);
		}
	}
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('saveSubscription'), //salva lo sottoscrizinoe dell'user per le notifiche push
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(
					'index', //
					'saveAddress', // salva l'indirizzo creato da eth-lightwallet
					'history',
					'details',
					'error', //pagina di errore
					'estimateGas',
					'checkAddress',
					'checkTxpool',
					'crypt', //cripta codice da js
					'decrypt', //decripta codice da js
					'rescan', // azzera il blocknumber del wallet, causando una rescansione della blockchain
					),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionCrypt()
	{
		echo CJSON::encode(array(
			'cryptedpass'=>isset($_POST['pass']) ? crypt::Encrypt($_POST['pass']) : '',
			'cryptedseed'=>isset($_POST['seed']) ? crypt::Encrypt($_POST['seed']) : ''
		));
	}

	public function actionDecrypt()
	{
		echo CJSON::encode(array(
			'decrypted'=>isset($_POST['pass']) ? crypt::Decrypt($_POST['pass']) : '',
			'decryptedseed'=>isset($_POST['cryptedseed']) ? crypt::Decrypt($_POST['cryptedseed']) : ''
		));
	}


	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		// controllo che sia un merchant e non uno user semplice
		if (Yii::app()->user->objUser['privilegi'] < 10){
			$this->render('error');
			die();
		}
		// solo in caso di commerciante
		$warningmessage = null;

		// verifico se è in scadenza
		$deadline = WebApp::StatoPagamenti(Yii::app()->user->objUser['id_user'],true);
		if ($deadline >= -31-28){
			$warningmessage[] = $this->writeMessage('deadline', 28+31 - $deadline);
		}

		$modelc=new Tokens('search');
			$modelc->unsetAttributes();

		$walletForm = new WalletTokenForm; //form di input dei dati

		//carico il wallet selezionato nei settings
		$settings=Settings::loadUser(Yii::app()->user->objUser['id_user']);
		if (empty($settings->id_wallet)){
			$from_address = '0x0000000000000000000000000000000000000000';
		}else{
			$wallet = Wallets::model()->findByPk($settings->id_wallet);
			$from_address = $wallet->wallet_address;
		}

		$modelc->from_address = $from_address;

		//carico i contatti dell'utente
		$criteria = new CDbCriteria();
		$criteria->compare('id_user',Yii::app()->user->objUser['id_user'],false);
		$dataProvider=new CActiveDataProvider('Contacts',array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 10,
			),
		));

		$this->render('index',array(
			'modelc'=>$modelc, //lista transazioni tokens
			'walletForm'=>$walletForm, //form per invio dati
			'from_address'=>$from_address, // indirizzo del wallet dell'utente
			'actualBlockNumberDec' => eth::latestBlockNumberDec(), // numero attuale del blocco sulla blockchain
			'dataProvider' => $dataProvider, // lista contatti
			'warningmessage'=>$warningmessage, // messaggio di scadenza
		));
	}

	/**
	 * Genero il div con l'avviso di creare un wallet Token
	 */
	public function writeMessage($what,$days=null){
		$http_host = $_SERVER['HTTP_HOST'];

		if ($what == 'deadline'){
			return '
			<div class="col m-t-25">
				<div class="alert alert-danger" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
					<b>ATTENZIONE!</b>
					<br>Restano alla scadenza '.abs($days).' giorni, dopodiché non potrai più usare l\'applicazione.
				</div>
			</div>
			';
		}

	}

	/**
	 * Lists all models.
	 */
	public function actionHistory()
	{
		$this->layout='//layouts/column_login';

		$criteria=new CDbCriteria();
		if (Yii::app()->user->objUser['privilegi'] == 10){
			$merchants=Merchants::model()->findByAttributes(array('id_user'=>Yii::app()->user->objUser['id_user']));
		 	$criteria->compare('id_merchant',$merchants->id_merchant,false);
		}
		// if (Yii::app()->user->objUser['privilegi'] == 15){
		// 	$associations=Associations::model()->findByAttributes(array('id_user'=>Yii::app()->user->objUser['id_user']));
		// 	$merchants=Merchants::model()->findByAttributes(array('id_association'=>$associations->id_association));
		// 	$criteria->compare('id_merchant',$merchants->id_merchant,false);
		// }

		//carico il wallet selezionato nei settings
		$settings=Settings::loadUser(Yii::app()->user->objUser['id_user']);
		if (!(isset($settings->id_wallet))){
			$wallet = new Wallets;
			$criteria->compare('wallet_address',0,false);
		}else{
			$wallet = Wallets::model()->findByAttributes(array('id_wallet'=>$settings->id_wallet));
			$criteria->compare('wallet_address',$wallet->wallet_address,false);
		}
		//visualizzo solo le transazioni generate da wallet: se item_desk == wallet
		$criteria->compare('item_desc','wallet',false);

		$dataProvider=new CActiveDataProvider('Tokens', array(
			'sort'=>array(
	    		'defaultOrder'=>array(
	      			'invoice_timestamp'=>true
	    		)
	  		),
		    'criteria'=>$criteria,
		));

		$this->render('history',array(
		 	'dataProvider'=>$dataProvider, //lista transazioni tokens
			'wallet'=>$wallet, //il wallet selezionato
			'actualBlockNumberDec' => eth::latestBlockNumberDec(), // numero attuale del blocco sulla blockchain
		));
	}
	/**
	 * MOstra i dettagli di una transazione token
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionDetails($id)
	{
		$this->layout='//layouts/column_login';
		$this->render('details',array(
			'model'=>$this->loadModel(crypt::Decrypt($id)),
			'actualBlockNumberDec' => eth::latestBlockNumberDec(), // numero attuale del blocco sulla blockchain
		));
	}



	/**
	 * Funziona che salva esclusivamente l'indirizzo generato da eth-lightwallet
	 */
	public function actionSaveAddress()
	{
		// $settings=Settings::load();
		//
		// if ($settings === null){
		// 	echo CJSON::encode(array("error"=>'Errore: I parametri di configurazione per la connessione al nodo POA non sono stati trovati'));
		// 	exit;
		// }
		$response = 0;
		//if( webRequest::url_test( $settings->poa_url ) ) {
			// mi connetto al nodo poa
			// $web3 = new Web3($settings->poa_url);
			// $web3 = new Web3(WebApp::getPoaNode());
		$poaNode = WebApp::getPoaNode();
		if (!$poaNode){
			$save = new Save;
			$save->WriteLog('wallet-tts','wallet','saveAddress',"All Nodes are down.");
		}else{
			$web3 = new Web3($poaNode);
			$eth = $web3->eth;
			//recupero l'ultimo block number

			$eth->getBlockByNumber('latest',false, function ($err, $block) use (&$response){
				if ($err !== null) {
					echo CJSON::encode(array("error"=>'Error: ' . $err->getMessage()));
					exit;
				}
				$response = $block->number;
			});
		}

		// se esiste aggiorno, altrimneti aggiungo
		// in questa ricerca devo trovare l'id_user e non il wallet address
		// se un user è già inserito aggiorno l0indirizzo e non il contrario
		$wallets=Wallets::model()->findByAttributes(array(
			//'wallet_address'=>$_POST['address'],
			'id_user' => Yii::app()->user->objUser['id_user']
		));
		if ($wallets === null){
			$wallets = new Wallets;
		}
		// salvo il nuovo indirizzo
		$wallets->id_user = Yii::app()->user->objUser['id_user'];
		$wallets->wallet_address = $_POST['address'];

		// metto il blocco a 0, in modo che se è un ripristino wallet, carica di nuovo tutte le transazioni
		// si potrebbe migliorare
		// TODO!!
		// cercare l'ultima transazione token in db con quel wallet e recuperare il numero blocco
		// in modo da non dover cercare nella blockchain dall'inizio
		$wallets->blocknumber = $response;

		if ($wallets->save()){
			// Salvo il wallet se si tratta di un utente ISTITUTO
			if (Yii::app()->user->objUser['id_institute'] > 0){
				$istituto = Institutes::model()->findByPk(Yii::app()->user->objUser['id_institute']);
				$istituto->wallet_address = $wallets->wallet_address;
				$istituto->save();
			}

			//assegno il nuovo indirizzo all'utente
			Settings::saveUser($wallets->id_user,$wallets->attributes,array('id_wallet'));
			$result = array(
				'success'=>true,
				'wallet'=>$wallets->wallet_address
			);
		}else{
			$result = array(
				'success'=>false,
				'wallet'=>$_POST['address']
			);
		}

		echo CJSON::encode($result);
	}





	//questa funzione crea l'account offline senza connessione al server
	// public function actionCreateAccount(){
	// 	$config = [
	// 	    'private_key_type' => OPENSSL_KEYTYPE_EC,
	// 	    'curve_name' => 'secp256k1'
	// 	];
	//
	// 	$res = openssl_pkey_new($config);
	//
	// 	if (!$res) {
	// 	    echo 'ERROR: Fail to generate private key. -> ' . openssl_error_string();
	// 	    exit;
	// 	}
	//
	// 	// Generate Private Key
	// 	openssl_pkey_export($res, $priv_key);
	//
	// 	// Get The Public Key
	// 	$key_detail = openssl_pkey_get_details($res);
	// 	$pub_key = $key_detail["key"];
	//
	// 	$priv_pem = PEM::fromString($priv_key);
	//
	// 	// Convert to Elliptic Curve Private Key Format
	// 	$ec_priv_key = ECPrivateKey::fromPEM($priv_pem);
	//
	// 	// Then convert it to ASN1 Structure
	// 	$ec_priv_seq = $ec_priv_key->toASN1();
	//
	// 	// Private Key & Public Key in HEX
	// 	$priv_key_hex = bin2hex($ec_priv_seq->at(1)->asOctetString()->string());
	// 	$priv_key_len = strlen($priv_key_hex) / 2;
	// 	$pub_key_hex = bin2hex($ec_priv_seq->at(3)->asTagged()->asExplicit()->asBitString()->string());
	// 	$pub_key_len = strlen($pub_key_hex) / 2;
	//
	// 	// Derive the Ethereum Address from public key
	// 	// Every EC public key will always start with 0x04,
	// 	// we need to remove the leading 0x04 in order to hash it correctly
	// 	$pub_key_hex_2 = substr($pub_key_hex, 2);
	// 	$pub_key_len_2 = strlen($pub_key_hex_2) / 2;
	//
	// 	// Hash time
	// 	$hash = Keccak::hash(hex2bin($pub_key_hex_2), 256);
	//
	// 	// Ethereum address has 20 bytes length. (40 hex characters long)
	// 	// We only need the last 20 bytes as Ethereum address
	// 	$wallet_address = '0x' . substr($hash, -40);
	// 	$wallet_private_key = '0x' . $priv_key_hex;
	//
	// 	// echo "\r\n   ETH Wallet Address: " . $wallet_address;
	// 	// echo "\r\n   Private Key: " . $wallet_private_key;
	// 	// exit;
	//
	// 	//Carico i parametri di connessione
	// 	$settings=Settings::load();
	// 	if ($settings === null || empty($settings->poa_url)){//} || empty($settings->poa_port)){
	// 		echo CJSON::encode(array("error"=>'Errore: I parametri di configurazione per la connessione al nodo POA non sono stati trovati'));
	// 		exit;
	// 	}
	// 	// mi connetto al nodo poa
	// 	//$web3 = new Web3\Web3($settings->poa_url.':'.$settings->poa_port);
	// 	$web3 = new Web3($settings->poa_url);
	// 	$eth = $web3->eth;
	// 	//recupero l'ultimo block number
	// 	$response = null;
	// 	$eth->getBlockByNumber('latest',false, function ($err, $block) use (&$response){
	// 		if ($err !== null) {
	// 			echo CJSON::encode(array("error"=>'Error: ' . $err->getMessage()));
	// 			exit;
	// 		}
	// 		$response = $block;
	// 	});
	//
	// 	//ritorno all'app e restituisco il token address e la chiave per aprirla
	// 	$send_json = array(
	// 		'token' => $wallet_address,
	// 		'key'=> crypt::Encrypt($wallet_private_key),
	// 		'url' => $settings->poa_url,
	// 		'port' => $settings->poa_port,
	// 		'blocknumber' => $response->number,
	// 	);
    // 	echo CJSON::encode($send_json);
	// }

	// public function actionNewAccount(){
	// 	//Carico i parametri di connessione
	// 	$settings=Settings::load();
	// 	//if ($settings === null || empty($settings->poa_url) || empty($settings->poa_port)){
	// 	if ($settings === null || empty($settings->poa_url)){//} || empty($settings->poa_port)){
	// 		echo CJSON::encode(array("error"=>'Errore: I parametri di configurazione per la connessione al nodo POA non sono stati trovati'));
	// 		exit;
	// 	}
	//
	// 	// mi connetto al nodo poa
	// 	//$web3 = new Web3\Web3($settings->poa_url.':'.$settings->poa_port);
	// 	$web3 = new Web3($settings->poa_url);
	// 	$personal = $web3->personal;
	// 	$eth = $web3->eth;
	//
	// 	$newAccount = '';
	// 	// create account
	// 	$password = Utility::passwordGenerator(32);
	// 	$personal->newAccount($password, function ($err, $account) use (&$newAccount) {
	// 		if ($err !== null) {
	// 			echo CJSON::encode(array("error"=>'Error: ' . $err->getMessage()));
	//  			exit;
	// 		}
	// 		$newAccount = $account;
	// 	});
	//
	// 	// remember to lock account after transaction
	// 	$personal->lockAccount($newAccount, function ($err, $locked) {
	// 		if ($err !== null) {
	// 			echo CJSON::encode(array("error"=>'Error: ' . $err->getMessage()));
	//  			exit;
	// 		}
	// 	});
	//
	// 	//recupero l'ultimo block number
	// 	$response = null;
	// 	$eth->getBlockByNumber('latest',false, function ($err, $block) use (&$response){
	// 		if ($err !== null) {
	// 			echo CJSON::encode(array("error"=>'Error: ' . $err->getMessage()));
	// 			exit;
	// 		}
	// 		$response = $block;
	// 	});
	//
	//
	// 	//ritorno all'app e restituisco il token address e la chiave per aprirla
	// 	$send_json = array(
	// 		'token' => $newAccount,
	// 		'key'=> crypt::Encrypt($password),
	// 		'url' => $settings->poa_url,
	// 		'port' => $settings->poa_port,
	// 		'blocknumber' => $response->number,
	// 	);
    // 	echo CJSON::encode($send_json);
	// }



	/**
	 * @param POST string address the Ethereum Address to be rescanned
	 */
	public function actionRescan(){
        //azzero il nuomero dei blocchi dell'indirizzo
		$model = Wallets::model()->findByAttributes(array('wallet_address'=>$_POST['wallet']));
		$model->blocknumber = '0x0';
		$model->update();

		echo CJSON::encode(array(
			'wallet' => $_POST['wallet'],
			"blocknumber"=>'0x0',
		));
	}

	/**
	 * @param POST string address the Ethereum Address to be paid
	 */
	public function actionCheckAddress(){
		// se sono un utente Istituto verifico anche che il wallet appartenga SOLO
		// ai cittadini di BOLT
		if (Yii::app()->user->objUser['id_institute'] > 0){
			$wallet = BoltWallets::model()->findByAttributes(['wallet_address'=>$_POST['to']]);
			if ($wallet === null){
				$save = new Save;
				$save->WriteLog('wallet-tts','wallet','checkAddress',"Inserted address isn\'t right!");
				echo CJSON::encode(array(
					'id'=>time(),
					'response'=>false,
				));
				return;
			}
		}

		$poaNode = WebApp::getPoaNode();
		if (!$poaNode){
			$save = new Save;
			$save->WriteLog('wallet-tts','wallet','checkAddress',"All Nodes are down.");
			echo CJSON::encode(array(
				'id'=>time(),
				'response'=>false,
			));
			return;
		}
		$web3 = new Web3($poaNode);
		$utils = $web3->utils;
		$response = $utils->isAddress($_POST['to']);


		echo CJSON::encode(array(
			'id' => $_POST['to'],
			"response"=>$response,
		));
	}

		/**
	 * Questa funzione controlla lo stato della transazione token
	 * Viene interrogato dal SW dopo che è stato registrata la richiesta in 'sync-txPool'
	 * La risposta viene salvata in indexedDB
	 * @param POST
	 * @param integer id_token the ID of the model to be searched
	 * @return
	 */
	public function actionCheckTxpool(){
		$model = $this->loadModel(crypt::Decrypt($_POST['id_token']));
		$wallets = Wallets::model()->findByAttributes(['id_user'=>Yii::app()->user->objUser['id_user']]);

		echo CJSON::encode(array(
			'id' => time(), //NECESSARIO PER IL SALVATAGGIO IN  indexedDB quando ritorna al Service Worker
			"status"=>$model->status,
			//"status_wlink"=>"<a href='index.php?r=tokens/view&id=".crypt::Encrypt($model->id_token)."'>". WebApp::walletStatus($model->status) ."</a>",
			"status_wlink"=>WebApp::translateMsg($model->status),
			"openUrl"=>Yii::app()->createUrl('tokens/view',array('id'=>crypt::Encrypt($model->id_token))), // url per i messaggi push
			'to_address'=>$model->to_address,
			'from_address'=>$model->from_address,
			'token_price'=>$model->token_price,
			'token_price_wsymbol' => WebApp::typePrice($model->token_price,($model->from_address == $wallets->wallet_address ? 'sent' : 'received')),
			'id_token'=>$_POST['id_token'],
		));
	}

	public function actionEstimateGas(){
		$fromAccount = $_POST['from'];
		$toAccount = $_POST['to'];
		$amount = $_POST['amount'];

		// $settings=Settings::load();
        // mi connetto al nodo poa
		// $web3 = new Web3($settings->poa_url);
		// $web3 = new Web3(WebApp::getPoaNode());
		$poaNode = WebApp::getPoaNode();
		if (!$poaNode){
			$save = new Save;
			$save->WriteLog('wallet-tts','wallet','estimateGas',"All Nodes are down.");
				echo CJSON::encode(array(
			 		'id'=>time(),
					'error'=>"All Nodes are down.",
			 		'success'=>false,
			 	));
			 	return;
		}
		$web3 = new Web3($poaNode);
		$eth = $web3->eth;
		$personal = $web3->personal;
		$utils = $web3->utils;
		$hex = $utils->toWei($amount, 'ether')->toHex();

		$gasPrice = null;
		// estimateGas
	    $eth->estimateGas([
	        	'from' => $fromAccount,
	        	'to' => $toAccount,
	        	'value' => '0x'.$hex
	    	], function ($err, $gas) use ($utils, $eth, $fromAccount, $toAccount, &$gasPrice) {
	        	if ($err !== null) {
	            	echo CJSON::encode(array("error"=>$err->getMessage()));
	            	exit;
	        	}
				$value = (string) $gas * 1;
				$gasPrice = $value / pow(10,8);
	    });
		//echo '<pre>'.print_r($gasPrice,true).'</pre>';
		//exit;
		$send_json = array(
			'gasPrice' => $gasPrice,
			'id' => time(), // id ci deve essere per il s.w.
		);
    	echo CJSON::encode($send_json);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Transactions the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Tokens::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Saves the Subscription for push messages.
	 * @param POST VAPID KEYS
	 * this function NOT REQUIRE user to login
	 */
	public function actionSaveSubscription()
	{
		ini_set("allow_url_fopen", true);
		//
 		$raw_post_data = file_get_contents('php://input');
 		if (false === $raw_post_data) {
 			throw new \Exception('Could not read from the php://input stream or invalid Subscription object received.');
 		}
 		$raw = json_decode($raw_post_data);
		$browser = $_SERVER['HTTP_USER_AGENT'];

		$Criteria=new CDbCriteria();
		$Criteria->compare('id_user',Yii::app()->user->objUser['id_user'], false);
		$Criteria->compare('browser',$browser, false);

		$vapidProvider=new CActiveDataProvider('PushSubscriptions', array(
			'criteria'=>$Criteria,
		));

		if ($vapidProvider->totalItemCount == 0 && $raw != null ){
			//save
			$vapid = new PushSubscriptions;
			$vapid->id_user = Yii::app()->user->objUser['id_user'];
			$vapid->browser = $browser;
			$vapid->endpoint = $raw->endpoint;
			$vapid->auth = $raw->keys->auth;
			$vapid->p256dh = $raw->keys->p256dh;
			$vapid->type = 'wallet';

			if (!$vapid->save()){
				echo 'Cannot save subscription on server!';
				exit;//
			}
			echo 'Subscription saved on server!';
		}else{
			//delete
			$iterator = new CDataProviderIterator($vapidProvider);
			foreach($iterator as $data) {
				echo '<pre>'.print_r($data->id_subscription,true).'</pre>';
				#exit;
				$vapid=PushSubscriptions::model()->findByPk($data->id_subscription)->delete();

				// if($vapid!==null)
				// 	$vapid->delete();
			}
			echo 'Subscriptions deleted on server!';
		}
	}



}

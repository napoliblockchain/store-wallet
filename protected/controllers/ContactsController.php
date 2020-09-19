<?php
Yii::import('libs.crypt.crypt');
Yii::import('libs.NaPacks.Settings');
Yii::import('libs.NaPacks.Logo');
Yii::import('libs.NaPacks.SaveModels');
Yii::import('libs.NaPacks.Save');
Yii::import('libs.NaPacks.Push');
Yii::import('libs.webRequest.webRequest');

class ContactsController extends Controller
{
	public function init()
	{
		// echo '<pre>'.print_r($_GET,true).'</pre>';
		// exit;
		Yii::app()->language = ( isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'it' );
		Yii::app()->sourceLanguage = ( isset($_COOKIE['langSource']) ? $_COOKIE['langSource'] : 'it_it' );

		new JsTrans('js',Yii::app()->language); // javascript translation

		if (isset(Yii::app()->user->objUser) && Yii::app()->user->objUser['facade'] != 'dashboard'){
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(
					'index',
					'add',
					'addbyid',
					'save',
					'getuser',
					'delete',
					'getuseraddress'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	// recupera informazioni sul' utente social
	public function actionGetuser()
	{
		// echo '<pre>'.print_r($_POST,true).'</pre>';
		// exit;
		$socials = Socialusers::model()->findByPk($_POST['id_social']);
		$get = (array) $socials->attributes;
		$get['id_user'] = crypt::Encrypt($socials->id_user);

		$wallets = Wallets::model()->findByAttributes(['id_user'=>$socials->id_user]);
		$get['address'] = $wallets->wallet_address;
		// echo '<pre>'.print_r($get,true).'</pre>';
		// exit;
		echo CJSON::encode($get);
	}

	// recupera l'address dell'utente tramite id social
	public function actionGetuseraddress()
	{
		$socials = Socialusers::model()->findByPk($_POST['id_social']);
		$wallets = Wallets::model()->findByAttributes(['id_user'=>$socials->id_user]);
		$return['address'] = $wallets->wallet_address;
		// echo '<pre>'.print_r($return,true).'</pre>';
		// exit;
		echo CJSON::encode($return);
	}

	/**
	 * salva in rubrica il contatto
	 *
	 * @param $_POST['id_user'] : id dell'utente aggiunto nella propria rubrica
	*/

	public function actionSave()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('id_user',Yii::app()->user->objUser['id_user'],false);
		$criteria->compare('id_social',$_POST['id_social'],false);

		$model = Contacts::model()->findAll($criteria);

		#echo '<pre>'.print_r($model,true).'</pre>';
		if (empty($model)){
			$model = new Contacts;
			$model->id_user = Yii::app()->user->objUser['id_user'];
			$model->id_social = $_POST['id_social'];
			$model->insert();

			//recupero le informazioni social dell'utente che aggiunge
			$socialUser = Socialusers::model()->findByAttributes(['id_user'=>Yii::app()->user->objUser['id_user']]); //chi aggiunge
			$socialContact = Socialusers::model()->findByPk($model->id_social); // il contatto aggiunto

			$who = !empty($socialUser->first_name) ? $socialUser->first_name : '';
			$who .= chr(32);
			$who .= !empty($socialUser->last_name) ? $socialUser->last_name : '';
			$who = ($who <> chr(32)) ? $who : !empty($socialUser->username) ? $socialUser->username : $socialUser->email;

			//salva la notifica
	 		$notification = array(
	 			'type_notification' => 'contact',	// il tipo di notifica
	 			'id_user' => $socialContact->id_user,		// il contatto da notificare (il followed)
	 			'id_tocheck' => Yii::app()->user->objUser['id_user'],	// colui che ti ha aggiunto nella rubrica
	 			'status' => 'followed',				// lo status è followed (seguito)
				//'description' => '{'.$who.'}' . Yii::t('lang',' is following you.'),
				'description' => '{'.$who.'} is following you.',
				'url' => Yii::app()->createUrl('contacts/addbyid',['id'=>crypt::Encrypt($socialUser->id_social)]), // id social del contattante
	 			'timestamp' => time(),
	 			'price' => 0,
	 			'deleted' => 0,
	 		);
			// echo '<pre>'.print_r($notification,true).'</pre>';
			// exit;

			// Salvo notifica e INVIO ANCHE UN MESSAGGIO PUSH
			$save = new Save;
		    Push::Send($save->Notification($notification));
		}
		echo CJSON::encode(['success'=>true]);
	}
	/**
	 * Apre la pagina di ricerca contatti
	 */
	public function actionAddbyid($id)
	{
		// echo '<pre>'.print_r($_GET,true).'</pre>';
		// exit;
		$model = new Socialusers('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['id'])) {
			$model->id_social = crypt::Decrypt($_GET['id']);
		}

		//carico il wallet selezionato nei settings
		$settings = Settings::loadUser(Yii::app()->user->objUser['id_user']);
		if (empty($settings->id_wallet)){
			$from_address = '0x0000000000000000000000000000000000000000';
		}else{
			$wallet = Wallets::model()->findByPk($settings->id_wallet);
			$from_address = $wallet->wallet_address;
		}

		$this->render('add',array(
			'model'=>$model,
			'from_address'=>$from_address, // indirizzo del wallet dell'utente
		));
	}

	/**
	 * Apre la pagina di ricerca contatti
	 */
	public function actionAdd()
	{
		// echo '<pre>'.print_r($_GET,true).'</pre>';
		// exit;
		$model = new Socialusers('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Socialusers'])) {
			$model->attributes = $_GET['Socialusers'];
		}

		//carico il wallet selezionato nei settings
		$settings = Settings::loadUser(Yii::app()->user->objUser['id_user']);
		if (empty($settings->id_wallet)){
			$from_address = '0x0000000000000000000000000000000000000000';
		}else{
			$wallet = Wallets::model()->findByPk($settings->id_wallet);
			$from_address = $wallet->wallet_address;
		}

		if (Yii::app()->request->isAjaxRequest)
			$this->renderPartial('add', array(
				'model' => $model,
				'from_address'=>$from_address, // indirizzo del wallet dell'utente
			));
		else
			$this->render('add', array(
				'model' => $model,
				'from_address'=>$from_address, // indirizzo del wallet dell'utente
			));

	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete()
	{
		// echo '<pre>'.print_r($_POST,true).'</pre>';
		// exit;
		$model = Contacts::model()->findByAttributes(['id_social'=>$_POST['id_social'],'id_user'=>Yii::app()->user->objUser['id_user']]);
		$model->delete();

		//recupero le informazioni social dell'utente che aggiunge
		$socialUser = Socialusers::model()->findByAttributes(['id_user'=>Yii::app()->user->objUser['id_user']]);
		$socialContact = Socialusers::model()->findByPk($_POST['id_social']);

		$who = !empty($socialUser->first_name) ? $socialUser->first_name : '';
		$who .= chr(32);
		$who .= !empty($socialUser->last_name) ? $socialUser->last_name : '';
		$who = ($who <> chr(32)) ? $who : !empty($socialUser->username) ? $socialUser->username : $socialUser->email;

		//salva la notifica
		$notification = array(
			'type_notification' => 'contact',	// il tipo di notifica
			'id_user' => $socialContact->id_user,		// il contatto da notificare (il followed)
			'id_tocheck' => Yii::app()->user->objUser['id_user'],	// colui che ti ha aggiunto nella rubrica
			'status' => 'unfollowed',				// lo status è unfollowed (non seguito)
			//'description' => '{'.$who.'}' . Yii::t('lang',' unfollowed you.'),
			'description' => '{'.$who.'} unfollowed you.',
			'url' => Yii::app()->createUrl('contacts/addbyid',['id'=>crypt::Encrypt($socialUser->id_social)]), // id social del contattante
			'timestamp' => time(),
			'price' => 0,
			'deleted' => 0,
		);
		// echo '<pre>'.print_r($notification,true).'</pre>';
		// exit;

		// Salvo notifica e INVIO ANCHE UN MESSAGGIO PUSH
		$save = new Save;
		Push::Send($save->Notification($notification));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		// echo '<pre>'.print_r(Yii::app()->user->objUser['id_user'],true).'</pre>';
		// exit;
		$criteria = new CDbCriteria();
		$criteria->compare('id_user',Yii::app()->user->objUser['id_user'],false);

		$dataProvider=new CActiveDataProvider('Contacts',array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 10,
			),
			// 'sort'=>array(
	    	// 	'defaultOrder'=>array(
	      	// 		'last_name'=>true,
			// 		//'first_name'=>true,
	    	// 	)
	  		// ),
		));


		//carico il wallet selezionato nei settings
		$settings = Settings::loadUser(Yii::app()->user->objUser['id_user']);
		if (empty($settings->id_wallet)){
			$from_address = '0x0000000000000000000000000000000000000000';
		}else{
			$wallet = Wallets::model()->findByPk($settings->id_wallet);
			$from_address = $wallet->wallet_address;
		}


		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'from_address'=>$from_address, // indirizzo del wallet dell'utente
		));
	}



	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Contacts the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Contacts::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Contacts $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='users-type-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}

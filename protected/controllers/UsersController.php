<?php
Yii::import('libs.crypt.crypt');
Yii::import('libs.NaPacks.Settings');
Yii::import('libs.NaPacks.Logo');

class UsersController extends Controller
{
	public function init()
	{
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
			//'postOnly + delete', // we only allow deletion via POST request
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
					'view', //visualizza dettagli socio
					'2fa', //abilita il 2fa
					'2faRemove', //rimuove il 2fa
					'saveSubscription', //salva lo sottoscrizinoe dell'user per le notifiche push
				),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{

		//carico il wallet selezionato nei settings
		$settings=Settings::loadUser(Yii::app()->user->objUser['id_user']);
		if (!(isset($settings->id_wallet))){
			$wallet_address = '0x0000000000000000000000000000000000000000';
		}else{
			$wallet = Wallets::model()->findByPk($settings->id_wallet);
			$wallet_address = $wallet->wallet_address;
		}

		$this->render('view',array(
			'model'=>$this->loadModel(crypt::Decrypt($id)),
			//'social'=>$social,
			'from_address'=>$wallet_address, // indirizzo del wallet dell'utente
		));
	}









	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function action2fa($id)
	{
		$model=$this->loadModel(crypt::Decrypt($id));

		if(isset($_POST['Users']))
		{
			$key = CHtml::encode($_POST['Users']['ga_cod']);
			$user  = Users::model()->findByPk(crypt::Decrypt($id));
			$ga = new GoogleAuthenticator();
			$checkResult = $ga->verifyCode($_POST['Users']['ga_secret_key'], $key, 2);    // 2 = 2*30sec clock tolerance

			if ($checkResult)
			{
				$model->ga_secret_key = $_POST['Users']['ga_secret_key'];

				if($model->save())
					$this->redirect(array('settings/index','id'=>crypt::Encrypt($model->id_user)));
			}
		}
		//carico il wallet selezionato nei settings
		$settings = Settings::loadUser(Yii::app()->user->objUser['id_user']);
		if (empty($settings->id_wallet)){
			$from_address = '0x0000000000000000000000000000000000000000';
		}else{
			$wallet = Wallets::model()->findByPk($settings->id_wallet);
			$from_address = $wallet->wallet_address;
		}

		$ga         = new GoogleAuthenticator();
        $secret     = $ga->createSecret();
        $qrCodeUrl  = $ga->getQRCodeGoogleUrl(Yii::app()->name, $secret);

        $this->render('2fa',array(
			'model'=>$model,
			'qrCodeUrl'=>$qrCodeUrl,
			'secret'=>$secret,
			'from_address'=>$from_address, // indirizzo del wallet dell'utente
		));
	}
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function action2faRemove($id)
	{
		$model=$this->loadModel(crypt::Decrypt($id));

		if(isset($_POST['Users']))
		{
			$key = CHtml::encode($_POST['Users']['ga_cod']);
			$user  = Users::model()->findByPk(crypt::Decrypt($id));
			$ga = new GoogleAuthenticator();
			$checkResult = $ga->verifyCode($user->ga_secret_key, $key, 2);    // 2 = 2*30sec clock tolerance

			if ($checkResult)
			{
				$model->ga_secret_key = NULL;
				if($model->save())
					$this->redirect(array('settings/index','id'=>crypt::Encrypt($model->id_user)));
			}
		}

		//carico il wallet selezionato nei settings
		$settings = Settings::loadUser(Yii::app()->user->objUser['id_user']);
		if (empty($settings->id_wallet)){
			$from_address = '0x0000000000000000000000000000000000000000';
		}else{
			$wallet = Wallets::model()->findByPk($settings->id_wallet);
			$from_address = $wallet->wallet_address;
		}

        $this->render('2faRemove',array(
			'model'=>$model,
			'from_address'=>$from_address, // indirizzo del wallet dell'utente
		));
	}





	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Users the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Users::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Users $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='users-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}




}

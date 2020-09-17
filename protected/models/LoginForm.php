<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;
	public $reCaptcha;
	//public $verifyCode;

	public $ga_cod;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		if (gethostname()=='CGF6135T'){
			$array =  array(
				// username and password are required
				array('username, password', 'required'),
				//ga_cod non è più required, ma facoltativo

				// password needs to be authenticated
				array('password', 'authenticate'),

				// username has to be a valid email address
				array('username', 'email'),

				// secret is required
				array('reCaptcha ', 'required'),
				// array('reCaptcha', 'application.extensions.reCaptcha2.SReCaptchaValidator', 'secret' => Settings::load()->reCaptcha2PrivateKey,'message' => 'The verification code is incorrect.'),
			);
        }else{
			$array =  array(
				// username and password are required
				array('username, password', 'required'),
				//ga_cod non è più required, ma facoltativo
				// password needs to be authenticated
				array('password', 'authenticate'),
				// username has to be a valid email address
				array('username', 'email'),
				// verifyCode needs to be entered correctly
				//array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
				// secret is required
				array('reCaptcha ', 'required'),
				array('reCaptcha', 'application.extensions.reCaptcha2.SReCaptchaValidator', 'secret' => Settings::load()->reCaptcha2PrivateKey,'message' => 'The verification code is incorrect.'),
			);
		}
		return $array;
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>'Email',
			'password'=>'Password',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 * @param string $attribute the name of the attribute to be validated.
	 * @param array $params additional parameters passed with rule when being executed.
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			// if(!$this->_identity->authenticate())
			//  	$this->addError('password','La password e/o l\'email non sono corrette.');
			$this->_identity->authenticate();
			$errorCode = $this->_identity->errorCode;

			switch ($errorCode){
				case UserIdentity::ERROR_PASSWORD_INVALID:
					$this->addError('password','La password non è corretta.');
					break;

				case UserIdentity::ERROR_USERNAME_INVALID:
					$this->addError('username','L\'email non è corretta.');
					break;

				case UserIdentity::ERROR_USERNAME_NOT_ACTIVE:
					$this->addError('password','L\'utente non è abilitato.');
					break;

				case UserIdentity::ERROR_GOOGLE_NOT_AUTHENTICATE:
					$this->addError('ga_cod','Il codice di Google Authenticator non è corretto.');
					break;

				case UserIdentity::ERROR_GOOGLE_NOT_ENABLED:
					$this->addError('ga_cod','L\'utente non ha abilitato il 2FA.');
					break;

				case UserIdentity::ERROR_USERNAME_NOT_PAYER:
					$this->addError('password',"Nessun pagamento trovato.");
					break;

				case UserIdentity::ERROR_USERNAME_NOT_MEMBER:
					$this->addError('password',"L'iscrizione è scaduta. Provvedere al pagamento della quota associativa per il rinnovo.");
					break;


			}
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=3600*24*90; // 90 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}

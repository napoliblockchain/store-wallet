
<?php

/**
 * This is the model class for table "np_settings".
 *
 * The followings are the available columns in table 'np_settings':
 * @property integer $id_exchanges
 * @property string $denomination
 * @property string $sshhost
 * @property string $sshuser
 * @property string $sshpassword
 *
 */
class SettingsWebappForm extends CFormModel
{
	//BTCPayServer
	public $BTCPayServerAddress;
	public $BTCPayServerAddressWebApp;

	//Exchange
	public $id_exchange;
	public $exchange_secret;
	public $exchange_key;
	public $only_for_bitstamp_id;

	//Associazione
	public $association_percent;
	public $association_receiving_address;
	public $quota_iscrizione_socio;
	public $quota_iscrizione_socioGiuridico;

	//POA TOKEN
	public $poa_url;
	public $poa_port;
	public $poa_expiration;
	public $poa_contractAddress;
	public $poa_abi;
	public $poa_bytecode;

	//sin per pairing con BTCPayServer
	public $sin;
	public $token;

	//server host
	public $sshhost;
	public $sshuser;
	public $sshpassword;
	public $rpchost;
	public $rpcport;

	//varie
	public $step;
	public $version;

	//GDPR
	public $gdpr_titolare;
	public $gdpr_vat;
	public $gdpr_address;
    public $gdpr_city;
    public $gdpr_country;
    public $gdpr_cap;
	public $gdpr_telefono;
	public $gdpr_fax;
	public $gdpr_email;
	public $gdpr_dpo_denomination;
	public $gdpr_dpo_email;
	public $gdpr_dpo_telefono;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('BTCPayServerAddress', 'required'),
			array('id_exchange, poa_expiration', 'numerical', 'integerOnly'=>true),
			array('quota_iscrizione_socio, quota_iscrizione_socioGiuridico', 'numerical', 'integerOnly'=>false),
			array('exchange_secret, exchange_key, association_receiving_address, poa_contractAddress', 'length', 'max'=>250),
			array('only_for_bitstamp_id, association_percent, poa_port', 'length', 'max'=>10),
			array('poa_url,BTCPayServerAddress,BTCPayServerAddressWebApp,version', 'length', 'max'=>50),
			array('sin,token,sshhost,sshuser,sshpassword,rpchost,rpcport', 'length', 'max'=>1000),
			array('poa_abi,poa_bytecode', 'length', 'max'=>15000),
			array('gdpr_titolare, gdpr_address, gdpr_city, gdpr_country, gdpr_cap, gdpr_dpo_denomination', 'length', 'max'=>250),
			array('gdpr_vat, gdpr_telefono, gdpr_fax, gdpr_email, gdpr_dpo_email, gdpr_dpo_telefono', 'length', 'max'=>50),
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_setting' => 'Id Impostazioni',
			'BTCPayServerAddress' => 'URL di BTCPay Server Utente',
			'BTCPayServerAddressWebApp' => 'URL di BTCPay Server Associazione',
			'id_exchange' => 'Id Exchange',
			'exchange_secret' => 'Chiave Segreta Exchange',
			'exchange_key' => 'Chiave Pubblica Exchange',
			'only_for_bitstamp_id'=>'Bitstamp ID Api',
			'association_percent'=>'Percentuale incasso sulle transazioni',
			'association_receiving_address'=>'Indirizzo BTC di ricezione',

			//
			'poa_url'=>'URL del nodo POA',
			'poa_port'=>'Porta del nodo POA',
			'poa_contractAddress'=>'Indirizzo dello Smart Contract',
			'poa_abi'=>'Smart Contract ABI',
			'poa_bytecode'=>'Smart Contract bytecode',
			'poa_expiration'=>'Il pagamento scade se l\'ammontare totale non è stato pagato dopo xxx minuti',
			//
			'version'=>'Versione applicazione',
			'quota_iscrizione_socio'=>'Quota Iscrizione (Persona Fisica)',
			'quota_iscrizione_socioGiuridico'=>'Quota Iscrizione (Persona Giuridica)',
			'sin'=>'SIN Pairing',
			'token'=>'Token Pairing',

			'sshhost' => 'Indirizzo tcp/ip Host VPS',
			'sshuser' => 'Utente ssh',
			'sshpassword'=>'Password',
			'rpchost' => 'Indirizzo tcp/ip Container Electrum',
			'rpcport' => 'Porta Container Electrum',

			'gdpr_titolare' =>'Titolare del trattamento (associazione)',
			'gdpr_address' =>'Indirizzo',
			'gdpr_vat' =>'Codice Fiscale',
			'gdpr_cap' =>'Cap',
			'gdpr_city' =>'Città',
			'gdpr_country' =>'Stato',
			'gdpr_telefono' =>'Telefono',
			'gdpr_fax' =>'Fax',
			'gdpr_email' => 'email Associazione',
			'gdpr_dpo_denomination' => 'Data Protection Officer (DPO)',
			'gdpr_dpo_email' => 'DPO email',
			'gdpr_dpo_telefono' => 'DPO Telefono',
		);
	}
}

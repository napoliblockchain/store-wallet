<?php

/**
 * This is the model class for table "np_transactions".
 *
 * The followings are the available columns in table 'np_transactions':
 */
class Transactions extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'np_transactions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('id_invoice, id_pos, id_merchant, status, btc_price, btc_due, price, currency, item_desc, item_code, id_invoice_bps, invoice_timestamp, expiration_timestamp, current_tempo, btc_paid, rate, bitcoin_address, token, satoshis_perbyte, total_fee', 'required'),
			array('status', 'required'),
			array('id_transaction,id_merchant,id_pos,id_bill,btc_price,btc_due,price,invoice_timestamp,expiration_timestamp,current_tempo,btc_paid,rate,satoshis_perbyte,total_fee', 'numerical', 'integerOnly'=>true),
			array('status', 'length', 'max'=>250),
			array('currency', 'length', 'max'=>10),
			array('token', 'length', 'max'=>5000),
			array('item_desc, item_code, id_invoice_bps, bitcoin_address ', 'length', 'max'=>60),

			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id_transaction, id_pos, id_merchant, status, btc_price, btc_due, price, currency, item_desc, item_code, id_invoice_bps, invoice_timestamp, expiration_timestamp, current_tempo, btc_paid, rate, bitcoin_address, token, satoshis_perbyte, total_fee', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_transaction' => '#',
			'id_pos' => 'Pos',
			'id_merchant' => 'Id Merchant',
			'status' => 'Stato',
			'btc_price' => 'Importo Btc',
			'btc_due' => 'Btc Dovuto',
			'price' => 'Importo',
			'currency' => 'Valuta',
			'item_desc' => 'Descrizione Oggetto',
			'item_code' => 'Codice Ordine',
			//'guid' => 'guid',
			'id_invoice_bps' => 'Codice Transazione',
			'invoice_timestamp' => 'Data emissione',
			'expiration_timestamp' => 'Scadenza',
			'current_tempo' => 'Orario Corrente',
			'btc_paid' => 'Btc Ricevuti',
			'rate' => 'Rate',
			'bitcoin_address' => 'Indirizzo',
			'token' => 'Token',
			'satoshis_perbyte' => 'Satoshis per byte',
			'total_fee' => 'Tassa Miner',
			'id_bill' => 'id fattura',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id_transaction',$this->id_transaction);
		$criteria->compare('id_pos',$this->id_pos,true);
		$criteria->compare('id_merchant',$this->id_merchant,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('btc_price',$this->btc_price,true);
		$criteria->compare('btc_due',$this->btc_due,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('currency',$this->currency,true);
		$criteria->compare('item_desc',$this->item_desc,true);
		$criteria->compare('item_code',$this->item_code,true);
		$criteria->compare('id_invoice_bps',$this->id_invoice_bps,true);
		$criteria->compare('invoice_timestamp',$this->invoice_timestamp,true);
		$criteria->compare('expiration_timestamp',$this->expiration_timestamp,true);
		$criteria->compare('current_tempo',$this->current_tempo,true);
		$criteria->compare('btc_paid',$this->btc_paid,true);
		$criteria->compare('rate',$this->rate,true);
		$criteria->compare('bitcoin_address',$this->bitcoin_address,true);
		$criteria->compare('token',$this->token,true);
		$criteria->compare('satoshis_perbyte',$this->satoshis_perbyte,true);
		$criteria->compare('total_fee',$this->total_fee,true);
		$criteria->compare('id_bill',$this->id_bill,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Stores the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

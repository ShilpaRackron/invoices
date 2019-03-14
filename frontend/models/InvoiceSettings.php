<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "invoice_settings".
 *
 * @property string $id
 * @property int $user_id
 * @property string $invoice_prefix
 * @property string $invoice_sufix
 * @property int $invoice_length
 */
class InvoiceSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'invoice_length','invoice_counter'], 'integer'],
            [['invoice_prefix','invoice_sufix'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'invoice_prefix' => 'Invoice Prefix',
            'invoice_sufix' => 'Invoice Sufix',
			'invoice_counter'=>'Starting Number',
            'invoice_length' => 'Invoice Length',
        ];
    }
	public static function getModel($user_id){
	   $invoiceSettingsObj = InvoiceSettings::findOne(['user_id' => $user_id]);
		if(!empty($invoiceSettingsObj))
			return $invoiceSettingsObj;
		else
			return new InvoiceSettings(); 
	}
	public function getInvoiceNumber($user_id){

		$invoiceSettingsObj = InvoiceSettings::findOne(['user_id' => $user_id]);
		$preFix = $invoiceSettingsObj->invoice_prefix;
		$sufix = $invoiceSettingsObj->invoice_sufix;
		$invoice_counter = $invoiceSettingsObj->invoice_counter;
		$length = $invoiceSettingsObj->invoice_length;
		$preFixText = ($preFix !="")?$preFix.'-':"";
		$sufixText = ($sufix !="")?'-'.$sufix:"";
		$invoiceNumber =$preFixText.str_pad($invoice_counter, $length, "0", STR_PAD_LEFT).$sufixText;
		return $invoiceNumber;
	}
	public function updateInvoiceNumber($user_id) {
		try{
		$invoiceSettingsObj						= InvoiceSettings::findOne(['user_id' => $user_id]);
		$invoice_counter						= (int)$invoiceSettingsObj->invoice_counter;
		$invoiceSettingsObj->invoice_counter	= $invoice_counter+1;
			if($invoiceSettingsObj->save()){
			} 		
		}catch(Exception $e){		
		}

		return true;
	}
	public function resetCounter($user_id){
		// Reset counter for Invoices on 1 days of each year
		$model = InvoiceSettings::find()->where(['user_id' => $user_id])->one();
		if($model && !empty($model)){
			$model->invoice_counter=001;
			$model->save();
		}
		return true;	
	}
}
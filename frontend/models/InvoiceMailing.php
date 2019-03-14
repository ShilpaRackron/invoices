<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "invoice_mailing".
 *
 * @property string $id
 * @property int $user_id
 * @property string $amazon_uk
 * @property string $amazon_de
 * @property string $amazon_es
 * @property string $amazon_fr
 * @property string $amazon_it
 * @property int $automatic_mailing
 */
class InvoiceMailing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_mailing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'automatic_mailing','automatic_reports_email'], 'integer'],
            [['amazon_uk', 'amazon_de', 'amazon_es', 'amazon_fr', 'amazon_it',], 'string'],
			[['report_months', 'report_send_day', 'next_send_date','report_start_date','inventory_report_day','inventory_report_send_date','inventory_month','automatic_inventory_email'], 'safe'],
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
            'amazon_uk' => 'Amazon Uk',
            'amazon_de' => 'Amazon De',
            'amazon_es' => 'Amazon Es',
            'amazon_fr' => 'Amazon Fr',
            'amazon_it' => 'Amazon It',
            'automatic_mailing' => 'Automatic Invoice Mailing',
			'automatic_reports_email'=>'Automatic Reports Mailing',
			'automatic_inventory_email'=>'Automatic Inventory Mailing',
			'report_months'=>'Report send Once in month(s)',
			'report_send_day'=>'Report send day of each month',
			'next_send_date' =>'Next Report Sending Date',
			'report_start_date'=>'Reports Start From',
			'inventory_report_day'=>'Inventory Report Send day of each month',
			'inventory_month'=>'Inventory Report send Once in month(s)',
			'inventory_report_send_date'=>'Inventory Next Report Send Date',
        ];
    }
	public static function getModel($user_id){
	   $invoiceMailingObj = InvoiceMailing::findOne(['user_id' => $user_id]);
		if(!empty($invoiceMailingObj))
			return $invoiceMailingObj;
		else
			return new InvoiceMailing(); 
	}
	public static function getEmailFooterText($user_id, $marketplaceId) {
	   	 $invoiceMailingObj = InvoiceMailing::findOne(['user_id' => $user_id]);
		 $footerText ="";
		 if(!empty($invoiceMailingObj)) {
		   switch($marketplaceId){		   
			case"Amazon.it":
			  $footerText = $invoiceMailingObj->amazon_it;
			   break;
			case"Amazon.co.uk":
			  $footerText = $invoiceMailingObj->amazon_uk;
			   break;
			case"Amazon.es":
			  $footerText = $invoiceMailingObj->amazon_es;
			   break;
			 
			 case"Amazon.fr":
			  $footerText = $invoiceMailingObj->amazon_fr;
			   break;
			  case"Amazon.de":
			  $footerText = $invoiceMailingObj->amazon_de;
			   break;			  
			  default:
			   $footerText ="";
		   }		 
		 }
		 return $footerText;
	}
}

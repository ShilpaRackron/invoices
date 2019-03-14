<?php

namespace frontend\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "company_info".
 *
 * @property string $id
 * @property int $user_id
 * @property string $company_header
 * @property string $company_logo
 * @property string $amazon_uk_footer
 * @property string $amazon_de_footer
 * @property string $amazon_es_footer
 * @property string $amazon_fr_footer
 * @property string $amazon_it_footer
 * @property int $is_active
 */
class CompanyInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'is_active'], 'integer'],
			[['company_logo'], 'file', 'extensions' => 'gif, jpg', 'mimeTypes' => 'image/jpeg, image/png',],			
            [['company_header', 'company_logo', 'amazon_uk_footer', 'amazon_de_footer', 'amazon_es_footer', 'amazon_fr_footer', 'amazon_it_footer','country','company_name','bank_name','company_name_in_bul','bank_ibn','bank_bic_swift','vat_article','vat_article_bul','vat_not_apply','vat_not_apply_bul','accountant_email','analytics_email','invoice_select_fields','creditmemo_selected_field','inventory_report_email'], 'string'],
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
            'company_header' => 'Company Header',
            'company_logo' => 'Company Logo',
            'amazon_uk_footer' => 'Amazon Uk Footer',
            'amazon_de_footer' => 'Amazon De Footer',
            'amazon_es_footer' => 'Amazon Es Footer',
            'amazon_fr_footer' => 'Amazon Fr Footer',
            'amazon_it_footer' => 'Amazon It Footer',
            'is_active' => 'Is Active',
			'country'=>'Seller Country',
			'company_name' =>"Company Name",
			'bank_name'=>'Bank Name',
			'company_name_in_bul'=>"Company Name In Bulgaria",
			'bank_ibn'=>'IBAN EURO',
			'bank_bic_swift'=>'BIC / Swift Code',
			'vat_article'=>'Vat Apply text',
			'vat_article_bul'=>'Vat apply text Bulgaria',
			'vat_not_apply'=>'Vat Not Apply Text',
			'vat_not_apply_bul'=>'Vat Not Apply Text Bulgaria',
			'accountant_email'=>'Report Receiver Accountant Emails',
			'analytics_email'=>'Report Receiver Analytics  Email',
			'inventory_report_email'=> 'Report Receiver Inventory Emails',
			'invoice_select_fields'=>'Select Email Fields for Invoices',
			'creditmemo_selected_field'=>'Select Email Fields for CreditNote',
        ];
    }
	public static function getModel($user_id) {
	   $companyObj = CompanyInfo::findOne(['user_id' => $user_id]);
		if(!empty($companyObj))
			return $companyObj;
		else
			return new CompanyInfo(); 
	}
}

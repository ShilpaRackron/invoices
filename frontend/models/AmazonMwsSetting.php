<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "amazon_mws_setting".
 *
 * @property string $id
 * @property int $user_id
 * @property string $mws_seller_id
 * @property string $mws_auth_token
 */
class AmazonMwsSetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amazon_mws_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'mws_seller_id', 'mws_auth_token'], 'required'],
			[['mws_seller_id', 'mws_auth_token'], 'unique'],
            [['user_id'], 'integer'],
			[['import_start_date','start_invoice_import'],'safe'],
            [['mws_seller_id', 'mws_auth_token'], 'string'],
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
            'mws_seller_id' => 'Seller ID',
            'mws_auth_token' => 'MWS Authorization Token',
			'import_start_date'=>'Invoice Import Start From',
			'start_invoice_import'=>'Enable Invoice Import'
        ];
    }
	public static function getModel($user_id){
	   $amazonMwsSettingObj = AmazonMwsSetting::findOne(['user_id' => $user_id]);
		if(!empty($amazonMwsSettingObj))
			return $amazonMwsSettingObj;
		else
			return new AmazonMwsSetting(); 
	}
	public static function getSellerInfo(){
		$user_id				= Yii::$app->user->id;
		$amazonMwsSettingObj	= AmazonMwsSetting::findOne(['user_id' => $user_id]);
		if(!empty($amazonMwsSettingObj))
			return $amazonMwsSettingObj;
		else
			return false;
	}
}

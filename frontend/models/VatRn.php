<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "vat_rn".
 *
 * @property string $id
 * @property int $user_id
 * @property string $country
 * @property string $rate_percentage
 * @property string $vat_no
 * @property string $central_bank
 */
class VatRn extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vat_rn';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'country', 'rate_percentage', 'vat_no', 'central_bank'], 'required'],
            [['user_id'], 'integer'],
            [['country'], 'string'],
            [['rate_percentage'], 'string', 'max' => 20],
            [['vat_no'], 'string', 'max' => 255],
            [['central_bank'], 'string', 'max' => 50],
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
            'country' => 'Country',
            'rate_percentage' => 'Rate Percentage',
            'vat_no' => 'Vat No',
            'central_bank' => 'Central Bank',
        ];
    }
	public static function getModel($user_id){
	   $vatRnObj = VatRn::findAll(['user_id' => $user_id]);
		if(!empty($vatRnObj))
			return $vatRnObj;
		else
			return new VatRn(); 
	}
	public static function checkExisting($user_id, $vat_id){
	   $vatRnObj = VatRn::findOne(['user_id' => $user_id,"id"=>$vat_id]);
		if(!empty($vatRnObj))
			return $vatRnObj;
		else
			return new VatRn(); 
	}

	public static function getUserVat($user_id, $country='default'){
	   $vatRnObj = VatRn::findOne(['user_id' => $user_id, 'country'=>$country]);
		if(!empty($vatRnObj))
			return $vatRnObj;
		else
			return new VatRn(); 
	}

	public function getSellerCountryVat($user_id, $country='default'){
	   $vatRnObj = VatRn::findOne(['user_id' => $user_id, 'country'=>$country]);
		if(!empty($vatRnObj))
			return $vatRnObj;
		else
			return new VatRn(); 
	}
}

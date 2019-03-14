<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "amazon_products".
 *
 * @property string $id
 * @property int $user_id
 * @property string $sku
 * @property string $asin
 * @property string $vat_id
 * @property string $product_name
 * @property double $vat_rate
 * @property double $value
 * @property string $comm_code 
 * @property string $condition_id
 */
class AmazonProducts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amazon_products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'sku'], 'required'],
            [['user_id'], 'integer'],
            [['product_name', 'comm_code'], 'string'],
            [['vat_rate', 'vat_value','price','weight'], 'number'],
            [['sku', 'asin', 'vat_id'], 'string', 'max' => 255],
            [['condition_id'], 'string', 'max' => 100],
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
            'sku' => 'SKU',
            'asin' => 'ASIN',
            'vat_id' => 'VAT',
            'product_name' => 'Name',
            'vat_rate' => 'VAT Rate',
            'vat_value' => 'Value',
            'comm_code' => 'Comm.Code',            
            'condition_id' => 'Condition ID',
			'weight'=>'Weight'
        ];
    }
	public function checkExistingProduct ($productAsin, $user_id){
		$checkData = AmazonProducts::findOne(['sku' => $productAsin,"user_id"=>$user_id]);
		return $checkData;		
	}
	public function getProductVat($productSku, $userId){
		$vatData = AmazonProducts::findOne(['sku' => $productSku,"user_id"=>$userId]);
		if(!empty($vatData)){
			return $vatData->vat_rate;	
		}
		return false;
	}
	public function getVat(){
		
		 return ($this->vat_id=="" || $this->vat_id==NULL)?"default":$this->vat_id;
	}

	public function getVatRate(){
		return ($this->vat_rate=="" || $this->vat_rate==NULL)?"default":$this->vat_rate."%";
		 //return "default";
	}
	public function getCommCode(){
	 return ($this->comm_code=="" || $this->comm_code==NULL)?"":$this->comm_code;
	}
}

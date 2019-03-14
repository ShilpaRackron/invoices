<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "amazon_inventory_adjustment".
 *
 * @property string $id
 * @property string $adjusted_date
 * @property string $transaction_item_id
 * @property string $fnsku
 * @property string $sku
 * @property string $product_name
 * @property string $fulfillment_center_id
 * @property int $quantity
 * @property string $reason
 * @property string $disposition
 * @property int $user_id
 */
class AmazonInventoryAdjustment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amazon_inventory_adjustment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['adjusted_date','need_purchase_invoice','requested_refund'], 'safe'],
            [['product_name', 'reason'], 'string'],
            [['quantity', 'user_id'], 'integer'],
            [['user_id'], 'required'],
            [['transaction_item_id', 'fnsku', 'sku', 'fulfillment_center_id', 'disposition'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'adjusted_date' => Yii::t('app', 'Adjusted Date'),
            'transaction_item_id' => Yii::t('app', 'Transaction Item ID'),
            'fnsku' => Yii::t('app', 'Fnsku'),
            'sku' => Yii::t('app', 'Sku'),
            'product_name' => Yii::t('app', 'Product Name'),
            'fulfillment_center_id' => Yii::t('app', 'Fulfillment Center ID'),
            'quantity' => Yii::t('app', 'Quantity'),
            'reason' => Yii::t('app', 'Reason'),
            'disposition' => Yii::t('app', 'Disposition'),
            'user_id' => Yii::t('app', 'User ID'),
			'requested_refund'=>Yii::t('app', 'Refund Requested'),
			'need_purchase_invoice'=>Yii::t('app', 'Need Purchase Invoice'),
        ];
    }

	function checkExistingProduct($sku, $user_id, $transaction_item_id)	{
		$checkData = AmazonInventoryAdjustment::findOne(['sku' => $sku, "user_id"=>$user_id,'transaction_item_id'=>$transaction_item_id]);
		if(!empty($checkData)){
			return true;
		}
		return false;	
	}
}

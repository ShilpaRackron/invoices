<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "amazon_inventory_summary".
 *
 * @property string $id
 * @property int $user_id
 * @property string $snapshot_date
 * @property string $transaction_type
 * @property string $fnsku
 * @property string $sku
 * @property string $product_name
 * @property string $fulfillment_center_id
 * @property int $quantity
 * @property string $disposition
 */
class AmazonInventorySummary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amazon_inventory_summary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'snapshot_date'], 'required'],
            [['user_id', 'quantity'], 'integer'],
            [['snapshot_date','transaction_type', 'fnsku', 'sku', 'product_name', 'fulfillment_center_id', 'quantity', 'disposition'], 'safe'],
            [['product_name', 'disposition'], 'string'],
            [['transaction_type', 'fnsku', 'sku', 'fulfillment_center_id'], 'string', 'max' => 255],
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
            'snapshot_date' => 'Snapshot Date',
            'transaction_type' => 'Transaction Type',
            'fnsku' => 'Fnsku',
            'sku' => 'Sku',
            'product_name' => 'Product Name',
            'fulfillment_center_id' => 'Fulfillment Center ID',
            'quantity' => 'Quantity',
            'disposition' => 'Disposition',
        ];
    }

	public function checkExistingProduct($sku, $user_id, $date){
		$checkData = AmazonInventorySummary::findOne(['sku' =>$sku, "user_id"=>$user_id, "snapshot_date"=>$date]);
		return $checkData;	
	}

}

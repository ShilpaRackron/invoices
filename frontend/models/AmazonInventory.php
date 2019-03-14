<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "amazon_inventory".
 *
 * @property string $id
 * @property int $user_id
 * @property string $marketplace
 * @property string $sku
 * @property string $fnsku
 * @property string $asin
 * @property string $product_name
 * @property string $product_condition
 * @property double $price
 * @property string $mfn_listing_exists
 * @property int $mfn_fulfillable_quantity
 * @property string $afn_listing_exists
 * @property int $afn_warehouse_quantity
 * @property int $afn_fulfillable_quantity
 * @property int $afn_unsellable_quantity
 * @property int $afn_reserved_quantity
 * @property int $afn_total_quantity
 * @property string $per_unit_volume
 * @property int $afn_inbound_working_quantity
 * @property int $afn_inbound_shipped_quantity
 * @property int $afn_inbound_receiving_quantity
 * @property string $import_date
 */
class AmazonInventory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amazon_inventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'import_date'], 'required'],
            [['user_id'], 'integer'],
            [['product_name'], 'string'],
            [['price'], 'number'],
            [['import_date', 'mfn_fulfillable_quantity', 'afn_warehouse_quantity', 'afn_fulfillable_quantity', 'afn_unsellable_quantity', 'afn_reserved_quantity', 'afn_total_quantity', 'afn_inbound_working_quantity', 'afn_inbound_shipped_quantity', 'afn_inbound_receiving_quantity'], 'safe'],
            [['marketplace', 'sku', 'fnsku', 'asin', 'product_condition', 'mfn_listing_exists', 'afn_listing_exists', 'per_unit_volume'], 'string', 'max' => 255],
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
            'marketplace' => 'Marketplace',
            'sku' => 'Sku',
            'fnsku' => 'Fnsku',
            'asin' => 'Asin',
            'product_name' => 'Product Name',
            'product_condition' => 'Product Condition',
            'price' => 'Price',
            'mfn_listing_exists' => 'Mfn Listing Exists',
            'mfn_fulfillable_quantity' => 'Mfn Fulfillable Quantity',
            'afn_listing_exists' => 'Afn Listing Exists',
            'afn_warehouse_quantity' => 'Afn Warehouse Quantity',
            'afn_fulfillable_quantity' => 'Afn Fulfillable Quantity',
            'afn_unsellable_quantity' => 'Afn Unsellable Quantity',
            'afn_reserved_quantity' => 'Afn Reserved Quantity',
            'afn_total_quantity' => 'Afn Total Quantity',
            'per_unit_volume' => 'Per Unit Volume',
            'afn_inbound_working_quantity' => 'Afn Inbound Working Quantity',
            'afn_inbound_shipped_quantity' => 'Afn Inbound Shipped Quantity',
            'afn_inbound_receiving_quantity' => 'Afn Inbound Receiving Quantity',
            'import_date' => 'Import Date',
        ];
    }
	public function checkExistingProduct($productSku, $user_id, $fulfillment_channel, $date=""){
		if($date==""){
			$date = date("Y-m-d");
		}
		$checkData = AmazonInventory::findOne(['sku' => $productSku, "user_id"=>$user_id,'marketplace'=>$fulfillment_channel,'import_date'=>$date]);
		return $checkData;
	}

	public function getInventoryQty($sku, $user_id,$date){

		$connection = Yii::$app->getDb();
		$tableName = $this->tableName();
		$command = $connection->createCommand("
			SELECT afn_total_quantity AS total_quantity
			FROM ".$tableName." 
			WHERE user_id = '".$user_id."'
			AND import_date = :date
			AND sku='".$sku."'
			GROUP BY sku", [':date' => $date]);

		$result = $command->queryAll();
		if(is_array($result) && !empty($result)){
			$total_quantity = (float)$result[0]['total_quantity'];
			return $total_quantity;
		}
		return '0';
	
	}
}

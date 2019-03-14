<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "amazon_inventory_data".
 *
 * @property string $id
 * @property int $user_id
 * @property string $snapshot_date
 * @property string $asin
 * @property string $fnsku
 * @property string $sku
 * @property string $product_name
 * @property int $total_quantity
 * @property int $sellable_quantity
 * @property int $unsellable_quantity
 * @property string $currency
 * @property double $your_price
 * @property double $sales_price
 * @property double $lowest_afn_new_price
 * @property double $lowest_mfn_new_price
 * @property string $import_date
 */
class AmazonInventoryData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amazon_inventory_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'total_quantity', 'sellable_quantity', 'unsellable_quantity'], 'integer'],
            [['snapshot_date', 'import_date'], 'safe'],
            [['product_name'], 'string'],
            [['your_price', 'sales_price', 'lowest_afn_new_price', 'lowest_mfn_new_price'], 'number'],
            [['asin', 'fnsku', 'sku'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'snapshot_date' => Yii::t('app', 'Snapshot Date'),
            'asin' => Yii::t('app', 'Asin'),
            'fnsku' => Yii::t('app', 'Fnsku'),
            'sku' => Yii::t('app', 'Sku'),
            'product_name' => Yii::t('app', 'Product Name'),
            'total_quantity' => Yii::t('app', 'Total Quantity'),
            'sellable_quantity' => Yii::t('app', 'Sellable Quantity'),
            'unsellable_quantity' => Yii::t('app', 'Unsellable Quantity'),
            'currency' => Yii::t('app', 'Currency'),
            'your_price' => Yii::t('app', 'Your Price'),
            'sales_price' => Yii::t('app', 'Sales Price'),
            'lowest_afn_new_price' => Yii::t('app', 'Lowest Afn New Price'),
            'lowest_mfn_new_price' => Yii::t('app', 'Lowest Mfn New Price'),
            'import_date' => Yii::t('app', 'Import Date'),
        ];
    }

	public function getInventoryQty($sku, $user_id,$date){

		$connection = Yii::$app->getDb();
		$tableName = $this->tableName();
		$command = $connection->createCommand("
			SELECT SUM(total_quantity) AS total_quantity
			FROM ".$tableName." 
			WHERE user_id = '".$user_id."'
			AND snapshot_date = :date
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

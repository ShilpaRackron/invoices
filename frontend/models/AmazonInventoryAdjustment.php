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

	 public $missing_check;
	 public $totalqty;
	 //public $reason;
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
            [['adjusted_date','need_purchase_invoice','requested_refund','missing_check','totalqty', 'disposition','reason'], 'safe'],
           // [['product_name', 'reason'], 'string'],
            [['quantity', 'user_id'], 'integer'],
            [['user_id'], 'required'],
            [['transaction_item_id', 'fnsku', 'sku', 'fulfillment_center_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'adjusted_date' => Yii::t('app', 'Date'),
            'transaction_item_id' => Yii::t('app', 'Item ID'),
            'fnsku' => Yii::t('app', 'Fnsku'),
            'sku' => Yii::t('app', 'Sku'),
            'product_name' => Yii::t('app', 'Product Name'),
            'fulfillment_center_id' => Yii::t('app', 'FC ID'),
            'quantity' => Yii::t('app', 'Qty'),
            'reason' => Yii::t('app', 'Reason'),
            'disposition' => Yii::t('app', 'Disp.'),
            'user_id' => Yii::t('app', 'User ID'),
			'requested_refund'=>Yii::t('app', 'Refund Requested'),
			'need_purchase_invoice'=>Yii::t('app', 'Need Purchase Invoice'),
			'missing_check'=>Yii::t('app', 'Check Missing Items'),
			'totalqty'=>Yii::t('app', 'Total Qty'),
        ];
    }
	public function getTotalQty($fnsku){
		$user_id = Yii::$app->user->id;	
	   $qtyData = AmazonInventoryAdjustment::find()->select(["SUM(quantity) as totalQty"])->where("fnsku = '{$fnsku}' AND user_id='{$user_id}'")->createCommand()->queryAll();
	   if(is_array($qtyData) && !empty($qtyData)){
		return $qtyData[0]['totalQty'];
	   }
	   return 0;
	}
	function checkExistingProduct($sku, $user_id, $transaction_item_id)	{
		$checkData = AmazonInventoryAdjustment::findOne(['sku' => $sku, "user_id"=>$user_id,'transaction_item_id'=>$transaction_item_id]);
		if(!empty($checkData)){
			return true;
		}
		return false;	
	}
	public function getInventoryStatus(){ 

		$status_array= ["1"=>'Software corrections (1)',"2"=>'Software corrections (2)',"3"=>'Catalog management (3)',"4"=>'Catalog management (4)',"5"=>'Unrecoverable inventory (5)',"6"=>'Damaged inventory (6)',"7"=>'Damaged inventory (7)',"D"=>'Unrecoverable inventory (D)',"E"=>'Damaged inventory (E)',"F"=>'Misplaced and found (F)',"H"=>'Damaged inventory (H)',"J"=>'Software corrections(J)',"K"=>'Damaged inventory (K)',"M"=>'Misplaced and found (M)',"N"=>'Transferring ownership (N)',"O"=>'Transferring ownership (O)',"P"=>'Damaged inventory (P)',"Q"=>'Other (Q)',"U"=>'Damaged inventory (U)'];
		return 	$status_array;
	
	}
	public function getReasonText($reason){	
		$reasonArray = $this->getInventoryStatus();
		if(isset($reasonArray[$reason])){
		 return $reasonArray[$reason];
		}
		return $reason;
	}
	public function getDateFormat($adjusted_date){
		if($adjusted_date){
			  $dt				=  \DateTime::createFromFormat('Y-m-d h:i:s', $adjusted_date);	
			  if($dt) {
				$adjusted_date		= $dt->format('d-m-Y h:i:s');
			  }
		}
	  return $adjusted_date;
	}
	
}

<?php

namespace frontend\models;
use frontend\models\AmazonOrders;
use Yii;

/**
 * This is the model class for table "credit_memo".
 *
 * @property string $id
 * @property int $user_id
 * @property string $amazon_order_id
 * @property double $total_amount_refund
 * @property string $date
 */
class CreditMemo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
	 public $order_type ;
	 public $year;
	 public $month;
	 public $country_code;
	 public $buyer_name;

    public static function tableName()
    {
        return 'credit_memo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           [['user_id','amazon_order_id'], 'required'],
			 [['amazon_order_id'], 'unique'],
            [['user_id', 'qty_return'], 'integer'],
            [['buyer_vat', 'seller_sku', 'markeplace', 'seller_order_id', 'order_adjustment_item_id', 'amazon_order_id', 'reason', 'status', 'license_plate_number', 'customer_comments', 'product_sku', 'product_name'], 'string'],
            [['total_amount_refund'], 'number'],
            [['invoice_number','return_date','order_type','order_import_date','credit_memo_no','year','month','creditmemo_email_date','creditmemo_email_sent','email_sending_type'], 'safe'],
            [['currency_code', 'asin', 'fnsku', 'fulfillment_center_id', 'detailed_disposition'], 'string', 'max' => 255],
        ];
    }
	public function getAmazon_orders(){
		return $this->hasOne(AmazonOrders::className(), ['id' => 'invoice_number']);
    
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'invoice_number' => 'Invoice Number',
            'buyer_vat' => 'Buyer Vat',
            'currency_code' => 'Currency Code',
            'seller_sku' => 'Seller Sku',
            'markeplace' => 'Markeplace',
            'seller_order_id' => 'Seller Order ID',
            'order_adjustment_item_id' => 'Order Adjustment Item ID',
            'amazon_order_id' => 'Amazon Order ID',
            'qty_return' => 'Qty Return',
            'total_amount_refund' => 'Amount',
            'return_date' => 'Return Date',
            'asin' => 'Asin',
            'fnsku' => 'Fnsku',
            'fulfillment_center_id' => 'Fulfillment Center ID',
            'detailed_disposition' => 'Detailed Disposition',
            'reason' => 'Reason',
            'status' => 'Status',
            'license_plate_number' => 'License Plate Number',
            'customer_comments' => 'Customer Comments',
            'product_sku' => 'Product Sku',
            'product_name' => 'Product Name',
			'order_type'=>'Type',
			'order_import_date'=>'Creditmemo Date',
			'credit_memo_no'=>'CreditMemo No',
			'creditmemo_email_sent'=>'Email Sent',
		   'creditmemo_email_date' =>'Creditmemo email Sent Date',
		   'email_sending_type'=>'Email Sending Type'
        ];
    }

	public function checkExistingOrder($amazon_order_id, $user_id){
		$checkData = CreditMemo::findOne(['amazon_order_id' => $amazon_order_id,'user_id'=>$user_id]);
		if($checkData){
			return $checkData;	
		}
		return false;
	}
	function getOrderRefundAmount($orderId,$qtyReturn){
		$user_id = Yii::$app->user->id; 
		//$qtyReturn = $this->qty_return;
		//$order_id  =$this->amazon_order_id;
		$amazonOrdersModel = new AmazonOrders();
		$orderData = $amazonOrdersModel->checkExistingOrder($orderId,$user_id);
		$orderReturnAmount =0;
		if(!empty($orderData)){
			$itemAmount = $orderData->item_price;
			$qtyShipped = $orderData->number_of_items_shipped;
			$itemPrice  = $itemAmount/$qtyShipped;
			$orderReturnAmount = "-".$qtyReturn*$itemPrice;
		}
		return $orderReturnAmount;
	}
	function getMarketPlace($orderId){
		$user_id = Yii::$app->user->id; 		
		//$orderId  =$this->amazon_order_id;
		$amazonOrdersModel = new AmazonOrders();
		$orderData = $amazonOrdersModel->checkExistingOrder($orderId,$user_id);
		$marketplace ="";
		if(!empty($orderData)){
			$marketplace = $orderData->marketplace_id;			
		}
		return $marketplace;
	}
	function getCurrencyCode($orderId){
		$user_id = Yii::$app->user->id; 		
		//$orderId  =$this->amazon_order_id;
		$amazonOrdersModel = new AmazonOrders();
		$orderData = $amazonOrdersModel->checkExistingOrder($orderId,$user_id);
		$order_currency ="";
		if(!empty($orderData)){
			$order_currency = $orderData->order_currency;			
		}
		return $order_currency;
	}

	function getOrderCurrencyCode($orderId, $user_id){
		$amazonOrdersModel = new AmazonOrders();
		$orderData = $amazonOrdersModel->checkExistingOrder($orderId,$user_id);
		$order_currency ="";
		if(!empty($orderData)){
			$order_currency = $orderData->order_currency;			
		}
		return $order_currency;
	}

	function getOrderType($orderId){
		$user_id = Yii::$app->user->id; 		
		//$orderId  =$this->amazon_order_id;
		$amazonOrdersModel = new AmazonOrders();
		$orderData = $amazonOrdersModel->checkExistingOrder($orderId,$user_id);
		$order_type ="";
		if(!empty($orderData)){
			$order_type = $orderData->fulfillment_channel;			
		}
		return $order_type;
	}
	public static function getYearsList($user_id) {
		$years = CreditMemo::find()->select('DISTINCT YEAR(`return_date`) as years')->where(["user_id"=>$user_id])->column();
		return array_combine($years, $years);
	}
	public function getCreditMemoInfo($user_id){
		/* $query = Yii::$app->getDb();
		$command = $query->createCommand("SELECT * FROM credit_memo LEFT JOIN amazon_orders ON amazon_orders.amazon_order_id =credit_memo.amazon_order_id WHERE credit_memo.user_id='".$user_id."' ORDER BY credit_memo_no DESC");
		$data = $command->queryAll();  */
		$data = CreditMemo::find()->select(['amazon_orders.*','credit_memo.*'])->join("LEFT JOIN",'amazon_orders', 'amazon_orders.amazon_order_id=credit_memo.amazon_order_id')->where(['credit_memo.user_id' => $user_id])->andWhere(["not",["credit_memo_no"=>NULL]]);

		return $data;
	}

	function getReturnOrderRefundAmount($orderId,$qtyReturn, $user_id){
		$amazonOrdersModel = new AmazonOrders();
		$orderData = $amazonOrdersModel->checkExistingOrder($orderId,$user_id);
		$orderReturnAmount =0;
		if(!empty($orderData)){
			$itemAmount = $orderData->item_price;
			$qtyShipped = $orderData->number_of_items_shipped;
			$itemPrice  = $itemAmount/$qtyShipped;
			$orderReturnAmount = "-".$qtyReturn*$itemPrice;
		}
		return $orderReturnAmount;
	}
}
<?php

namespace frontend\models;

use Yii;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "amazon_orders".
 *
 * @property string $id
 * @property int $user_id
 * @property string $latest_ship_date
 * @property string $order_type
 * @property string $purchase_date
 * @property string $buyer_email
 * @property string $amazon_order_id
 * @property string $is_replacement_order
 * @property string $last_update_date
 * @property int $number_of_items_shipped
 * @property string $ship_service_level
 * @property string $order_status
 * @property string $sales_channel
 * @property string $shipped_by_amazon_tfm
 * @property string $is_business_order
 * @property string $latest_delivery_date
 * @property int $number_of_items_unshipped
 * @property string $payment_method_detail
 * @property string $buyer_name
 * @property string $earliest_delivery_date
 * @property string $is_premium_order
 * @property string $order_currency
 * @property double $total_amount
 * @property string $earliest_ship_date
 * @property int $marketplace_id
 * @property string $fulfillment_channel
 * @property string $payment_method
 * @property string $city
 * @property string $address_type
 * @property string $postal_code
 * @property string $state_or_region
 * @property string $phone
 * @property string $country_code
 * @property string $customer_name
 * @property string $address_2
 * @property int $is_prime
 * @property string $shipment_category
 *@property sting $invoice_number
 *@property sting $buyer_vat
 */
class AmazonOrders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
	 public $year;
	 public $month;
    public static function tableName()
    {
        return 'amazon_orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'amazon_order_id'], 'required'],
			[['amazon_order_id'], 'unique'],
            [['latest_ship_date', 'purchase_date', 'last_update_date', 'latest_delivery_date', 'earliest_delivery_date', 'earliest_ship_date', 'marketplace_id', 'protocol_invoice_number', 'buyer_email','is_replacement_order', 'is_business_order', 'is_premium_order','product_sku','product_name','order_import_date','invoice_number','year','month','invoice_send_date','email_sending_type'], 'safe'],
            [['order_type', 'sales_channel', 'payment_method_detail', 'buyer_name', 'buyer_vat', 'fulfillment_channel', 'payment_method', 'customer_name', 'shipment_category', 'ship_address_1', 'ship_address_2', 'ship_address_3', 'tracking_number', 'carrier', 'fulfillment_center_id'], 'string'],
            [['total_amount', 'item_promotion_discount', 'ship_promotion_discount', 'item_price', 'item_tax', 'shipping_price', 'shipping_tax', 'gift_wrap_price', 'gift_wrap_tax'], 'number'],
            [['invoice_number', 'protocol_invoice_number', 'buyer_email', 'amazon_order_id', 'ship_service_level', 'order_status', 'shipped_by_amazon_tfm', 'marketplace_id', 'city', 'state_or_region', 'address_2', 'merchant_order_id', 'shipment_id', 'shipment_item_id', 'amazon_order_item_id', 'merchant_order_item_id', 'ship_city', 'ship_state', 'ship_postal_code', 'ship_country'], 'string', 'max' => 255],
            [['address_type'], 'string', 'max' => 100],
            [['order_currency', 'country_code'], 'string', 'max' => 10],
            [['postal_code', 'phone', 'ship_phone_number'], 'string', 'max' => 20],
			];
    }

	public function getCreditmemo(){
		return $this->hasOne(AmazonOrders::className(), ['id' => 'amazon_order_id']);
    
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_number' => 'Invoice Number',
            'protocol_invoice_number' => 'Protocol Invoice Number',
            'user_id' => 'User ID',
            'latest_ship_date' => 'Latest Ship Date',
            'order_type' => 'Order Type',
            'purchase_date' => 'Date',
            'buyer_email' => 'Buyer Email',
            'amazon_order_id' => 'Amazon Order ID',
            'is_replacement_order' => 'Is Replacement Order',
            'last_update_date' => 'Last Update Date',
            'number_of_items_shipped' => 'Number Of Items Shipped',
            'ship_service_level' => 'Ship Service Level',
            'order_status' => 'Order Status',
            'sales_channel' => 'Channel',
            'shipped_by_amazon_tfm' => 'Shipped By Amazon Tfm',
            'is_business_order' => 'Is Business Order',
            'latest_delivery_date' => 'Latest Delivery Date',
            'number_of_items_unshipped' => 'Number Of Items Unshipped',
            'payment_method_detail' => 'Payment Method Detail',
            'buyer_name' => 'Buyer Name',
            'buyer_vat' => 'Buyer Vat',
            'earliest_delivery_date' => 'Earliest Delivery Date',
            'is_premium_order' => 'Is Premium Order',
            'order_currency' => 'Order Currency',
            'total_amount' => 'Total',
            'earliest_ship_date' => 'Earliest Ship Date',
            'marketplace_id' => 'Marketplace ID',
            'fulfillment_channel' => 'Type',
            'payment_method' => 'Payment Method',
            'city' => 'City',
            'address_type' => 'Address Type',
            'postal_code' => 'Postal Code',
            'state_or_region' => 'State Or Region',
            'phone' => 'Phone',
            'country_code' => 'Country',
            'customer_name' => 'Customer Name',
            'address_2' => 'Address 2',
            'is_prime' => 'Is Prime',
            'shipment_category' => 'Shipment Category',
            'merchant_order_id' => 'Merchant Order ID',
            'shipment_id' => 'Shipment ID',
            'shipment_item_id' => 'Shipment Item ID',
            'amazon_order_item_id' => 'Amazon Order Item ID',
            'merchant_order_item_id' => 'Merchant Order Item ID',
            'ship_address_1' => 'Ship Address 1',
            'ship_address_2' => 'Ship Address 2',
            'ship_address_3' => 'Ship Address 3',
            'ship_city' => 'Ship City',
            'ship_state' => 'Ship State',
            'ship_postal_code' => 'Ship Postal Code',
            'ship_country' => 'Ship Country',
            'ship_phone_number' => 'Ship Phone Number',
            'item_promotion_discount' => 'Item Promotion Discount',
            'ship_promotion_discount' => 'Ship Promotion Discount',
            'tracking_number' => 'Tracking Number',
            'carrier' => 'Carrier',
            'fulfillment_center_id' => 'Warehouse',
            'item_price' => 'Amount',
            'item_tax' => 'Item Tax',
            'shipping_price' => 'Shipping Price',
            'shipping_tax' => 'Shipping Tax',
            'gift_wrap_price' => 'Gift Wrap Price',
            'gift_wrap_tax' => 'Gift Wrap Tax',
            'invoice_email_sent' => 'Email Sent',
			'product_sku'=>"SKU",
			'product_name'=>"Product Name",
			'order_import_date'=>'Invoice Date',
			'invoice_send_date'=>'Email Sent Date',
			'email_sending_type'=>'Email Sending Type'
        ];
    }
	public function checkExistingOrder($amazon_order_id, $user_id){
		$checkData = AmazonOrders::findOne(['amazon_order_id' => $amazon_order_id, "user_id"=>$user_id]);
		return $checkData;	
	}
	public function getOrdersByDate($purchase_date, $user_id){
	
		//$orderData = AmazonOrders::findAll(['purchase_date' =>$purchase_date,'user_id'=>$user_id]);
		$orderData = AmazonOrders::find()
				->where(['AND', "order_import_date='".$purchase_date."'"])
				->andWhere(['and', "user_id='$user_id'"])->all();
		//echo $orderData->createCommand()->getRawSql();
		//die();
		return $orderData;
	}
	public function getMonthlyOrders($currenttMonth, $todayDate, $user_id){
	
		$orderData = AmazonOrders::find()
				->where(['and', "order_import_date >='$currenttMonth'", "order_import_date<='$todayDate'"])
				->andWhere(['and', "user_id='$user_id'"])->all();
			//echo $orderData->createCommand()->getRawSql();

		return $orderData;
	}
	public static function getYearsList($user_id) {
		$years = AmazonOrders::find()->select('DISTINCT YEAR(`order_import_date`) as years')->where(["user_id"=>$user_id])->column();
		return array_combine($years, $years);
	}
	public function getChartData($user_id){
		$connection = Yii::$app->getDb();
		 $date = date('Y-m-d', strtotime("-1 year"));
		$command = $connection->createCommand("
			SELECT YEAR(latest_ship_date) AS y, MONTH(latest_ship_date) AS m, COUNT(DISTINCT amazon_order_id) as toltalorders, SUM(item_price) as totalsale FROM amazon_orders WHERE  latest_ship_date >$date AND user_id =$user_id GROUP BY y, m");
		$result = $command->queryAll();
		$countArray = [];
		if(!empty($result)){
			
			foreach($result as $key=>$data){
				
				$months =  $data['y'].'-'.str_pad($data['m'],2,0,STR_PAD_LEFT);
				$totalOrders =$data['toltalorders'];
				$totalSale =$data['totalsale'];
			   $countArray[$months] =trim($totalOrders);
			}		
		}		
		return $countArray;
	}

	public function getChartSaleData($user_id){
		$connection = Yii::$app->getDb();
		 $date = date('Y-m-d', strtotime("-1 year"));
		$command = $connection->createCommand("
			SELECT YEAR(latest_ship_date) AS y, MONTH(latest_ship_date) AS m, COUNT(DISTINCT amazon_order_id) as toltalorders, SUM(item_price) as totalsale FROM amazon_orders WHERE latest_ship_date >$date AND user_id =$user_id GROUP BY y, m");
		$result = $command->queryAll();
		$countArray = [];
		if(!empty($result)){		
			foreach($result as $key=>$data){
				$months =  $data['y'].'-'.str_pad($data['m'],2,0,STR_PAD_LEFT);
				$totalSale =$data['totalsale'];
			    $countArray[$months] =trim($totalSale);
			}		
		}
		return $countArray;
	}

	public function getYearTotalSale($user_id,$country,$year){
	   $connection = Yii::$app->getDb();
	   $country =($country=='default')?"IT":$country;
	   $command = $connection->createCommand("SELECT YEAR(latest_ship_date) AS y, SUM(item_price) as totalsale FROM amazon_orders WHERE YEAR(latest_ship_date)=$year AND user_id='".$user_id."' AND country_code='".$country."' GROUP BY y");
		$result = $command->queryAll();
		$totalSale =0;
		if(!empty($result)){		
			foreach($result as $key=>$data){
				$totalSale =$totalSale+$data['totalsale'];			    
			}		
		}
		return $totalSale;
	}
	public function getOrderCountries($user_id, $year){
	   $connection = Yii::$app->getDb();
	   $command = $connection->createCommand("SELECT country_code, SUM(item_price) as totalsale FROM amazon_orders WHERE YEAR(latest_ship_date)='".$year."' AND user_id='".$user_id."' GROUP BY country_code");
		$result = $command->queryAll();
		
		$totalSale =[];
		if(!empty($result)){		
			foreach($result as $key=>$data){
				$totalSale[$data['country_code']] = $data['totalsale'];
			}		
		}		
		return $totalSale;	
	}
	public function getSalesInventory($sku, $user_id, $date,$invoice_email_sent =1){

		list($star_date, $end_date)= explode(' - ', $date);
		$connection = Yii::$app->getDb();
		$tableName = $this->tableName();
		if($invoice_email_sent==NULL){
			$command = $connection->createCommand("
			SELECT SUM(item_price) AS total_amount, SUM(item_tax) as total_tax
			FROM ".$tableName." 
			WHERE user_id = '".$user_id."'
			AND order_import_date BETWEEN :start_date AND :end_date
			AND product_sku='".$sku."'
			AND (invoice_email_sent IS NULL OR invoice_email_sent=0)
			GROUP BY product_sku", [':start_date' => $star_date,':end_date'=>$end_date]);

		}else{
		$command = $connection->createCommand("
			SELECT SUM(item_price) AS total_amount, SUM(item_tax) as total_tax
			FROM ".$tableName." 
			WHERE user_id = '".$user_id."'
			AND order_import_date BETWEEN :start_date AND :end_date
			AND product_sku='".$sku."'
			AND invoice_email_sent='".$invoice_email_sent."'
			GROUP BY product_sku", [':start_date' => $star_date,':end_date'=>$end_date]);
		}
		$result = $command->queryAll();
		if(is_array($result) && !empty($result)){
			$totalAmount = (float)$result[0]['total_amount'];
			return $totalAmount;
		}
		return '0';
	}
	

	public function getInventoryQty($sku, $user_id, $date,$invoice_email_sent =1){

		list($star_date, $end_date)= explode(' - ', $date);
		$connection = Yii::$app->getDb();
		$tableName = $this->tableName();
		$command = $connection->createCommand("
			SELECT SUM(number_of_items_shipped) AS qty
			FROM ".$tableName." 
			WHERE user_id = '".$user_id."'
			AND order_import_date BETWEEN :start_date AND :end_date
			AND product_sku='".$sku."'			
			GROUP BY product_sku", [':start_date' => $star_date,':end_date'=>$end_date]);

		$result = $command->queryAll();
		if(is_array($result) && !empty($result)){
			$totalAmount = (float)$result[0]['qty'];
			return $totalAmount;
		}
		return '0';
	}	


	public function getInvoiceData($sku, $user_id, $date) {

		list($star_date, $end_date)= explode(' - ', $date);
		$connection = Yii::$app->getDb();
		$tableName = $this->tableName();
		$command = $connection->createCommand("
			SELECT SUM(item_tax) AS tax, buyer_vat as vat_percentage
			FROM ".$tableName." 
			WHERE user_id = '".$user_id."'
			AND order_import_date BETWEEN :start_date AND :end_date
			AND product_sku='".$sku."'			
			GROUP BY product_sku", [':start_date' => $star_date,':end_date'=>$end_date]);

		$result = $command->queryAll();
		
		if(is_array($result) && !empty($result)){
			
			return $result[0];
		}
		return [];
	}	


}

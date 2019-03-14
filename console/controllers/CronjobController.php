<?php
  namespace console\controllers;
  
use Yii;
use yii\console\Controller; 
use frontend\models\User;
use frontend\models\UserSearch;
use frontend\models\AmazonOrders;
use frontend\models\AmazonProducts;
use frontend\models\AmazonMwsSetting;
use frontend\models\CompanyInfo;
use frontend\models\InvoiceMailing;
use frontend\models\InvoiceSettings;
use frontend\models\VatRn;
use frontend\models\AmazonLogInfo;
use frontend\models\InvoiceCronjob;
use frontend\models\CreditMemo;
use frontend\models\CreditmemoSettings;
use frontend\models\AmazonReportInfo;
use frontend\models\AmazonInventory;
use frontend\models\AmazonInventorySearch;
use frontend\models\AmazonOrdersSearch;
use frontend\models\CreditMemoSearch;
use frontend\models\AmazonInventorySummary;
use frontend\models\AmazonInventoryData;
use yii2tech\spreadsheet\Spreadsheet;
use frontend\models\AmazonInventoryDataSearch;	
use MCS\MWSClient;
use DateTime;
use Exception;
use DateTimeZone;
use Mpdf\Mpdf;
use DOMDocument;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ArrayDataProvider;

use frontend\models\AmazonInventoryAdjustment;	
use frontend\models\AmazonInventoryAdjustmentSearch;	
use frontend\models\AmazonReimbursements;	
use frontend\models\AmazonReimbursementsSearch;


class CronjobController extends Controller {

    public function actionIndex() {
        echo "cron service runnning";
    }

   

	public function getamazonClientInfo($user_id) {

		   
		$mwsSettingsobj		= AmazonMwsSetting::getModel($user_id);
		if($mwsSettingsobj== false){
			echo "Your MWS Seller information not completed. Please complete";
		}
		
		if($mwsSettingsobj->mws_seller_id !=""){
			$mws_seller_id = $mwsSettingsobj->mws_seller_id;
			$mws_auth_token = $mwsSettingsobj->mws_auth_token;
			$client = new MWSClient([
			'Marketplace_Id' => Yii::$app->params['marketplace_id'],
			'Seller_Id' => $mws_seller_id, //'AJ08MYWRY2147',
			'Access_Key_ID' => Yii::$app->params['aws_access_key'],
			'Secret_Access_Key' => Yii::$app->params['aws_secret_key'],
			'MWSAuthToken' => $mws_auth_token]);
			return 	$client;
		}else{
			return false;
		}

	}
	public function startCronJob($user_id, $cronjobName=""){
	   $cronObj					= new InvoiceCronjob();
	   $cronObj->user_id		= $user_id;
	   $cronObj->cronjob_name	= $cronjobName;
	   $cronObj->start_time		= date("Y-m-d H:i:s");
	   $cronObj->status			= 'started';
	   if($cronObj->save()){
		return $cronObj->id;
	   }
	}
	public function endCronJob($cronid){
	   $cronObj					= InvoiceCronjob::findOne(["id"=>$cronid]);
	   if($cronObj){
		   $cronObj->end_time		= date("Y-m-d H:i:s");
		   $cronObj->status			= 'Completed';
		   if($cronObj->save()){
				return $cronObj->id;
		   }
	   }

	}
		
	// Set this cronjob for  AFN orders to run import old data approx 1  year. This cronjob job must need to schecdule only once.
	public function actionCreateordersreport() {
		   $getUsers = User::find()->where(["=","status","1"])->andWhere(['=','import_initial_orders', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {
			try{
			$user_id = 	$userData->id;
			
			$client = $this->getamazonClientInfo($user_id);
			if ($client && $client->validateCredentials()) {
				$mwsSettingsobj		= AmazonMwsSetting::getModel($user_id);
				$import_start_date  = $mwsSettingsobj->import_start_date;
				if($import_start_date =="" || $import_start_date==NULL){
					continue;
				}
				$i =1;
				$date1 = $import_start_date; 
				$date2 = date('Y-m-d');
				$ts1 = strtotime($date1);
				$ts2 = strtotime($date2);
				$year1 = date('Y', $ts1);
				$year2 = date('Y', $ts2);
				$month1 = date('m', $ts1);
				$month2 = date('m', $ts2);
				$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
				$day1    = date('d', $ts1);
				$day2    = date('d', $ts2);
				$dayDiff  =	$day2-$day1;
				if($dayDiff >0){
					 $diff= $diff+1;
				}
				$todaydate = $date1;
				if($diff >1) {
					for($i=1; $i<=$diff; $i++) {
						
						$time		= strtotime($todaydate);
						$toDate		= date("Y-m-d", strtotime("+29 days", $time));
						$fromDate	= new DateTime($todaydate);	   // '2018-01-01'
						$fromDate->setTimeZone(new DateTimeZone('GMT'));
						$todayTime = date('Y-m-d');
						$toDate   = ($toDate>$todayTime)?$todayTime:$toDate;
						$endDate	= new DateTime($toDate);
						echo "Creating report for User $user_id from $todaydate to $toDate";

						$cron_id = $this->startCronJob($user_id,"Create Invoice Report for User $user_id from $todaydate to $endDate");
						$report_id = $client->RequestReport('_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_',$fromDate, $endDate,true);
						//$amazonReportInfoModel = new AmazonReportInfo();
						if($report_id){
							$this->savereportdata($report_id, '_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_','SUBMITTED',$user_id,$todaydate, $toDate);
							echo "Report _GET_AMAZON_FULFILLED_SHIPMENTS_DATA_ submit completed User $user_id";
						}
						sleep(300);
						// Create MFN report
						$report_id2 = $client->RequestReport('_GET_FLAT_FILE_ORDERS_DATA_',$fromDate, $endDate, true);
						if($report_id2){
							$this->savereportdata($report_id2, '_GET_FLAT_FILE_ORDERS_DATA_','SUBMITTED',$user_id,$todaydate, $toDate);
							echo "Report _GET_FLAT_FILE_ORDERS_DATA_ submit completed User $user_id";
						}
						$toDate		= date("Y-m-d", strtotime("+1 days", strtotime($toDate)));
						$todaydate = $toDate;
						sleep(300);					
					}
				} else{
				
						$time		= strtotime($todaydate);
						$toDate		= date("Y-m-d");
						$fromDate	= new DateTime($todaydate);	   // '2018-01-01'
						$fromDate->setTimeZone(new DateTimeZone('GMT'));
						$endDate	= NULL; 
						echo "Creating report for User $user_id from $todaydate to $toDate";

						$cron_id = $this->startCronJob($user_id,"Create Invoice Report for User $user_id");
						$report_id = $client->RequestReport('_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_',$fromDate, $endDate,true);
						//$amazonReportInfoModel = new AmazonReportInfo();
						if($report_id){
							$this->savereportdata($report_id, '_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_','SUBMITTED',$user_id,$todaydate, $toDate);
							echo "Report _GET_AMAZON_FULFILLED_SHIPMENTS_DATA_ submit completed User $user_id";
						}
						sleep(300);
						// Create MFN report
						$report_id2 = $client->RequestReport('_GET_FLAT_FILE_ORDERS_DATA_',$fromDate, $endDate, true);
						if($report_id2){
							$this->savereportdata($report_id2, '_GET_FLAT_FILE_ORDERS_DATA_','SUBMITTED',$user_id,$todaydate, $toDate);
							echo "Report _GET_FLAT_FILE_ORDERS_DATA_ submit completed User $user_id";
						} 
						//$todaydate = $toDate;
						sleep(300);	
				}
				$this->updateUserStatus($user_id,'import_initial_orders');
				echo "Report generated successfully";
				$this->endCronJob($cron_id);
			}
			else {
				//Yii::$app->session->setFlash('error', "Amazion API information not validate");
				//die("Amazion API information not validate");
			}
			}catch(Exception $e){
				echo "<pre>";
				print_r($e->getMessage());
				//die();
				//continue;
				sleep(300);
			}
		}
		echo "DONE";
		 return true;
	}


	// Set this cronjob for AFN orders to run after each 4 hours
	public function actionCreatedailyreport() {
		   $getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {
			try{
			$user_id = 	$userData->id;
			
			$client = $this->getamazonClientInfo($user_id);
			if ($client && $client->validateCredentials()) {
									
					//$todaydate = date("Y-m-d H:i:s", strtotime("-11 hours"));;				
					$todaydate = date("Y-m-d H:i:s", strtotime("-4 days"));				
					echo "Creating daily report for User $user_id ";
					$fromDate	= new DateTime($todaydate);	   // '2018-01-01'
					$fromDate->setTimeZone(new DateTimeZone('GMT'));
					$endDateTime	= date("Y-m-d H:i:s"); //Null;
					$endDate	= new DateTime($endDateTime);	   // '2018-01-01'
					$endDate->setTimeZone(new DateTimeZone('GMT'));

					$cron_id = $this->startCronJob($user_id,"Create daily Invoice Report for User $user_id at $todaydate for _GET_AMAZON_FULFILLED_SHIPMENTS_DATA_ AND _GET_FLAT_FILE_ORDERS_DATA_");
					$report_id = $client->RequestReport('_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_',$fromDate, $endDate,true);
					$amazonReportInfoModel = new AmazonReportInfo();
					if($report_id){
						$this->savereportdata($report_id, '_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_','SUBMITTED',$user_id,$todaydate, $endDateTime);
						echo "Report submit completed User $user_id";
					}
					sleep(200);
					// Create MFN report for User
					$report_id2 = $client->RequestReport('_GET_FLAT_FILE_ORDERS_DATA_',$fromDate, $endDate, true);
					if($report_id2){
						$this->savereportdata($report_id2, '_GET_FLAT_FILE_ORDERS_DATA_','SUBMITTED',$user_id,$todaydate, $endDateTime);
						echo "Report submit completed User $user_id";
					}

					//$todaydate = $toDate; 
					sleep(200);
				echo "Report generated successfully";
				$this->endCronJob($cron_id);
			}			
			}catch(Exception $e){
				//echo "<pre>";
				//print_r($e->getMessage());
				//die();
				//continue;
			}
		}
		echo "DONE";
		 return true;
	}
	public function savereportdata($report_id, $report_type, $report_status, $user_id,$start_date,$end_date, $marketplace=NULL){
			$amazonReportInfoModel = new AmazonReportInfo();
			$amazonReportInfoModel->user_id = $user_id;
			$amazonReportInfoModel->report_id = $report_id;
			$amazonReportInfoModel->report_type = $report_type;
			$amazonReportInfoModel->report_status = $report_status;
			$amazonReportInfoModel->date_created = date('Y-m-d H:i:s');
			$amazonReportInfoModel->start_date = $start_date;
			$amazonReportInfoModel->marketplace =$marketplace;
			$amazonReportInfoModel->end_date = ($end_date!=NULL)?$end_date:date('Y-m-d H:i:s');
			if($amazonReportInfoModel->save()){
				//return true;
			}else{
				echo "Error in saving report data";
			}
			 return true;
		}

		// get AFN Orders information
		 public function actionGetorderdata($counter =0) {
		  
		 $getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			
			foreach($getUsers as  $key=>$userData) {
			$user_id	= 	$userData->id;
			
			 $cron_id = $this->startCronJob($user_id,"Get Order reports data for User $user_id");
			 $client	= $this->getamazonClientInfo($user_id);
			 $orderApiData =[];
			 if ($client && $client->validateCredentials()) {
				 echo "\\ Checking user for  ID $user_id";
				$amazonReportInfoModel = new AmazonReportInfo();
				$reportData =  $amazonReportInfoModel->getReportInfo($user_id, ['_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_','_GET_FLAT_FILE_ORDERS_DATA_']);

			 if(!empty($reportData)) {
				 foreach($reportData as $key=>$reportInfo) {
					 try{
					 $id        = $reportInfo->id;
					 $report_id = $reportInfo->report_id;
					 //$user_id   = $reportInfo->user_id;
					 $reportType = $reportInfo->report_type;
					 echo "\n\n report type= $reportType Stated"; 
					 $reportApiData = $client->GetReport($report_id);

					 if(!is_array($reportApiData) && $reportApiData =="_CANCELLED_"){
					   echo "\n\nCancelled for user $user_id with Report id $report_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_CANCELLED_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(!is_array($reportApiData) && $reportApiData =="_DONE_NO_DATA_"){
						 echo "\n\nNo data found for report id $report_id for userid $user_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_DONE_NO_DATA_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(is_array($reportApiData) && !empty($reportApiData)){
						echo "\n\nReported started imported for userid $user_id";
						echo "\n\n report type= $reportType";
						
						
						if($reportType=='_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_'){
							echo "starting import data";
						  $completed = $this->importOrdersByApi($reportApiData, $user_id);
						  if($completed) {
							$amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
							$amazonReportInfoModelObj->report_status ='_DONE_';
							$amazonReportInfoModelObj->state =1;
							$amazonReportInfoModelObj->save();
						   }
						}else{
						   $completed = $this->importFlatOrdersByApi($reportApiData, $user_id);
						   if($completed){
						    $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
							$amazonReportInfoModelObj->report_status ='_DONE_';
							$amazonReportInfoModelObj->state =1;
							$amazonReportInfoModelObj->save();
						   }
						}
						
						echo "\n\nReport completed";
					 }else{
					 }
					 sleep(240);
					 } catch(Exception $e){
						// print_r($e->getMessage());
						 // Increase waiting time if Throlled occured
						 //sleep(240);
						//continue;
					}
				}
			 }
			} 
			$this->endCronJob($cron_id);
		  }
		  echo "Waiting to check the data";
		  sleep(240);
		  echo "Checking reports if still need import";
		$checkReports  =  AmazonReportInfo::findOne(["report_type"=>['_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_','_GET_FLAT_FILE_ORDERS_DATA_'],"state"=>'0']);
		if(($checkReports && !empty($checkReports) ) && $counter < 4){
			// run the reports untill finish
			echo "Reports still not finished";
			$counter = $counter+1;
			$this->actionGetorderdata($counter);
		}
		 //$this->updateInvoices();
		  echo "\n\ncronjob Completed successfully";
		  return true;
	  }
		// get MFN Orders information
	  

	 public function importFlatOrdersByApi($orders, $user_id) {		
		if(!empty($orders)){
			$invoiceSettings = new InvoiceSettings();
			$ordersData = $orders;//$orders['ListOrders'];
				foreach ($ordersData as $key=>$order) {					
				 
				$amazonOrders		= new AmazonOrders();
				$checkIfOrderExist	= $amazonOrders->checkExistingOrder($order['order-id'], $user_id);
				if(!empty($checkIfOrderExist)) {
					$amazonOrders =  $checkIfOrderExist;
				}
				$amazonOrders->user_id						=$user_id;
				$amazonOrders->latest_ship_date				=(isset($order['shipment-date']))?date('Y-m-d h:i:s', strtotime($order['shipment-date'])):date('Y-m-d h:i:s', strtotime($order['purchase-date']));
				$amazonOrders->order_type					=$order['ship-service-level'];
				$amazonOrders->purchase_date				=(isset($order['purchase-date'])&& $order['purchase-date'] !="")?date('Y-m-d h:i:s', strtotime($order['purchase-date'])):"";
				$amazonOrders->buyer_email					=$order['buyer-email'];
				$channelArray								= explode('@marketplace.',$order['buyer-email']);
				$amazonOrders->sales_channel				=(isset($channelArray[1]) && !empty($channelArray[1]))?$channelArray[1]:"";
				$amazonOrders->amazon_order_id				= $order['order-id'];
				$amazonOrders->merchant_order_id			= $order['order-id'];
				$amazonOrders->is_replacement_order			= false;
				$amazonOrders->last_update_date				=""; 
				$amazonOrders->number_of_items_shipped		=$order['quantity-purchased'];
				$amazonOrders->ship_service_level			=$order['ship-service-level'];
				$amazonOrders->order_status					='Shipped';
				//$amazonOrders->sales_channel				="";
				$amazonOrders->shipped_by_amazon_tfm		= "";
				$amazonOrders->is_business_order			=false;
				$amazonOrders->latest_delivery_date			=(isset($order['delivery-end-date']) && !empty($order['delivery-end-date']))?date('Y-m-d h:i:s', strtotime($order['delivery-end-date'])):""; 
				$amazonOrders->number_of_items_unshipped	=0;
				$amazonOrders->payment_method_detail		="";
				$amazonOrders->buyer_name					=$order['buyer-name'];
				$amazonOrders->earliest_delivery_date		= (isset($order['delivery-end-date']) && !empty($order['delivery-end-date']))?date('Y-m-d h:i:s', strtotime($order['delivery-end-date'])):""; 
				$amazonOrders->is_premium_order				=0;  //$order['IsPremiumOrder'];
				$itemPrice									= $order['item-price'];
				$amazonOrders->order_currency				=$order['currency'];
				$amazonOrders->item_tax						=$order['item-tax'];
				$amazonOrders->shipping_price				= $order['shipping-price'];
				$amazonOrders->shipping_tax					= $order['shipping-tax'];				
				$amazonOrders->ship_address_1				= htmlspecialchars($order['ship-address-1']);
				$amazonOrders->ship_address_2				= htmlspecialchars($order['ship-address-2']);
				$amazonOrders->ship_address_3				= htmlspecialchars($order['ship-address-3']);
				$amazonOrders->ship_city					= $order['ship-city'];
				$amazonOrders->ship_state					= $order['ship-state'];
				$amazonOrders->ship_postal_code				= $order['ship-postal-code'];
				$amazonOrders->ship_country					= $order['ship-country'];
				$amazonOrders->ship_phone_number			= $order['ship-phone-number'];
				$amazonOrders->total_amount					= $itemPrice;
				$amazonOrders->earliest_ship_date			=(isset($order['delivery-end-date']) && !empty($order['delivery-end-date']))?date('Y-m-d h:i:s', strtotime($order['delivery-end-date'])):""; 
				$amazonOrders->marketplace_id				=""; //$order['MarketplaceId'];
				$amazonOrders->fulfillment_channel			="MFN";
				$amazonOrders->payment_method				="";
				
				$amazonOrders->city							=$order['ship-city'];
				$amazonOrders->address_type					="";
				$amazonOrders->postal_code					=$order['ship-postal-code'];
				$amazonOrders->state_or_region				= $order['ship-state'];
				$amazonOrders->phone						= $order['buyer-phone-number'];
				$amazonOrders->country_code					= $order['ship-country'];
				$amazonOrders->customer_name				= $order['recipient-name'];
				$amazonOrders->address_2					= htmlspecialchars($order['ship-address-1']).' '.htmlspecialchars($order['ship-address-2']);
				$amazonOrders->tracking_number				= "";
				$amazonOrders->carrier						= "";
				$amazonOrders->item_price					= $order['item-price'];
				$amazonOrders->item_promotion_discount		= 0;
				$amazonOrders->ship_promotion_discount		= 0;

				$productSku							= $order['sku'];
				$productName						= utf8_encode($order['product-name']);
				$amazonOrders->product_sku			= $productSku;
				$amazonOrders->product_name			= $productName;
				//$amazonOrders->order_import_date	= date("Y-m-d H:i:s");
				$date1								= $amazonOrders->latest_ship_date;
				$date2								= date('Y-m-d');
				$dateObj1							= date_create($date1);
				$dateObj2							= date_create($date2 );
				$diff								= date_diff($dateObj1,$dateObj2);
				$dayDiff							= $diff->days;
				$amazonOrders->order_import_date	= ($dayDiff>2)?$date1:date("Y-m-d H:i:s");
				$amazonProductObj					= new AmazonProducts(); 
				$checkProduct						= $amazonProductObj->checkExistingProduct($productSku, $user_id);
				if(!empty($checkProduct)) {
					$amazonProductObj		= $checkProduct;								
				} 
				$amazonProductObj->product_name = $productName;
				$amazonProductObj->sku			= $productSku;
				$amazonProductObj->asin			= "";
				$amazonProductObj->price		= $itemPrice;
				$amazonProductObj->user_id		= $user_id;
				$amazonProductObj->condition_id = ""; 
			  
				try{
					if($amazonOrders->save()){
						$amazonProductObj->save(false);
							
						} else{
								
						}
					}
					catch(Exception $e){
						continue;
					}
				}
				//$this->insertLog("Invoices imported");
				//Yii::$app->session->setFlash('success', "Invoices imported");
			}
			return true;
		}

	  public function importOrdersByApi($orders, $user_id){		
				if(!empty($orders)){
					$i=0;
					$invoiceSettings = new InvoiceSettings();
					$ordersData = $orders;//$orders['ListOrders'];
					echo "\n\nTotal Records need to import are ". count($ordersData);
						foreach ($ordersData as $key=>$order) {
						$amazonOrders		= new AmazonOrders();
						$amazon_order_id = $order['amazon-order-id'];
						$shipmentItemId ="";
						$checkIfOrderExist	= $amazonOrders->checkExistingOrder($amazon_order_id, $user_id);
						$orderQty =0;
						if(!empty($checkIfOrderExist)){
						  $amazonOrders =  $checkIfOrderExist;
						  $shipmentItemId = $amazonOrders->shipment_item_id;
						  $orderQty =  $amazonOrders->number_of_items_shipped;
						}
						$amazonOrders->shipment_item_id				= $order['shipment-item-id'];

						$amazonOrders->user_id						=$user_id;
						$amazonOrders->latest_ship_date				=(isset($order['shipment-date']))?date('Y-m-d h:i:s', strtotime($order['shipment-date'])):date('Y-m-d h:i:s', strtotime($order['purchase-date']));
						$amazonOrders->order_type					=$order['ship-service-level'];
						$amazonOrders->purchase_date				=(isset($order['purchase-date'])&& $order['purchase-date'] !="")?date('Y-m-d h:i:s', strtotime($order['purchase-date'])):"";
						$amazonOrders->buyer_email					=$order['buyer-email'];
						$amazonOrders->amazon_order_id				= $order['amazon-order-id'];
						$amazonOrders->merchant_order_id			= $order['merchant-order-id'];
						$amazonOrders->is_replacement_order			= false;
						$amazonOrders->last_update_date				=""; 
						
						if($shipmentItemId !=$order['shipment-item-id'] && $shipmentItemId !="")  {
							$oldShipmentId = $amazonOrders->shipment_category;
							$amazonOrders->shipment_category = $oldShipmentId.",SHIPMENT_ID=".$order['shipment-item-id'];
							$orderQty = $orderQty+$order['quantity-shipped'];
							$amazonOrders->number_of_items_shipped		=  $orderQty;
						}else{
							$amazonOrders->number_of_items_shipped		=$order['quantity-shipped'];
						}
						$amazonOrders->ship_service_level			=$order['ship-service-level'];
						$amazonOrders->order_status					='Shipped';
						$amazonOrders->sales_channel				=$order['sales-channel'];
						$amazonOrders->shipped_by_amazon_tfm		= "";
						$amazonOrders->is_business_order			=false;
						$amazonOrders->latest_delivery_date			=(isset($order['estimated-arrival-date']))?date('Y-m-d h:i:s', strtotime($order['estimated-arrival-date'])):""; 
						$amazonOrders->number_of_items_unshipped	=0;
						$amazonOrders->payment_method_detail		="";
						$amazonOrders->buyer_name					=$order['buyer-name'];
						$amazonOrders->earliest_delivery_date		= (isset($order['estimated-arrival-date']))?date('Y-m-d h:i:s', strtotime($order['estimated-arrival-date'])):""; 
						$amazonOrders->is_premium_order				=0;  //$order['IsPremiumOrder'];
						$itemPrice									= $order['item-price'];
						$amazonOrders->order_currency				=$order['currency'];
						$amazonOrders->item_tax						=$order['item-tax'];
						$amazonOrders->shipping_price				= $order['shipping-price'];
						$amazonOrders->shipping_tax					= $order['shipping-tax'];
						$amazonOrders->gift_wrap_price				= $order['gift-wrap-price'];
						$amazonOrders->gift_wrap_tax				= $order['gift-wrap-tax'];
						$amazonOrders->ship_address_1				= htmlspecialchars($order['ship-address-1']);
						$amazonOrders->ship_address_2				= htmlspecialchars($order['ship-address-2']);
						$amazonOrders->ship_address_3				= htmlspecialchars($order['ship-address-3']);
						$amazonOrders->ship_city					= $order['ship-city'];
						$amazonOrders->ship_state					= $order['ship-state'];
						$amazonOrders->ship_postal_code				= $order['ship-postal-code'];
						$amazonOrders->ship_country					= $order['ship-country'];
						$amazonOrders->ship_phone_number			= $order['ship-phone-number'];
						$amazonOrders->total_amount					= $itemPrice;
						$amazonOrders->earliest_ship_date			=(isset($order['estimated-arrival-date']))?date('Y-m-d h:i:s', strtotime($order['estimated-arrival-date'])):""; 
						$amazonOrders->marketplace_id				=$order['sales-channel']; //$order['MarketplaceId'];
						$amazonOrders->fulfillment_channel			=$order['fulfillment-channel'];
						$amazonOrders->payment_method				="";
						
						$amazonOrders->city							=$order['bill-city'];
						$amazonOrders->address_type					="";
						$amazonOrders->postal_code					=$order['bill-postal-code'];
						$amazonOrders->state_or_region				= $order['bill-state'];
						$amazonOrders->phone						= $order['buyer-phone-number'];
						$amazonOrders->country_code					= $order['bill-country'];
						$amazonOrders->customer_name				= $order['recipient-name'];
						$amazonOrders->address_2					= htmlspecialchars($order['bill-address-1']).' '.htmlspecialchars($order['bill-address-2']);
						$amazonOrders->tracking_number				= $order['tracking-number'];
						$amazonOrders->carrier						= $order['carrier'];
						$amazonOrders->item_price					= $order['item-price'];
						$amazonOrders->item_promotion_discount		= $order['item-promotion-discount'];
						$amazonOrders->ship_promotion_discount		= $order['ship-promotion-discount'];
						$amazonOrders->is_prime						= false;
						$amazonOrders->fulfillment_center_id		= $order['fulfillment-center-id'];

						$amazonOrders->shipment_id					= $order['shipment-id'];
						
						$amazonOrders->amazon_order_item_id			= $order['amazon-order-item-id'];
						$amazonOrders->merchant_order_item_id		= $order['merchant-order-item-id'];
						
						$productSku									= $order['sku'];
						$productName								= htmlspecialchars($order['product-name']);
						$amazonOrders->product_sku					= $productSku;
						$amazonOrders->product_name					= $productName;
						$date1										= $amazonOrders->latest_ship_date;
						$date2										= date('Y-m-d');						
						$dateObj1									= date_create($date1);
						$dateObj2									= date_create($date2 );
						$diff										= date_diff($dateObj1,$dateObj2);
						$dayDiff									= $diff->days;
						$amazonOrders->order_import_date	= ($dayDiff>2)?$amazonOrders->latest_ship_date:date("Y-m-d H:i:s");
						$amazonProductObj							= new AmazonProducts(); 
						$checkProduct								= $amazonProductObj->checkExistingProduct($productSku, $user_id);
						if(!empty($checkProduct)) {
							$amazonProductObj		= $checkProduct;								
						} 
						$amazonProductObj->product_name = $productName;
						$amazonProductObj->sku			= $productSku;
						$amazonProductObj->asin			= "";
						$amazonProductObj->price		= $itemPrice;
						$amazonProductObj->user_id		= $user_id;
						$amazonProductObj->condition_id = ""; 
						try{
							//echo "\n\norder starting imported with Id =".$amazonOrders->amazon_order_id;
							if($amazonOrders->save()){
								$amazonProductObj->save();
								$i++;
								}
								else{
									/* echo "<pre> \n\nElse";
									print_r($amazonOrders->getErrors());
									die(); */
								}
							}
							catch(Exception $e){
								/* echo "<pre>\n\n";
								print_r($e->getMessage());
								die("In CATCH"); */ 
								continue;
							}
						}
						echo "\n\nTotal  records Imported = $i";
			}
			return true;
		}
		//  Create order return report for new users, Must be run only one time

		public function actionCreateordersreturnreport() {
		$getUsers = User::find()->where(['=','status', "1"])->andWhere(["=","import_initial_creditmemo","1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			
			foreach($getUsers as  $key=>$userData) {

				try{
					$user_id	= 	$userData->id;
					$client = $this->getamazonClientInfo($user_id);
					if ($client && $client->validateCredentials()) {
						$mwsSettingsobj		= AmazonMwsSetting::getModel($user_id);
						$import_start_date  = $mwsSettingsobj->import_start_date;
						if($import_start_date =="" || $import_start_date==NULL){
							continue;
						}
						$i =1;
						$date1 = $import_start_date; 
						//$todaydate = "2018-01-01";
						$date2 = date('Y-m-d');
						$ts1 = strtotime($date1);
						$ts2 = strtotime($date2);
						$year1 = date('Y', $ts1);
						$year2 = date('Y', $ts2);
						$month1 = date('m', $ts1);
						$month2 = date('m', $ts2); 						
						$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
						$day1    = date('d', $ts1);
						$day2    = date('d', $ts2);
						$dayDiff  =	$day2-$day1;
						if($dayDiff >0){
							 $diff= $diff+1;
						}
						$todaydate = $date1;
						echo "Creating report for User $user_id \n\n";
						if($diff >=1) {
						for($i=1; $i<=$diff; $i++) {
							$time		= strtotime($todaydate);
							$toDate		= date("Y-m-d", strtotime("+29 days", $time));
							$todayTime = date('Y-m-d');
							$toDate   = ($toDate > $todayTime)?$todayTime:$toDate;

							$fromDate	= new DateTime($todaydate);
							$fromDate->setTimeZone(new DateTimeZone('GMT'));
							$endDate	= new DateTime($toDate); 
							$endDate->setTimeZone(new DateTimeZone('GMT'));
							$report_id = $client->RequestReport('_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_', $fromDate,$endDate, true);
							echo "Report created with id $report_id";
							if($report_id){
								$this->savereportdata($report_id, '_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_','SUBMITTED',$user_id,$todaydate, $toDate);
							}
							$toDate		= date("Y-m-d", strtotime("+1 days", strtotime($toDate)));
							$todaydate = $toDate; 
								sleep(240);
							}
						}else{
							//$time		= strtotime($todaydate);
							$toDate		= date("Y-m-d");
							$fromDate	= new DateTime($todaydate);
							$fromDate->setTimeZone(new DateTimeZone('GMT'));
							$endDate	= Null; //new DateTime($toDate); 
							$report_id = $client->RequestReport('_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_', $fromDate,$endDate, true);
							echo "Report created with id $report_id";
							if($report_id){
								$this->savereportdata($report_id, '_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_','SUBMITTED',$user_id,$todaydate, $toDate);
							}
							sleep(240);
						}
							echo "report for User $user_id completed \n\n";
						}else{
						
						echo "<pre> ISSUE with API Connection";						
					
						}
						$this->updateUserStatus($user_id,'import_initial_creditmemo');
				}
				catch(Exception $e){
				  // echo "<pre>";
				   //print_r($e->getMessage());
				}

			}
			echo "Done";
			 return true;
		}

	   // Will be run after each 4 hours;
		public function actionGenerateOrdersReturnReport() {
		$getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {
				try{
					$user_id	= 	$userData->id;
					$client = $this->getamazonClientInfo($user_id);
					if ($client && $client->validateCredentials()) {
							//$todaydate = date("Y-m-d", strtotime("-11 hours"));
							$todaydate = date("Y-m-d", strtotime("-4 days"));
							echo "Creating report for User $user_id \n\n";
							$fromDate	= new DateTime($todaydate);
							$fromDate->setTimeZone(new DateTimeZone('GMT'));
							//$endDate	= Null; 
							$endDateTime	= date("Y-m-d H:i:s"); //Null;
							$endDate	= new DateTime($endDateTime);	   // '2018-01-01'
							$endDate->setTimeZone(new DateTimeZone('GMT'));

							$report_id = $client->RequestReport('_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_', $fromDate,$endDate, true);
							echo "Report created with id $report_id";
							if($report_id){
								$this->savereportdata($report_id, '_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_','SUBMITTED',$user_id,$todaydate, $endDateTime);
							}
							
								sleep(240);
							
							echo "report for User $user_id completed \n\n";
						}
				}
				catch(Exception $e){
				
				}
			}
			echo "Done";
			 return true;
		}
		public function actionGetorderreturndata( $counter=0 ){
	  	 $getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			$errorMessage =false;
			foreach($getUsers as  $key=>$userData) {
				
				$user_id	= 	$userData->id;
				$errorMessage =false;
	  			 $client = $this->getamazonClientInfo($user_id);
				 if ($client && $client->validateCredentials()) {
				 $amazonReportInfoModel = new AmazonReportInfo();
				 $reportData =  $amazonReportInfoModel->getReportInfo($user_id, '_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_');
				  if(!empty($reportData)){
					 
					foreach($reportData as $key=>$returnData){
						try{
						$report_id = $returnData->report_id;
						$id = $returnData->id;
						echo "Checking data for report id $report_id for User id $user_id \n\n";
						$returnReportData = $client->GetReport($report_id);
						if(!is_array($returnReportData) && $returnReportData =="_CANCELLED_"){
							echo "report id $report_id for User id $user_id is cancelled \n\n";
						   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
						   $amazonReportInfoModelObj->report_status ='_CANCELLED_';
						   $amazonReportInfoModelObj->state =1;
						   $amazonReportInfoModelObj->save();
						 }elseif(!is_array($returnReportData) && $returnReportData =="_DONE_NO_DATA_"){
							 echo "report id $report_id for User id $user_id has _DONE_NO_DATA_ \n\n";
						   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
						   $amazonReportInfoModelObj->report_status ='_DONE_NO_DATA_';
						   $amazonReportInfoModelObj->state =1;
						   $amazonReportInfoModelObj->save();
						 }
						 elseif(!empty($returnReportData)){
							echo "report id $report_id for User id $user_id ready to import data \n\n";
							 echo "Total records for import :". count($returnReportData);
							$this->saveReturnOrder($returnReportData, $user_id);
							$amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
							$amazonReportInfoModelObj->report_status ='_DONE_';
							$amazonReportInfoModelObj->state =1;
							$amazonReportInfoModelObj->save();
						 }else{
						   echo "Report '$report_id' still not ready for user $user_id \n\n";
						 }
						 sleep(120);
						 }
						  catch(Exception $e){  
							echo $e->getMessage();
							//$errorMessage =true;
							// increase waiting time if request throlled
							 sleep(120);
						  }
					}
				}
			  }
		  
		}
		echo "\n Wait Checking more Reports";
		$checkReports  =  AmazonReportInfo::findOne(["report_type"=>['_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_'],"state"=>'0']);
		if( ($checkReports && !empty($checkReports) ) && $counter < 4) {
			// run the reports untill finish
			echo "Reports still not finished, trying to run the process again";
			$counter = $counter+1; 
			$this->actionGetorderreturndata($counter);
		}
		 //$this->updateCreditMemo();
		  return true;
		
	  }


	  public function saveReturnOrder($returnReportData, $user_id){
		 
	  	  if(!empty($returnReportData)) {
				$refund =$returnReportData;
				foreach($refund as $key=>$returnData){
						try{
						$creditmemoSettings	= new CreditmemoSettings();
						$amazonOrders		= new AmazonOrders();
						$creditMemoModel	= new CreditMemo();
						$amazon_order_id	= $returnData['order-id'];
						$checkOrder			= $creditMemoModel->checkExistingOrder($amazon_order_id, $user_id);
						$updateInvoiceCounter = true;
						if(!empty($checkOrder)) {
							$creditMemoModel		= $checkOrder;								
							//$updateInvoiceCounter = false;
						}
						/*else{
							$creditmemo_number					= $creditmemoSettings->getCreditmemoNumber($user_id);
							$creditMemoModel->credit_memo_no	= $creditmemo_number; 
						}*/
						$checkIfOrderExist			= $amazonOrders->checkExistingOrder($amazon_order_id, $user_id);
						if($checkIfOrderExist){
						$creditMemoModel->invoice_number	= $checkIfOrderExist->invoice_number;
						}
						else{
							$creditMemoModel->invoice_number	= null;
						}
						$creditMemoModel->user_id						= $user_id;
						$creditMemoModel->amazon_order_id				= $amazon_order_id ;
						$creditMemoModel->qty_return					= $returnData['quantity'] ;
						$creditMemoModel->seller_sku					= $returnData['sku'];
						$creditMemoModel->return_date					= date("Y-m-d H:i:s", strtotime($returnData['return-date']));
						$creditMemoModel->fulfillment_center_id			= $returnData['fulfillment-center-id'];
						$creditMemoModel->detailed_disposition			= $returnData['detailed-disposition'];
						$creditMemoModel->reason						= $returnData['reason'];
						$creditMemoModel->status						= $returnData['status'];
						$creditMemoModel->license_plate_number			= $returnData['license-plate-number'];
						$creditMemoModel->customer_comments				= $returnData['customer-comments'];
						$creditMemoModel->product_sku					= $returnData['sku'];
						$creditMemoModel->product_name					= htmlspecialchars($returnData['product-name']);
						$creditMemoModel->total_amount_refund			=0;
						$creditMemoModel->order_import_date			= date("Y-m-d H:i:s");
						$date1										= $creditMemoModel->return_date;
						$date2										= date('Y-m-d');
						$date1										= date_create($date1);
						$date2										= date_create($date2 );			
						$diff										= date_diff($date1,$date2);
						$dayDiff									= $diff->days;
						$creditMemoModel->order_import_date	= ($dayDiff>2)?$creditMemoModel->return_date:date("Y-m-d H:i:s");
						echo"\n\n";
						echo "importing for order id =".$amazon_order_id;
						echo"\n\n";
						if($creditMemoModel->save()){
												
						}else{
						 echo "<pre>";
						  print_r($creditMemoModel->getErrors());
						  //die();
						
						}
					}catch(Exception $e){
						echo "<pre>";
						print_r($e->getMessage());

					}
					}
					
				}
			return true;	  
	  }
	  public function updateUserStatus($user_id, $column ='import_initial_orders'){
	  $usersObj = User::findOne(['id'=>$user_id]);
	  $usersObj->$column =2;
	  $usersObj->save();
	   return true;	  
	}
	public function actionGenrateInvoiceNumber(){
		$this->updateInvoices();
		 return true;
	}

	public function actionGenrateCreditMemoNumber(){
		$this->updateCreditMemo();	
		 return true;
	}

	public function updateCreditMemo(){
		$getUsers = User::find()->where(['status'=>"1"])->andWhere(['>', 'id', '1'])->all();			
			if(empty($getUsers)){
				 return true;
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {
				
						$user_id   = $userData->id;
						$creditMemoModel	= new CreditMemo();
						//$getUserOrders	= CreditMemo::find()->where(["user_id"=>$user_id])->andWhere(['is', 'credit_memo_no', NULL])->andWhere(['not', ['invoice_number'=>NULL]])->orderBy(['return_date'=>SORT_ASC])->all();

						$getUserOrders	= CreditMemo::find()->where(["user_id"=>$user_id])->andWhere(['is', 'credit_memo_no', NULL])->andWhere(['not', ['invoice_number'=>NULL]])->orderBy(['order_import_date'=>SORT_ASC])->all();


						echo "Checking for User $user_id \n\n";
						if(!empty($getUserOrders)){
							echo "Record found for User $user_id \n\n";					
							foreach($getUserOrders as $key=>$orderData){
								try {
								$id					= $orderData->id;
								$creditMemoData		= CreditMemo::find()->where(['id' => $id])->one(); //CreditMemo::findOne(['id' => $id]);
								$creditmemoSettings	= new CreditmemoSettings();
								$credit_memo_no		= $creditmemoSettings->getCreditmemoNumber($user_id);
								$creditMemoData->credit_memo_no	= $credit_memo_no; 
								if($creditMemoData->save()){
									echo "\\ Record saved with order Id $id";
									$creditmemoSettings->updateCreditmemoNumber($user_id);
								} else{
								}
								}catch(Exception $e){ }
							}
							
						}
				
			}
			return true;
		}
	public function updateInvoices(){
		$getUsers = User::find()->where(['status'=>"1"])->andWhere(['>', 'id', '1'])->all();			
			if(empty($getUsers)){
				 return true;
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {
				
						$user_id   = $userData->id;						
						//$getUserOrders	= AmazonOrders::find()->where(["user_id"=>$user_id])->andWhere(['is', 'invoice_number', NULL])->orderBy(['latest_ship_date'=>SORT_ASC])->all();
						$getUserOrders	= AmazonOrders::find()->where(["user_id"=>$user_id])->andWhere(['is', 'invoice_number', NULL])->orderBy(['order_import_date'=>SORT_ASC])->all();

						echo "Checking for User $user_id \n\n";
						 $invoice_number ="";
						if(!empty($getUserOrders)){


							echo "Record found for User $user_id \n\n";	
							
							foreach($getUserOrders as $key=>$orderData){
								try {
								$id					= $orderData->id;
								$amazon_order_id    = $orderData->amazon_order_id;
								//$amazonOrders		= AmazonOrders::find()->where(['id' => $id])->one();
								$amazonOrders		= AmazonOrders::findOne($id);								
								$invoiceSettings	= new InvoiceSettings();
								$invoice_number		= $invoiceSettings->getInvoiceNumber($user_id);
								echo "Invoice Number for User_id = $user_id is $invoice_number \n\n";
								$amazonOrders->invoice_number	= $invoice_number; 

								if($amazonOrders->save()){
									echo "\\ Record saved with order Id $id \n\n";
									$invoiceSettings->updateInvoiceNumber($user_id);
								} else{	
									/*  echo "<pre>";
									 print_r($amazonOrders->attributes);
									 
									echo "<pre> IN Else";
									print_R($amazonOrders->getErrors());
									die(); */
								}
								}catch(Exception $e){
									/* echo "<pre>";
									print_R($e->getMessage());
									die();*/
								}
							}
						}
				}
			return true;
		}

	public function actionResetInvoicesCounter(){
	   $month   = date('m');
	   $day   = date('m');
	   if($month==01 && $day==01) {
		   $getUsers = User::find()->where(['status'=>"1"])->andWhere(['>', 'id', '1'])->all();
		   

			if(empty($getUsers)){
				 return true;
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {				
						$user_id   = $userData->id;
						$invoiceSettings		= new InvoiceSettings();
						$invoiceSettings->resetCounter($user_id );
						$creditmemoSettings	= new CreditmemoSettings();
						$creditmemoSettings->resetCounter($user_id );
			}
	   }
		 return true;
	}
	public function deleteReportsData(){
		 //$amazonReportInfoModel = new AmazonReportInfo();
		 //AmazonReportInfo::deleteAll(['clientid' => $clientid]);
	
	}

	

		function actionCreateInventoryReports() {			
			$getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			if(empty($getUsers)){
				die("No User Exists");
			}
			$marketplacesData = ['A1PA6795UKMFR9' => 'mws-eu.amazonservices.com','A1RKKUPIHCS9HS' =>'mws-eu.amazonservices.com','A13V1IB3VIYZZH' => 'mws-eu.amazonservices.com','APJ6JRA9NG5V4' => 'mws-eu.amazonservices.com','A1F83G8C2ARO7P' => 'mws-eu.amazonservices.com'];

			foreach($getUsers as  $key=>$userData) {
			try{
				$user_id = 	$userData->id;
				$client = $this->getamazonClientInfo($user_id);
				if ($client && $client->validateCredentials()) {
						foreach($marketplacesData as $marketCode=>$markeplceValue){	
							$client->config['Marketplace_Id']=$marketCode; 

							$todaydate = date("Y-m-d H:i:s", strtotime("-1 hour"));;				
							$fromDate	= new DateTime($todaydate);	   // '2018-01-01'
							$fromDate->setTimeZone(new DateTimeZone('GMT'));
							$endDate	= Null; 
							$cron_id = $this->startCronJob($user_id,"Create _GET_FBA_MYI_UNSUPPRESSED_INVENTORY_DATA_ Report for User $user_id at $todaydate");
							$report_id = $client->RequestReport('_GET_FBA_MYI_UNSUPPRESSED_INVENTORY_DATA_',$fromDate, $endDate,false);
							$amazonReportInfoModel = new AmazonReportInfo();
							if($report_id){
								$marketPlace ="";
								switch($marketCode){
								 case "APJ6JRA9NG5V4":
									  $marketPlace ="IT";
									break;
								 case "A13V1IB3VIYZZH":
									  $marketPlace ="FR";
									break;
								 case "A1RKKUPIHCS9HS":
									  $marketPlace ="ES";
									break;
								 case "A1PA6795UKMFR9":
									  $marketPlace ="DE";
									break;
								 case "A1F83G8C2ARO7P":
									  $marketPlace ="UK";
									break;
								}
								$this->savereportdata($report_id, '_GET_FBA_MYI_UNSUPPRESSED_INVENTORY_DATA_','SUBMITTED',$user_id,$todaydate, $endDate, $marketPlace);
								echo "Report submit completed User $user_id";
							}
							sleep(60);
						}
						echo "Report generated successfully";
						$this->endCronJob($cron_id);
				}			
			}catch(Exception $e){
				//continue;
			}
		}	
		 return true;
		}
		
		public function actionSendInvoiceEmail(){
		$getUsers = InvoiceMailing::find()->where(['automatic_mailing'=>1])->all();
		if(empty($getUsers)){
			 return true;
			die("No User Exists");
		}else {
			foreach($getUsers as  $key=>$userData) {
					$user_id   = $userData->user_id;
					$allNonSentInvoices =  AmazonOrders::find()->where(['user_id'=>$user_id,'invoice_email_sent'=>0])->andWhere(['not', ['invoice_number'=>NULL]])->all();
					if(empty($allNonSentInvoices)){
						continue;
					}else{
						foreach ($allNonSentInvoices as $key=>$orderData){
							$amazonOrderId = $orderData->amazon_order_id;
							$this->sendInvoices($amazonOrderId,$user_id);
						}
					}
			}
		}
			echo "DONE";
		return true;
	}
	public function sendInvoices($amazon_order_id, $user_id){
				try{	
					
				if(isset($amazon_order_id) && $amazon_order_id !="") {
					$amazonOrdersModel	= new AmazonOrders();
					$companyInfoModel	= CompanyInfo::getModel($user_id);
					$amazonOrdersModel	= $amazonOrdersModel->checkExistingOrder($amazon_order_id, $user_id);
					$vat				= VatRn::getUserVat($user_id);
					$productModel		= new AmazonProducts();
					$viewPath = Yii::getAlias('@frontend/views/user/'.'_invoice_pdf');
					
					$content			= $this->renderPartial('_invoice_pdf', ['companyModel' => $companyInfoModel,'amazonOrderModel'=>$amazonOrdersModel,'vat'=>$vat,'productModel'=>$productModel]);
					
					$getEmailFooterText = InvoiceMailing::getEmailFooterText($user_id,$amazonOrdersModel->sales_channel)	;
					$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'default_font_size' => 9,'default_font' => 'Tahoma,sans-serif']);
					$mpdf->WriteHTML($content);
					$filename	= uniqid()."_invoice_".$amazon_order_id.".pdf";
					$path = Yii::getAlias('@webroot').'/uploads/invoices/';
					$filePath = $path.''.$filename;
					$mpdf->Output($filePath,'F');
					if(file_exists($filePath)) {
						$body = nl2br($getEmailFooterText);
						$userData = User::findOne($user_id);
						$senderEmail =$userData->email;
						$message = Yii::$app->mailer->compose()
						->setFrom($senderEmail)
						->setTo($amazonOrdersModel->buyer_email)
						->setSubject("Invoice PDF for Order number: $amazon_order_id")
						->setHtmlBody($body);
						$message->attach($filePath);
						if($message->send()) {
							$amazonOrdersModel->invoice_email_sent =1;
							$amazonOrdersModel->invoice_send_date =date('Y-m-d');
							$amazonOrdersModel->email_sending_type ='Auto';
							 echo "Email send successfully.";
							 sleep(10);
							//$this->insertLog("Invoice pdf sent to customer for Amazon order  $amazon_order_id");
							$amazonOrdersModel->save();
						}
					}
				}
				} catch (Exception $e) {
					//$this->insertLog("PDF File Not exists for Amazon order  $amazon_order_id ".$e->getMessage());
				}
				return true;
	}

	public function actionSendCreditMemoEmail(){
		$getUsers = InvoiceMailing::find()->where(['automatic_mailing'=>1])->all();
		if(empty($getUsers)){
			return true;
			die("No User Exists");
		}
		else{
			foreach($getUsers as  $key=>$userData) {
				try{ 
					$user_id   = $userData->user_id;
					$allNonSentInvoices =  CreditMemo::find()->where(['user_id'=>$user_id,'creditmemo_email_sent'=>0])->andWhere(['not', ['credit_memo_no'=>NULL]])->andWhere(['not', ['invoice_number'=>NULL]])->all();
					if(empty($allNonSentInvoices)){
						continue;
					}else{
						foreach ($allNonSentInvoices as $key=>$orderData){
							$amazonOrderId = $orderData->amazon_order_id;
							$this->sendCreditNotes($amazonOrderId,$user_id);
						}
					}
				}
				catch(Exception $e){				
				}
			}
			echo "DONE";
		}
		return true;
	}
	public function sendCreditNotes($amazon_order_id, $user_id){
				try{
				if(isset($amazon_order_id) && $amazon_order_id !="") {
					$amazonCreditModel	= new CreditMemo();
					$amazonOrdersModel	= new AmazonOrders();
					$orderDetails 		= $amazonOrdersModel->checkExistingOrder($amazon_order_id, $user_id);
					$companyInfoModel	= CompanyInfo::getModel($user_id);
					$amazonCreditModel	= $amazonCreditModel->checkExistingOrder($amazon_order_id, $user_id);
					//$invoiceNo = $amazonCreditModel->invoice_number;
					$vat				= VatRn::getUserVat($user_id);
					$productModel		= new AmazonProducts();
					$content			= $this->renderPartial('_invoice_creditmemopdf', ['order_id'=>$amazon_order_id,'companyModel' => $companyInfoModel,'amazonOrderModel'=>$orderDetails,'amazonCreditModel'=>$amazonCreditModel,'vat'=>$vat,'productModel'=>$productModel]);
					$getEmailFooterText = InvoiceMailing::getEmailFooterText($user_id,$orderDetails->sales_channel);
					$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'default_font_size' => 9,'default_font' => 'Tahoma,sans-serif']);
					$mpdf->WriteHTML($content);
					$filename	= uniqid()."_creditmemo_".$amazon_order_id.".pdf";
					$path = Yii::getAlias('@webroot').'/uploads/invoices/';
					$filePath = $path.''.$filename;
					$mpdf->Output($filePath,'F');						
					if(file_exists($filePath)) {
						$body = nl2br($getEmailFooterText);
						$userData = User::findOne($user_id);
						$senderEmail =$userData->email;
						//$orderDetails->buyer_email
						$message = Yii::$app->mailer->compose()
						->setFrom($senderEmail)
						->setReplyTo($senderEmail)
						->setTo($orderDetails->buyer_email)
						->setSubject("Credit Note for Order number: $amazon_order_id")
						->setHtmlBody($body);
						$message->attach($filePath);
						if($message->send()){							
							$amazonCreditModel->creditmemo_email_sent =1;
							$amazonCreditModel->creditmemo_email_date =date('Y-m-d');
							$amazonCreditModel->email_sending_type ='Auto';
							$amazonCreditModel->save();
							sleep(10);
							}
						
						}
				}
				} catch (Exception $e) {
				echo "<pre>"; print_r($e->getMessage()); die();
			}
			return true;
	}
	public function actionGetinventorydata() {
		   $getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
		   if(empty($getUsers)){
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {			
			 $user_id	= $userData->id;
			 $client	= $this->getamazonClientInfo($user_id);
			 $orderApiData =[];
			 if ($client && $client->validateCredentials()) {
				 echo "\\ Checking user for  ID $user_id";
				$amazonReportInfoModel = new AmazonReportInfo();
				$reportData =  $amazonReportInfoModel->getReportInfo($user_id, ['_GET_FBA_MYI_UNSUPPRESSED_INVENTORY_DATA_']);				  
			 if(!empty($reportData)) {
				 foreach($reportData as $key=>$reportInfo) {
					 try{
					 $id        = $reportInfo->id;
					 $report_id = $reportInfo->report_id;
					 $marketplace = $reportInfo->marketplace;
					 //$user_id   = $reportInfo->user_id;
					 $reportType = $reportInfo->report_type;
					 echo "\n\n report type= $reportType Stated"; 
					 $reportApiData = $client->GetReport($report_id);

					 if(!is_array($reportApiData) && $reportApiData =="_CANCELLED_"){
					   echo "\n\nCancelled for user $user_id with Report id $report_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_CANCELLED_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(!is_array($reportApiData) && $reportApiData =="_DONE_NO_DATA_"){
						 echo "\n\nNo data found for report id $report_id for userid $user_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_DONE_NO_DATA_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(!empty($reportApiData)){						 
						echo "\n\nReported started imported for userid $user_id";
						echo "\n\n report type= $reportType";							
						  $completed = $this->importMechantInventory($reportApiData, $user_id, $marketplace);
						   if($completed) {
							$amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
							$amazonReportInfoModelObj->report_status ='_DONE_';
							$amazonReportInfoModelObj->state =1;
							$amazonReportInfoModelObj->save();
						   }						   
						echo "\n\nReport completed";
					 }else{
						// echo "<pre> for User $user_id Report Id: $report_id";
						// print_r($reportApiData);
					   //echo "\n\nReport Still not available";
					 }
					 sleep(60);
					 } catch(Exception $e){
						echo "<pre>";
						print_r($e->getMessage());
						die();
					}
				 }
				}
			 }
		  }
		 
	  }

	  function importMechantInventory($reportApiData, $user_id, $marketplace) {
			
			foreach($reportApiData as $key=>$data){
				
			   $amazonInventory = new AmazonInventory();
				$productSku					= 	$data['sku'];
				$date						= date("Y-m-d");
				$checkIfProductExist	= $amazonInventory->checkExistingProduct($productSku, $user_id, $marketplace, $date);
				if(!empty($checkIfProductExist)) {
					$amazonInventory =  $checkIfProductExist;
				}
				
				$amazonInventory->product_name			=  $data['product-name'];
				$amazonInventory->price					= $data['your-price'];
				//$amazonInventory->quantity			= $data['quantity'];
				$amazonInventory->fnsku					= $data['fnsku'];				
				$amazonInventory->asin					= $data['asin'];
				$amazonInventory->marketplace			=$marketplace;
				$amazonInventory->product_condition		= $data['condition'];				
				$amazonInventory->mfn_listing_exists	= (isset($data['mfn-listing-exists']))?$data['mfn-listing-exists']:0;

				$amazonInventory->mfn_fulfillable_quantity			= (isset($data['mfn-fulfillable-quantity']))?$data['mfn-fulfillable-quantity']:0;
				$amazonInventory->afn_listing_exists			= (isset($data['afn-listing-exists']))?$data['afn-listing-exists']:0;

				$amazonInventory->afn_warehouse_quantity			= (isset($data['afn-warehouse-quantity']))?$data['afn-warehouse-quantity']: 0;

				$amazonInventory->afn_fulfillable_quantity	= (isset($data['afn-fulfillable-quantity']))?$data['afn-fulfillable-quantity']:0;
				$amazonInventory->afn_unsellable_quantity						= (isset($data['afn-unsellable-quantity']))?$data['afn-unsellable-quantity']:0;
				$amazonInventory->afn_reserved_quantity						= (isset($data['afn-reserved-quantity']))?$data['afn-reserved-quantity']:0;
				$amazonInventory->afn_total_quantity						= (isset($data['afn-total-quantity']))?$data['afn-total-quantity']:0;
				$amazonInventory->sku				= $productSku;

				$amazonInventory->per_unit_volume	= (isset($data['per-unit-volume']))?$data['per-unit-volume']:0;
				$amazonInventory->afn_inbound_working_quantity		= (isset($data['afn-inbound-working-quantity']))?$data['afn-inbound-working-quantity']:0;

				$amazonInventory->afn_inbound_shipped_quantity	= $data['afn-inbound-shipped-quantity'];
				$amazonInventory->afn_inbound_receiving_quantity =  (isset($data['afn-inbound-receiving_quantity']))?$data['afn-inbound-receiving_quantity']:0;			
				
				$amazonInventory->import_date				= date('Y-m-d');
				$amazonInventory->user_id					= $user_id;
				try{
					if($amazonInventory->save()){
						echo "SAVED";
					}
					else{
						
					}
				}catch(Exception $e){					  
				}
			}
			return true;		
		}

		public function sendSalesAnalyticsReport($user_id, $report_date, $report_subject_Date) {
			try{
			$companyInfoModel	= CompanyInfo::getModel($user_id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
			$analytics_email =  $companyInfoModel->analytics_email;
			if($analytics_email==""){				
				return "Email not exist";
			}
			$companySubjectName = $companyInfoModel->company_name;
			$file_name =$companyName."_sales_analytics_report_".$report_date.".csv";
			
			$searchModel = new AmazonOrdersSearch();
			$queryParams=[];
			//$queryParams['AmazonOrdersSearch']['year'] = date("Y");
			$queryParams['AmazonOrdersSearch']['order_import_date'] = $report_date;
			$queryParams['AmazonOrdersSearch']['user_id'] = $user_id;
			$invoiceColumns = $companyInfoModel->invoice_select_fields;			
			if($invoiceColumns != NULL && $invoiceColumns !=""){
			  $columns = explode(",", $invoiceColumns);
			}else{
				$columns = ['order_import_date','invoice_number','protocol_invoice_number','purchase_date','buyer_name','buyer_email','buyer_vat','number_of_items_shipped','item_price','latest_ship_date','amazon_order_id','product_sku','product_name','last_update_date','ship_service_level','order_status','sales_channel','latest_delivery_date','marketplace_id','fulfillment_channel','payment_method','customer_name','address_2','address_type','city','state_or_region','country_code','postal_code','phone','fulfillment_center_id','shipment_id','ship_address_1','ship_address_2','ship_address_3','ship_city','ship_state','ship_postal_code','ship_country','ship_phone_number','ship_promotion_discount','tracking_number','carrier','shipping_price','shipping_tax','gift_wrap_price','gift_wrap_tax'];
			}
			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			}			 
			//$dataProvider = $searchModel->searchExport($queryParams);
			$dataProvider = $searchModel->searchExportReports($queryParams, $user_id);

			if(empty($dataProvider)){
				return "No data exist";
			}
			$exporter = new CsvGrid([
            'dataProvider' => $dataProvider,
				'columns' => $attributeArray,
			]);

			$path = Yii::getAlias('@webroot').'/uploads/reports/';
			$filePath = $path.''.$file_name;
			//$exporter->save($filePath);
			$exporter->export()->saveAs($filePath);
			$text ="";
		    if(file_exists($filePath)) {
				$getEmailFooterText = InvoiceMailing::getEmailFooterText($user_id,"Amazon.it");
				$analytics_email =  $companyInfoModel->analytics_email;
				$analytics_emails = explode(",", $analytics_email);
				$emailsArray = array_combine($analytics_emails, $analytics_emails);
				$body = nl2br($getEmailFooterText);
				$userData = User::findOne($user_id);
				$senderEmail =$userData->email;
				$senderName =$userData->name;
				$senderData[$senderEmail]=$senderName;
				/* $subject = str_replace("_"," ", $file_name);
				$subject = str_replace(".csv"," ", $subject);
				$subject = str_replace("-"," ", $subject);
				 */

				$subject   = $companySubjectName." Sales Analytics Report ".$report_subject_Date;
				$message = Yii::$app->mailer->compose()
				->setFrom($senderData)
				->setTo($emailsArray)
				->setReplyTo($senderData)
				->setSubject($subject)
				->setHtmlBody($body);
				$message->attach($filePath);
				if($message->send()) {	
					$text ="Email Send Successfully for Sales Analytics Report";
					sleep(10);
				}else{
				  $text ="Error in Sending email for Sales Analytics Report";
				}
				sleep(20);
				unlink($filePath);
			}
			}
			catch(Exception $e){
				$text = $e->getMessage() .' in Sales Analytics Report';
			}
			return $text;
		}


		public function sendCreditNoteAnalyticsReport($user_id, $report_date, $report_subject_Date) {
			try{
			$companyInfoModel	= CompanyInfo::getModel($user_id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
			$analytics_email =  $companyInfoModel->analytics_email;
			if($analytics_email==""){
				return "Email not exist";
			}

			$file_name =$companyName."_creditnote_analytics_report_".$report_date.".csv";
			$companySubjectName = $companyInfoModel->company_name;

			$searchModel = new CreditMemoSearch(); 
			$queryParams=[];
			$queryParams['CreditMemoSearch']['user_id'] =$user_id;
			$queryParams['CreditMemoSearch']['return_date'] = $report_date;
			$invoiceColumns = $companyInfoModel->creditmemo_selected_field;	
			if($invoiceColumns != NULL && $invoiceColumns !=""){
			  $columns = explode(",", $invoiceColumns);
			}else{
				$columns = ['credit_memo_no','invoice_number','order_import_date','return_date','seller_sku','product_sku','product_name','seller_order_id','qty_return','amazon_order_id','license_plate_number','order_adjustment_item_id','fulfillment_center_id','reason','detailed_disposition','status'];
			}
			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			}

			//$dataProvider = $searchModel->searchExport($queryParams);
			$dataProvider = $searchModel->searchExportReports($queryParams, $user_id);

		   	if(empty($dataProvider)){
				return "No data exist";
			}
			$exporter = new CsvGrid([
            'dataProvider' =>$dataProvider,
			 'columns' =>$attributeArray,
			]);
			$path = Yii::getAlias('@webroot').'/uploads/reports/';
			$filePath = $path.''.$file_name;
			//$exporter->save($filePath);
			$exporter->export()->saveAs($filePath);
			sleep(30);
			$text ="";
				if(file_exists($filePath)) {
					$getEmailFooterText = InvoiceMailing::getEmailFooterText($user_id,"Amazon.it");
					$analytics_email =  $companyInfoModel->analytics_email;
					$analytics_emails = explode(",", $analytics_email);
					$emailsArray = array_combine($analytics_emails, $analytics_emails);
					$body = nl2br($getEmailFooterText);
					$userData = User::findOne($user_id);
					$senderEmail =$userData->email;
					$senderName =$userData->name;
					$senderData[$senderEmail]=$senderName;
					/* $subject = str_replace("_"," ", $file_name);
				    $subject = str_replace(".csv"," ", $subject);
					$subject = str_replace("-"," ", $subject);*/

					$subject   =  $companySubjectName." Creditnote Analytics Report ".$report_subject_Date;

					$message = Yii::$app->mailer->compose()
					->setFrom($senderData)
					->setTo($emailsArray)
					->setReplyTo($senderData)
					->setSubject($subject)
					->setHtmlBody($body);
					$message->attach($filePath);
					if($message->send()) {
						$text ="Email send successfully Credit Note Analytics Report";
						sleep(30);
					}else{
					$text ="Error in sending email for Credit Note Analytics Report";
					}
					//sleep(20);
					unlink($filePath);
				}

				
			}
			catch(Exception $e){
				$text =$e->getMessage() .' In Credit Note Analytics Report';
			}
			return $text;
		}
		public function sendSalesReport($user_id, $report_date, $report_subject_Date) {
			try{
			
			echo "Creating report for user $user_id";
			$companyInfoModel	= CompanyInfo::getModel($user_id);
			$accountant_email =  $companyInfoModel->accountant_email;
			if($accountant_email==""){
				//echo "Email not exist";
				return "Email not exist for Sales Report Accountant";
			}
			$companySubjectName = $companyInfoModel->company_name;
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
			$file_name =$companyName."_sales_report_".$report_date.".csv";
			
			$searchModel = new AmazonOrdersSearch(); 
			
			/* $queryParams['AmazonOrdersSearch']['month'] =date("m");
			$queryParams['AmazonOrdersSearch']['year'] =date("Y");*/
			$queryParams['AmazonOrdersSearch']['order_import_date'] = $report_date;
			$queryParams['AmazonOrdersSearch']['user_id'] = $user_id;
			$invoiceColumns = $companyInfoModel->invoice_select_fields;			
			if($invoiceColumns != NULL && $invoiceColumns !=""){
			  $columns = explode(",", $invoiceColumns);
			}
			else{
				$columns = ['order_import_date','invoice_number','protocol_invoice_number','purchase_date','buyer_name','buyer_email','buyer_vat','number_of_items_shipped','item_price','latest_ship_date','amazon_order_id','product_sku','product_name','last_update_date','ship_service_level','order_status','sales_channel','latest_delivery_date','marketplace_id','fulfillment_channel','payment_method','customer_name','address_2','address_type','city','state_or_region','country_code','postal_code','phone','fulfillment_center_id','shipment_id','ship_address_1','ship_address_2','ship_address_3','ship_city','ship_state','ship_postal_code','ship_country','ship_phone_number','ship_promotion_discount','tracking_number','carrier','shipping_price','shipping_tax','gift_wrap_price','gift_wrap_tax'];
			}
			
			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			}
			 
			$dataProvider = $searchModel->searchExportReports($queryParams, $user_id);
			
			if(empty($dataProvider)) {
				return "No data exist";
			}

		  	$exporter = new CsvGrid([
            'dataProvider' =>$dataProvider,
			'columns' =>$attributeArray, 
			]);
			$path = Yii::getAlias('@webroot').'/uploads/reports/';
			$filePath = $path.''.$file_name;
			//$exporter->save($filePath);
			$exporter->export()->saveAs($filePath);
			sleep(30);
			echo "Creating csv file...";
			$text ="";
		    if(file_exists($filePath)) {
				echo "\n\nFile created successfully..\n\n";
				echo "Trying to send email\n\n";
				$getEmailFooterText = InvoiceMailing::getEmailFooterText($user_id,"Amazon.it");
				$accountant_emails = explode(",", $accountant_email);
				$emailsArray = array_combine($accountant_emails, $accountant_emails);
				$body = nl2br($getEmailFooterText);
				$userData = User::findOne($user_id);
				$senderEmail =$userData->email;
				$senderName =$userData->name;
				$senderData[$senderEmail]=$senderName;
				/*$subject = str_replace("_"," ", $file_name);
				$subject = str_replace(".csv"," ", $subject);
				$subject = str_replace("-"," ", $subject);
				*/
				$subject = $companySubjectName." Sales Analytics Report -".$report_subject_Date;

				$message = Yii::$app->mailer->compose()
				->setFrom($senderData)
				->setTo($emailsArray)
				->setReplyTo($senderData)
				->setSubject($subject)
				->setHtmlBody($body);
				$message->attach($filePath);
				if($message->send()) {
					$text = "Email sent successfully for Sales Report accountant";
					sleep(30);
				}else{
					$text ="Error in sending email for Sales Report Accountant";
				} 
				//sleep(20);
				unlink($filePath);
			}
			else{
				$text = "File not yet exists for Sales Report accountant";
				
			}
			//return $exporter->send($file_name);
			}
			catch(Exception $e){
				$text = $e->getMessage() ." in Sales Report  Accountant";
			}

			return $text;
		}

		public function sendCreditNoteAccountReport($user_id, $report_date, $report_subject_Date) {
			try{
		    $companyInfoModel	= CompanyInfo::getModel($user_id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
			$accountant_email =  $companyInfoModel->accountant_email;
			if($accountant_email=="") {
				
				return "Email not exist for CreditNote Account Report";;
			}
			//$file_name =$companyName."_creditnote_account_report_".$report_date.".csv";
			$file_name =$companyName."_CreditNoteAccountReport_".$report_date.".csv";
			$companySubjectName = $companyInfoModel->company_name;
			$searchModel = new CreditMemoSearch(); 
			$queryParams=[];
			$queryParams['CreditMemoSearch']['user_id'] =$user_id;
			$queryParams['CreditMemoSearch']['return_date'] = $report_date;

			$dataProvider = $searchModel->searchExportReports($queryParams, $user_id);
			
			if(empty($dataProvider)){
				return "No data exist";
			}
			$invoiceColumns = $companyInfoModel->creditmemo_selected_field;			
			if($invoiceColumns != NULL && $invoiceColumns !=""){
				$columns = explode(",", $invoiceColumns);
			}else{
				$columns =     ['credit_memo_no','invoice_number','order_import_date','return_date','seller_sku','product_sku','product_name','seller_order_id','qty_return','amazon_order_id','license_plate_number','order_adjustment_item_id','fulfillment_center_id','reason','detailed_disposition','status'];
			}
			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			} 
			$exporter = new CsvGrid([
            'dataProvider' =>$dataProvider,
				'columns' => $attributeArray,
			]);
			$path = Yii::getAlias('@webroot').'/uploads/reports/';
			$filePath = $path.''.$file_name;
			//$exporter->save($filePath);
			$exporter->export()->saveAs($filePath);
			sleep(30);
			$text="";
		    if(file_exists($filePath)) {
				$getEmailFooterText = InvoiceMailing::getEmailFooterText($user_id,"Amazon.it");
				//$accountant_email =  $companyInfoModel->accountant_email;
				$accountant_emails = explode(",", $accountant_email);
				$emailsArray = array_combine($accountant_emails, $accountant_emails);				
				$body = nl2br($getEmailFooterText);
				$userData = User::findOne($user_id);
				$senderEmail =$userData->email;
				$senderName =$userData->name;
				$senderData[$senderEmail]=$senderName;
				/* $subject = str_replace("_"," ", $file_name);
				$subject = str_replace(".csv"," ", $subject);
				$subject = str_replace("-"," ", $subject);
				 */
				$subject = $companySubjectName." Credit Note Account Report ".$report_subject_Date;

				$message = Yii::$app->mailer->compose()
				->setFrom($senderData)
				->setTo($emailsArray)
				->setReplyTo($senderData)
				->setSubject($subject)
				->setHtmlBody($body);
				$message->attach($filePath);
				if($message->send()) {
					$text= "Email send successfully for CreditNote Account Report";
					sleep(30);
				}else{
					$text= "Error in sending email for CreditNote Account Report";
				}
				//sleep(30);
				unlink($filePath);
			 }
			}
			catch(Exception $e){
				$text=$e->getMessage()." for CreditNote Account Report";
			}
			
			return $text;
		}

		public function actionSendreports(){
		 
		$getUsers = InvoiceMailing::find()->where(['automatic_reports_email'=>1])->all();
		if(empty($getUsers)){
			die("No User Exists");
			return true;
		}else {
			$todayDate = date("Y-m-d");
			$todayDay  = date("d");

			foreach($getUsers as  $key=>$userData) { 
				try{
				$user_id	   = $userData->user_id;
				$nextSendDate  = $userData->next_send_date;
				$reportMonth   = ($userData->report_months !=NULL && $userData->report_months >0)?$userData->report_months:1;
				$reportDay	   = ($userData->report_send_day != NULL && $userData->report_send_day >0)?$userData->report_send_day:4;				
				$id            = $userData->id;
				$reportInfo	   ="";
				if(($nextSendDate==NULL || $nextSendDate==$todayDate) && $reportDay==$todayDay) {
					$cron_id = $this->startCronJob($user_id, "Send reports to user $user_id for $nextSendDate");
					$newDay= $reportDay-1;
					$reportStartDate = date("Y-m-d",strtotime("-$reportMonth months -$newDay days"));
					$reportEndDate = date("Y-m-d",strtotime("-$reportDay days"));

					$dt				= DateTime::createFromFormat('Y-m-d', $reportStartDate);
					$startDate		= $dt->format('d/m/Y');
					
					$dt1				= DateTime::createFromFormat('Y-m-d', $reportEndDate);
					$endDate		= $dt1->format('d/m/Y');

					$report_date = $reportStartDate.' - '.$reportEndDate;
					$report_subject_Date  = "From ".$startDate.' up to '.$endDate;
					
					$reportInfo .="<br>".$this->sendSalesReport($user_id, $report_date, $report_subject_Date);
					sleep(20);
					 $reportInfo .="<br>".$this->sendSalesAnalyticsReport($user_id, $report_date,$report_subject_Date);
					sleep(30);

					$reportInfo .="<br>".$this->sendCreditNoteAccountReport($user_id, $report_date, $report_subject_Date);
					sleep(30); 
					$reportInfo .= "<br>".$this->sendCreditNoteAnalyticsReport($user_id, $report_date, $report_subject_Date);
					sleep(30);

					//echo  $reportInfo ;
					$nextUpdateDate = date("Y-m-d", strtotime("+$reportMonth months"));
					$model = InvoiceMailing::findOne($id);
					$model->next_send_date = $nextUpdateDate;
					$model->save();
					$this->endCronJob($cron_id);
				}else{
					echo "No record found";
				}
				
				}
				catch(Exception $e){
					continue;
					//echo "<pre>";
					//print_r($e->getMessage());
				}
			}
		}

		echo "DONE";
		return true;
	}


	public function actionSendInventoryReports(){
		 
		$getUsers = InvoiceMailing::find()->where(['automatic_inventory_email'=>1])->all();
		if(empty($getUsers)){
			die("No User Exists");
			return true;
		}else {
			$todayDate = date("Y-m-d");
			$todayDay  = date("d");

			foreach($getUsers as  $key=>$userData) { 
				try{
				$user_id	   = $userData->user_id;
				$nextSendDate  = $userData->inventory_report_send_date;
				$reportMonth   = ($userData->inventory_month !=NULL && $userData->inventory_month >0)?$userData->inventory_month:1;
				$reportDay	   = ($userData->inventory_report_day != NULL && $userData->inventory_report_day >0)?$userData->inventory_report_day:4;				
				$id            = $userData->id;
				$reportInfo	   ="";
				if(($nextSendDate==NULL || $nextSendDate==$todayDate) && $reportDay==$todayDay) {
					
					$cron_id = $this->startCronJob($user_id, "Send Inventory reports to user $user_id for $nextSendDate");
					$newDay= $reportDay-1;
					$reportStartDate = date("Y-m-d",strtotime("-$reportMonth months -$newDay days"));
					$reportEndDate = date("Y-m-d",strtotime("-$reportDay days"));
					$report_date = $reportStartDate.' - '.$reportEndDate;
					$reportInfo .= "<br>".$this->sendInventoryReport($user_id, $report_date);
					echo  $reportInfo ;
					$nextUpdateDate = date("Y-m-d", strtotime("+$reportMonth months"));
					$model = InvoiceMailing::findOne($id);
					$model->inventory_report_send_date = $nextUpdateDate;
					$model->save();					
					$this->endCronJob($cron_id);
				}else{
					echo "No record found";
				}
				
				}
				catch(Exception $e){
					echo "<pre>";
					print_r($e->getMessage());
				}
			}
		}

		echo "DONE";
		return true;
	}
	public function actionResetcreditmemoinvoicenumber(){				
			$user_id   = 6;			
			$getUserOrders	= CreditMemo::find()->where(["user_id"=>$user_id])->andWhere(['is', 'credit_memo_no', NULL])->andWhere(['not', ['invoice_number'=>NULL]])->orderBy(['return_date'=>SORT_ASC])->all();
			echo "Checking for User $user_id \n\n";
			if(!empty($getUserOrders)){

				echo "Record found for User $user_id \n\n";					
				foreach($getUserOrders as $key=>$orderData){
					try {
					$id					= $orderData->id;
					$amazonOrders		= new AmazonOrders();
					$creditMemoModel	= new CreditMemo();
					$creditMemoModel	= CreditMemo::find()->where(['id' => $id])->one(); //CreditMemo::findOne(['id' => $id]);
					$amazon_order_id	= $creditMemoModel->amazon_order_id; 

					$checkIfOrderExist	= $amazonOrders->checkExistingOrder($amazon_order_id, $user_id);
					if($checkIfOrderExist){
						$creditMemoModel->invoice_number	= $checkIfOrderExist->invoice_number;
					}
					else{
						$creditMemoModel->invoice_number	= null;
					}
					$creditmemoSettings	= new CreditmemoSettings();
					$credit_memo_no		= $creditmemoSettings->getCreditmemoNumber($user_id);
					$creditMemoModel->credit_memo_no	= $credit_memo_no; 
					if($creditMemoModel->save()){
						echo "\\ Record saved with order Id $id";
						$creditmemoSettings->updateCreditmemoNumber($user_id);
					} else{
					}
					}catch(Exception $e){ }
				}
				
			}			
			return true;	
	}

	// Set this cronjob for AFN orders to run after each 4 hours
	public function actionCreatemissingreport() {
		   $getUsers = User::find()->where(['=','status', "1"])->andWhere(['=', 'id', '4'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {
			try{
			$user_id = 	$userData->id;
			
			$client = $this->getamazonClientInfo($user_id);
			if ($client && $client->validateCredentials()) {
									
					//$todaydate = date("Y-m-d H:i:s", strtotime("-11 hours"));;				
					$todaydate = '2018-10-01'; //date("Y-m-d H:i:s", strtotime("-4 days"));				
					echo "Creating daily report for User $user_id ";
					$fromDate	= new DateTime($todaydate);	   // '2018-01-01'
					$fromDate->setTimeZone(new DateTimeZone('GMT'));
					$endDateTime	= '2018-10-30'; //date("Y-m-d H:i:s"); //Null;
					$endDate	= new DateTime($endDateTime);	   // '2018-01-01'
					$endDate->setTimeZone(new DateTimeZone('GMT'));

					$cron_id = $this->startCronJob($user_id,"Create daily Invoice Report for User $user_id at $todaydate for _GET_AMAZON_FULFILLED_SHIPMENTS_DATA_ AND _GET_FLAT_FILE_ORDERS_DATA_");
					$report_id = $client->RequestReport('_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_',$fromDate, $endDate,true);
					$amazonReportInfoModel = new AmazonReportInfo();
					if($report_id){
						$this->savereportdata($report_id, '_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_','SUBMITTED',$user_id,$todaydate, $endDateTime);
						echo "Report submit completed User $user_id";
					}
					sleep(200);
					// Create MFN report for User
					$report_id2 = $client->RequestReport('_GET_FLAT_FILE_ORDERS_DATA_',$fromDate, $endDate, true);
					if($report_id2){
						$this->savereportdata($report_id2, '_GET_FLAT_FILE_ORDERS_DATA_','SUBMITTED',$user_id,$todaydate, $endDateTime);
						echo "Report submit completed User $user_id";
					}

					//$todaydate = $toDate; 
					sleep(200);
				echo "Report generated successfully";
				$this->endCronJob($cron_id);
			}			
			}catch(Exception $e){
				//echo "<pre>";
				//print_r($e->getMessage());
				//die();
				//continue;
			}
		}
		echo "DONE";
		 return true;
	}

	// Set this cronjob for AFN orders to run after each 4 hours
	//create-inventory-summary-report
	public function actionCreateInventorySummaryReport() {
		   $getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {
			try{
			$user_id = 	$userData->id;
			$client = $this->getamazonClientInfo($user_id);
			if ($client && $client->validateCredentials()) {
				//$todaydate = date("Y-m-d h:i:s", strtotime("-6 months"));
				$todaydate = date("Y-m-d h:i:s");
				$fromDate	= new DateTime($todaydate);
				$fromDate->setTimeZone(new DateTimeZone('GMT'));
				$endDateTime	= date("Y-m-d h:i:s");
				$endDate	= new DateTime($endDateTime);
				$endDate->setTimeZone(new DateTimeZone('GMT'));

				$cron_id = $this->startCronJob($user_id,"Create daily Invoice Report for User $user_id at $todaydate for _GET_FBA_FULFILLMENT_INVENTORY_SUMMARY_DATA_");
				$report_id = $client->RequestReport('_GET_FBA_FULFILLMENT_INVENTORY_SUMMARY_DATA_',$fromDate, $endDate,true);
				$amazonReportInfoModel = new AmazonReportInfo();
				if($report_id){
					$this->savereportdata($report_id, '_GET_FBA_FULFILLMENT_INVENTORY_SUMMARY_DATA_','SUBMITTED',$user_id,$todaydate, $endDateTime);
					echo "Report submit completed User $user_id";
				}
				sleep(30);
				echo "Report generated successfully";
				$this->endCronJob($cron_id);
			}			
			}catch(Exception $e){				
			}
		}
		echo "DONE";
		 return true;
	}


	// get AFN Orders information
		 public function actionImportInventoryData($counter =0) {
		  
		 $getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			
			foreach($getUsers as  $key=>$userData) {
			 $user_id	= 	$userData->id;
			 $cron_id = $this->startCronJob($user_id,"Get Order reports data for User $user_id");
			 $client	= $this->getamazonClientInfo($user_id);
			 $orderApiData =[];
			 if ($client && $client->validateCredentials()) {
				 echo "\\ Checking user for  ID $user_id";
				$amazonReportInfoModel = new AmazonReportInfo();
				$reportData =  $amazonReportInfoModel->getReportInfo($user_id, ['_GET_FBA_FULFILLMENT_INVENTORY_SUMMARY_DATA_']);

			 if(!empty($reportData)) {
				 foreach($reportData as $key=>$reportInfo) {
					 try{
					 $id        = $reportInfo->id;
					 $report_id = $reportInfo->report_id;					
					 $reportType = $reportInfo->report_type;
					 echo "\n\n report type= $reportType Stated"; 
					 $reportApiData = $client->GetReport($report_id);

					 if(!is_array($reportApiData) && $reportApiData =="_CANCELLED_"){
					   echo "\n\nCancelled for user $user_id with Report id $report_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_CANCELLED_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(!is_array($reportApiData) && $reportApiData =="_DONE_NO_DATA_"){
						 echo "\n\nNo data found for report id $report_id for userid $user_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_DONE_NO_DATA_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(is_array($reportApiData) && !empty($reportApiData)){						
						if($reportType=='_GET_FBA_FULFILLMENT_INVENTORY_SUMMARY_DATA_'){
							echo "starting import data";
						  $completed = $this->importDailyInventorySummary($reportApiData, $user_id);
						  if($completed) {
							$amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
							$amazonReportInfoModelObj->report_status ='_DONE_';
							$amazonReportInfoModelObj->state =1;
							$amazonReportInfoModelObj->save();
						   }
						}						
						echo "\n\nReport completed";
					 }else{
					 }
					 sleep(50);
					 } catch(Exception $e){
							//continue;
					}
				}
			 }
			} 
			$this->endCronJob($cron_id);
		  }
		  echo "Waiting to check the data";
		  sleep(50);
		  echo "Checking reports if still need import";
		$checkReports  =  AmazonReportInfo::findOne(["report_type"=>['_GET_FBA_FULFILLMENT_INVENTORY_SUMMARY_DATA_'],"state"=>'0']);
		if( ($checkReports && !empty($checkReports) ) && $counter <4 ){
			// run the reports untill finish
			echo "Reports still not finished";
			$counter = $counter+1; 
			$this->actionImportInventoryData($counter);
		}
		 //$this->updateInvoices();
		  echo "\n\ncronjob Completed successfully";
		  return true;
	  }
	  public function importDailyInventorySummary($inventoryData, $user_id){		
				if(!empty($inventoryData)){
					$i=0;
					foreach ($inventoryData as $key=>$inventory) {						
						$inventoryModel	= new AmazonInventorySummary();
						$sku							= $inventory['sku'];
						$snapshotDate						= date('Y-m-d h:i:s', strtotime($inventory['snapshot-date']));
						//$checkExistingRecords = $inventoryModel->checkExistingProduct($sku, $user_id, $snapshotDate);
						$inventoryModel->user_id			= $user_id;
						$inventoryModel->snapshot_date		= date('Y-m-d h:i:s', strtotime($inventory['snapshot-date']));
						$inventoryModel->transaction_type	=$inventory['transaction-type'];
						$inventoryModel->fnsku				= $inventory['fnsku'];
						$inventoryModel->product_name		= $inventory['product-name'];
						$inventoryModel->fulfillment_center_id	= $inventory['fulfillment-center-id'];
						$inventoryModel->quantity			= $inventory['quantity'];
						$inventoryModel->disposition		=$inventory['disposition'];
						try{							
							if($inventoryModel->save()){
							
							} else{
								/* echo "<pre>";
								print_r($inventoryModel);
								die()*/
							}
						}

						catch(Exception $e){ 
							continue; 
						}						
					}									
				}
			return true;
		}





		//create-inventory-summary-report
		// create-inventory-health-report
	public function actionCreateInventoryHealthReport() {
		   $getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {
			try{
			$user_id = 	$userData->id;
			$client = $this->getamazonClientInfo($user_id);
			if ($client && $client->validateCredentials()) {
				$todaydate = date("Y-m-d h:i:s", strtotime("-24 hours"));
				//$todaydate = date("Y-m-d h:i:s");
				$fromDate	= new DateTime($todaydate);
				$fromDate->setTimeZone(new DateTimeZone('GMT'));
				$endDateTime	= date("Y-m-d h:i:s");
				$endDate	= new DateTime($endDateTime);
				$endDate->setTimeZone(new DateTimeZone('GMT'));

				$cron_id = $this->startCronJob($user_id,"Create daily Invoice Report for User $user_id at $todaydate for _GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_");
				$report_id = $client->RequestReport('_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_',$fromDate, $endDate,true);
				$amazonReportInfoModel = new AmazonReportInfo();
				if($report_id){
					$this->savereportdata($report_id, '_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_','SUBMITTED',$user_id,$todaydate, $endDateTime);
					echo "Report submit completed User $user_id";
				}
				sleep(30);
				echo "Report generated successfully";
				$this->endCronJob($cron_id);
			}			
			}catch(Exception $e){				
			}
		}
		echo "DONE";
		 return true;
	}


	// get AFN Orders information
	//import-inventory-health-data
		 public function actionImportInventoryHealthData($counter=0) {
		  
		 $getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			
			foreach($getUsers as  $key=>$userData) {
			 $user_id	= 	$userData->id;
			 $cron_id = $this->startCronJob($user_id,"Get Order reports data for User $user_id");
			 $client	= $this->getamazonClientInfo($user_id);
			 $orderApiData =[];
			 if ($client && $client->validateCredentials()) {
				 echo "\\ Checking user for  ID $user_id";
				$amazonReportInfoModel = new AmazonReportInfo();
				$reportData =  $amazonReportInfoModel->getReportInfo($user_id, ['_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_']);

			 if(!empty($reportData)) {
				 foreach($reportData as $key=>$reportInfo) {
					 try{
					 $id        = $reportInfo->id;
					 $report_id = $reportInfo->report_id;					
					 $reportType = $reportInfo->report_type;
					 echo "\n\n report type= $reportType Stated"; 
					 $reportApiData = $client->GetReport($report_id);

					 if(!is_array($reportApiData) && $reportApiData =="_CANCELLED_"){
					   echo "\n\nCancelled for user $user_id with Report id $report_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_CANCELLED_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(!is_array($reportApiData) && $reportApiData =="_DONE_NO_DATA_"){
						 echo "\n\nNo data found for report id $report_id for userid $user_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_DONE_NO_DATA_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(is_array($reportApiData) && !empty($reportApiData)){						
						
							echo "starting import data";
						  $completed = $this->importDailyInventoryHealth($reportApiData, $user_id);
						  if($completed) {
							$amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
							$amazonReportInfoModelObj->report_status ='_DONE_';
							$amazonReportInfoModelObj->state =1;
							$amazonReportInfoModelObj->save();
						   }
						echo "\n\nReport completed";
					 }else{
					 }
					 sleep(50);
					 } catch(Exception $e){
							//continue;
					}
				}
			 }
			} 
			$this->endCronJob($cron_id);
		  }
		  echo "Waiting to check the data";
		  sleep(50);
		  echo "Checking reports if still need import";
		$checkReports  =  AmazonReportInfo::findOne(["report_type"=>['_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_'],"state"=>'0']);
		if( ($checkReports && !empty($checkReports) ) && $counter <4 ) {
			// run the reports untill finish
			echo "Reports still not finished";
			$counter = $counter+1; 
			$this->actionImportInventoryHealthData($counter );
		}
		 //$this->updateInvoices();
		  echo "\n\ncronjob Completed successfully";
		  return true;
	  }
	  public function importDailyInventoryHealth($inventoryData, $user_id){		
				if(!empty($inventoryData)){
					$i=0;
					foreach ($inventoryData as $key=>$inventory) {						
						$inventoryModel	= new AmazonInventoryData();
						$sku								= $inventory['sku'];
						$snapDate							= $inventory['snapshot-date'];
						$snapshotDate						= date('Y-m-d', strtotime($snapDate));
						$inventoryModel->user_id					= $user_id;
						$inventoryModel->snapshot_date				= $snapshotDate;
						$inventoryModel->asin 						=$inventory['asin'];
						$inventoryModel->fnsku						= $inventory['fnsku'];
						$inventoryModel->sku						= $inventory['sku'];
						$inventoryModel->product_name				= $inventory['product-name'];
						$inventoryModel->total_quantity				= $inventory['total-quantity'];
						$inventoryModel->sellable_quantity			= $inventory['sellable-quantity'];
						$inventoryModel->unsellable_quantity 		= $inventory['unsellable-quantity'];
						$inventoryModel->currency  					= $inventory['currency'];
						$inventoryModel->your_price 				= $inventory['your-price'];
						$inventoryModel->sales_price  				= $inventory['sales-price'];
						$inventoryModel->lowest_afn_new_price 		= $inventory['lowest-afn-new-price'];
						$inventoryModel->import_date 				= date("Y-m-d");
						$inventoryModel->lowest_mfn_new_price 		= $inventory['lowest-mfn-new-price'];
						try{							
							if($inventoryModel->save()){
							
							} else{
								/* echo "<pre>";
								print_r($inventoryModel);
								die()*/
							}
						}

						catch(Exception $e){ 
							continue; 
						}						
					}									
				}
			return true;
		}

		public function sendInventoryReport($user_id, $report_date) {
			try{
				$companyInfoModel	= CompanyInfo::getModel($user_id);
				$companyName = str_replace(" ","-",$companyInfoModel->company_name);
				$inventory_report_email =  $companyInfoModel->inventory_report_email;
				if($inventory_report_email=="") {				
					return "Email not exist";
				}
				 $file_name =$companyName."_inventory_report_".$report_date.".csv";
				 $date ="";
				 list($startDate, $endDate)= explode(' - ', $report_date);
				 $column_start =	 date('d/m/Y', strtotime($startDate));
				 $column_end =	 date('d/m/Y', strtotime($endDate));
				 $date = $report_date;
				 $queryParams=[];
				 $queryParams['AmazonInventorySearch']['import_date']=$date;
				
				 $searchModel  = new AmazonInventorySearch();
				 $dataProvider = $searchModel->searchData($queryParams, $user_id);
				 $inventoryData = $dataProvider->query->all();
				 $inventoryProvider = [];
				if(count($inventoryData) >0){
				foreach($inventoryData as $key=>$data){
					$amazonModel			= new AmazonOrders();
					$inventoryModel			= new AmazonInventory();
					$productModel			= new AmazonProducts();
				   	$sku					= $data->sku;					
					$invoiceData			= $amazonModel->getInvoiceData($sku, $user_id, $date);
					$vat_percentage			= $productModel->getProductVat($sku, $user_id);
					$vat_percentage			= ($vat_percentage >0)?$vat_percentage:0;
					$tax_amount             =0;
					$totalVat				=0;
					if(is_array($invoiceData)&& !empty($invoiceData) ){
						$tax_amount			=  $invoiceData['tax'];
					}
					$invoiceSales			= $amazonModel->getSalesInventory($sku, $user_id, $date);
					$invoiceNoSales			= $amazonModel->getSalesInventory($sku, $user_id, $date,NULL);
					$invoiceSalesQty		= $amazonModel->getInventoryQty($sku, $user_id, $date);

					$invoiceSales			= ($invoiceSales!=NULL)?$invoiceSales:0;
					$invoiceNoSales			= ($invoiceNoSales!=NULL)?$invoiceNoSales:0;
					$total_amount			= (float)((float)$invoiceSales + (float)$invoiceNoSales);
					
					$avg_qty_start			= $inventoryModel->getInventoryQty($sku, $user_id,$startDate);
					$avg_qty_end			= $inventoryModel->getInventoryQty($sku, $user_id,$endDate);
					$sale_qty				= ($invoiceSalesQty>0)?$invoiceSalesQty:0;
					$net_amount				= $total_amount;
					if($vat_percentage >0){					
						$net_amount				= (float)(($total_amount*100)/(100+$vat_percentage));
						$totalVat				=  $total_amount -$net_amount;
					}
					$total_amount									= $total_amount+$tax_amount;
					$inventoryProvider[$key]['asin']				= $data->asin;
					$inventoryProvider[$key]['sku']					= $data->sku;
					$inventoryProvider[$key]['product_name']		= $data->product_name;
					$inventoryProvider[$key]['avg_qty_start']		= $avg_qty_start;
					$inventoryProvider[$key]['avg_qty_end']			= $avg_qty_end;
				    $inventoryProvider[$key]['sale_qty']			= $sale_qty;
					$inventoryProvider[$key]['invoice_sales']		= $invoiceSales;
					$inventoryProvider[$key]['no_invoice_sales']	= $invoiceNoSales;

					$inventoryProvider[$key]['tax_amount']			= $tax_amount;
					$inventoryProvider[$key]['vat_percentage']		= $vat_percentage;
					$inventoryProvider[$key]['vat_amount']			= $totalVat;
					$inventoryProvider[$key]['net_amount']			= $net_amount;	
					$inventoryProvider[$key]['total_amount']		= $total_amount;
					unset($amazonModel);
					unset($inventoryModel);
					unset($productModel);
				}
			
			   $exporter = new CsvGrid([
				'dataProvider' => new ArrayDataProvider([
						'allModels' => $inventoryProvider,
					]),
					'columns' => [
						[
							'attribute' => 'asin',
							'format' => 'text',
						    'header' => 'ASIN',
						],
						[
							'attribute' => 'sku',
							'format' => 'text',
						    'header' => 'SKU',
						],
						[
							'attribute' => 'product_name',
							'format' => 'text',
						    'header' => 'Product Name',
						],
						[
							'attribute' => 'avg_qty_start',
							'format' => 'text',
						    'header' => 'Av.Qty at '.$column_start,
						],
						[
							'attribute' => 'avg_qty_end',
							'format' => 'text',
						    'header' => 'Av.Qty at '.$column_end,
						],
						[
							'attribute' => 'sale_qty',
							'format' => 'text',
						    'header' => 'Sales Qty',
						],
							[
							'attribute' => 'invoice_sales',
							'format' => 'decimal',
						    'header' => 'Invoice Sales',
						],
							[
							'attribute' => 'no_invoice_sales',
							'format' => 'text',
						    'header' => 'NO invoice sales',
						],
						[
							'attribute' => 'tax_amount',
							'format' => 'decimal',							
						    'header' => 'Tax Amount',
						],
						[
							'attribute' => 'vat_percentage',
							'format' => 'decimal',							
						    'header' => 'Vat %',
						],
						[
							'attribute' => 'vat_amount',
							'format' => 'decimal',							
						    'header' => 'Vat Amount',
						],
						[
							'attribute' => 'net_amount',
							'format' => 'decimal',							
						    'header' => 'Net Amount',
						],
						[
							'attribute' => 'total_amount',
							'format' => 'decimal',							
						    'header' => 'Total Amount',
						],
					],				
				]);

				$path = Yii::getAlias('@webroot').'/uploads/reports/';
				$filePath = $path.''.$file_name;			
				$exporter->export()->saveAs($filePath);
				$text ="";
				if(file_exists($filePath)) {
					$getEmailFooterText		= InvoiceMailing::getEmailFooterText($user_id,"Amazon.it");
					$inventory_report_email =  $companyInfoModel->inventory_report_email;
					$inventory_report_emails = explode(",", $inventory_report_email);
					$emailsArray			= array_combine($inventory_report_emails, $inventory_report_emails);
					$body					= nl2br($getEmailFooterText);
					$userData				= User::findOne($user_id);
					$senderEmail			= $userData->email;
					$senderName				= $userData->name;
					$senderData[$senderEmail]=$senderName;
					$subject = str_replace("_"," ", $file_name);
					$subject = str_replace(".csv"," ", $subject);

					$message = Yii::$app->mailer->compose()
					->setFrom($senderData)
					->setTo($emailsArray)
					->setSubject($subject)
					->setHtmlBody($body);
					$message->attach($filePath);
					if($message->send()) {	
						$text ="Email Send Successfully for inventory Report";
						sleep(10);
					}else{
					  $text ="Error in Sending email for inventory Report";
					}
					sleep(20);
					unlink($filePath);
				}
			 } else{
				$text ="No record Found to export";
			 }
			}
			catch(Exception $e){
				$text = $e->getMessage() .' in inventory Report';
			}
			return $text;
		}

		// Set this cronjob for AFN orders to run after each 4 hours
	public function actionCreateInventoryAdjustmentReport() {
		   $getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {

			try{
			$user_id = 	$userData->id;
			$client = $this->getamazonClientInfo($user_id);
			
			if ($client && $client->validateCredentials()) {
					$todaydate = date("Y-m-d H:i:s", strtotime("-3 days"));				
					echo "Creating daily inventory adjustment report for User $user_id ";
					$fromDate	= new DateTime($todaydate);	   // '2018-01-01'
					$fromDate->setTimeZone(new DateTimeZone('GMT'));
					$endDateTime	= date("Y-m-d H:i:s"); //Null;
					$endDate	= new DateTime($endDateTime);	   // '2018-01-01'
					$endDate->setTimeZone(new DateTimeZone('GMT'));

					$cron_id = $this->startCronJob($user_id,"Create Inventory Adjustment report for  $user_id at $todaydate for _GET_FBA_FULFILLMENT_INVENTORY_ADJUSTMENTS_DATA_");
					$report_id = $client->RequestReport('_GET_FBA_FULFILLMENT_INVENTORY_ADJUSTMENTS_DATA_',$fromDate, $endDate,true);
					$amazonReportInfoModel = new AmazonReportInfo();

					if($report_id){
						$this->savereportdata($report_id, '_GET_FBA_FULFILLMENT_INVENTORY_ADJUSTMENTS_DATA_','SUBMITTED',$user_id,$todaydate, $endDateTime);
						echo "Report submit completed User $user_id";
					} 					
					sleep(30);
				echo "Report generated successfully";
				$this->endCronJob($cron_id);
			}			
			}catch(Exception $e){
			   echo"<pre>";
			   print_r($e->getMessage());
			   die();
			}
		}
		echo "DONE";
		 return true;
	}

	// get AFN Orders information
		 public function actionGetInventoryAdjustmentData($counter =0) {
		  
		 $getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			
			foreach($getUsers as  $key=>$userData) {
			$user_id	= 	$userData->id;
			
			 $cron_id = $this->startCronJob($user_id,"Get Inventory adjusment data for User $user_id");
			 $client	= $this->getamazonClientInfo($user_id);
			 $orderApiData =[];
			 if ($client && $client->validateCredentials()) {
				 echo "\\ Checking user for  ID $user_id";
				$amazonReportInfoModel = new AmazonReportInfo();
				$reportData =  $amazonReportInfoModel->getReportInfo($user_id, ['_GET_FBA_FULFILLMENT_INVENTORY_ADJUSTMENTS_DATA_']);

			 if(!empty($reportData)) {
				 foreach($reportData as $key=>$reportInfo) {
					 try{
					 $id        = $reportInfo->id;
					 $report_id = $reportInfo->report_id;
					 //$user_id   = $reportInfo->user_id;
					 $reportType = $reportInfo->report_type;
					 //echo "\n\n report type= $reportType for Report Id: $report_id Started"; 
					 $reportApiData = $client->GetReport($report_id);				 
					  
					 if(!is_array($reportApiData) && $reportApiData =="_CANCELLED_"){
					   echo "\n\nCancelled for user $user_id with Report id $report_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_CANCELLED_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(!is_array($reportApiData) && $reportApiData =="_DONE_NO_DATA_"){
						 echo "\n\nNo data found for report id $report_id for userid $user_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_DONE_NO_DATA_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(is_array($reportApiData) && !empty($reportApiData)){
						echo "\n\nReported started imported for userid $user_id";						
					    $completed = $this->importInventoryAdjustmentData($reportApiData, $user_id);
					   if($completed){
							$amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
							$amazonReportInfoModelObj->report_status ='_DONE_';
							$amazonReportInfoModelObj->state =1;
							$amazonReportInfoModelObj->save();
					   }
						
						echo "\n\nReport completed";
					 }else{
					 }
					 sleep(60);
					 } catch(Exception $e){
						 echo $e->getMessage();
						continue 2;
					}
				}
			 }
			} 
			$this->endCronJob($cron_id);
		  }
		  echo "Waiting to check the data";
		  sleep(60);
		  echo "Checking reports if still need import";
		$checkReports  =  AmazonReportInfo::findOne(["report_type"=>['_GET_FBA_FULFILLMENT_INVENTORY_ADJUSTMENTS_DATA_'],"state"=>'0']);
		if(($checkReports && !empty($checkReports)) && $counter < 4) {
			// run the reports untill finish
			echo "Reports still not finished";
			$counter = $counter+1;
			echo "\n \n Counter = $counter \n \n";
			$this->actionGetInventoryAdjustmentData($counter);
		}		 
		 echo "\n\ncronjob Completed successfully";
		 return true;
	  }

	  function importInventoryAdjustmentData($inventoryData, $user_id){					
		if(!empty($inventoryData)){
					$i=0;

					foreach ($inventoryData as $key=>$inventory) {						
						$inventoryModel							= new AmazonInventoryAdjustment();
						$transaction_item_id					= $inventory['transaction-item-id'];
						$sku									= $inventory['sku'];
						echo "Transaction ID =$transaction_item_id \n\n";
						$checkExistingRecords					= $inventoryModel->checkExistingProduct($sku, $user_id, $transaction_item_id);
						if($checkExistingRecords){
							// Skip existing items
							continue;
						}
						echo "\n\n AFTER \n\n";
						$inventoryModel->user_id				= $user_id;
						$inventoryModel->adjusted_date			= date('Y-m-d h:i:s', strtotime($inventory['adjusted-date']));
						$inventoryModel->transaction_item_id	= $inventory['transaction-item-id'];
						$inventoryModel->fnsku					= $inventory['fnsku'];
						$inventoryModel->product_name			= $inventory['product-name'];
						$inventoryModel->fulfillment_center_id	= $inventory['fulfillment-center-id'];
						$inventoryModel->quantity				= $inventory['quantity'];
						$inventoryModel->disposition			= $inventory['disposition'];
						$inventoryModel->reason					= $inventory['reason'];
						$inventoryModel->sku					= $sku;
						try{
							if($inventoryModel->save()){
							    echo "\n\n SAVED \n\n";
							} else{
								echo "<pre>";
								print_r($inventoryModel->getErrors());
								//die();
							}
						}
						catch(Exception $e){ 
							echo "<pre>";
								print_r($e->getMessage());
								//die();
							continue; 
						}						
					}
					return true;
				}
				return false;
		}

		//  This report should work once for a user. 
		public function actionCreateReportInventoryAdjustment() {

		   $getUsers = User::find()->where(["=","status","1"])->andWhere(['=','import_initial_inventory_adjustment', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {
			try{
			$user_id = 	$userData->id;
			
			$client = $this->getamazonClientInfo($user_id);
			if ($client && $client->validateCredentials()) {
				$mwsSettingsobj		= AmazonMwsSetting::getModel($user_id);
				$import_start_date  = $mwsSettingsobj->import_start_date;
				if($import_start_date =="" || $import_start_date==NULL){
					continue;
				}
				$i =1;

				$todaydate = date('Y-m-d', strtotime("-1 year"));
				$time		= strtotime($todaydate);
				$toDate		= date('Y-m-d');
				$fromDate	= new DateTime($todaydate);
				$fromDate->setTimeZone(new DateTimeZone('GMT'));
				$endDate	= new DateTime($toDate);

				echo "Creating report for User $user_id from $todaydate to $toDate";

				$cron_id = $this->startCronJob($user_id,"Create Inventory adjustment Report for User $user_id from $todaydate to $toDate");
				$report_id = $client->RequestReport('_GET_FBA_FULFILLMENT_INVENTORY_ADJUSTMENTS_DATA_',$fromDate, $endDate,true);
				//$amazonReportInfoModel = new AmazonReportInfo();
				if($report_id){
					$this->savereportdata($report_id, '_GET_FBA_FULFILLMENT_INVENTORY_ADJUSTMENTS_DATA_','SUBMITTED',$user_id,$todaydate, $toDate);
					echo "Report _GET_FBA_FULFILLMENT_INVENTORY_ADJUSTMENTS_DATA_ submit completed User $user_id";
				}
				sleep(10);						
				$this->updateUserStatus($user_id,'import_initial_inventory_adjustment');
				echo "Report generated successfully";
				$this->endCronJob($cron_id);
			}
			else {
				//Yii::$app->session->setFlash('error', "Amazion API information not validate");
				//die("Amazion API information not validate");
			}
			}catch(Exception $e){
				echo "<pre>";
				print_r($e->getMessage());
				sleep(30);
			}
		}
		echo "DONE";
		 return true;
	}

	// Will be run after each 4 hours;
		public function actionGenerateReimbursementsReport() {
		$getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			foreach($getUsers as  $key=>$userData) {
				try{
					$user_id	= 	$userData->id;
					$client = $this->getamazonClientInfo($user_id);
					if ($client && $client->validateCredentials()) {
							//$todaydate = date("Y-m-d", strtotime("-11 hours"));
							$todaydate = date("Y-m-d", strtotime("-58 days"));
							echo "Creating report for User $user_id \n\n";
							$fromDate	= new DateTime($todaydate);
							$fromDate->setTimeZone(new DateTimeZone('GMT'));
							//$endDate	= Null; 
							$endDateTime	= date("Y-m-d H:i:s"); //Null;
							$endDate	= new DateTime($endDateTime);	   // '2018-01-01'
							$endDate->setTimeZone(new DateTimeZone('GMT'));

							$report_id = $client->RequestReport('_GET_FBA_REIMBURSEMENTS_DATA_', $fromDate,$endDate, true);
							echo "Report created with id $report_id";
							if($report_id){
								$this->savereportdata($report_id, '_GET_FBA_REIMBURSEMENTS_DATA_','SUBMITTED',$user_id,$todaydate, $endDateTime);
							} 							
							sleep(40);
							echo "report for User $user_id completed \n\n";
						}
				}
				catch(Exception $e){
				
				}
			}
			echo "Done";
			 return true;
		}


		 public function actionGetReimbursementsData($counter =0) {
		  
		 $getUsers = User::find()->where(['=','status', "1"])->andWhere(['>', 'id', '1'])->all();
			
			if(empty($getUsers)){
				die("No User Exists");
			}
			
			foreach($getUsers as  $key=>$userData) {
			$user_id	= 	$userData->id;
			
			 $cron_id = $this->startCronJob($user_id,"Get reimbursements data for User $user_id");
			 $client	= $this->getamazonClientInfo($user_id);
			 $orderApiData =[];
			 if ($client && $client->validateCredentials()) {
				 echo "\\ Checking user for  ID $user_id";
				$amazonReportInfoModel = new AmazonReportInfo();
				$reportData =  $amazonReportInfoModel->getReportInfo($user_id, ['_GET_FBA_REIMBURSEMENTS_DATA_']);

			 if(!empty($reportData)) {
				 foreach($reportData as $key=>$reportInfo) {
					 try{
					 $id        = $reportInfo->id;
					 $report_id = $reportInfo->report_id;
					 //$user_id   = $reportInfo->user_id;
					 $reportType = $reportInfo->report_type;
					 //echo "\n\n report type= $reportType for Report Id: $report_id Started"; 
					 $reportApiData = $client->GetReport($report_id);				 
					  
					 if(!is_array($reportApiData) && $reportApiData =="_CANCELLED_"){
					   echo "\n\nCancelled for user $user_id with Report id $report_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_CANCELLED_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(!is_array($reportApiData) && $reportApiData =="_DONE_NO_DATA_"){
						 echo "\n\nNo data found for report id $report_id for userid $user_id";
					   $amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
					   $amazonReportInfoModelObj->report_status ='_DONE_NO_DATA_';
					   $amazonReportInfoModelObj->state =1;
					   $amazonReportInfoModelObj->save();
					 }
					 elseif(is_array($reportApiData) && !empty($reportApiData)){
						echo "\n\nReported started imported for userid $user_id";						
					    $completed = $this->importReimbursementsData($reportApiData, $user_id);
					   if($completed){
							$amazonReportInfoModelObj = AmazonReportInfo::findOne($id);
							$amazonReportInfoModelObj->report_status ='_DONE_';
							$amazonReportInfoModelObj->state =1;
							$amazonReportInfoModelObj->save();
					   }
						
						echo "\n\nReport completed";
					 }else{
					 }
					 sleep(60);
					 } catch(Exception $e){
						 echo $e->getMessage();
						continue 2;
					}
				}
			 }
			} 
			$this->endCronJob($cron_id);
		  }
		  echo "Waiting to check the data";
		  sleep(60);
		  echo "Checking reports if still need import";
		$checkReports  =  AmazonReportInfo::findOne(["report_type"=>['_GET_FBA_REIMBURSEMENTS_DATA_'],"state"=>'0']);
		if(($checkReports && !empty($checkReports)) && $counter < 4) {
			// run the reports untill finish
			echo "Reports still not finished";
			$counter = $counter+1;
			echo "\n \n Counter = $counter \n \n";
			$this->actionGetInventoryAdjustmentData($counter);
		}		 
		 echo "\n\ncronjob Completed successfully";
		 return true;
	  }

	  public function importReimbursementsData($reimbursementsData, $user_id){					
		if(!empty($reimbursementsData)){
					$i=0;				 
					foreach ($reimbursementsData as $key=>$inventory) {

						
						$reimbursementsModel							= new AmazonReimbursements();
						$reimbursement_id						= $inventory['reimbursement-id'];
						$sku									= $inventory['sku'];
						echo "Reimbursement ID =$reimbursement_id \n\n";
						$checkExistingRecords					= $reimbursementsModel->checkExistingProduct($sku, $user_id, $reimbursement_id);
						if($checkExistingRecords){
							// Skip existing items
							continue;
						}
						echo "\n\n AFTER \n\n";
						$reimbursementsModel->user_id						= $user_id;
						$reimbursementsModel->approval_date					= date('Y-m-d h:i:s', strtotime($inventory['approval-date']));
						$reimbursementsModel->reimbursement_id				= $reimbursement_id;
						$reimbursementsModel->case_id						= $inventory['case-id'];
						$reimbursementsModel->amazon_order_id				= $inventory['amazon-order-id'];
						$reimbursementsModel->reason						= $inventory['reason'];
						$reimbursementsModel->sku							= $sku;
						$reimbursementsModel->fnsku							= $inventory['fnsku'];
						$reimbursementsModel->asin							= $inventory['asin'];
						$reimbursementsModel->product_name					= $inventory['product-name'];
						$reimbursementsModel->item_condition 				= $inventory['condition'];
						$reimbursementsModel->currency_unit					= $inventory['currency-unit'];
						$reimbursementsModel->amount_per_unit				= $inventory['amount-per-unit'];
						$reimbursementsModel->amount_total					= $inventory['amount-total'];

						$reimbursementsModel->quantity_reimbursed_cash		= $inventory['quantity-reimbursed-cash'];
						$reimbursementsModel->quantity_reimbursed_inventory	= $inventory['quantity-reimbursed-inventory'];
						$reimbursementsModel->quantity_reimbursed_total		= $inventory['quantity-reimbursed-total'];
						$reimbursementsModel->original_reimbursement_id		= $inventory['original-reimbursement-id'];
						$reimbursementsModel->original_reimbursement_type	= $inventory['original-reimbursement-type'];
						
						try{
							if($reimbursementsModel->save()){
							    echo "\n\n SAVED \n\n";
							} else{
								echo "<pre>";
								print_r($reimbursementsModel->getErrors());
								die();
							}
						}
						catch(Exception $e){ 
							echo "<pre>";
								print_r($e->getMessage());
								die();
							continue; 
						}						
					}
					return true;
				}
				return false;
		}

	}
?>
<?php
	
	namespace frontend\controllers;
	
	use Yii;
	use yii2tech\spreadsheet\Spreadsheet;
	use yii\data\ActiveDataProvider;
	use frontend\models\User;
	use frontend\models\UserSearch;
	use frontend\models\AmazonOrders;
	use frontend\models\AmazonOrdersSearch;
	use frontend\models\AmazonProducts;
	use frontend\models\AmazonMwsSetting;
	use frontend\models\CompanyInfo;
	use frontend\models\InvoiceMailing;
	use frontend\models\InvoiceSettings;
	use frontend\models\VatRn;
	use frontend\models\CreditMemo;
	use frontend\models\AmazonLogInfo;
	use frontend\models\CreditMemoSearch;
	use frontend\models\CreditmemoSettings;
	use frontend\models\AmazonReportInfo;
	use frontend\models\AmazonInventory;
	use frontend\models\AmazonInventorySearch;
	use frontend\models\AmazonInventoryData;
	use frontend\models\AmazonInventoryDataSearch;	
	use yii\web\Controller;
	use yii\web\NotFoundHttpException;
	use yii\filters\AccessControl;
	use yii\web\UploadedFile;
	use yii\imagine\Image;  
	use Imagine\Image\Box;
	use yii2tech\csvgrid\CsvGrid;
	use yii\data\ArrayDataProvider;	
	use MCS\MWSClient;
	use DateTime;
	use Exception;
	use DateTimeZone;
	use Mpdf\Mpdf;
	use DOMDocument;

	/**
		* UserController implements the CRUD actions for User model.
	*/
	class UserController extends Controller
	{
		/**
			* {@inheritdoc}
		*/
		public function behaviors()
		{
			return [
			'access' => [
			'class' => AccessControl::className(),
			'only' => ['dashboard','invoices','products','importinvoices','importproducts', 'setting', 'savecompanyinfo','saveinvoicemailing','saveinvoicenumber','savevatrn','savemwssetting','testconnection','viewinvoicedetail','downloadpdf','setbuyervat','sendpdf','exportanalytics','exportaccount','removevtrn','wizard-setup','savewizardinfo','credit-memo','viewcreditmemodetail','downloadcreditmemopdf','sendcreditmemopdf','setbuyervatcc','productvatedit','importcreditmemo','setprotocolno','exportanalyticscreditmemo','exportcreditmemoaccount','savecreditmemonumber','savetab','getinventorydata','manage-inventory','exportanalyticsinventory','exportinventoryaccount','delete-inventory','send-sales-analytics-report','send-credit-note-analytics-report','send-sales-report','send-credit-note-account-report','updateprofile','import-order-request','import-credit-note-request','getinventoryreportdata','export-inventory-report','index','send-inventory-report','save-invoice-fields','save-creditmemo-fields'],
			'rules' => [
			[                        
			'allow' => true,
			'roles' => ['@'],
			],
			
			],
            ],            
			];
		}
		
		
		
		
		/**
			* Finds the User model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return User the loaded model
			* @throws NotFoundHttpException if the model cannot be found
		*/
		protected function findModel($id)
		{
			if (($model = User::findOne($id)) !== null) {
				return $model;
			}
			
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}
		public function actionIndex(){
		   $user_info = Yii::$app->user;
			if($user_info->id !=""){
			   $this->redirect(['user/dashboard']);
			   
			}else{
				$this->redirect(['site/index']);
			}
			return ;
		
		}
		
		public function actionDashboard() {
			$user_info = Yii::$app->user;			
			if($user_info->id !=""){
				$todaydate = date("Y-m-d");
				$current_month = date('Y-m-01');
				$orderObj		= new AmazonOrders();

				for ($i = 0; $i <= 11; $i++) 
					{
						$monthData			= date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
					    $months[$monthData] = $monthData;
					}
					$months =array_reverse($months);
					$monthData =array_keys($months);
					
					$gettotalOrders =$orderObj->getChartData(Yii::$app->user->id); 
					
					$non_matches = array_diff( array_keys($months),array_keys($gettotalOrders));
					  
					// foreach of those keys, set their associated values to zero in the second array
					foreach ($non_matches as $match) {
						$gettotalOrders[$match] = 0;
					}
					ksort($gettotalOrders);
					
					$gettotalSale =$orderObj->getChartSaleData(Yii::$app->user->id);
					
					$non_matches1 = array_diff( array_keys($months),array_keys($gettotalSale));
				    foreach ($non_matches1 as $match) {
						$gettotalSale[$match] = 0;
					}				
					ksort($gettotalSale);
					$saleArrayStr = array_values($gettotalSale);
					$countArrayStr = array_values($gettotalOrders);
					//$saleArrayStr = implode(",",$saleArray);
					//$countArrayStr = implode(",",$countArray);

				$getTodayOrder	=  $orderObj->getOrdersByDate($todaydate, $user_info->id);
				$getMonthlyOrder =  $orderObj->getMonthlyOrders($current_month, $todaydate, $user_info->id);
				$vatModel			= VatRn::getModel(Yii::$app->user->id);
				$vatNewModel        = new VatRn();

				return $this->render('dashboard', ['todayOrder' => $getTodayOrder,"monthlyOrder"=>$getMonthlyOrder,'orderModel'=>$orderObj,'monthData'=>$monthData,'saleArrayStr'=>$saleArrayStr,'countArrayStr'=>$countArrayStr,'vatModel'=>$vatModel,'vatNewModel'=>$vatNewModel]);
			}
		}
		public function actionInvoices(){
			
			$id	= 		Yii::$app->user->id;
			$userModel = $this->findModel($id);
			$searchModel = new AmazonOrdersSearch(); 			
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			Yii::$app->session->set('exportModel',Yii::$app->request->queryParams);
		    $invoiceModel = new AmazonOrders();
			return $this->render('invoices_grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'invoiceModel'=>$invoiceModel,
			'userModel'=>$userModel
			]);
		}
		public function getamazonClientInfo(){
			$mwsSettingsobj		= AmazonMwsSetting::getSellerInfo();
			if($mwsSettingsobj== false){
				Yii::$app->getSession()->setFlash('error', 'Your MWS Seller information not completed. Please complete');
				$this->redirect(['user/setting']);
			}
			$mws_seller_id = $mwsSettingsobj->mws_seller_id;
			$mws_auth_token = $mwsSettingsobj->mws_auth_token;
			$client = new MWSClient([
			'Marketplace_Id' => Yii::$app->params['marketplace_id'],
			'Seller_Id' => $mws_seller_id, //'AJ08MYWRY2147',
			'Access_Key_ID' => Yii::$app->params['aws_access_key'],
			'Secret_Access_Key' => Yii::$app->params['aws_secret_key'],
			'MWSAuthToken' => $mws_auth_token]);
			return 	$client;
		}
		
		public function actionImportinvoices(){
			
			/* try{
				
				$client = $this->getamazonClientInfo();
				if ($client->validateCredentials()) {
					$todaydate = date("Y-m-d", strtotime("-4 hours"));
					$fromDate = new DateTime($todaydate);	   // '2018-01-01'
					$orders = $client->ListOrders($fromDate, true, ['Shipped'],['AFN', 'MFN']);
					$this->importuserorders($orders, $client);
				}
				else {
					Yii::$app->session->setFlash('error', "Amazion API information not validate");
					//die("Amazion API information not validate");
					
				}
			}
			catch (Exception $e) {
				echo "<pre>";
				print_r($e->getMessage());
				die();
				$this->insertLog("Error in importing order : ".$e->getMessage());
				Yii::$app->session->setFlash('error', "Error in importing invoices ". $e->getMessage());
			} */
			exit;
			//return $this->redirect(['user/invoices']);
		}
		public function importuserorders($orders,$client){
		
				if(!empty($orders)){
					$invoiceSettings = new InvoiceSettings();
					$ordersData = $orders['ListOrders'];
						foreach ($ordersData as $key=>$order) {
							
							$amazonOrders		= new AmazonOrders();
							$checkIfOrderExist	= $amazonOrders->checkExistingOrder($order['AmazonOrderId'], Yii::$app->user->id);
							$updateInvoiceCounter = true;
							if(!empty($checkIfOrderExist)) {
								$amazonOrders		= $checkIfOrderExist;	
								$updateInvoiceCounter = false;
							}
							else{
									$invoice_number					= $invoiceSettings->getInvoiceNumber(Yii::$app->user->id);
									$amazonOrders->invoice_number	= $invoice_number; 
							}
							
							$amazonOrders->user_id						=Yii::$app->user->id;
							$amazonOrders->latest_ship_date				=(isset($order['LatestShipDate']))?date('Y-m-d h:i:s', strtotime($order['LatestShipDate'])):"";
							$amazonOrders->order_type					=(isset($order['OrderType']))?$order['OrderType']:"";
							$amazonOrders->purchase_date				=(isset($order['PurchaseDate']))?date('Y-m-d h:i:s', strtotime($order['PurchaseDate'])):"";
							$amazonOrders->buyer_email					=(isset($order['BuyerEmail']))?$order['BuyerEmail']:"";
							$amazonOrders->amazon_order_id				=$order['AmazonOrderId'];
							$amazonOrders->is_replacement_order			=(isset($order['IsReplacementOrder']))?$order['IsReplacementOrder']:0;
							$amazonOrders->last_update_date				=(isset($order['LastUpdateDate']))?date('Y-m-d h:i:s', strtotime($order['LastUpdateDate'])):""; 
							$amazonOrders->number_of_items_shipped		=$order['NumberOfItemsShipped'];
							$amazonOrders->ship_service_level			=$order['ShipServiceLevel'];
							$amazonOrders->order_status					=$order['OrderStatus'];
							$amazonOrders->sales_channel				=$order['SalesChannel'];
							$amazonOrders->shipped_by_amazon_tfm		=isset($order['ShippedByAmazonTFM'])?$order['ShippedByAmazonTFM']:"";
							$amazonOrders->is_business_order			=$order['IsBusinessOrder'];
							$amazonOrders->latest_delivery_date			=(isset($order['LatestDeliveryDate']))?date('Y-m-d h:i:s', strtotime($order['LatestDeliveryDate'])):""; 
							$amazonOrders->number_of_items_unshipped	=$order['NumberOfItemsUnshipped'];
							$amazonOrders->payment_method_detail		=$order['PaymentMethodDetails']['PaymentMethodDetail'];
							$amazonOrders->buyer_name					=$order['BuyerName'];
							$amazonOrders->earliest_delivery_date		= (isset($order['EarliestDeliveryDate']))?date('Y-m-d h:i:s', strtotime($order['EarliestDeliveryDate'])):""; 
							$amazonOrders->is_premium_order				=$order['IsPremiumOrder'];
							$amazonOrders->order_currency				=$order['OrderTotal']['CurrencyCode'];
							$amazonOrders->total_amount					=$order['OrderTotal']['Amount'];
							$amazonOrders->earliest_ship_date			=(isset($order['EarliestShipDate']))?date('Y-m-d h:i:s', strtotime($order['EarliestShipDate'])):""; 
							$amazonOrders->marketplace_id				=$order['MarketplaceId'];
							$amazonOrders->fulfillment_channel			=$order['FulfillmentChannel'];
							$amazonOrders->payment_method				=$order['PaymentMethod'];
							
							$amazonOrders->city						=$order['ShippingAddress']['City'];
							$amazonOrders->address_type					=isset($order['ShippingAddress']['AddressType'])?$order['ShippingAddress']['AddressType']:"";
							$amazonOrders->postal_code					=$order['ShippingAddress']['PostalCode'];
							$amazonOrders->state_or_region				= (isset($order['ShippingAddress']['StateOrRegion']))?$order['ShippingAddress']['StateOrRegion']:"";
							$amazonOrders->phone						= isset($order['ShippingAddress']['Phone'])?$order['ShippingAddress']['Phone']:"";
							$amazonOrders->country_code					= $order['ShippingAddress']['CountryCode'];
							$amazonOrders->customer_name				= $order['ShippingAddress']['Name'];
							$amazonOrders->address_2					= isset($order['ShippingAddress']['AddressLine1'])?$order['ShippingAddress']['AddressLine1']:"";
							$amazonOrders->is_prime						= $order['IsPrime'];
							$amazonOrders->shipment_category			= $order['ShipmentServiceLevelCategory'];
							if($amazonOrders->save()){
								if($updateInvoiceCounter== true) {
									$invoiceSettings->updateInvoiceNumber(Yii::$app->user->id);
								}
								
								//$items = $client->ListOrderItems($amazonOrders->amazon_order_id);
								//$this->importproducts($items);
							}
						}
						$nextToken = (isset($orders['NextToken']) && $orders['NextToken'] !="")?$orders['NextToken']:"";
						if(!empty($nextToken)){
						 $nextOrders = $client->ListOrdersByNextToken($nextToken);
						 sleep(60);
						 $this->importuserorders($nextOrders, $client);
					   }
						$this->insertLog("Invoices imported");
						Yii::$app->session->setFlash('success', "Invoices imported");
					}
					return true;
		}

		public function insertLog($logData){
			$logObj = new AmazonLogInfo();
			$logObj->insertLogData($logData);
			return true;
		}
		
		public function importproducts($items){
			try{ 
				if(is_array($items) && !empty($items)){
					foreach($items as $itemKey=>$itemData){
						$amazonProductObj	= new AmazonProducts(); 
						$checkProduct		= $amazonProductObj->checkExistingProduct($itemData['ASIN']);
						if(!empty($checkProduct)) {
							$amazonProductObj		= $checkProduct;								
						}
						$amazonProductObj->user_id		= Yii::$app->user->id;
						$amazonProductObj->product_name = $itemData['Title'];
						$amazonProductObj->sku			= $itemData['SellerSKU'];
						$amazonProductObj->asin			= $itemData['ASIN'];
						$amazonProductObj->price		= $itemData['ItemPrice']['Amount'];
						$amazonProductObj->condition_id = $itemData['ConditionId'];
						$amazonProductObj->save();
					}
				}
				} catch (Exception $e) {
				
			}
			return true;
		}
		public function actionSetting(){
			
			$amazonMwsSettingModel	 = AmazonMwsSetting::getModel(Yii::$app->user->id);
			$companyInfoModel		 = CompanyInfo::getModel(Yii::$app->user->id);
			$invoiceMailingModel	 = InvoiceMailing::getModel(Yii::$app->user->id);
			$invoiceSettingsModel	 = InvoiceSettings::getModel(Yii::$app->user->id);
			$creditmemoSettingsModel = CreditmemoSettings::getModel(Yii::$app->user->id);
			$vatRnModel				 = VatRn::getModel(Yii::$app->user->id);

			$invoiceModel = new AmazonOrders();
			$creditMemoModel = new CreditMemo();

			
			return $this->render('settings', ['amazonMwsSettingModel' => $amazonMwsSettingModel,'companyInfoModel'=>$companyInfoModel,'invoiceMailingModel'=>$invoiceMailingModel,'invoiceSettingsModel'=>$invoiceSettingsModel,'vatRnModel'=>$vatRnModel,'creditmemoSettingsModel'=>$creditmemoSettingsModel,'invoiceModel'=>$invoiceModel,'creditMemoModel'=>$creditMemoModel]);
		}
		public function actionSavecompanyinfo(){
			
			try{
				$model = new CompanyInfo();
				
				if (Yii::$app->request->isPost) {
					
					$checkExisting = CompanyInfo::findOne(["user_id"=>Yii::$app->user->id]);
					if($checkExisting){
					  $model = $checkExisting;
					}
					
					//die();
					$model->user_id =Yii::$app->user->id; 
					if ($model->load(Yii::$app->request->post())) {
						
						if (isset($_FILES['CompanyInfo']['tmp_name']['company_logo']) && !empty($_FILES['CompanyInfo']['tmp_name']['company_logo'])) { 
							$path = Yii::getAlias('@webroot').'/uploads/';
							/* if(!empty($model->company_logo)) {
								$exitingimageName= $path.''.$model->company_logo;
								$exitingThumbimageName= $path.'thumb/'.$model->company_logo;
								if(file_exists($exitingimageName)){
									unlink($exitingimageName);
								}
								if(file_exists($exitingThumbimageName)){
									unlink($exitingThumbimageName);
								}
								
							} */
							$imageData = UploadedFile::getInstance($model, 'company_logo');				
							$filename =uniqid()."_company_header";
							$model->company_logo = $filename.'.'.$imageData->extension;
							$imageData->saveAs($path . $filename . '.' . $imageData->extension);
							$thumbPath = $path.'thumb/';
							$imagine = Image::getImagine();
							$image = $imagine->open($path . $model->company_logo);				
							$image->resize(new Box(100, 100))->save($thumbPath.''. $model->company_logo, ['quality' => 70]); 
						}
						$model->invoice_select_fields		= implode(",", $model->invoice_select_fields);
						$model->creditmemo_selected_field  = implode(",", $model->creditmemo_selected_field);					
						if($model->save()){
							Yii::$app->session->setFlash('success', "Company information saved.");
							$this->insertLog("Company information modified");
							}else{
							Yii::$app->session->setFlash('error', "Error in saving data.");			
						}
						
					}
				} 
				} catch (Exception $e) {
					//echo "<pre>";
					Yii::$app->session->setFlash('error', "Error ".$e->getMessage());
			}
			return $this->redirect(['user/setting']);
		}
		public function actionSaveinvoicemailing()
		{
			$model = InvoiceMailing::getModel(Yii::$app->user->id);
			if (Yii::$app->request->isPost) {
				$model->user_id =Yii::$app->user->id; 
				if ($model->load(Yii::$app->request->post())) {
					if ($model->validate()) {
						if($model->save()){
							Yii::$app->session->setFlash('success', "Invoices emailing information updated successfully.");
							$this->insertLog("Invoices emailing information updated");
							} else{
							Yii::$app->session->setFlash('error', "Data not saved.");
						}
					}
				}
			}
			return $this->redirect(['user/setting']);
		}
		public function actionSaveinvoicenumber()
		{
			$model = InvoiceSettings::getModel(Yii::$app->user->id);
			if (Yii::$app->request->isPost) {
				$model->user_id =Yii::$app->user->id; 
				if ($model->load(Yii::$app->request->post())) {
					if ($model->validate()) {
						if($model->save()){
							Yii::$app->session->setFlash('success', "Invoices number setting updated successfully.");
							$this->insertLog("Invoices number setting updated");
							} else{
							Yii::$app->session->setFlash('error', "Data not saved.");
						}
					}
				}
			}
			return $this->redirect(['user/setting']);
		}

		public function actionSavecreditmemonumber()
		{
			$model = CreditmemoSettings::getModel(Yii::$app->user->id);
			if (Yii::$app->request->isPost) {
				$model->user_id =Yii::$app->user->id; 
				if ($model->load(Yii::$app->request->post())) {
					if ($model->validate()) {
						if($model->save()){
							Yii::$app->session->setFlash('success', "Creditmemo number setting updated successfully.");
							$this->insertLog("Creditmemo number setting updated");
							} else{
							Yii::$app->session->setFlash('error', "Data not saved.");
						}
					}
				}
			}
			return $this->redirect(['user/setting']);
		}

		public function actionSavevatrn()
		{	 try{
			if (Yii::$app->request->isPost) {	
				
				$postData		= Yii::$app->request->post();
				
				if(!empty($postData['VatRn'])){ 
					
					foreach($postData['VatRn'] as $key=>$data){
						$model = new VatRn();
						if($key !=0)
						$model = VatRn::checkExisting(Yii::$app->user->id, $key);
					
						/*if(!isset($data['country'])){
							$model->country='default';
						}*/
						
						$model->user_id				= Yii::$app->user->id;
						$model->country				= isset($data['country'])?$data['country']:'default';
						$model->rate_percentage		= $data['rate_percentage'];
						$model->vat_no				= $data['vat_no'];
						$model->central_bank		= $data['central_bank'];
										
						if ($model->validate()) {
							$model->save();
						}
					}
					Yii::$app->getSession()->setFlash('success', 'Vat saved successfully');
					$this->insertLog("Vat updated");
				}
			}
			} catch (Exception $e) {
					Yii::$app->getSession()->setFlash('error', 'Error in saving data '. $e->getMessage());
		}
		return $this->redirect(['user/setting',"#"=>"w7-tab4"]);
		}
		public function actionSavemwssetting()
		{
			$model = AmazonMwsSetting::getModel(Yii::$app->user->id);
			if (Yii::$app->request->isPost) {
				$model->user_id =Yii::$app->user->id; 
				if ($model->load(Yii::$app->request->post())) {
					if ($model->validate()) {
						$checkImportStart = $model->start_invoice_import;
						$dateImportStart = 	 $model->import_start_date;
						$userModel = $this->findModel(Yii::$app->user->id);
						if($checkImportStart==0 && ($dateImportStart !="" && $dateImportStart != NULL)){
						   $model->import_start_date =1;						   
						   $userModel->import_initial_orders =1;
						   $userModel->import_initial_creditmemo =1;
						   $userModel->import_initial_inventory_adjustment  =1; 
						}
						if($model->save()){
							$userModel->save();
							Yii::$app->getSession()->setFlash('success', 'Data saved successfully');
							$this->insertLog("Amazon MWS information updated");
							}else{
							Yii::$app->getSession()->setFlash('error', 'Error in saving data');
						}
					}else{
						Yii::$app->getSession()->setFlash('error', 'Error in saving MWS Setting data. This Seller Id and token already exist');
					}
				}
			}
			return $this->redirect(['user/setting']);
		}
		public function actionTestconnection(){
			if (Yii::$app->request->isPost) {
				$seller_id			=trim(Yii::$app->request->post('seller_id')); 
				$MWSAuthToken		=trim(Yii::$app->request->post('auth_token'));
				$client = new MWSClient([
				'Marketplace_Id' => Yii::$app->params['marketplace_id'],
				'Seller_Id' => $seller_id,
				'Access_Key_ID' => Yii::$app->params['aws_access_key'],
				'Secret_Access_Key' => Yii::$app->params['aws_secret_key'],
				'MWSAuthToken' => $MWSAuthToken]);
				 
				if ($client->validateCredentials()) {
					echo "Your information are validated";
					$this->insertLog("Your information are validated");
				}
				else{

					echo "Your information not validated";
					$this->insertLog("MWS information failed to validate");
				}
			}
			exit;
		}
		public function actionViewinvoicedetail() {
			$amazon_order_id = Yii::$app->request->get('amazon_order_id');
			if(isset($amazon_order_id) && $amazon_order_id !="") {				
				$client				= $this->getamazonClientInfo();
				$items				= $client->ListOrderItems($amazon_order_id);
				$amazonOrdersModel	= new AmazonOrders();
				$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
				$productModel		= new AmazonProducts();
				$amazonOrdersModel	= $amazonOrdersModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
				$vat			= new VatRn(); //VatRn::getUserVat(Yii::$app->user->id);
				return $this->render('invoice_details', ['companyModel' => $companyInfoModel,'amazonOrderModel'=>$amazonOrdersModel,'vat'=>$vat,'productModel'=>$productModel,'orderItems'=>$items]);
			}
		}
		public function actionViewcreditmemodetail() {
			$amazon_order_id = Yii::$app->request->get('amazon_order_id');
			if(isset($amazon_order_id) && $amazon_order_id !="") {
				$amazonCreditModel	= new CreditMemo();
				$amazonOrdersModel	= new AmazonOrders();
				$productModel		= new AmazonProducts();
				$orderDetails 		= $amazonOrdersModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
				$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
				$amazonCreditModel	= $amazonCreditModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
				$vat				= new  VatRn(); //VatRn::getUserVat(Yii::$app->user->id);
				//$invoiceNo = $amazonCreditModel->invoice_number;				
				return $this->render('creditmemo_details', ['order_id'=>$amazon_order_id,'companyModel' => $companyInfoModel,'amazonOrderModel'=>$orderDetails,'amazonCreditModel'=>$amazonCreditModel,'vat'=>$vat,'productModel'=>$productModel]);
			}
		}
		public function actionDownloadpdf(){
			$amazon_order_id = Yii::$app->request->get('amazon_order_id');
			if(isset($amazon_order_id) && $amazon_order_id !="") {
				$client				= $this->getamazonClientInfo();
				$items				= $client->ListOrderItems($amazon_order_id);
				$amazonOrdersModel	= new AmazonOrders();
				$productModel		= new AmazonProducts();
				$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
				$amazonOrdersModel	= $amazonOrdersModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
				$vat				= new VatRn(); //VatRn::getUserVat(Yii::$app->user->id);
				$content = $this->renderPartial('_invoice_pdf', ['companyModel' => $companyInfoModel,'amazonOrderModel'=>$amazonOrdersModel,'vat'=>$vat,'productModel'=>$productModel,'orderItems'=>$items]);
				$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'default_font_size' => 9,'default_font' => 'Tahoma,sans-serif']);
				$mpdf->WriteHTML($content);
				$mpdf->Output();
				$this->insertLog("Invoice pdf created");
			}
		}
		public function actionDownloadcreditmemopdf(){
			$amazon_order_id = Yii::$app->request->get('amazon_order_id');
			if(isset($amazon_order_id) && $amazon_order_id !="") {
				$amazonCreditModel	= new CreditMemo();
				$amazonOrdersModel	= new AmazonOrders();
				$productModel		= new AmazonProducts();
				$orderDetails 		= $amazonOrdersModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
				$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
				$amazonCreditModel	= $amazonCreditModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
				//$invoiceNo = $amazonCreditModel->invoice_number;
				$vat			= new VatRn();//VatRn::getUserVat(Yii::$app->user->id);
				$content		= $this->renderPartial('_invoice_creditmemopdf', ['order_id'=>$amazon_order_id,'companyModel' => $companyInfoModel,'amazonOrderModel'=>$orderDetails,'amazonCreditModel'=>$amazonCreditModel,'vat'=>$vat,'productModel'=>$productModel]);
				$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'default_font_size' => 9,'default_font' => 'Tahoma,sans-serif']);
				$mpdf->WriteHTML($content);
				$mpdf->Output();
				$this->insertLog("Invoice pdf created");
			}
		}
		public function actionSendpdf(){
			try{
				 
				
				$amazon_order_id = Yii::$app->request->get('amazon_order_id');
				//$amazon_order_id = "402-4493308-0846727";
				if(isset($amazon_order_id) && $amazon_order_id !="") {				
					
					//$items				= $client->ListOrderItems($amazon_order_id);
					$client				= $this->getamazonClientInfo();
					$items				= $client->ListOrderItems($amazon_order_id);
					$amazonOrdersModel	= new AmazonOrders();
					$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
					$amazonOrdersModel	= $amazonOrdersModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
					$vat			= VatRn::getUserVat(Yii::$app->user->id);
					$productModel		= new AmazonProducts();
					$content			= $this->renderPartial('_invoice_pdf', ['companyModel' => $companyInfoModel,'amazonOrderModel'=>$amazonOrdersModel,'vat'=>$vat,'productModel'=>$productModel,'orderItems'=>$items]);					
					$getEmailFooterText = InvoiceMailing::getEmailFooterText(Yii::$app->user->id,$amazonOrdersModel->sales_channel)	;
					$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'default_font_size' => 9,'default_font' => 'Tahoma,sans-serif']);
					$mpdf->WriteHTML($content);
					$filename	= uniqid()."_invoice_".$amazon_order_id.".pdf";
					$path = Yii::getAlias('@webroot').'/uploads/invoices/';
					$filePath = $path.''.$filename;
					$mpdf->Output($filePath,'F');
						
					if(file_exists($filePath)) {
						
						//$body ="Hi,<br> <p>Please find the attached file for invoice for amazon order : $amazon_order_id.</p>". $getEmailFooterText;
						$body = nl2br($getEmailFooterText);
						 $userData = User::findOne(Yii::$app->user->id);
						$senderEmail =$userData->email;
						$message = Yii::$app->mailer->compose()
						->setFrom($senderEmail)
						->setTo($amazonOrdersModel->buyer_email)
						->setSubject("Invoice PDF for Order number: $amazon_order_id")
						->setHtmlBody($body);
						$message->attach($filePath);
						if($message->send()){
							$amazonOrdersModel->invoice_email_sent =1;
							$amazonOrdersModel->invoice_send_date =date('Y-m-d');
							$amazonOrdersModel->email_sending_type ='Manual';
							Yii::$app->session->setFlash('success', "Email send successfully.");
							$this->insertLog("Invoice pdf sent to customer for Amazon order  $amazon_order_id");
							$amazonOrdersModel->save();
							}else{
							Yii::$app->session->setFlash('error', "Error in sending pdf email.");
							$this->insertLog("Error in sending pdf email for Amazon order  $amazon_order_id");
						}
						unlink($filePath);
						
						}else{
							Yii::$app->session->setFlash('error', "PDF File Not exists");
							$this->insertLog("PDF File Not exists for Amazon order  $amazon_order_id");
					}
				}
				} catch (Exception $e) {
					Yii::$app->session->setFlash('error', $e->getMessage());
					$this->insertLog("PDF File Not exists for Amazon order  $amazon_order_id ".$e->getMessage());
				//echo "<pre>"; print_r($e->getMessage()); die();
			}
			exit;
		}
		public function actionSendcreditmemopdf(){
			try{
			
				$amazon_order_id = Yii::$app->request->get('amazon_order_id');	
				//$amazon_order_id = "405-2056010-8215538";
				if(isset($amazon_order_id) && $amazon_order_id !="") {
					$amazonCreditModel	= new CreditMemo();
					$amazonOrdersModel	= new AmazonOrders();
					//$refundAmount       = $this->getRefundOrderAmount($amazon_order_id);
					//$client				= $this->getamazonClientInfo();
					//$items				= $client->ListOrderItems($amazon_order_id);
					//echo "<pre>"; print_r($items); die();
					//$orderDetails 			= $client->GetOrder($amazon_order_id);					
					$orderDetails 		= $amazonOrdersModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
					$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
					$amazonCreditModel	= $amazonCreditModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
					//$invoiceNo = $amazonCreditModel->invoice_number;
					$vat				= VatRn::getUserVat(Yii::$app->user->id);
					$productModel		= new AmazonProducts();

					$content			= $this->renderPartial('_invoice_creditmemopdf', ['order_id'=>$amazon_order_id,'companyModel' => $companyInfoModel,'amazonOrderModel'=>$orderDetails,'amazonCreditModel'=>$amazonCreditModel,'vat'=>$vat,'productModel'=>$productModel]);					
					$getEmailFooterText = InvoiceMailing::getEmailFooterText(Yii::$app->user->id,$orderDetails->sales_channel);
					
					$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'default_font_size' => 9,'default_font' => 'Tahoma,sans-serif']);
					$mpdf->WriteHTML($content);
					$filename	= uniqid()."_creditmemo_".$amazon_order_id.".pdf";
					$path = Yii::getAlias('@webroot').'/uploads/invoices/';
					$filePath = $path.''.$filename;
					$mpdf->Output($filePath,'F');						
					if(file_exists($filePath)) {						
						//$body ="Hi,<br> <p>Please find the attached file for invoice for amazon order : $amazon_order_id.</p>". $getEmailFooterText;
						$body = nl2br($getEmailFooterText);
						$userData = User::findOne(Yii::$app->user->id);
						$senderEmail =$userData->email;
						$message = Yii::$app->mailer->compose()
						->setFrom($senderEmail)
						->setTo($orderDetails->buyer_email)
						
						->setSubject("Invoice Credit Memo for Order number: $amazon_order_id")
						->setHtmlBody($body);
						$message->attach($filePath);
						if($message->send()){
							Yii::$app->session->setFlash('success', "Email send successfully");
							$amazonCreditModel->creditmemo_email_sent =1;
							$amazonCreditModel->creditmemo_email_date =date('Y-m-d');
							$amazonCreditModel->email_sending_type ='Manual';
							
							$this->insertLog("Invoice credit memo sent to customer for Amazon order  $amazon_order_id");
							$amazonCreditModel->save();
							}else{
								Yii::$app->session->setFlash('error', "Error in sending credit memo email");
							}
						unlink($filePath);
						}else{
							Yii::$app->session->setFlash('error', "PDF File Not exists");						
					}
				}
				} catch (Exception $e) {
					Yii::$app->session->setFlash('error', $e->getMessage());
				//echo "<pre>"; print_r($e->getMessage()); die();
			}
			exit;
		}
		
		public function actionSetbuyervat() {
			try{
				$amazon_order_id = Yii::$app->request->get('amazon_order_id');
				if((isset($amazon_order_id) && $amazon_order_id !="") ) {
					$data = Yii::$app->request->post();
					if(isset($data['vatnumber']) && !empty($data['vatnumber'])){
						$amazonObj = new AmazonOrders();
						$amazonOrdersModel	= $amazonObj->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
						$amazonOrdersModel->buyer_vat =	$data['vatnumber'];
						if($amazonOrdersModel->save()){
							echo "Data saved successfully";
							Yii::$app->session->setFlash('success', "Vat saved successfully.");
							$this->insertLog("Buyer vat Saved successfully");
						}
					}
				}
				}catch (Exception $e) {
					Yii::$app->session->setFlash('error', "Error in Saving data.");
				echo "Error in Saving data";
			}
			exit;
		}

		public function actionSetbuyervatcc() {
			try{
				
				$amazon_order_id = Yii::$app->request->get('amazon_order_id');
				if((isset($amazon_order_id) && $amazon_order_id !="") ) {
					$user_id = Yii::$app->user->id;
					$data = Yii::$app->request->post();
					
					if(isset($data['vatnumber']) && !empty($data['vatnumber'])){
						$creditMemoModel	= new CreditMemo();
						$amazonOrdersModel	= $creditMemoModel->checkExistingOrder($amazon_order_id, $user_id);
						$amazonOrdersModel->buyer_vat =	$data['vatnumber'];
						if($amazonOrdersModel->save()){
							echo "Data saved successfully";
							$this->insertLog("Buyer vat Saved successfully");
						}
					}
				}
				}catch (Exception $e) {
				echo "Error in Saving data";
			}
			exit;
		}
		public function actionExportanalytics() {

			$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
			$file_name =$companyName."_sales_analytics_report_".date("m-Y").".xls";			
			$searchModel = new AmazonOrdersSearch();
			$queryParams=[];
			if(Yii::$app->session->get('exportModel')){
				$queryParams=Yii::$app->session->get('exportModel');
				if($queryParams['AmazonOrdersSearch']['year']!="" || $queryParams['AmazonOrdersSearch']['month'] !=""){
					$year   = (!empty($queryParams['AmazonOrdersSearch']['year']))?$queryParams['AmazonOrdersSearch']['year']:date('Y');
					if(!empty($queryParams['AmazonOrdersSearch']['month'])){
						$month  = $queryParams['AmazonOrdersSearch']['month'];
						$file_name =$companyName."_sales_analytics_report_".$month."-".$year.".xls";
					}else{
					   $file_name =$companyName."_sales_analytics_report_".$year.".xls";
					}
				}				
			}
			$id			= Yii::$app->user->id;
			$userModel	= $this->findModel($id);			

			if(!empty($userModel->selected_invoice_fields)) {
					$columns	= explode(",",$userModel->selected_invoice_fields);
			}else{			
				$columns = ['order_import_date','invoice_number','protocol_invoice_number','purchase_date','buyer_name','buyer_email','buyer_vat','number_of_items_shipped','item_price','latest_ship_date','amazon_order_id','product_sku','product_name','last_update_date','ship_service_level','order_status','sales_channel','latest_delivery_date','marketplace_id','fulfillment_channel','payment_method','customer_name','address_2','address_type','city','state_or_region','country_code','postal_code','phone','fulfillment_center_id','shipment_id','ship_address_1','ship_address_2','ship_address_3','ship_city','ship_state','ship_postal_code','ship_country','ship_phone_number','ship_promotion_discount','tracking_number','carrier','shipping_price','shipping_tax','gift_wrap_price','gift_wrap_tax'];
			}
			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			}
			$dataProvider = $searchModel->search($queryParams);
			$exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
				'columns' => $attributeArray,
			]);
			return $exporter->send($file_name);
		}


		public function actionExportanalyticscreditmemo() {
			
			$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
			$file_name =$companyName."_creditnote_analytics_report_".date("m-Y").".xls";
			 
			$searchModel = new CreditMemoSearch(); 
			$queryParams=[];
			if(Yii::$app->session->get('creditExportModel')){
				$queryParams=Yii::$app->session->get('creditExportModel');				
				if(!empty($queryParams['CreditMemoSearch']['year']) || !empty($queryParams['CreditMemoSearch']['month'])){					
					$year   = (!empty($queryParams['CreditMemoSearch']['year']))?$queryParams['CreditMemoSearch']['year']:date('Y');
					if(!empty($queryParams['CreditMemoSearch']['month'])){
						$month  = $queryParams['CreditMemoSearch']['month'];
						$file_name =$companyName."_creditnote_analytics_report_".$month."-".$year.".xls";
					}else{
					   $file_name =$companyName."_creditnote_analytics_report_".$year.".xls";
					}
				}	

			}
			$id			= Yii::$app->user->id;
			$userModel	= $this->findModel($id);			

			if(!empty($userModel->selected_creditnote_fields)) {
				$columns	= explode(",",$userModel->selected_creditnote_fields);
			}else{
			$columns = ['credit_memo_no','invoice_number','order_import_date','return_date','seller_sku','product_sku','product_name','seller_order_id','qty_return','amazon_order_id','license_plate_number','order_adjustment_item_id','fulfillment_center_id','reason','detailed_disposition','status'];
			}
			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			}

			$dataProvider = $searchModel->search($queryParams);
		   	$exporter = new Spreadsheet([
            'dataProvider' =>$dataProvider,
			 'columns' =>$attributeArray,
			]);
			return $exporter->send($file_name);

			
			
		}
		public function actionExportaccount() {
			
			$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);

			$file_name =$companyName."_sales_report_".date("m-Y").".xls";
			$searchModel = new AmazonOrdersSearch(); 
			$queryParams=[];
			if(Yii::$app->session->get('exportModel')){
				$queryParams=Yii::$app->session->get('exportModel');
				if($queryParams['AmazonOrdersSearch']['year']!="" || $queryParams['AmazonOrdersSearch']['month'] !=""){
					
					$year   = (!empty($queryParams['AmazonOrdersSearch']['year']))?$queryParams['AmazonOrdersSearch']['year']:date('Y');
					if(!empty($queryParams['AmazonOrdersSearch']['month'])){
						$month  = $queryParams['AmazonOrdersSearch']['month'];
						$file_name =$companyName."_sales_report_".$month."-".$year.".xls";
					}else{
					   $file_name =$companyName."_sales_report_".$year.".xls";
					}
				}	
			}
			$id			= Yii::$app->user->id;
			$userModel	= $this->findModel($id);

			if(!empty($userModel->selected_invoice_fields)) {
				$columns	= explode(",",$userModel->selected_invoice_fields);
			}else{
			$columns = ['order_import_date','invoice_number','protocol_invoice_number','purchase_date','buyer_name','buyer_email','buyer_vat','number_of_items_shipped','item_price','latest_ship_date','amazon_order_id','product_sku','product_name','last_update_date','ship_service_level','order_status','sales_channel','latest_delivery_date','marketplace_id','fulfillment_channel','payment_method','customer_name','address_2','address_type','city','state_or_region','country_code','postal_code','phone','fulfillment_center_id','shipment_id','ship_address_1','ship_address_2','ship_address_3','ship_city','ship_state','ship_postal_code','ship_country','ship_phone_number','ship_promotion_discount','tracking_number','carrier','shipping_price','shipping_tax','gift_wrap_price','gift_wrap_tax'];
			}
			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			}
			$dataProvider = $searchModel->search($queryParams);
		  	$exporter = new Spreadsheet([
            'dataProvider' =>$dataProvider,
				'columns' =>$attributeArray, 
			]);
			return $exporter->send($file_name);
		}

		public function actionExportcreditmemoaccount() {

		    $companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
			$file_name =$companyName."_creditnote_account_report_".date("m-Y").".xls";
			
			$searchModel = new CreditMemoSearch(); 
			$queryParams=[];
			if(Yii::$app->session->get('creditExportModel')){
				$queryParams=Yii::$app->session->get('creditExportModel');
				if(!empty($queryParams['CreditMemoSearch']['year']) || !empty($queryParams['CreditMemoSearch']['month'])){					
					$year   = (!empty($queryParams['CreditMemoSearch']['year']))?$queryParams['CreditMemoSearch']['year']:date('Y');
					if(!empty($queryParams['CreditMemoSearch']['month'])){
						$month  = $queryParams['CreditMemoSearch']['month'];
						$file_name =$companyName."_creditnote_account_report_".$month."-".$year.".xls";
					}else{
					   $file_name =$companyName."_creditnote_account_report_".$year.".xls";
					}
				}	
			}
			$dataProvider = $searchModel->search($queryParams);
			$id			= Yii::$app->user->id;
			$userModel	= $this->findModel($id);

			if(!empty($userModel->selected_creditnote_fields)) {
				$columns	= explode(",",$userModel->selected_creditnote_fields);
			}else{
			$columns =     ['credit_memo_no','invoice_number','order_import_date','return_date','seller_sku','product_sku','product_name','seller_order_id','qty_return','amazon_order_id','license_plate_number','order_adjustment_item_id','fulfillment_center_id','reason','detailed_disposition','status'];
			}
			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			}
			
			$exporter = new Spreadsheet([
            'dataProvider' =>$dataProvider,
				'columns' =>$attributeArray, 
			]);
			return $exporter->send($file_name);
		}
		
		public function actionRemovevtrn(){
			
			if (Yii::$app->request->isPost) {
				$postData = Yii::$app->request->post();
				if(isset($postData['is_ajax']) && $postData['is_ajax']==1){
					$rowId = $postData['row_id'];
					$model = VatRn::findOne($rowId);
					$model->delete();
					echo "Data deleted successfully";
					$this->insertLog("Data deleted successfully");
				}
			}
			exit;
		}
		public function actionWizardSetup(){
			
			$amazonMwsSettingModel	= AmazonMwsSetting::getModel(Yii::$app->user->id);
			$companyInfoModel		= CompanyInfo::getModel(Yii::$app->user->id);
			$invoiceMailingModel	= InvoiceMailing::getModel(Yii::$app->user->id);
			$invoiceSettingsModel	= InvoiceSettings::getModel(Yii::$app->user->id);
			$vatRnModel				= VatRn::getModel(Yii::$app->user->id);
			$creditmemoSettingsModel		= CreditmemoSettings::getModel(Yii::$app->user->id);
			return $this->render('_wizard', ['amazonMwsSettingModel' => $amazonMwsSettingModel,'companyInfoModel'=>$companyInfoModel,'invoiceMailingModel'=>$invoiceMailingModel,'invoiceSettingsModel'=>$invoiceSettingsModel,'vatRnModel'=>$vatRnModel,'creditmemoSettingsModel'=>$creditmemoSettingsModel]);
			
		}
		public function actionSavewizardinfo(){
			if(Yii::$app->request->isPost) {
				$postData = Yii::$app->request->post();
				$this->savecompanyinfo($postData, $_FILES);
				$this->saveinvoicemailing($postData);
				$this->saveinvoicenumber($postData);
				$this->savevatrn($postData);
				$this->savemwssetting($postData);
				$this->savecreditmemosetting($postData);
			}
			return $this->redirect(['user/setting']);	
		}
		
		private function savecompanyinfo($postData, $FILES){
			
			try{
				$model = CompanyInfo::getModel(Yii::$app->user->id);
				if (!empty($postData)) {
					
					$model->user_id =Yii::$app->user->id; 
					if ($model->load($postData)) {
						if (isset($FILES['CompanyInfo']) && !empty($FILES['CompanyInfo'])) {
							$path = Yii::getAlias('@webroot').'/uploads/';
							if(!empty($model->company_logo)){
								$exitingimageName= $path.''.$model->company_logo;
								$exitingThumbimageName= $path.'thumb/'.$model->company_logo;
								if(file_exists($exitingimageName)){
									unlink($exitingimageName);
								}
								if(file_exists($exitingThumbimageName)){
									unlink($exitingThumbimageName);
								}
								
							}
							$imageData = UploadedFile::getInstance($model, 'company_logo');				
							$filename =uniqid()."_company_header";
							$model->company_logo = $filename.'.'.$imageData->extension;
							$imageData->saveAs($path . $filename . '.' . $imageData->extension);
							$thumbPath = $path.'thumb/';
							$imagine = Image::getImagine();
							$image = $imagine->open($path . $model->company_logo);				
							$image->resize(new Box(100, 100))->save($thumbPath.''. $model->company_logo, ['quality' => 70]); 
						}
						if($model->save()){
							Yii::$app->session->setFlash('success', "Company information saved.");
							$this->insertLog("Company information modified");
							}else{
							Yii::$app->session->setFlash('error', "Error in saving company information data.");			
						}
						
					}
				} 
				} catch (Exception $e) {
				
			}
			return true;
		}
		private function saveinvoicemailing($postData)
		{
			$model = InvoiceMailing::getModel(Yii::$app->user->id);
			if (!empty($postData)) {
				$model->user_id =Yii::$app->user->id; 
				if ($model->load($postData)) {
					if ($model->validate()) {
						if($model->save()){
							Yii::$app->session->setFlash('success', "Invoices emailing information updated successfully.");
							$this->insertLog("Invoices emailing information updated");
							} else{
							Yii::$app->session->setFlash('error', "Error in saving Invoice mailing");
						}
					}
				}
			}
			return true;
		}
		private function saveinvoicenumber($postData)
		{
			$model = InvoiceSettings::getModel(Yii::$app->user->id);
			if (!empty($postData)) {
				$model->user_id =Yii::$app->user->id; 
				if ($model->load($postData)) {
					if ($model->validate()) {
						if($model->save()){
							Yii::$app->session->setFlash('success', "Invoices number setting updated successfully.");
							$this->insertLog("Invoices number setting updated");
							} else{
							Yii::$app->session->setFlash('error', "Error in Saving Invoice Number.");
						}
					}
				}
			}
			return true;
		}

		private function savecreditmemosetting($postData)
		{
			$model = CreditmemoSettings::getModel(Yii::$app->user->id);
			if (!empty($postData)) {
				$model->user_id =Yii::$app->user->id; 
				if ($model->load($postData)) {
					if ($model->validate()) {
						if($model->save()){
							Yii::$app->session->setFlash('success', "Creditmemo number setting updated successfully.");
							$this->insertLog("Creditmemo number setting updated");
							} else{
							Yii::$app->session->setFlash('error', "Error in Saving Creditmemo Number.");
						}
					}
				}
			}
			return true;
		}
		private function savevatrn($postData)
		{	 try{
			if (!empty($postData)) {	
				
				//$postData		= Yii::$app->request->post();				
				if(!empty($postData['VatRn'])){ 
					
					foreach($postData['VatRn'] as $key=>$data){
						
						$model = new VatRn();
						$model = VatRn::checkExisting(Yii::$app->user->id, $key);
						if(!isset($data['country'])){
							$model->country='default';
						}
						
						$model->user_id				= Yii::$app->user->id;
						$model->country				= $data['country'];
						$model->rate_percentage		= $data['rate_percentage'];
						$model->vat_no				= $data['vat_no'];
						$model->central_bank		= $data['central_bank'];
						if ($model->validate()) {
							$model->save();
						}
					}
					Yii::$app->getSession()->setFlash('success', 'Vat saved successfully');
					$this->insertLog("Vat updated");
				}
			}
			} catch (Exception $e) {
			Yii::$app->getSession()->setFlash('error', 'Error in saving Vat RN data');
		}
		return true;
		
		}
		private function savemwssetting($postData)
		{
			$model = AmazonMwsSetting::getModel(Yii::$app->user->id);
			if (!empty($postData)) {
				$model->user_id =Yii::$app->user->id; 
				if ($model->load($postData)) {
					if ($model->validate()) {
						if($model->save()){
							Yii::$app->getSession()->setFlash('success', 'Data saved successfully');
							$this->insertLog("Amazon MWS information updated");
							}else{
							Yii::$app->getSession()->setFlash('error', 'Error in saving MWS Setting data');
						}
					}else{
						Yii::$app->getSession()->setFlash('error', 'Error in saving MWS Setting data. This Seller Id and token already exist');
					}
				}
			}
			return true;
		}
		function actionGetRefundOrders() {
			
			//$amazon_order_id = "408-3520642-6771517";
			$mwsSettingsobj		= AmazonMwsSetting::getSellerInfo();
			if($mwsSettingsobj== false){
				Yii::$app->getSession()->setFlash('error', 'Your MWS Seller information not completed. Please complete');
				$this->redirect(['user/setting']);
			}
			$mws_seller_id = $mwsSettingsobj->mws_seller_id;
			$mws_auth_token = $mwsSettingsobj->mws_auth_token;		
			
			$serviceUrl = "https://mws-eu.amazonservices.com/Finances/2015-05-01";	  
			$config = array (
			'ServiceURL' => $serviceUrl,
			'ProxyHost' => null,
			'ProxyPort' => -1,
			'ProxyUsername' => null,
			'ProxyPassword' => null,
			'MaxErrorRetry' => 3,
			);
			
			$service = new \MWSFinancesService_Client(
			Yii::$app->params['aws_access_key'],
			Yii::$app->params['aws_secret_key'],
			"MSW Finance",
			2,
			$config);
			
			$date =gmdate('Y-m-d\TH:i:s.u\Z', strtotime("-1 hour"));
			$request = new \MWSFinancesService_Model_ListFinancialEventsRequest();
			$request->setSellerId($mws_seller_id);
			$request->setMWSAuthToken($mws_auth_token);
			//$request->setAmazonOrderId($amazon_order_id );
			//$request->setFinancialEventGroupId($mws_auth_token);
			$request->setPostedAfter($date);
			//$request->setPostedBefore($mws_auth_token);
			
			$response = $service->ListFinancialEvents($request);
			$financereEvent = $response->getListFinancialEventsResult()->getFinancialEvents();

			//$refundAmount =0;
			$user_id = Yii::$app->user->id;
			if($financereEvent->isSetRefundEventList() ) {
				$refund = $financereEvent->getRefundEventList();
				foreach($refund as $key=>$classObj){
					$refundAmount =0; 
					if($classObj->isSetShipmentItemAdjustmentList()){
					$shipCharge = $classObj->getShipmentItemAdjustmentList();
					$amazon_order_id = $classObj->getAmazonOrderId();
					$postDate        = $classObj->getPostedDate();
					$marketplaceName  = $classObj->getMarketplaceName();
					$sellerOrderId    = $classObj->getSellerOrderId();
					$returnQty       =0;
					foreach($shipCharge as $key=>$shipData) {
							if($shipData->isSetItemFeeAdjustmentList()){
								$itemFeeAdjustmentList		= $shipData->getItemFeeAdjustmentList();
							 }
							if($shipData->isSetPromotionAdjustmentList()){								
								$promotionAdjustmentList    = $shipData->getPromotionAdjustmentList();
							}
							$currencyCode ='EUR';
							if($shipData->isSetItemChargeAdjustmentList()) {
								$ItemChargeAdjustmentList	= $shipData->getItemChargeAdjustmentList(); 
								//$shipData->ItemChargeAdjustmentList;							
								$returnQty					= $shipData->getQuantityShipped();
								$orderAdjustmentItemId		= $shipData->getOrderAdjustmentItemId();
								$sellerSKU					= $shipData->getSellerSKU();
								foreach($ItemChargeAdjustmentList as $key=>$shipCurrency){	
									$amount = $shipCurrency->getChargeAmount()->getCurrencyAmount();
									$currencyCode =$shipCurrency->getChargeAmount()->getCurrencyCode();
									$refundAmount = $refundAmount+$amount;
								}								
							}

							if($refundAmount !=0) {
							//echo $refundAmount; die();
							$invoiceSettings	= new InvoiceSettings();
							$creditMemoModel	= new CreditMemo();
							$checkOrder			= $creditMemoModel->checkExistingOrder($amazon_order_id, $user_id);
							$updateInvoiceCounter = true;
							if(!empty($checkOrder)) {
								$creditMemoModel		= $checkOrder;								
								$updateInvoiceCounter = false;
							}else{
								$invoice_number						= $invoiceSettings->getInvoiceNumber($user_id);
								$creditMemoModel->invoice_number	= $invoice_number; 
							}
							$creditMemoModel->user_id = $user_id;
							$creditMemoModel->amazon_order_id = $amazon_order_id ;
							$creditMemoModel->qty_return = $returnQty ;
							$creditMemoModel->order_adjustment_item_id = $orderAdjustmentItemId ;
							$creditMemoModel->seller_sku = $sellerSKU;
							$creditMemoModel->currency_code = $currencyCode;
							$creditMemoModel->markeplace = $marketplaceName;
							$creditMemoModel->seller_order_id = $sellerOrderId;
							$creditMemoModel->total_amount_refund = abs($refundAmount);
							$creditMemoModel->date = $postDate;
							if($creditMemoModel->save()){
								if($updateInvoiceCounter == true) {
									$invoiceSettings->updateInvoiceNumber($user_id);
								}						
							}
							echo "<br>";
							echo "Amazon order $amazon_order_id Imported successfully for user ID $user_id";
						}
						}
					}
				}
			}		
			exit;
		}
		
		
		function getRefundOrderAmount($amazon_order_id){
			
			//$amazon_order_id = "408-3520642-6771517";
			$mwsSettingsobj		= AmazonMwsSetting::getSellerInfo();
			if($mwsSettingsobj== false){
				Yii::$app->getSession()->setFlash('error', 'Your MWS Seller information not completed. Please complete');
				$this->redirect(['user/setting']);
			}
			$mws_seller_id = $mwsSettingsobj->mws_seller_id;
			$mws_auth_token = $mwsSettingsobj->mws_auth_token;		
			
			$serviceUrl = "https://mws-eu.amazonservices.com/Finances/2015-05-01";	  
			$config = array (
			'ServiceURL' => $serviceUrl,
			'ProxyHost' => null,
			'ProxyPort' => -1,
			'ProxyUsername' => null,
			'ProxyPassword' => null,
			'MaxErrorRetry' => 3,
			);
			
			$service = new \MWSFinancesService_Client(
			Yii::$app->params['aws_access_key'],
			Yii::$app->params['aws_secret_key'],
			"MSW Finance",
			2,
			$config);
			
			$date = date("Y-m-d H:i:s", strtotime("-3 months"));
			$request = new \MWSFinancesService_Model_ListFinancialEventsRequest();
			$request->setSellerId($mws_seller_id);
			$request->setMWSAuthToken($mws_auth_token);
			$request->setAmazonOrderId($amazon_order_id );
			
			$response = $service->ListFinancialEvents($request);
			$refund = $response->getListFinancialEventsResult()->getFinancialEvents()->getRefundEventList();
			$refundAmount =0;
			if(!empty($refund) ){
				foreach($refund as $key=>$classObj){
					$shipCharge = $classObj->ShipmentItemAdjustmentList;
					if($shipCharge && !empty($shipCharge)){
						foreach($shipCharge as $key=>$shipData){
							$ItemChargeAdjustmentList =$shipData->ItemChargeAdjustmentList;
							if($ItemChargeAdjustmentList && !empty($ItemChargeAdjustmentList)){
								
								foreach($ItemChargeAdjustmentList as $key=>$shipCurrency){									
									$amount = $shipCurrency->getChargeAmount()->getCurrencyAmount();
									$refundAmount = $refundAmount+$amount;
								}								
							}
							
						}
						
					}
				}
			}
			return ($refundAmount);
		}
		public function actionCreditMemo(){	
			$id		= Yii::$app->user->id;
			$userModel	= $this->findModel($id);
			$searchModel = new CreditMemoSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			 Yii::$app->session->set('creditExportModel',Yii::$app->request->queryParams);
			  $invoiceModel = new CreditMemo();
			return $this->render('/creditmemo/index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
				'invoiceModel'=>$invoiceModel,
				'userModel'=>$userModel
			]);
		}


		public function actionImportcreditmemo(){
			
				try{
					$user_id = 	Yii::$app->user->id;					
					$client				= $this->getamazonClientInfo($user_id);			
					if (!empty($client)  && $client->validateCredentials()) {

						echo "Import Credit memo started for user $user_id";
						$mwsSettingsobj		= AmazonMwsSetting::getModel($user_id);
							if($mwsSettingsobj== false){
								return false;
								//continue; // no user setting exist to import creditmemo
							 }
							$mws_seller_id = $mwsSettingsobj->mws_seller_id;
							$mws_auth_token = $mwsSettingsobj->mws_auth_token;
							
							$serviceUrl = "https://mws-eu.amazonservices.com/Finances/2015-05-01";	  
							$config = array (
							'ServiceURL' => $serviceUrl,
							'ProxyHost' => null,
							'ProxyPort' => -1,
							'ProxyUsername' => null,
							'ProxyPassword' => null,
							'MaxErrorRetry' => 3,
							);
							
							$service = new \MWSFinancesService_Client(
							Yii::$app->params['aws_access_key'],
							Yii::$app->params['aws_secret_key'],
							"MSW Finance",
							2,
							$config); 			
							$date =gmdate('Y-m-d\TH:i:s.u\Z', strtotime("-3 months"));
							$request = new \MWSFinancesService_Model_ListFinancialEventsRequest();
							$request->setSellerId($mws_seller_id);
							$request->setMWSAuthToken($mws_auth_token);
							//$request->setAmazonOrderId($amazon_order_id );
							$request->setPostedAfter($date);
							$this->addRefundOrderAmount($user_id, $service, $request);
							echo "Request completed for user $user_id";	
					
				}
				
			}
			catch (Exception $e) {
					echo "<pre>";
					print_r($e->getMessage());
					//die();
				}
			echo "Cron Run Successfully";
			exit;
	}

	function addRefundOrderAmount($user_id, $service, $request) {
		
			$response = $service->ListFinancialEvents($request);
			if($response->getListFinancialEventsResult()->isSetFinancialEvents()) {
				$financereEvent = $response->getListFinancialEventsResult()->getFinancialEvents();
				$this->importCreditMemoData($financereEvent, $user_id);
				if($response->getListFinancialEventsResult()->isSetNextToken())	 {
					$nextToken = $response->getListFinancialEventsResult()->getNextToken();
					if($nextToken){
						$merchantId = $request->getSellerId(); 
						$getMSWToken = $request->getMWSAuthToken(); 
						$requestToken = new \MWSFinancesService_Model_ListFinancialEventsByNextTokenRequest();
						$requestToken->setSellerId($merchantId);
						$requestToken->setNextToken($nextToken);
						$requestToken->setMWSAuthToken($getMSWToken);
						$responseToken = $service->ListFinancialEventsByNextToken($requestToken);
						$this->importCreditMemoWithNextToken($user_id, $service, $responseToken, $merchantId, $getMSWToken);
					}
				}
			}
			return true;
		}

		function importCreditMemoData($financereEvent, $user_id){
		
		
		if($financereEvent->isSetRefundEventList() ) {
				$refund = $financereEvent->getRefundEventList();
				foreach($refund as $key=>$classObj){				
					$refundAmount =0; 
					if($classObj->isSetShipmentItemAdjustmentList()){
					$shipCharge = $classObj->getShipmentItemAdjustmentList();
					$amazon_order_id = $classObj->getAmazonOrderId();
					$postDate        = $classObj->getPostedDate();
					$marketplaceName  = $classObj->getMarketplaceName();
					$sellerOrderId    = $classObj->getSellerOrderId();
					$returnQty       =0;
					foreach($shipCharge as $key=>$shipData) {
							if($shipData->isSetItemFeeAdjustmentList()){
								$itemFeeAdjustmentList		= $shipData->getItemFeeAdjustmentList();
							 }
							if($shipData->isSetPromotionAdjustmentList()){								
								$promotionAdjustmentList    = $shipData->getPromotionAdjustmentList();
							}
							$currencyCode ='EUR';
							if($shipData->isSetItemChargeAdjustmentList()) {
								$ItemChargeAdjustmentList	= $shipData->getItemChargeAdjustmentList(); 
								//$shipData->ItemChargeAdjustmentList;							
								$returnQty					= $shipData->getQuantityShipped();
								$orderAdjustmentItemId		= $shipData->getOrderAdjustmentItemId();
								$sellerSKU					= $shipData->getSellerSKU();

								foreach($ItemChargeAdjustmentList as $key=>$shipCurrency){	
									//$chargeType   = $shipCurrency->getChargeType();
									$amount			= $shipCurrency->getChargeAmount()->getCurrencyAmount();
									$currencyCode	= $shipCurrency->getChargeAmount()->getCurrencyCode();
									$refundAmount	= $refundAmount+$amount;

								}								
							}

							if($refundAmount !=0) {
							//echo $refundAmount; die();
							$invoiceSettings	= new InvoiceSettings();
							$creditMemoModel	= new CreditMemo();
							$checkOrder			= $creditMemoModel->checkExistingOrder($amazon_order_id, $user_id);
							$updateInvoiceCounter = true;
							if(!empty($checkOrder)) {
								$creditMemoModel		= $checkOrder;								
								$updateInvoiceCounter = false;
							}else{
								$creditmemo_number		= $creditmemoSettings->getCreditmemoNumber($user_id);
								$creditMemoModel->credit_memo_no	= $creditmemo_number; 
								//$creditMemoModel->invoice_number	= $invoice_number; 
							}

							/* $checkOrder			= $creditMemoModel->checkExistingOrder($amazon_order_id, $user_id);
							$updateInvoiceCounter = true;
							if(!empty($checkOrder)) {
								$creditMemoModel		= $checkOrder;								
								$updateInvoiceCounter = false;
							}else{
								$invoice_number						= $invoiceSettings->getInvoiceNumber($user_id);
								$creditMemoModel->invoice_number	= $invoice_number; 
							}*/
							$checkIfOrderExist	= $amazonOrders->checkExistingOrder($amazon_order_id, $user_id);
							$creditMemoModel->invoice_number	= $checkIfOrderExist->invoice_number;

							$creditMemoModel->user_id = $user_id;
							$creditMemoModel->amazon_order_id = $amazon_order_id ;
							$creditMemoModel->qty_return = $returnQty ;
							$creditMemoModel->order_adjustment_item_id = $orderAdjustmentItemId ;
							$creditMemoModel->seller_sku = $sellerSKU;
							$creditMemoModel->currency_code = $currencyCode;
							$creditMemoModel->markeplace = $marketplaceName;
							$creditMemoModel->seller_order_id = $sellerOrderId;
							$creditMemoModel->total_amount_refund = abs($refundAmount);
							$creditMemoModel->date = $postDate;
							if($creditMemoModel->save()){
								if($updateInvoiceCounter == true) {
									$invoiceSettings->updateInvoiceNumber($user_id);
								}						
							}
							echo "<br>";
							echo "Amazon order $amazon_order_id Imported successfully for user ID $user_id";
						}
						} 
				}
				}
			}
			return true;
		}
		public function importCreditMemoWithNextToken($user_id, $service, $responseToken, $merchantId, $getMSWToken) {
			$events = $responseToken->getListFinancialEventsByNextTokenResult();			
			if($events->isSetFinancialEvents()){
			  $financialEvents = $events->getFinancialEvents();
			  if($financialEvents->isSetRefundEventList()){
				  $this->importCreditMemoData($financialEvents, $user_id);
			  }			  
			}
			if($events->isSetNextToken()) {				
				$nextToken = $events->getNextToken();
				$requestTokenNew = new \MWSFinancesService_Model_ListFinancialEventsByNextTokenRequest();
				$requestTokenNew->setSellerId($merchantId);
				$requestTokenNew->setNextToken($nextToken);
				$requestTokenNew->setMWSAuthToken($getMSWToken);
				$requestTokenNew = $service->ListFinancialEventsByNextToken($requestTokenNew);
				sleep(10);
				$this->importCreditMemoWithNextToken($user_id, $service, $requestTokenNew, $merchantId, $getMSWToken);
			}
			return true;
		}
		public function actionSetprotocolno(){
		 
		  try{
				$amazon_order_id = Yii::$app->request->get('amazon_order_id');
				if((isset($amazon_order_id) && $amazon_order_id !="") ) {
					$data = Yii::$app->request->post();
					if(isset($data['protocol_invoice_number']) && !empty($data['protocol_invoice_number'])){
						$amazonObj = new AmazonOrders();
						$amazonOrdersModel	= $amazonObj->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
						$amazonOrdersModel->protocol_invoice_number =	$data['protocol_invoice_number'];
						if($amazonOrdersModel->save()){
							echo "Data saved successfully";
							$this->insertLog("Protocol invoice number Saved successfully");
						}
					}
				}
				}catch (Exception $e) {
				echo "Error in Saving data";
			}
			exit;
		
		}
		public function actionSavetab(){
			if (Yii::$app->request->isPost) {
				$tabPar = Yii::$app->request->post();
				$tabid = $tabPar['curTab'];
				Yii::$app->session->set('currentTab',$tabid);
			}
		}

		public function actionGetinventorydata(){
		   $user_id	= 		Yii::$app->user->id;	
		 $getUsers = User::find()->where(['=','status', "1"])->andWhere(['=', 'id', $user_id])->all();
			
			 if(empty($getUsers)){
				die("No User Exists");
			} 
			
			foreach($getUsers as  $key=>$userData) {			
			
			 $client	= $this->getamazonClientInfo();
			 $orderApiData =[];
			 if ($client && $client->validateCredentials()) {
				 echo "\\ Checking user for  ID $user_id";
				$amazonReportInfoModel = new AmazonReportInfo();
				$reportData =  $amazonReportInfoModel->getReportInfo($user_id, ['_GET_MERCHANT_LISTINGS_DATA_']);

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
					 elseif(!empty($reportApiData)){						 
						echo "\n\nReported started imported for userid $user_id";
						echo "\n\n report type= $reportType";
						  $completed = $this->importMechantInventory($reportApiData, $user_id);
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
					 sleep(120);
					 } catch(Exception $e){
						
					}
				 }
				}
			 }
		  }
		 
	  }
		function importMechantInventory($reportApiData, $user_id) {
			 		 
			foreach($reportApiData as $key=>$data){
				
			   $amazonInventory = new AmazonInventory();
				$productSku = 	$data['seller-sku'];
			    $checkIfProductExist	= $amazonInventory->checkExistingProduct($productSku, $user_id);
				if(!empty($checkIfProductExist)) {
					$amazonInventory =  $checkIfOrderExist;
				}				
				$amazonInventory->item_name				=  $data['item-name'];
				$amazonInventory->item_description		= $data['item-description'];				
				$amazonInventory->listing_id			= $data['listing-id'];				
				$amazonInventory->price					= $data['price'];
				$amazonInventory->quantity				= $data['quantity'];
				$amazonInventory->open_date				= (isset($data['open-date']) && !empty($data['open-date']))?date('Y-m-d', strtotime($data['open-date'])):date('Y-m-d');
				//$data['open-date'];
				$amazonInventory->image_url					= $data['image-url'];				
				$amazonInventory->item_is_marketplace		= $data['item-is-marketplace'];				
				//$amazonInventory->product_id_type			= $data['product-id-type'];				
				$amazonInventory->shop_shipping_fee			= $data['zshop-shipping-fee'];				
				$amazonInventory->item_note					= $data['item-note'];
				$amazonInventory->item_condition			= $data['item-condition'];
				$amazonInventory->shop_category1			= $data['zshop-category1'];
				$amazonInventory->shop_browse_path			= $data['zshop-browse-path'];
				$amazonInventory->shop_storefront_feature	= $data['zshop-storefront-feature'];
				$amazonInventory->asin1						= $data['asin1'];
				$amazonInventory->asin2						= $data['asin2'];
				$amazonInventory->asin3						= $data['asin3'];
				$amazonInventory->seller_sku				= $productSku;				
				$amazonInventory->will_ship_internationally	= $data['will-ship-internationally'];
				$amazonInventory->expedited_shipping		= $data['expedited-shipping'];
				$amazonInventory->product_id				= $data['product-id'];
				$amazonInventory->bid_for_featured_placement =  $data['bid-for-featured-placement'];
				$amazonInventory->add_delete				= $data['add-delete'];
				$amazonInventory->pending_quantity			= $data['pending-quantity'];
				$amazonInventory->fulfillment_channel		= $data['fulfillment-channel'];
				$amazonInventory->business_price			= $data['Business Price'];
				$amazonInventory->merchant_shipping_group	= $data['merchant-shipping-group'];
				$amazonInventory->created_date				= date('Y-m-d');
				$amazonInventory->user_id					= $user_id;

				$getMarketplace   = explode("_",$amazonInventory->fulfillment_channel);
				$amazonInventory->marketplace_code =(isset($getMarketplace[1]))?$getMarketplace[1]:"";

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

		public function actionManageInventory(){
			
			$searchModel = new AmazonInventorySearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			
			return $this->render('/amazoninventory/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			]);
		}
		public function actionExportinventoryaccount(){
			$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
		    $file_name =$companyName."_inventory_account_".date("m-Y").".xls";
			$exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => AmazonInventory::find()->where(['user_id' => Yii::$app->user->id]),
            ])				
			]);
			return $exporter->send($file_name);
		}

		public function actionExportanalyticsinventory(){
			$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);

			$file_name =$companyName."_inventory_account_".date("m-Y").".xls";
			$exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => AmazonInventory::find()->where(['user_id' => Yii::$app->user->id]),
            ]),				
			]);
			return $exporter->send($file_name);
		}

		public function actionDeleteInventory($id)
		{
			$id = Yii::$app->request->get('id');
			if($id !=NULL && $id >0){
				$user_id = Yii::$app->user->id;
				$model = AmazonInventory::findOne(['id'=>$id,'user_id'=>$user_id]);
				if (!empty($model)) {
					$model->delete();
					return $this->redirect(['manage-inventory']);
				}
			}
		}
		public function actionSendSalesAnalyticsReport() {
			try{
			$user_id = Yii::$app->user->id;
			$companyInfoModel	= CompanyInfo::getModel($user_id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
			$analytics_email =  $companyInfoModel->analytics_email;
			$analytics_email =(isset($_POST['email']) && !empty($_POST['email']))?$_POST['email']:$analytics_email;
			if(isset($_POST['attribute_data']['attributes']) && !empty($_POST['attribute_data']['attributes'])){
				$columns = $_POST['attribute_data']['attributes'];
				$id		= Yii::$app->user->id;
				$userModel	= $this->findModel($id);
				$userModel->selected_invoice_fields = implode(",",$columns);
				$userModel->save();
			}
			else{
					$columns = ['order_import_date','invoice_number','protocol_invoice_number','purchase_date','buyer_name','buyer_email','buyer_vat','number_of_items_shipped','item_price','latest_ship_date','amazon_order_id','product_sku','product_name','last_update_date','ship_service_level','order_status','sales_channel','latest_delivery_date','marketplace_id','fulfillment_channel','payment_method','customer_name','address_2','address_type','city','state_or_region','country_code','postal_code','phone','fulfillment_center_id','shipment_id','ship_address_1','ship_address_2','ship_address_3','ship_city','ship_state','ship_postal_code','ship_country','ship_phone_number','ship_promotion_discount','tracking_number','carrier','shipping_price','shipping_tax','gift_wrap_price','gift_wrap_tax'];
			}

			if($analytics_email==""){
				Yii::$app->getSession()->setFlash('error', 'Analytics email not set in company settings. Please set the email there.');
				$this->insertLog("Error In sending analytics email: 'Analytics email not set in company settings. Please set the email there.");
				return true;
			}

			$file_name =$companyName."_sales_analytics_report_".date("m-Y").".csv";			
			$searchModel = new AmazonOrdersSearch();
			$queryParams=[];
					

			if(Yii::$app->session->get('exportModel')){
				$queryParams=Yii::$app->session->get('exportModel');
				if($queryParams['AmazonOrdersSearch']['year']!="" || $queryParams['AmazonOrdersSearch']['month'] !=""){
					$year   = (!empty($queryParams['AmazonOrdersSearch']['year']))?$queryParams['AmazonOrdersSearch']['year']:date('Y');
					if(!empty($queryParams['AmazonOrdersSearch']['month'])){
						$month  = $queryParams['AmazonOrdersSearch']['month'];
						$file_name =$companyName."_sales_analytics_report_".$month."-".$year.".csv";
					}else{
					   $file_name =$companyName."_sales_analytics_report_".$year.".csv";
					}
				}				
			}
			$export_report_range =(isset($_POST['export_report_range']) && !empty($_POST['export_report_range']))?$_POST['export_report_range']:"";

			if($export_report_range !=""){
				//$queryParams['AmazonOrdersSearch']['purchase_date'] = $export_report_range;
				$queryParams['AmazonOrdersSearch']['order_import_date'] = $export_report_range;
				$file_name =$companyName."_sales_analytics_report_".$export_report_range.".csv";
			}

			
			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			}			 
			$dataProvider = $searchModel->search($queryParams);
			$exporter = new CsvGrid([
            'dataProvider' => $dataProvider,
				'columns' => $attributeArray,
			]);

			$path = Yii::getAlias('@webroot').'/uploads/reports/';
			$filePath = $path.''.$file_name;
			//$exporter->save($filePath);
			$exporter->export()->saveAs($filePath);
		    if(file_exists($filePath)) {
				$getEmailFooterText = InvoiceMailing::getEmailFooterText($user_id,"Amazon.it");
				//$analytics_email =  $companyInfoModel->analytics_email;
				$analytics_emails = explode(",", $analytics_email);
				$emailsArray = array_combine($analytics_emails, $analytics_emails);
				$subject = str_replace("_"," ", $file_name);
				$subject = str_replace(".csv"," ", $subject);
				$body = nl2br($getEmailFooterText);
				$userData = User::findOne($user_id);
				$senderEmail =$userData->email;
				$senderName =$userData->name;
				$senderData[$senderEmail]=$senderName;

				$message = Yii::$app->mailer->compose()
				->setFrom($senderData)
				->setTo($emailsArray)
				->setReplyTo($senderData)
				->setSubject($subject)
				//->setReturnPath($senderData)
				//->setCharset("iso-8859-1")
				->setHtmlBody($body);
				$message->attach($filePath);
				if($message->send()) {						
					Yii::$app->getSession()->setFlash('success', 'Email sent successfully');
				}
				sleep(10);
				unlink($filePath);
			}else{
				$this->insertLog("Error In sending analytics email: Xls file not yet ready to send. Please try again");
				Yii::$app->getSession()->setFlash('error', 'Xls file not yet ready to send. Please try again');
			
			}
			}
			catch(Exception $e){
				$this->insertLog("Error In sending analytics email: ". $e->getMessage());
				Yii::$app->getSession()->setFlash('error', $e->getMessage());
			}
			exit;
		}


		public function actionSendCreditNoteAnalyticsReport() {
			
			try{
				$user_id = Yii::$app->user->id;
			$companyInfoModel	= CompanyInfo::getModel($user_id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
			$analytics_email =  $companyInfoModel->analytics_email;
			$analytics_email =(isset($_POST['email']) && !empty($_POST['email']))?$_POST['email']:$analytics_email;
			if(isset($_POST['attribute_data']['attributes']) && !empty($_POST['attribute_data']['attributes'])){
				$columns = $_POST['attribute_data']['attributes'];
				$id		= Yii::$app->user->id;
				$userModel	= $this->findModel($id);
				$userModel->selected_creditnote_fields = implode(",",$columns);
				$userModel->save();
				//Yii::$app->session->set('creditnote_columns',$columns);
			}
			else{
				$columns = ['credit_memo_no','invoice_number','order_import_date','return_date','seller_sku','product_sku','product_name','seller_order_id','qty_return','amazon_order_id','license_plate_number','order_adjustment_item_id','fulfillment_center_id','reason','detailed_disposition','status'];
			}

			if($analytics_email==""){
				Yii::$app->getSession()->setFlash('error', 'Analytics email not set in company settings. Please set the email there.');
				$this->insertLog("Error In sending analytics email: Analytics email not set in company settings. Please set the email there.");
				return true;
			}

			$file_name =$companyName."_creditnote_analytics_report_".date("m-Y").".csv";
			 
			$searchModel = new CreditMemoSearch(); 
			$queryParams=[];
			if(Yii::$app->session->get('creditExportModel')){
				$queryParams=Yii::$app->session->get('creditExportModel');				
				if(!empty($queryParams['CreditMemoSearch']['year']) || !empty($queryParams['CreditMemoSearch']['month'])){					
					$year   = (!empty($queryParams['CreditMemoSearch']['year']))?$queryParams['CreditMemoSearch']['year']:date('Y');
					if(!empty($queryParams['CreditMemoSearch']['month'])){
						$month  = $queryParams['CreditMemoSearch']['month'];
						$file_name =$companyName."_creditnote_analytics_report_".$month."-".$year.".csv";
					}else{
					   $file_name =$companyName."_creditnote_analytics_report_".$year.".csv";
					}
				}	

			}
			$export_report_range =(isset($_POST['export_report_range']) && !empty($_POST['export_report_range']))?$_POST['export_report_range']:"";

			if($export_report_range !=""){
				$queryParams['CreditMemoSearch']['return_date'] = $export_report_range;
				$file_name =$companyName."_creditnote_analytics_report_".$export_report_range.".csv";
			}

			
			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			}
			$dataProvider = $searchModel->search($queryParams);
			$exporter = new CsvGrid([
            'dataProvider' =>$dataProvider,
			 'columns' =>$attributeArray,
			]);
			$path = Yii::getAlias('@webroot').'/uploads/reports/';
			$filePath = $path.''.$file_name;
			//$exporter->save($filePath);
			$exporter->export()->saveAs($filePath);
			sleep(10);
		    if(file_exists($filePath)) {
				$getEmailFooterText = InvoiceMailing::getEmailFooterText($user_id,"Amazon.it");
				//$analytics_email =  $companyInfoModel->analytics_email;
				$analytics_emails = explode(",", $analytics_email);
				$emailsArray = array_combine($analytics_emails, $analytics_emails);
				$subject = str_replace("_"," ", $file_name);
				$subject = str_replace(".csv"," ", $subject); 
				$body = nl2br($getEmailFooterText);
				$userData = User::findOne($user_id);
				$senderEmail =$userData->email;
				$senderName =$userData->name;
				$senderData[$senderEmail]=$senderName;

				$message = Yii::$app->mailer->compose()
				->setFrom($senderData)
				->setTo($emailsArray)
				->setReplyTo($senderData)
				->setSubject($subject)
				//->setReturnPath($senderData)
				//->setCharset("iso-8859-1")
				->setHtmlBody($body);
				$message->attach($filePath);
				if($message->send()) {
					Yii::$app->getSession()->setFlash('success', 'Email sent successfully.');					
				}
				sleep(10);
				unlink($filePath);
			}
			}
			catch(Exception $e){
				Yii::$app->getSession()->setFlash('error', $e->getMessage());
				$this->insertLog("Error In sending email: ". $e->getMessage());				
			}
			exit;
		}
		public function actionSendSalesReport() {
			try{
			$user_id = Yii::$app->user->id;
			$companyInfoModel	= CompanyInfo::getModel($user_id);
			$accountant_email =  $companyInfoModel->accountant_email;
			$accountant_email =(isset($_POST['email']) && !empty($_POST['email']))?$_POST['email']:$accountant_email;
			if(isset($_POST['attribute_data']['attributes']) && !empty($_POST['attribute_data']['attributes'])){
					$columns = $_POST['attribute_data']['attributes'];
					$id		= Yii::$app->user->id;
					$userModel	= $this->findModel($id);
					$userModel->selected_invoice_fields = implode(",",$columns);
					$userModel->save();
					//Yii::$app->session->set('selected_columns',$columns);
				}
				else{
					$columns = ['order_import_date','invoice_number','protocol_invoice_number','purchase_date','buyer_name','buyer_email','buyer_vat','number_of_items_shipped','item_price','latest_ship_date','amazon_order_id','product_sku','product_name','last_update_date','ship_service_level','order_status','sales_channel','latest_delivery_date','marketplace_id','fulfillment_channel','payment_method','customer_name','address_2','address_type','city','state_or_region','country_code','postal_code','phone','fulfillment_center_id','shipment_id','ship_address_1','ship_address_2','ship_address_3','ship_city','ship_state','ship_postal_code','ship_country','ship_phone_number','ship_promotion_discount','tracking_number','carrier','shipping_price','shipping_tax','gift_wrap_price','gift_wrap_tax'];
				}
			if($accountant_email==""){
				Yii::$app->getSession()->setFlash('error', 'Accountant Email not set in company Settings');
				$this->insertLog("Error In sending email: Email not set in company settings. Please set the email there.");
				return true;
			}			
			$companyName = str_replace(" ","_",$companyInfoModel->company_name);
			$file_name =$companyName."_sales_report_".date("m-Y").".csv";
			$searchModel = new AmazonOrdersSearch(); 
			
			$queryParams=[];
			if(Yii::$app->session->get('exportModel')){
				$queryParams=Yii::$app->session->get('exportModel');
				if($queryParams['AmazonOrdersSearch']['year']!="" || $queryParams['AmazonOrdersSearch']['month'] !=""){
					$year  = (!empty($queryParams['AmazonOrdersSearch']['year']))?$queryParams['AmazonOrdersSearch']['year']:date('Y');
					if(!empty($queryParams['AmazonOrdersSearch']['month'])){
						$month  = $queryParams['AmazonOrdersSearch']['month'];
						$file_name =$companyName."_sales_report_".$month."-".$year.".csv";
					}else{
					   $file_name =$companyName."_sales_report_".$year.".csv";
					}
				}				
			}
			$export_report_range =(isset($_POST['export_report_range']) && !empty($_POST['export_report_range']))?$_POST['export_report_range']:"";

			if($export_report_range !=""){
				//$queryParams['AmazonOrdersSearch']['purchase_date'] = $export_report_range;
				$queryParams['AmazonOrdersSearch']['order_import_date'] = $export_report_range;
				$file_name =$companyName."_sales_report_".$export_report_range.".csv";
			}

			
			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			}
			 
			$dataProvider = $searchModel->search($queryParams);
		  	$exporter = new CsvGrid([
            'dataProvider' =>$dataProvider,
				'columns' =>$attributeArray, 
			]);
			$path = Yii::getAlias('@webroot/uploads/reports/');
			$file_name =str_replace(" ","", $file_name);
			$filePath = $path.''.$file_name;
			//$exporter->save($filePath);
			$exporter->export()->saveAs($filePath);
			sleep(20);
		    if(file_exists($filePath)) {
				$getEmailFooterText = InvoiceMailing::getEmailFooterText($user_id,"Amazon.it");
				$accountant_emails = explode(",", $accountant_email);
				$emailsArray = array_combine($accountant_emails, $accountant_emails);
				$body = nl2br($getEmailFooterText);
				$userData = User::findOne($user_id);
				//$senderEmail =$userData->email;
				$senderEmail =$userData->email;
				$senderName =$userData->name;
				$senderData[$senderEmail]=$senderName;
				$subject = str_replace("_"," ", $file_name);
				$subject = str_replace(".csv"," ", $subject);
				$message = Yii::$app->mailer->compose()
				->setFrom($senderData)
				->setTo($emailsArray)
				->setReplyTo($senderData)
				->setSubject($subject)				
				->setHtmlBody($body);
				
				//$attachment  = \Swift_Attachment::fromPath($filePath);	
				$message->attach($filePath);
				//$message->attach(\Swift_Attachment::fromPath($filePath));

				if($message->send()) {	
					//echo "Email Send successfully";
					Yii::$app->getSession()->setFlash('success', 'Email sent successfully ');
				}else{
					//echo "Error ";
					Yii::$app->getSession()->setFlash('error', "Error in sending email");					
				}
				sleep(10);
				unlink($filePath);
			}
			else{
				//echo 'csv file not yet exists';
				Yii::$app->getSession()->setFlash('error', 'XLS file not yet exists');
				$this->insertLog("Error In sending email: XLS file not yet exists.");
				
			}			
			}
			catch(Exception $e){
				
				Yii::$app->getSession()->setFlash('error', $e->getMessage());
				$this->insertLog("Error In sending email: ". $e->getMessage());
			}
			//echo "OUT";
			exit;
		}

		public function actionSendCreditNoteAccountReport() {
			try{
				$user_id = Yii::$app->user->id;
		    $companyInfoModel	= CompanyInfo::getModel($user_id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
			$accountant_email =  $companyInfoModel->accountant_email;
			$accountant_email =(isset($_POST['email']) && !empty($_POST['email']))?$_POST['email']:$accountant_email;
			if(isset($_POST['attribute_data']['attributes']) && !empty($_POST['attribute_data']['attributes'])){
				$columns = $_POST['attribute_data']['attributes'];
				$id		= Yii::$app->user->id;
				$userModel	= $this->findModel($id);
				$userModel->selected_creditnote_fields = implode(",",$columns);
				$userModel->save();

				//Yii::$app->session->set('creditnote_columns',$columns);
			}
			else{
				$columns =     ['credit_memo_no','invoice_number','order_import_date','return_date','seller_sku','product_sku','product_name','seller_order_id','qty_return','amazon_order_id','license_plate_number','order_adjustment_item_id','fulfillment_center_id','reason','detailed_disposition','status'];
			}

			if($accountant_email=="") {
				Yii::$app->getSession()->setFlash('error', "Email not set in company settings. Please sent the account email to receive emails.");
				$this->insertLog("Error In sending email: Email not set in company settings. Please sent the account email to receive emails.");
				return true;
			}
			$file_name =$companyName."_creditnote_account_report_".date("m-Y").".csv";
			$searchModel = new CreditMemoSearch(); 
			$queryParams=[];
			if(Yii::$app->session->get('creditExportModel')){
				$queryParams=Yii::$app->session->get('creditExportModel');				
				if(!empty($queryParams['CreditMemoSearch']['year']) || !empty($queryParams['CreditMemoSearch']['month'])){					
					$year   = (!empty($queryParams['CreditMemoSearch']['year']))?$queryParams['CreditMemoSearch']['year']:date('Y');
					if(!empty($queryParams['CreditMemoSearch']['month'])){
						$month  = $queryParams['CreditMemoSearch']['month'];
						$file_name =$companyName."_creditnote_account_report_".$month."-".$year.".csv";
					}else{
					   $file_name =$companyName."_creditnote_account_report_".$year.".csv";
					}
				}	

			}
			
			$export_report_range =(isset($_POST['export_report_range']) && !empty($_POST['export_report_range']))?$_POST['export_report_range']:"";

			if($export_report_range !=""){
				$queryParams['CreditMemoSearch']['return_date'] = $export_report_range;
				$file_name =$companyName."_creditnote_account_report_".$export_report_range.".csv";
			}

			$dataProvider = $searchModel->search($queryParams);

			$attributeArray =[];
			foreach($columns as $key=>$fieldName){
			  $attributeArray[$key] =['attribute' => $fieldName];
			} 
			$exporter = new CsvGrid([
            'dataProvider' =>$dataProvider,
				'columns' =>$attributeArray,
			]);
			$path = Yii::getAlias('@webroot').'/uploads/reports/';
			$filePath = $path.''.$file_name;
			//$exporter->save($filePath);
			$exporter->export()->saveAs($filePath);
		    if(file_exists($filePath)) {
				$getEmailFooterText = InvoiceMailing::getEmailFooterText($user_id,"Amazon.it");
				//$accountant_email =  $companyInfoModel->accountant_email;
				$accountant_emails = explode(",", $accountant_email);
				$emailsArray = array_combine($accountant_emails, $accountant_emails);
				$subject = str_replace("_"," ", $file_name);
				$subject = str_replace(".csv"," ", $subject);
				$body = nl2br($getEmailFooterText);
				$userData = User::findOne($user_id);
				$senderEmail =$userData->email;
				$senderName =$userData->name;
				$senderData[$senderEmail]=$senderName;
				$message = Yii::$app->mailer->compose()
				->setFrom($senderData)
				->setTo($emailsArray)
				->setReplyTo($senderData)
				->setSubject($subject)
				//->setReturnPath($senderData)
				//->setCharset("iso-8859-1")
				->setHtmlBody($body);
				$message->attach($filePath);
				if($message->send()) {
					Yii::$app->getSession()->setFlash('success', "Email send successfully.");
				}
				sleep(10);
				unlink($filePath);
			 }
			}
			catch(Exception $e){
				Yii::$app->getSession()->setFlash('error', $e->getMessage());
				$this->insertLog("Error In sending email: ".$e->getMessage());
			}
			exit;
		}
		public function actionUpdateprofile(){
			try{
				$id	= 		Yii::$app->user->id;
				$model = $this->findModel($id);
				if ($model->load(Yii::$app->request->post())) {
					if($model->save()){
						Yii::$app->getSession()->setFlash('success', "Profile information saved successfully.");
					}else{
						Yii::$app->getSession()->setFlash('Error', "Profile information not saved");
					}
				}
			}
			catch(EXCEPTION $e){
				Yii::$app->getSession()->setFlash('Error', $e->getMessage());
			}
			return $this->render('update', [
				'model' => $model,
			]);
	  
	  }
	  public function actionImportOrderRequest(){
	  
	  	try{
			 
			  
			 
			if(!isset($_POST['import_order_date']) || empty($_POST['import_order_date'])) {
			   Yii::$app->getSession()->setFlash('Error', "Please select date range to import orders");
			   die();
			}
			$user_id	= Yii::$app->user->id;			
			$client		= $this->getamazonClientInfo();
			if ($client && $client->validateCredentials()) {
				$import_start_date  = $_POST['import_order_date'];
				list($todaydate, $toDate) = explode(' - ', $import_start_date);
				$i =1;

				$fromDate	= new DateTime($todaydate);
				$fromDate->setTimeZone(new DateTimeZone('GMT'));				
				$endDate	= new DateTime($toDate);
				$report_id = $client->RequestReport('_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_',$fromDate, $endDate,true);

				if($report_id){
					$this->savereportdata($report_id, '_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_','SUBMITTED',$user_id,$todaydate, $toDate);
					Yii::$app->session->setFlash('success', "Import Request submitted successfully. Order(s) will be import as per cronjob time.");
				}
				sleep(10);
				// Create MFN report
				$report_id2 = $client->RequestReport('_GET_FLAT_FILE_ORDERS_DATA_',$fromDate, $endDate, true);
				if($report_id2){
					$this->savereportdata($report_id2, '_GET_FLAT_FILE_ORDERS_DATA_','SUBMITTED',$user_id,$todaydate, $toDate);
					Yii::$app->session->setFlash('success', "Import Request submitted successfully. Order(s) will be import as per cronjob time.");					
				}
				sleep(10);																	
			}
			else {
				Yii::$app->session->setFlash('error', "Amazon API not validated");
			}
			}catch(Exception $e){
				Yii::$app->session->setFlash('error', $e->getMessage());				
			}
			exit;	  
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


 public function actionImportCreditNoteRequest(){
	  	try{
			if(!isset($_POST['import_order_date']) || empty($_POST['import_order_date'])) {
			   Yii::$app->getSession()->setFlash('Error', "Please select date range to import orders");
			   die();
			}
			$user_id	= Yii::$app->user->id;
			$client		= $this->getamazonClientInfo();
			if ($client && $client->validateCredentials()) {
				$import_start_date  = $_POST['import_order_date'];
				list($todaydate, $toDate) = explode(' - ', $import_start_date);
				$i =1;
				$fromDate	= new DateTime($todaydate);
				$fromDate->setTimeZone(new DateTimeZone('GMT'));				
				$endDate	= new DateTime($toDate);
				$report_id = $client->RequestReport('_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_',$fromDate, $endDate,true);

				if($report_id){
					$this->savereportdata($report_id, '_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_','SUBMITTED',$user_id,$todaydate, $toDate);
					Yii::$app->session->setFlash('success', "Import Request submitted successfully. Order(s) will be import as per cronjob time.");
				}
				sleep(10);																				
			}
			else {
				Yii::$app->session->setFlash('error', "Amazon API not validated");
			}
			}catch(Exception $e){
				Yii::$app->session->setFlash('error', $e->getMessage());				
			}
			exit;	  
	  }

	 public function actionExportInventoryReport(){
	 
	 try{
			//$_GET['report_type'] ='2018-12-16 - 2018-12-17';
			//$_GET['import_type'] ='C';
			if(!isset($_GET['report_type']) || empty($_GET['report_type'])) {
			   Yii::$app->getSession()->setFlash('Error', "Please select date range to generate report");
			   die();
			}
			 $import_type = $_GET['import_type'];
			 $report_type = $_GET['report_type'];
			 $user_id	= Yii::$app->user->id;

			 $companyInfoModel	= CompanyInfo::getModel($user_id);
			 $companyName = str_replace(" ","-",$companyInfoModel->company_name);
			 $file_name =$companyName."_inventory_data_report".date('Y-m-d').".csv";
			 $date ="";
			 if($import_type =='C'){
			 	 list($startDate, $endDate)= explode(' - ', $report_type);
				 $column_start =	 date('d/m/Y', strtotime($startDate));
				 $column_end =	 date('d/m/Y', strtotime($endDate));
				 $file_name =$companyName."_inventory_report_".$report_type.".csv";
				 $date =  $report_type;
			 }else{
				$startDate = date('Y-m-d', strtotime(" -$report_type days"));			 
				$endDate   = date('Y-m-d');
				$column_start =	 date('d/m/Y', strtotime(" -$report_type days"));
				$column_end =	 date('d/m/Y');
				$file_name =$companyName."_inventory_report_".$report_type.".csv";
				$date =  $startDate.' - '.$endDate;
			 }
			 $queryParams=[];
			 $queryParams['AmazonInventorySearch']['import_date']=$date;
			 $queryParams['AmazonInventorySearch']['marketplace']='IT';

			 //$searchModel  = new AmazonInventoryDataSearch();
			 $searchModel  = new AmazonInventorySearch();
			 $dataProvider = $searchModel->searchData($queryParams, $user_id);
			 $inventoryData = $dataProvider->query->all();
			 $inventoryProvider = [];
			 
			 if(count($inventoryData) >0 ){
				foreach($inventoryData as $key=>$data){
					$amazonModel			= new AmazonOrders();
					$inventoryModel			= new AmazonInventory();
					$productModel			= new AmazonProducts();
				   	$sku					= $data->sku;
					//$vat_percentage			=0;
					$invoiceData			= $amazonModel->getInvoiceData($sku, $user_id, $date);
					$vat_percentage			= $productModel->getProductVat($sku, $user_id);
					$vat_percentage			= ($vat_percentage >0)?$vat_percentage:0;
					$tax_amount             =0;					
					$totalVat				=0;
					if(is_array($invoiceData)&& !empty($invoiceData) ){
						$tax_amount			=  $invoiceData['tax'];
						//$vat_percentage		=  ($invoiceData['vat_percentage']>0)?$invoiceData['vat_percentage']:0;
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
					$total_amount			= $total_amount+$tax_amount;
					
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
			 }else{
			 	Yii::$app->getSession()->setFlash('error', "No record found for matching date $date. Please choose other date");
			    $this->redirect(['user/manage-inventory']);
				return;
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
			return $exporter->export()->send($file_name);
		 }catch(Exception $e){
			echo"<pre>";
			print_r($e->getMessage());
			die();
		 }
	 
	 }


	 public function actionSendInventoryReport() {
			try{
			$user_id = Yii::$app->user->id;
			$companyInfoModel	= CompanyInfo::getModel($user_id);
			$companyName = str_replace(" ","-",$companyInfoModel->company_name);
			$inventory_report_email =  $companyInfoModel->inventory_report_email;
			$inventory_report_email =(isset($_POST['email']) && !empty($_POST['email']))?$_POST['email']:$inventory_report_email;	

			if($inventory_report_email==""){
				Yii::$app->getSession()->setFlash('error', 'Inventory email not set in company settings. Please set the email there.');
				$this->insertLog("Error In sending Inventory report email: 'Inventory report email not set in company settings. Please set the email there.");
				return true;
			}

			if(!isset($_GET['report_type']) || empty($_GET['report_type'])) {
			   Yii::$app->getSession()->setFlash('Error', "Please select date range to generate report");
			   die();
			}
			 $import_type = $_GET['import_type'];
			 $report_type = $_GET['report_type'];
			 $user_id	= Yii::$app->user->id;

			 $companyInfoModel	= CompanyInfo::getModel($user_id);
			 $companyName = str_replace(" ","-",$companyInfoModel->company_name);
			 $file_name =$companyName."_inventory_data_report".date('Y-m-d').".csv";
			 $date ="";
			 if($import_type =='C'){
			 	 list($startDate, $endDate)= explode(' - ', $report_type);
				 $column_start =	 date('d/m/Y', strtotime($startDate));
				 $column_end =	 date('d/m/Y', strtotime($endDate));
				 $file_name =$companyName."_inventory_report_".$report_type.".csv";
				 $date =  $report_type;
			 }else{
				$startDate = date('Y-m-d', strtotime(" -$report_type days"));			 
				$endDate   = date('Y-m-d');
				$column_start =	 date('d/m/Y', strtotime(" -$report_type days"));
				$column_end =	 date('d/m/Y');
				$file_name =$companyName."_inventory_report_".$report_type.".csv";
				$date =  $startDate.' - '.$endDate;
			 }
			 $queryParams=[];
			  $queryParams=[];
			 $queryParams['AmazonInventorySearch']['import_date']=$date;

			 //$searchModel  = new AmazonInventoryDataSearch();
			 $searchModel  = new AmazonInventorySearch();
			 $dataProvider = $searchModel->searchData($queryParams, $user_id);
			 $inventoryData = $dataProvider->query->all();
			 $inventoryProvider = [];
			 
			 if(count($inventoryData) >0 ){
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
			 }else{
			 	Yii::$app->getSession()->setFlash('error', "No record found for matching date $date. Please choose other date");
			    $this->redirect(['user/manage-inventory']);
				return;
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
			//$exporter->save($filePath);
			$exporter->export()->saveAs($filePath);
		    if(file_exists($filePath)) {
				$getEmailFooterText = InvoiceMailing::getEmailFooterText($user_id,"Amazon.it");				
				$inventory_report_emails = explode(",", $inventory_report_email);
				$emailsArray = array_combine($inventory_report_emails, $inventory_report_emails);
				$subject = str_replace("_"," ", $file_name);
				$subject = str_replace(".csv"," ", $subject);
				$body = nl2br($getEmailFooterText);
				$userData = User::findOne($user_id);
				$senderEmail =$userData->email;
				$senderName =$userData->name;
				$senderData[$senderEmail]=$senderName;

				$message = Yii::$app->mailer->compose()
				->setFrom($senderData)
				->setTo($emailsArray)
				->setReplyTo($senderData)
				->setSubject($subject)
				//->setReturnPath($senderData)
				//->setCharset("iso-8859-1")
				->setHtmlBody($body);
				$message->attach($filePath);
				if($message->send()) {						
					Yii::$app->getSession()->setFlash('success', 'Email sent successfully');
				}
				sleep(10);
				unlink($filePath);
			}else{
				$this->insertLog("Error In sending inventory email: file not yet ready to send. Please try again");
				Yii::$app->getSession()->setFlash('error', 'file not yet ready to send. Please try again');
			
			}
			}
			catch(Exception $e){
				$this->insertLog("Error In sending inventory email: ". $e->getMessage());
				Yii::$app->getSession()->setFlash('error', $e->getMessage());
			}
			exit;
		}


	  public function actionGetinventoryreportdata(){
		try{
		  $user_id	= 		Yii::$app->user->id;
		  $client	= $this->getamazonClientInfo();
		  $orderApiData =[];
			
		  if ($client && $client->validateCredentials()) {
				 echo "\\ Checking user for  ID $user_id";
				$amazonReportInfoModel = new AmazonReportInfo();
				//$reportData =  $amazonReportInfoModel->getReportInfo($user_id, ['_GET_FBA_FULFILLMENT_CUSTOMER_SHIPMENT_SALES_DATA_']);
				//$report_id = 127354017848;  //127349017848; //127279017848; //127274017848; //127270017848;
				echo"Report Id = 127354017848, 127349017848, 127279017848 _GET_FBA_MYI_UNSUPPRESSED_INVENTORY_DATA_<br>";
				$report_id =  	158281017882;
				$reportApiData = $client->GetReport($report_id);
				echo "<pre>";
				print_r($reportApiData);
				die("");
				/* foreach($reportApiData as $key=>$inventory) {
					
					$snapDate = $inventory['snapshot-date'];
					echo $snapshotDate						= date('Y-m-d h:i:s', strtotime($snapDate));
					
					die();
				} */
				
			 } else{
				echo "Not validated";
			 }
		  
		} catch(EXCEPTION $e){
			echo "<pre>";
			print_r($e->getMessage());
			die();
		}
	  }
	  public function actionSaveInvoiceFields(){
	  	try{
			if(isset($_POST['attribute_data']['attributes']) && !empty($_POST['attribute_data']['attributes'])){
					$columns = $_POST['attribute_data']['attributes'];
					$id		= Yii::$app->user->id;
					$userModel	= $this->findModel($id);
					$userModel->selected_invoice_fields = implode(",",$columns);
					if($userModel->save()){
						Yii::$app->getSession()->setFlash('success', "Fields saved successfully");					
					}else{
						Yii::$app->getSession()->setFlash('error', "Error in saving fields data");
					}
					
			}
		}catch(Exception $e){
		 Yii::$app->getSession()->setFlash('error', $e->getMessage());
		}
		exit;
	  }


	   public function actionSaveCreditmemoFields() {

	  	try{
			if(isset($_POST['attribute_data']['attributes']) && !empty($_POST['attribute_data']['attributes'])) {

					$columns	= $_POST['attribute_data']['attributes'];
					$id			= Yii::$app->user->id;
					$userModel	= $this->findModel($id);
					$userModel->selected_creditnote_fields = implode(",",$columns);
					if($userModel->save()){
						Yii::$app->getSession()->setFlash('success', "Fields saved successfully");
					}else{

						Yii::$app->getSession()->setFlash('error', "Error in saving fields data");
					}
			}
		}catch(Exception $e){
			Yii::$app->getSession()->setFlash('error', $e->getMessage());
		}
		exit;
	  }
}
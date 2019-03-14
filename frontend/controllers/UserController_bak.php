<?php
	
	namespace frontend\controllers;
	
	use Yii;
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
	use yii\web\Controller;
	use yii\web\NotFoundHttpException;
	use yii\filters\AccessControl;
	use yii\web\UploadedFile;
	use yii\imagine\Image;  
	use Imagine\Image\Box;
	
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
			'only' => ['dashboard','invoices','products','importinvoices','importproducts', 'setting', 'savecompanyinfo','saveinvoicemailing','saveinvoicenumber','savevatrn','savemwssetting','testconnection','viewinvoicedetail','downloadpdf','setbuyervat','sendpdf','exportanalytics','exportaccount','removevtrn','wizard-setup','savewizardinfo','credit-memo','viewcreditmemodetail','downloadcreditmemopdf','sendcreditmemopdf','setbuyervatcc'],
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
		
		public function actionDashboard() {
			$user_info = Yii::$app->user;
			
			if($user_info->id !=""){
				$todaydate = date("Y-m-d");
				$current_month = date('Y-m-01');
				$orderObj		= new AmazonOrders();
				$getTodayOrder	=  $orderObj->getOrdersByDate($todaydate, $user_info->id);
				$getMonthlyOrder =  $orderObj->getMonthlyOrders($current_month, $todaydate, $user_info->id);
				//$model=  $this->findModel($user_info->id);
				return $this->render('dashboard', ['todayOrder' => $getTodayOrder,"monthlyOrder"=>$getMonthlyOrder]);
			}
		}
		public function actionInvoices(){
			
			$searchModel = new AmazonOrdersSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			
			return $this->render('invoices_grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
			
			try{
				
				$client = $this->getamazonClientInfo();		 
				$invoiceSettings = new InvoiceSettings();
				if ($client->validateCredentials()) {
					$todaydate = date("Y-m-d", strtotime("-2 days"));
					$fromDate = new DateTime($todaydate);	   // '2018-01-01'
					$orders = $client->ListOrders($fromDate, true, ['Shipped'],['AFN', 'MFN']);
					if(!empty($orders)){
						$ordersData = $orders['ListOrders'];
						foreach ($ordersData as $key=>$order) {
							
							$amazonOrders		= new AmazonOrders();
							$checkIfOrderExist	= $amazonOrders->checkExistingOrder($order['AmazonOrderId'], Yii::$app->user->id);
							$updateInvoiceCounter = true;
							if(!empty($checkIfOrderExist)) {
								$amazonOrders		= $checkIfOrderExist;	
								$updateInvoiceCounter = false;
								}else{
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
						$this->insertLog("Invoices imported");
						Yii::$app->session->setFlash('success', "Invoices imported");
					}
				}
				else {
					Yii::$app->session->setFlash('error', "Amazion API information not validate");
					//die("Amazion API information not validate");
					
				}
			}
			catch (Exception $e) {
				$this->insertLog("Error in importing order : ".$e->getMessage());
				Yii::$app->session->setFlash('error', "Error in importing invoices ". $e->getMessage());
			}
			exit;
			//return $this->redirect(['user/invoices']);
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
			
			$amazonMwsSettingModel	= AmazonMwsSetting::getModel(Yii::$app->user->id);
			$companyInfoModel		= CompanyInfo::getModel(Yii::$app->user->id);
			$invoiceMailingModel	= InvoiceMailing::getModel(Yii::$app->user->id);
			$invoiceSettingsModel	= InvoiceSettings::getModel(Yii::$app->user->id);
			$vatRnModel				= VatRn::getModel(Yii::$app->user->id);
			
			return $this->render('settings', ['amazonMwsSettingModel' => $amazonMwsSettingModel,'companyInfoModel'=>$companyInfoModel,'invoiceMailingModel'=>$invoiceMailingModel,'invoiceSettingsModel'=>$invoiceSettingsModel,'vatRnModel'=>$vatRnModel]);
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
						if($model->save()){
							Yii::$app->getSession()->setFlash('success', 'Data saved successfully');
							$this->insertLog("Amazon MWS information updated");
							}else{
							Yii::$app->getSession()->setFlash('error', 'Error in saving data');
						}
					}
				}
			}
			return $this->redirect(['user/setting']);
		}
		public function actionTestconnection(){
			if (Yii::$app->request->isPost) {
				$seller_id			=Yii::$app->request->post('seller_id');
				$MWSAuthToken		=Yii::$app->request->post('auth_token');
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
				$amazonOrdersModel	= $amazonOrdersModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
				$vat			= VatRn::getUserVat(Yii::$app->user->id);
				return $this->render('invoice_details', ['companyModel' => $companyInfoModel,'amazonOrderModel'=>$amazonOrdersModel,'orderItems'=>$items,'vat'=>$vat]);
			}
		}
		public function actionViewcreditmemodetail() {
			$amazon_order_id = Yii::$app->request->get('amazon_order_id');
			if(isset($amazon_order_id) && $amazon_order_id !="") {
				$amazonCreditModel	= new CreditMemo();
				$refundAmount 		=	$this->getRefundOrderAmount($amazon_order_id);
				$client				= $this->getamazonClientInfo();
				$items				= $client->ListOrderItems($amazon_order_id);
				$orderDetails 			= $client->GetOrder($amazon_order_id);
				$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
				$amazonCreditModel	= $amazonCreditModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
				$vat			= VatRn::getUserVat(Yii::$app->user->id);
				//$invoiceNo = $amazonCreditModel->invoice_number;				
				return $this->render('creditmemo_details', ['order_id'=>$amazon_order_id,'companyModel' => $companyInfoModel,'amazonOrderModel'=>$orderDetails,'orderItems'=>$items,'refundAmount'=>$refundAmount,'amazonCreditModel'=>$amazonCreditModel,'vat'=>$vat]);
			}
		}
		public function actionDownloadpdf(){
			$amazon_order_id = Yii::$app->request->get('amazon_order_id');
			if(isset($amazon_order_id) && $amazon_order_id !="") {
				$client				= $this->getamazonClientInfo();
				$items				= $client->ListOrderItems($amazon_order_id);
				$amazonOrdersModel	= new AmazonOrders();
				$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
				$amazonOrdersModel	= $amazonOrdersModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
				$vat			= VatRn::getUserVat(Yii::$app->user->id);
				$content = $this->renderPartial('_invoice_pdf', ['companyModel' => $companyInfoModel,'amazonOrderModel'=>$amazonOrdersModel,'orderItems'=>$items,'vat'=>$vat]);
				$mpdf = new Mpdf();
				$mpdf->WriteHTML($content);
				$mpdf->Output();
				$this->insertLog("Invoice pdf created");
			}
		}
		public function actionDownloadcreditmemopdf(){
			$amazon_order_id = Yii::$app->request->get('amazon_order_id');
			if(isset($amazon_order_id) && $amazon_order_id !="") {
				$amazonCreditModel	= new CreditMemo();
				$refundAmount		= $this->getRefundOrderAmount($amazon_order_id);
				$client				= $this->getamazonClientInfo();
				$items				= $client->ListOrderItems($amazon_order_id);
				$orderDetails 		= $client->GetOrder($amazon_order_id);
				$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
				$amazonCreditModel	= $amazonCreditModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
				//$invoiceNo = $amazonCreditModel->invoice_number;
				$vat			= VatRn::getUserVat(Yii::$app->user->id);
				$content		= $this->renderPartial('_invoice_creditmemopdf', ['order_id'=>$amazon_order_id,'companyModel' => $companyInfoModel,'amazonOrderModel'=>$orderDetails,'orderItems'=>$items,'refundAmount'=>$refundAmount,'orderItems'=>$items,'amazonCreditModel'=>$amazonCreditModel,'vat'=>$vat]);
				$mpdf = new Mpdf();
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
				
					$client				= $this->getamazonClientInfo();
					$items				= $client->ListOrderItems($amazon_order_id);					 
					$amazonOrdersModel	= new AmazonOrders();
					$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
					$amazonOrdersModel	= $amazonOrdersModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
					$vat			= VatRn::getUserVat(Yii::$app->user->id);
					$content			= $this->renderPartial('_invoice_pdf', ['companyModel' => $companyInfoModel,'amazonOrderModel'=>$amazonOrdersModel,'orderItems'=>$items,'vat'=>$vat]);					
					$getEmailFooterText = InvoiceMailing::getEmailFooterText(Yii::$app->user->id,$amazonOrdersModel->sales_channel)	;
					$mpdf = new Mpdf();
					$mpdf->WriteHTML($content);
					$filename	= uniqid()."_invoice_".$amazon_order_id.".pdf";
					$path = Yii::getAlias('@webroot').'/uploads/invoices/';
					$filePath = $path.''.$filename;
					$mpdf->Output($filePath,'F');
						
					if(file_exists($filePath)) {
						
						$body ="Hi,<br> <p>Please find the attached file for invoice for amazon order : $amazon_order_id.</p>". $getEmailFooterText;
						$message = Yii::$app->mailer->compose()
						->setFrom(Yii::$app->params['adminEmail'])
						->setTo($amazonOrdersModel->buyer_email)
						->setSubject("Invoice PDF for Order number: $amazon_order_id")
						->setHtmlBody($body);
						$message->attach($filePath);
						if($message->send()){
							echo "Email send successfully";
							$this->insertLog("Invoice pdf sent to customer for Amazon order  $amazon_order_id");
							}else{
							echo "Error in sending pdf email";
						}
						
						}else{
						echo "PDF File Not exists";
					}
				}
				} catch (Exception $e) {
				echo "<pre>"; print_r($e->getMessage()); die();
			}
			exit;
		}
		public function actionSendcreditmemopdf(){
			try{
			
				$amazon_order_id = Yii::$app->request->get('amazon_order_id');	
				//$amazon_order_id = "405-2056010-8215538";
				if(isset($amazon_order_id) && $amazon_order_id !="") {
					$amazonCreditModel	= new CreditMemo();
					$refundAmount       = $this->getRefundOrderAmount($amazon_order_id);
					$client				= $this->getamazonClientInfo();
					$items				= $client->ListOrderItems($amazon_order_id);
					//echo "<pre>"; print_r($items); die();
					$orderDetails 			= $client->GetOrder($amazon_order_id);					
					$companyInfoModel	= CompanyInfo::getModel(Yii::$app->user->id);
					$amazonCreditModel	= $amazonCreditModel->checkExistingOrder($amazon_order_id, Yii::$app->user->id);
					//$invoiceNo = $amazonCreditModel->invoice_number;
					$vat			= VatRn::getUserVat(Yii::$app->user->id);
					$content			= $this->renderPartial('_invoice_creditmemopdf', ['order_id'=>$amazon_order_id,'companyModel' => $companyInfoModel,'amazonOrderModel'=>$orderDetails,'orderItems'=>$items,'refundAmount'=>$refundAmount,'orderItems'=>$items,'amazonCreditModel'=>$amazonCreditModel,'vat'=>$vat]);					
					$getEmailFooterText = InvoiceMailing::getEmailFooterText(Yii::$app->user->id,$amazonCreditModel->markeplace)	;
					$mpdf = new Mpdf();
					$mpdf->WriteHTML($content);
					$filename	= uniqid()."_creditmemo_".$amazon_order_id.".pdf";
					$path = Yii::getAlias('@webroot').'/uploads/invoices/';
					$filePath = $path.''.$filename;
					$mpdf->Output($filePath,'F');
						
					if(file_exists($filePath)) {
						
						$body ="Hi,<br> <p>Please find the attached file for invoice for amazon order : $amazon_order_id.</p>". $getEmailFooterText;
						
						$message = Yii::$app->mailer->compose()
						->setFrom(Yii::$app->params['adminEmail'])
						->setTo($amazonCreditModel->buyer_email)
						
						->setSubject("Invoice Credit Memo for Order number: $amazon_order_id")
						->setHtmlBody($body);
						$message->attach($filePath);
						if($message->send()){
							echo "Email send successfully";
							$this->insertLog("Invoice credit memo sent to customer for Amazon order  $amazon_order_id");
							}else{
							echo "Error in sending credit memo email";
						}
						
						}else{
						echo "PDF File Not exists";
					}
				}
				} catch (Exception $e) {
				echo "<pre>"; print_r($e->getMessage()); die();
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
							$this->insertLog("Buyer vat Saved successfully");
						}
					}
				}
				}catch (Exception $e) {
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
			$file = Yii::createObject([
			'class' => 'codemix\excelexport\ExcelFile',
			'writerClass' => '\PHPExcel_Writer_Excel5',
			'sheets' => [
			'Active Users' => [
            'class' => 'codemix\excelexport\ActiveExcelSheet',
            'query' => AmazonOrders::find()->where(['user_id' => Yii::$app->user->id]),
            'attributes' => [
			'purchase_date',
			'buyer_name', 
			'buyer_email',
			'buyer_vat',
			'invoice_number',
			'number_of_items_shipped',
			'number_of_items_unshipped',
			'total_amount',
			'is_prime',
			'latest_ship_date',
			'order_type',				 
			'amazon_order_id', 
			'is_replacement_order',
			'last_update_date',
			'ship_service_level',
			'order_status', 
			'sales_channel',
			'shipped_by_amazon_tfm', 
			'is_business_order', 
			'latest_delivery_date',
			'payment_method_detail',
			'earliest_delivery_date',
			'is_premium_order', 
			'order_currency',
			'earliest_ship_date',
			'marketplace_id', 
			'fulfillment_channel',
			'payment_method',
			'city',
			'address_type',
			'postal_code',
			'state_or_region',
			'phone',
			'country_code',
			'customer_name',
			'address_2',
			'shipment_category'
            ],
            'titles' => [
			'D' => 'Analytics',
            ],
			],
			
			],
			]);
			$file_name ="analytics_".date("y-m-d")."_data.xlsx";
			$file->send($file_name);
			
			
		}
		public function actionExportaccount() {
			$file = Yii::createObject([
			'class' => 'codemix\excelexport\ExcelFile',
			'writerClass' => '\PHPExcel_Writer_Excel5',
			'sheets' => [
			'Active Users' => [
            'class' => 'codemix\excelexport\ActiveExcelSheet',
            'query' => AmazonOrders::find()->where(['user_id' => Yii::$app->user->id]),
            'attributes' => [
			'purchase_date',
			'buyer_name', 
			'buyer_email',
			'buyer_vat',
			'invoice_number',
			'number_of_items_shipped',
			'number_of_items_unshipped',
			'total_amount',
			'is_prime',
			'latest_ship_date',
			'order_type',				 
			'amazon_order_id', 
			'is_replacement_order',
			'last_update_date',
			'ship_service_level',
			'order_status', 
			'sales_channel',
			'shipped_by_amazon_tfm', 
			'is_business_order', 
			'latest_delivery_date',
			'payment_method_detail',
			'earliest_delivery_date',
			'is_premium_order', 
			'order_currency',
			'earliest_ship_date',
			'marketplace_id', 
			'fulfillment_channel',
			'payment_method',
			'city',
			'address_type',
			'postal_code',
			'state_or_region',
			'phone',
			'country_code',
			'customer_name',
			'address_2',
			'shipment_category'
            ],
			
            // If not specified, the label from the respective record is used.
            // You can also override single titles, like here for the above `team.name`
            'titles' => [
			'D' => 'account',
            ],
			],
			
			],
			]);
			$file_name ="account_".date("y-m-d")."_data.xlsx";
			$file->send($file_name);
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
			
			return $this->render('_wizard', ['amazonMwsSettingModel' => $amazonMwsSettingModel,'companyInfoModel'=>$companyInfoModel,'invoiceMailingModel'=>$invoiceMailingModel,'invoiceSettingsModel'=>$invoiceSettingsModel,'vatRnModel'=>$vatRnModel]);
			
		}
		public function actionSavewizardinfo(){
			if(Yii::$app->request->isPost) {
				$postData = Yii::$app->request->post();
				$this->savecompanyinfo($postData, $_FILES);
				$this->saveinvoicemailing($postData);
				$this->saveinvoicenumber($postData);
				$this->savevatrn($postData);
				$this->savemwssetting($postData);
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
			 $searchModel = new CreditMemoSearch();
			 $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('/creditmemo/index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}
	}	
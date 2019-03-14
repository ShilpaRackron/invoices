<?php
	
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;
	use yii\widgets\Pjax;
	$this->title = Yii::t('app', 'Invoice Details');
	$this->params['breadcrumbs'][] = $this->title;
	
	use kartik\popover\PopoverX;
	
?>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/bootstrap.min.js"></script>

<div class="invoice-details">
	<?php $ajaxSaveProtocolUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/setprotocolno', "amazon_order_id"=>$amazonOrderModel->amazon_order_id], true ); ?>
	<div class="col-md-9 col-md-xl-2 order-xl-2 order-lg-1 order-md-1 order-sm-1 order-1">	
		<div class="row">
			<div class="col-md-3 col-md-sm-4 col-md-md-4 col-md-lg-4 col-md-xl-9">					
				<!--<button id="set_buyer_vat" class="btn btn-secondary btn-block m-b-5"></button> -->
				<?php $ajaxSaveVatUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/setbuyervat', "amazon_order_id"=>$amazonOrderModel->amazon_order_id], true ); ?>
				<?php PopoverX::begin([
					'placement' => PopoverX::ALIGN_BOTTOM,
					'toggleButton' => ['label'=>'Set Buyer VAT', 'class'=>'btn btn-secondary'],
					'header' => '<i class="glyphicon glyphicon-lock"></i> Set Buyer Vat Number',
					'footer' => Html::button('Submit', [
					'class' => 'btn btn-sm btn-primary', 
					'onclick' => 'submitVat()'
					]) 
					]);
					// form with an id used for action buttons in footer
					echo $content = "<input type='text' name='buyer_vat' id='buyer_vat' placeholder ='Please enter Buyer Vat'>";
					//echo $form->field($amazonOrderModel, 'buyer_vat')->textInput(['placeholder'=>'Enter Buyer Vat']);
					
					//ActiveForm::end();
					PopoverX::end();
				?>
				
				<script type="text/javascript">
					function submitVat() {			       
						var vatId = $("#buyer_vat").val();
						if(vatId ==""){
							alert("Please enter Buyer Vat");
							return false;
							}else{
							
							$.ajax({
								url: '<?php echo $ajaxSaveVatUrl;?>',
								type: 'POST',
								data: {"vatnumber":vatId},
								success: function (response) 
								{
									location.reload(true);
								},
								error  : function () 
								{
									alert("Error");
								}
							});
							return false;
						}
					}

					function submitProtocol() {			       
						var protocol_number = $("#protocol_invoice_number").val();
						if(protocol_number ==""){
							alert("Please enter Protocol No");
							return false;
							}else{
							
							$.ajax({
								url: '<?php echo $ajaxSaveProtocolUrl;?>',
								type: 'POST',
								data: {"protocol_invoice_number":protocol_number},
								success: function (response) 
								{
									location.reload(true);
								},
								error  : function () 
								{
									alert("Error");
								}
							});
							return false;
						}
					}
				</script>
			</div>
			<div class="col-md-3 col-md-sm-4 col-md-md-4 col-md-lg-4 col-md-xl-9">
				<a href="<?php echo $ajaxUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/downloadpdf', "amazon_order_id"=>$amazonOrderModel->amazon_order_id], true );?>" target="_blank" class="btn btn-primary btn-block m-b-5">Show PDF</a>
			</div>
			<div class="col-md-3 col-md-sm-4 col-md-md-4 col-md-lg-4 col-md-xl-9">
				<?php 				 
					$ajaxUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/sendpdf', "amazon_order_id"=>$amazonOrderModel->amazon_order_id], true );
					echo Html::a('Send PDF',['user/sendpdf'], ['class'=>'btn btn-primary', 'title' => Yii::t('yii', 'Close'), 'onclick'=>" $.ajax({
					type     :'POST',
					cache    : false,
					data:{is_ajax: true },
					url  : '".$ajaxUrl."',
					success  : function(response) {
					location. reload(true);
					}
					});return false;",
					]);	
				?>
			</div>

			<div class="col-md-3 col-md-sm-4 col-md-md-4 col-md-lg-4 col-md-xl-9">
			<?php PopoverX::begin([
					'placement' => PopoverX::ALIGN_BOTTOM,
					'toggleButton' => ['label'=>'Set Invoice No', 'class'=>'btn btn-secondary'],
					'header' => '<i class="glyphicon glyphicon-lock"></i> Set Invoice No',
					'footer' => Html::button('Submit', [
					'class' => 'btn btn-sm btn-primary', 
					'onclick' => 'submitProtocol()'
					]) 
					]);
					// form with an id used for action buttons in footer
					echo $content = "<input type='text' name='protocol_invoice_number' id='protocol_invoice_number' placeholder ='Add Protocol Invoice No'>";							
					
					PopoverX::end();
				?>
			</div>
		</div>
	</div>	
	
	
	<div class="invoice-title">			
		<h4 class="pull-right font-16">
			<strong> Fattura <?php echo $amazonOrderModel->invoice_number;?></strong>
		</h4>
		<h3 style="min-height: 30px;"></h3>
		
		<?php	
		if(trim($amazonOrderModel->protocol_invoice_number) !=""){
		  echo '<h4 class="pull-right font-16"> <strong> Protocol No '.$amazonOrderModel->protocol_invoice_number.'</strong></h4>';
		}
		?>
	</div>
	<hr>
	
	<div class="row">
		<div class="col-md-6">
			<?php if( $companyModel->company_logo !=""){ ?>
				<?php  echo Html::img(    Url::to('@web/uploads/thumb/'.$companyModel->company_logo), ['alt' => 'Company Logo', "height"=>'100px', 'width'=>'200px']); ?>
			<?php } ?>
			<address>
				<strong>
					<?php echo nl2br($companyModel->company_header);?>
				</strong>
			</address>
		</div>
		<div class="col-md-6 ">
			<address>
				Fattura a: <br>
				<strong><?php echo $amazonOrderModel->buyer_name;?>.</strong><br>
				<?php echo $amazonOrderModel->address_2;?> <br>
				<?php echo $amazonOrderModel->city;?><br>												
				<?php echo $amazonOrderModel->state_or_region ;?> <br>
				<?php echo $amazonOrderModel->postal_code ;?>
				<?php echo $amazonOrderModel->country_code ;?> <br>
				Spedito a:
				<strong><?php echo $amazonOrderModel->buyer_name;?>.</strong><br>
				<?php echo $amazonOrderModel->address_2;?> <br>
				<?php echo $amazonOrderModel->city;?><br>												
				<?php echo $amazonOrderModel->state_or_region ;?> <br>
				<?php echo $amazonOrderModel->postal_code ;?>
				<?php echo $amazonOrderModel->country_code ;?> <br>
				
				<?php 
					if(trim($amazonOrderModel->buyer_vat) !=""){
						echo "Vat Number: ". $amazonOrderModel->buyer_vat;
						echo "<br>";
					}
				?>
			</address>
		</div>
	</div>
	
	<div class="row">
		
		<table class="table table-responsive" style="width:100%">
			<thead>
				<tr><th>Descrizione</th>
					<th>Qta.</th>
					<th>Prezzo</th>
					<th>Totale senza IVA</th>
					<th>% IVA</th>
					<th>Totale</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$vatPercentage =$vat->rate_percentage;;
					$totalVat = 0;
					foreach($orderItems as $key=>$itemData ) { 
						$shippingTax			= isset($itemData['ShippingTax']['Amount'])?$itemData['ShippingTax']['Amount']:"";
						$promotionDiscount	= isset($itemData['PromotionDiscount']['Amount'])?$itemData['PromotionDiscount']['Amount']:"";
						
						$itemGrossPrice		= $itemData['ItemPrice']['Amount'];
						$netPrice				=(float)(($itemGrossPrice*100)/(100+$vatPercentage));
						$productnetIVA		= (float)($itemGrossPrice -$netPrice);
						$totalVat				= (float)($totalVat+$productnetIVA);
					?>
					<tr>
						<td><?php echo $itemData['Title'];?></td>
						<td><?php echo $itemData['QuantityOrdered'];?></td>
						<td><?php echo $itemGrossPrice;?></td>
						<td><?php echo number_format($netPrice,2);?></td>
						<td><?php echo $vatPercentage;?>%</td>
						<td><?php echo $itemGrossPrice;?></td>
					</tr>
				<?php } ?>
				<tr>
					<td colspan="9" class="text-right">
						<?php $orderTotal = $amazonOrderModel->total_amount;
							$netOrderAmount = $orderTotal -$totalVat; 
						?>
						Subtotale: <?php echo number_format($netOrderAmount,2) .' '. $amazonOrderModel->order_currency;?><br>
						IVA <?php echo $vatPercentage;?>%: <?php echo number_format($totalVat,2);?> <?php echo $amazonOrderModel->order_currency;?><br>
						<strong>Totale: <?php echo number_format($orderTotal, 2);?> <?php echo $amazonOrderModel->order_currency;?></strong>
					</td>
				</tr>
			</tbody>
		</table>		
	</div>
	<div class="row">
		<div class="col-md-12">
			<div style="padding-top: 20px; color: grey;">
				<?php
					switch($amazonOrderModel->sales_channel){								   
						case 'Amazon.it':
						$footerText= $companyModel->amazon_it_footer;
						break;
						case 'Amazon.co.uk':
						$footerText= $companyModel->amazon_uk_footer;
						break;
						case 'Amazon.de':
						$footerText= $companyModel->amazon_de_footer;
						break;
						case 'Amazon.es':
						$footerText= $companyModel->amazon_es_footer;
						break;
						case 'Amazon.fr':
						$footerText= $companyModel->amazon_fr_footer;
						break;
						default:
						$footerText="";
					}
					echo  $footerText;
				?>					
			</div>
			<div style="border-top: 1px solid silver; padding-top: 20px; color: grey; display: inline-block; width: 100%;">
				<div style="float: left;" class="col-md-6">
					Additional Amazon Information: (not included in PDF)<br>
					Amazon Order ID: <?php echo $amazonOrderModel->amazon_order_id;?>  <br>
					Purchase Date : <?php echo $amazonOrderModel->purchase_date;?>  <br>
					Mechant Order ID:  <?php echo $amazonOrderModel->merchant_order_id;?><br>
					Tracking Number:  <?php echo $amazonOrderModel->tracking_number;?><br>
					Carrier:  <?php echo $amazonOrderModel->carrier;?><br>
					Shipment ID:  <?php echo $amazonOrderModel->shipment_id;?><br>
					Buyer e-mail: <?php echo $amazonOrderModel->buyer_email;?> <br>
					Buyer phone:  <?php echo $amazonOrderModel->phone;?><br>
					Ship-to phone:  <?php echo $amazonOrderModel->ship_phone_number;?><br>
					Fulfillment center ID:  <?php echo $amazonOrderModel->fulfillment_center_id;?><br>
					Fulfillment channel:  <?php echo $amazonOrderModel->fulfillment_channel;?><br>
					Sales channel: <?php echo $amazonOrderModel->sales_channel ;?> <br>
					Warehouse:  <?php echo $amazonOrderModel->ship_country;?> <?php echo ($amazonOrderModel->fulfillment_center_id !="")?"(".$amazonOrderModel->fulfillment_center_id.")":"";?><br>
				</div>
				
				<div style="right; text-align: right;" class="col-md-6">
					<!--Bookkeeping information: <br>
						Currency rate: 1.1380<br>
						Subtotal: 14.54 GBP<br>
						VAT: 0.73 GBP<br>
					Total: 15.27 GBP<br> -->
				</div>
			</div>
		</div>
	</div>
	
	<!--</div>
		</div>
	</div>-->
</div>
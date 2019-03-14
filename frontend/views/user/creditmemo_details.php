<?php
	
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;
	use yii\widgets\Pjax;
	$this->title = Yii::t('app', 'Credit Memos');
	$this->params['breadcrumbs'][] = $this->title;	
	//use kartik\popover\PopoverX;
	
?>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/bootstrap.min.js"></script>
<div class="invoice-details">
	
	<div class="col-md-9 col-md-xl-2 order-xl-2 order-lg-1 order-md-1 order-sm-1 order-1">	
		<div class="row">
			<div class="col-md-3 col-md-sm-4 col-md-md-4 col-md-lg-4 col-md-xl-9">					
				<!--<button id="set_buyer_vat" class="btn btn-secondary btn-block m-b-5"></button> -->
				<?php $ajaxSaveVatUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/setbuyervatcc', "amazon_order_id"=>$order_id], true ); ?>
				
			</div>
			<div class="col-md-3 col-md-sm-4 col-md-md-4 col-md-lg-4 col-md-xl-9">
				<a href="<?php echo $ajaxUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/downloadcreditmemopdf', "amazon_order_id"=>$order_id], true );?>" target="_blank" class="btn btn-primary btn-block m-b-5">Show PDF</a>
			</div>
			<div class="col-md-3 col-md-sm-4 col-md-md-4 col-md-lg-4 col-md-xl-9">
				<?php 				 
					$ajaxUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/sendcreditmemopdf', "amazon_order_id"=>$order_id], true );
					echo Html::a('Send PDF',['user/sendcreditmemopdf'], ['class'=>'btn btn-primary', 'title' => Yii::t('yii', 'Close'), 'onclick'=>" $.ajax({
					type     :'POST',
					cache    : false,
					data:{is_ajax: true },
					url  : '".$ajaxUrl."',
					success  : function(response) {
					//alert('here');
					location. reload(true);
					}
					});return false;",
					]);	
				?>	 
				<?php if($amazonCreditModel->creditmemo_email_sent==1) { ?>
				<p><small>Credit Note already sent by <?php echo $amazonCreditModel->email_sending_type;?> on day: <?php echo date("d F Y", strtotime($amazonCreditModel->creditmemo_email_date));?></small></p>
				<?php } ?>
			</div>
		</div>
	</div>	
	
	
	<div class="invoice-title">			
		<h4 class="pull-right font-16">
			<p><strong>( Retours)</strong></p>
			<strong> Fattura <?php echo $amazonCreditModel->credit_memo_no;?></strong>
		</h4>
		<h3 style="min-height: 30px;"></h3>
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
				Spedito a:<br>
				<strong><?php echo $amazonOrderModel->buyer_name;?>.</strong><br>
				<?php echo $amazonOrderModel->address_2;?> <br>
				<?php echo $amazonOrderModel->city;?><br>												
				<?php echo $amazonOrderModel->state_or_region ;?> <br>
				<?php echo $amazonOrderModel->postal_code ;?>
				<?php echo $amazonOrderModel->country_code ;?> <br>
				
				<?php 
					if($amazonCreditModel->buyer_vat !=""){
						echo "Vat Number: ". $amazonCreditModel->buyer_vat;
						echo "<br>";
					}
				?>
			</address>
		</div>
	</div>
	
	<div class="row">
		
		<table class="table table-responsive" style="width:100%">
			<thead>
				<tr><th>Product</th>
					<th>Qta.</th>
					<th>Prix</th>
					<th>Montant HT</th>
					<th>Taux TVA</th>
					<th>Montant</th>
				</tr>
			</thead>
			<tbody>
				<?php

					$countryCode	= $amazonOrderModel->country_code;
					$user_id		= Yii::$app->user->id;
					$vatData		= $vat->getSellerCountryVat($user_id, $countryCode);
					$vatPercentage	= $vatData->rate_percentage;
					$vatCountry     = $vatData->country;

					$totalVat = 0;
					$qtyReturn			= $amazonCreditModel->qty_return;
					$amazon_orderId		= $amazonCreditModel->amazon_order_id;
					$productSKU			= $amazonCreditModel->seller_sku;
					$productName		= $amazonCreditModel->product_name;
					$productVatPercentage	= $productModel->getProductVat($productSKU, Yii::$app->user->id);
					$vatPercentage      =($productVatPercentage >0 )?$productVatPercentage:$vatPercentage;
					$currency			= $amazonCreditModel->getCurrencyCode($amazon_orderId);
					$itemGrossPrice		= $amazonCreditModel->getOrderRefundAmount($amazon_orderId,$qtyReturn );;
					$netPrice			=(float)(($itemGrossPrice*100)/(100+$vatPercentage));
					$productnetIVA		= (float)($itemGrossPrice -$netPrice);
						//$totalVat				= (float)($totalVat+$productnetIVA);
					$itemPrice     = $netPrice/$qtyReturn;
					?>
					<tr>
						<td><?php echo $productName;?></td>
						<td>-<?php echo $amazonCreditModel->qty_return;?></td>
						<td><?php echo number_format($itemPrice,2);?></td>
						<td><?php echo number_format($netPrice,2);?></td>
						<td><?php echo ($vatPercentage>0)?$vatPercentage:0;?>%</td>
						<td><?php echo number_format($itemGrossPrice,2);?></td>
					</tr>
				<?php  ?>
				<tr>
					<td colspan="9" class="text-right">
						<?php //$orderTotal = $amazonOrderModel['OrderTotal']['Amount'];
							//$netOrderAmount = $orderTotal -$totalVat; 
						?>
						Subtotale: <?php echo number_format($netPrice,2) .' '. $currency;?><br>
						IVA <?php echo ($vatPercentage>0)?$vatPercentage:0;?>%: <?php echo number_format($productnetIVA,2);?> <?php echo $currency;?><br>
						<strong>Totale: <?php echo number_format($itemGrossPrice, 2);?> <?php echo $currency;?></strong>
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
					Amazon Order ID: <?php echo $amazon_orderId;?>  <br>
					Mechant Order ID:  <br>
					Tracking Number:  <?php echo $amazonOrderModel->tracking_number;?><br>
					Carrier:  <?php echo $amazonOrderModel->carrier;?><br>
					Shipment ID:  <?php echo $amazonOrderModel->shipment_id;?><br>
					Buyer e-mail: <?php echo $amazonOrderModel->buyer_email;?> <br>
					Buyer phone:  <?php echo $amazonOrderModel->phone;?><br>
					Ship-to phone:  <?php echo $amazonOrderModel->ship_phone_number;?><br>
					Fulfillment center ID:  <?php echo $amazonCreditModel->fulfillment_center_id;?><br>
					Fulfillment channel:  <?php echo $amazonOrderModel->fulfillment_channel;?><br>
					Sales channel: <?php echo $amazonOrderModel->sales_channel ;?> <br>
					Original Fulfillment Warehouse:  <?php echo $amazonOrderModel->ship_country;?> <?php echo ($amazonOrderModel->fulfillment_center_id !="")?"(".$amazonOrderModel->fulfillment_center_id.")":"";?><br>
				</div>
				
				<div style="right; text-align: right;" class="col-md-6">
				<p>Original Invoice: Number: <?php echo $amazonCreditModel->invoice_number ;?> </p>
				<p>Return Date: <?php echo date("F d, Y", strtotime($amazonCreditModel->return_date));?></p>
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
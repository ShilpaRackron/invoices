<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = Yii::t('app', 'Credit Memos');
$this->params['breadcrumbs'][] = $this->title;
?>
 <div class="invoice-details">

<div class="invoice-title">			
	<h4 class="pull-right font-16" style="float:right; text-align:right;font-size:12px; color:#676a6d; font-family:arial;">
	<p><strong>( Retours)</strong></p>
		<strong> Fattura <?php echo $amazonCreditModel->invoice_number;?></strong>
	</h4>
</div>
<hr style="margin-top:0px;">
									
<div class="row">
 <table width="100%">
  <tr>
    <td><div class="col-md-6" style="float-left; width:40%; font-size:11px; color:#676a6d; font-family:arial;">
		<?php if( $companyModel->company_logo !=""){ ?>
		<?php  echo Html::img(    Url::to('@web/uploads/thumb/'.$companyModel->company_logo), ['alt' => 'Company Logo', "height"=>'100px', 'width'=>'200px']); ?>
		<?php } ?>
		<address>
		<strong>
		<?php echo nl2br($companyModel->company_header);?>
		</strong>
		</address>
	</div>
	</td>
	<td>
		<div class="col-md-6 "style="right; width:40%; color:#676a6d; font-size:11px; font-family:arial;">
		<address>
			Fattura a: <br>
			<strong><?php echo $amazonOrderModel['ShippingAddress']['Name'];?>.</strong><br>
				<?php echo $amazonOrderModel['ShippingAddress']['AddressLine1'];?> <br>
				<?php echo $amazonOrderModel['ShippingAddress']['City'];?><br>												
				<?php //echo $amazonOrderModel->state_or_region ?> <br>
				<?php echo $amazonOrderModel['ShippingAddress']['PostalCode'] ;?> <br>
				<?php echo $amazonOrderModel['ShippingAddress']['CountryCode'] ;?> <br>
				Spedito a:
				<strong><?php echo $amazonOrderModel['ShippingAddress']['Name'];?>.</strong><br>
				<?php echo $amazonOrderModel['ShippingAddress']['AddressLine1'];?> <br>
				<?php echo $amazonOrderModel['ShippingAddress']['City'];?><br>												
				<?php //echo $amazonOrderModel->state_or_region ;?> <br>
				<?php echo $amazonOrderModel['ShippingAddress']['PostalCode'] ;?>
				<?php echo $amazonOrderModel['ShippingAddress']['CountryCode'] ;?> <br>
			<?php //if($amazonOrderModel->buyer_vat !=""){ echo "Vat Number: ".$amazonOrderModel->buyer_vat; }?> 
			</address>
	</div>
	</td>
  </tr>
 </table>
</div>
<style>
  .table{font-size:11px; font-family:arial; color:#676a6d; margin-top:20px;}
  .table th{ background:#2B333E; color:#fff; font-size:11px; text-align:left;  padding:8px;}
   .table td{ border-bottom:1px solid #ddd; padding:8px;font-size:11px;}
</style>
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
					$vatPercentage =$vat->rate_percentage;
					$totalVat = 0;
					$productSKU = $amazonCreditModel->seller_sku;
					$productName ="";
					$currency = $amazonCreditModel->currency_code;
					foreach($orderItems as $key=>$itemData ) { 
					  if($itemData['SellerSKU']==$productSKU){
						$productName = $itemData['Title'];
					  }
					}
						$itemGrossPrice		= $amazonCreditModel->total_amount_refund;
						$netPrice			=(float)(($itemGrossPrice*100)/(100+$vatPercentage));
						$productnetIVA		= (float)($itemGrossPrice -$netPrice);
						//$totalVat				= (float)($totalVat+$productnetIVA);
					?>
					<tr>
						<td><?php echo $productName;?></td>
						<td>-<?php echo $amazonCreditModel->qty_return;?></td>
						<td><?php echo number_format($itemGrossPrice,2);?></td>
						<td>-<?php echo number_format($netPrice,2);?></td>
						<td><?php echo $vatPercentage;?>%</td>
						<td>-<?php echo number_format($itemGrossPrice,2);?></td>
					</tr>
				<?php  ?>
				<tr>
					<td colspan="9" style="text-align:right">
						<?php //$orderTotal = $amazonOrderModel['OrderTotal']['Amount'];
							//$netOrderAmount = $orderTotal -$totalVat; 
						?>
						Subtotale: -<?php echo number_format($netPrice,2) .' '. $currency;?><br>
						IVA <?php echo $vatPercentage;?>%: -<?php echo number_format($productnetIVA,2);?> <?php echo $currency;?><br>
						<strong>Totale: -<?php echo number_format($itemGrossPrice, 2);?> <?php echo $currency;?></strong>
					</td>
				</tr>
			</tbody>
		</table>		
	</div>
			<div class="row">
				<div class="col-md-9">
					<div style="padding-top: 20px; color: grey;">
					<?php
					  switch($amazonOrderModel['SalesChannel']){								   
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
				</div>

			</div>

		</div>
	<!--</div>
</div>
</div>-->
<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'Invoice Details');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container"> 
<div class="row">
				<div class="col-12 col-xl-10 order-xl-1 order-lg-2 order-md-2 order-sm-2 order-2">
					<div class="card m-b-20">
						<div class="card-body">
							<div class="row">
								<div class="col-12">
									<div class="row">
										<div class="col-12">
											<div class="invoice-title">			
												<h4 class="pull-right font-16">
													<strong> Fattura <?php echo $amazonOrderModel->id;?></strong>
												</h4>
													<h3 style="min-height: 30px;"></h3>
											</div>
										<hr>
										</div>
									</div>
									<div class="row">
										<div class="col-6">
											<?php if( $companyModel->company_logo !=""){ ?>
											<img src="<?php echo $companyModel->company_logo;?>">
											<?php } ?>
											<address>
											<strong>
											<?php echo nl2br($companyModel->company_header);?>
											</strong>
											</address>
										</div>
										<div class="col-6 ">
											<address>
												Fattura a: <br>
												<strong><?php echo $amazonOrderModel->buyer_name;?>.</strong><br>
												<?php echo $amazonOrderModel->address_2;?> <br>
												<?php echo $amazonOrderModel->city;?><br>												<?php echo $amazonOrderModel->state_or_region ;?> <br>
												<?php echo $amazonOrderModel->postal_code ;?>
												<?php echo $amazonOrderModel->country_code ;?> <br>
												Spedito a:
												<strong><?php echo $amazonOrderModel->buyer_name;?>.</strong><br>
												<?php echo $amazonOrderModel->address_2;?> <br>
												<?php echo $amazonOrderModel->city;?><br>												<?php echo $amazonOrderModel->state_or_region ;?> <br>
												<?php echo $amazonOrderModel->postal_code ;?>
												<?php echo $amazonOrderModel->country_code ;?> <br>
												</address>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<div class="panel panel-default">
										<table class="table table-responsive" style="width:100%">
											<thead>
												<tr><th>Descrizione</th>
												<th>Qta.</th>
												<th>Prezzo</th>
												<th>Totale senza IVA</th> 															<th>% IVA</th> 																			<th>Totale</th>
												</tr>
											</thead>
											<tbody>
											<?php
											$vatPercentage ="5";
											foreach($orderItems as $key=>$itemData ) { 
												  $shippingTax			= $itemData['ShippingTax']['Amount'];
												  $promotionDiscount	= $itemData['PromotionDiscount']['Amount'];
												?>
											<tr>
												<td><?php echo $itemData['Title'];?></td>
												<td><?php echo $itemData['QuantityOrdered'];?></td>
												<td><?php echo $itemData['ItemPrice']['Amount'];?></td>
												<td><?php echo $itemData['ItemPrice']['Amount'];?></td>
												<td><?php echo $vatPercentage;?>%</td>
											</tr>
											<?php } ?>
											<tr>
												<td colspan="9" class="text-right">
													Subtotale: <?php 
														$orderTotal = $amazonOrderModel->total_amount;
														$totalVat = ($orderTotal*$vatPercentage)/100;
														$totalAmount = $orderTotal +$totalVat; 
														echo $orderTotal .' '. $amazonOrderModel->order_currency;?><br>
													IVA <?php echo $vatPercentage;?>%: <?php echo $totalVat;?> <?php echo $amazonOrderModel->order_currency;?><br>
													<strong>Totale: <?php echo $totalAmount;?> <?php echo $amazonOrderModel->order_currency;?></strong>
												</td>
											</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12">
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
										<div style="width: 50%; float: left;">
											Additional Amazon Information: (not included in PDF)<br>
											Amazon Order ID: <?php echo $amazonOrderModel->amazon_order_id;?>  <br>
											Mechant Order ID:  <br>
											Tracking Number:  <br>
											Carrier:  <br>
											Shipment ID:  <br>
											Buyer e-mail: <?php echo $amazonOrderModel->buyer_email;?> <br>
											Buyer phone:  <?php echo $amazonOrderModel->phone;?><br>
											Ship-to phone:  <?php echo $amazonOrderModel->phone;?><br>
											Fulfillment center ID:  <br>
											Fulfillment channel:  <?php echo $amazonOrderModel->fulfillment_channel;?><br>
											Sales channel: <?php echo $amazonOrderModel->sales_channel ;?> <br>
											Warehouse:  <br>
										</div>
										<div style="width: 50%; float: right; text-align: right;">
										Bookkeeping information: <br>
												Currency rate: 1.1380<br>
												Subtotal: 14.54 GBP<br>
												VAT: 0.73 GBP<br>
												Total: 15.27 GBP<br>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-xl-2 order-xl-2 order-lg-1 order-md-1 order-sm-1 order-1">
					<div class="card m-b-20 text-center">
						<div class="card-body">
							<div class="row">
								<div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-12">					
									<button id="set_buyer_vat" order_id="171-8190780-6979569_DsTfD3Cck" current="" class="btn btn-secondary btn-block m-b-5">
										Set Buyer VAT
									</button>
								</div>
								<div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-12">
									<a href="print_pdf.php?form=invoice_pdf&amp;amazon_order_id=171-8190780-6979569_DsTfD3Cck" target="_blank" class="btn btn-primary btn-block m-b-5">
										Show PDF
									</a>
								</div>
								<div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-12">
									<button id="mail_pdf" order_id="171-8190780-6979569_DsTfD3Cck" class="btn btn-success btn-block">Send PDF</button>
								</div>
							</div>
													</div>
					</div>
				</div>
			</div>
			</div>
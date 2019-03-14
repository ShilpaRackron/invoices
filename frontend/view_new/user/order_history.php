<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Order History';
$this->params['breadcrumbs'][] = $this->title;


?>

<!--DASHBOARD-->
	<section>
		<div class="tz">
			<!--LEFT SECTION-->
			<div class="tz-l">
				
				<div class="tz-l-2">
					<ul>
						<li>
							<a href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("user/dashboard");?>" ><img src="<?php echo Yii::getAlias("@web/themes/stralinglead/");?>images/icon/dbl1.png" alt="" /> My Dashboard</a>
						</li>
						<li>
							<a  href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("user/update-profile");?>"><img src="<?php echo Yii::getAlias("@web/themes/stralinglead/");?>images/icon/dbl1.png" alt="" /> Update Profile</a>
						</li>
						<li>
							<a class="tz-lma" href="#"><img src="<?php echo Yii::getAlias("@web/themes/stralinglead/");?>images/icon/dbl6.png" alt="" /> My Orders</a>
						</li>

						<li>
							<a href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("user/change-password");?>"><img src="<?php echo Yii::getAlias("@web/themes/stralinglead/");?>images/icon/dbl6.png" alt="" /> Change Password</a>
						</li>
					</ul>
				</div>
			</div>
			<!--CENTER SECTION-->
			<div class="tz-1">
				<div class="tz-1-com tz-1-main">					
					<div class="db-list-com tz-db-table">
						<div class="ds-boar-title">
							<h2>My Orders</h2>							
						</div>
						<table class="responsive-table bordered">
							<thead>
								<tr>
									<th>Sr No</th>									
									<th>Total Contacts</th>
									<th>Total Payment</th>
									<th>Purchase Date</th>
									<th>Download</th>
								</tr>
							</thead>
							<tbody>
							<?php $userOrders = $model->getUserOrders();
							if(!empty($userOrders) && count($userOrders) >0){
								$i=1;
								foreach($userOrders as $orderData){
									?>
									<tr>
										<td><?php echo $i;?></td>
										<td><?php echo $orderData->total_order_items;?></td>
										<td><?php echo $orderData->total_payment;?> USD</td>
										<td><?php echo $orderData->purchase_date;?></td>
										<td>
										<?php  echo Html::a('<span class="glyphicon glyphicon-export">Download</span>',["user/exportcsv","order_id"=>$orderData->id],["class"=>"db-list-edit"]);?>
										
										</td>									
									</tr>
									<?php
											$i++;
								}
								
							}
							?>
								
															
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!--RIGHT SECTION-->
			
		</div>
	</section>
	<!--END DASHBOARD-->
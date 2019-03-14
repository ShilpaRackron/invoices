<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Change Password';
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
							<a href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("user/dashboard");?>"><img src="<?php echo Yii::getAlias("@web/themes/stralinglead/");?>images/icon/dbl1.png" alt="" /> My Dashboard</a>
						</li>
						<li>
							<a href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("user/update-profile");?>" ><img src="<?php echo Yii::getAlias("@web/themes/stralinglead/");?>images/icon/dbl1.png" alt="" /> Update Profile</a>
						</li>
						<li>
							<a href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("user/orders");?>"><img src="<?php echo Yii::getAlias("@web/themes/stralinglead/");?>images/icon/dbl6.png" alt="" /> My Orders</a>
						</li>

						<li>
							<a class="tz-lma" href="#"><img src="<?php echo Yii::getAlias("@web/themes/stralinglead/");?>images/icon/dbl6.png" alt="" /> Change Password</a>
						</li>
						
						
					</ul>
				</div>
			</div>
			<!--CENTER SECTION-->
			<div class="tz-2">
				<div class="tz-2-com tz-2-main">
					<h4>Profile</h4>
					<div class="db-list-com tz-db-table">
						<div class="ds-boar-title">
							<h2>Edit Profile</h2>							
						</div>
						<div class="tz2-form-pay tz2-form-com">
							
							 <?php $form = ActiveForm::begin(['id' =>'update-profile','class'=>"col s12"]); ?>
								
								<div class="row">
									<div class="input-field col s12 m6">
										
										<?= $form->field($model, 'password')->passwordInput(["class"=>"validate" , 'placeholder' =>'password']); ?>
										
									</div>
									<div class="input-field col s12 m6">
										<?= $form->field($model, 'confirm_password')->passwordInput(["class"=>"validate" , 'placeholder' =>'Confirm Password']); ?>
									</div>
								</div>
								<div class="row">
									<div class="input-field col s12">
										<?= Html::submitButton('SUBMIT', ['class' => 'waves-effect waves-light full-btn', 'name' => 'signup-button']) ?>
									</div>
								</div>
							<?php ActiveForm::end(); ?>  
						</div>
						
					</div>
				</div>
			</div>
			<!--RIGHT SECTION-->
			
		</div>
	</section>
	<!--END DASHBOARD-->
	
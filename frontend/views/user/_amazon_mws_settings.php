<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonMwsSetting */
/* @var $form ActiveForm */
?>
<div class="user-_amazon_mws_settings">
<h2>Amazon MWS</h2>
<p>In order to download your orders and turn them to VAT invoices, machine needs your SellerID and MWS Authorization Token.</p>
    <?php 
	$form = ActiveForm::begin(['action' => ['user/savemwssetting']]); ?>
        <?= $form->field($model, 'mws_seller_id') ?>
        <?= $form->field($model, 'mws_auth_token') ?>
		<?php echo $form->field($model, 'import_start_date')->widget(\yii\jui\DatePicker::class, ['dateFormat' =>'yyyy-MM-dd']); ?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>

			&nbsp;
				
			<?php 				 
			$ajaxUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/testconnection'], true );
			echo Html::a('Test Connection',['user/testconnection'], ['class'=>'btn btn-primary', 'title' => Yii::t('yii', 'Close'), 'onclick'=>" $.ajax({
				type     :'POST',
				cache    : false,
				data:{seller_id: $('#amazonmwssetting-mws_seller_id').val(), auth_token: $('#amazonmwssetting-mws_auth_token').val() },
				url  : '".$ajaxUrl."',
				success  : function(response) {
					alert(response);
				}
				});return false;",
                ]);	?>
        </div>
    <?php ActiveForm::end(); ?>

	<p>If you do not have these data yet, please <a href="https://sellercentral-europe.amazon.com/gp/mws/registration/register.html" target="_blank">login/register to Amazon MWS here</a></p>
<p>choose I want to use an application to access my Amazon seller account with MWS</p>
<p>Enter app name as Invoice Machine</p>
<p>Enter our Developer Account Number: 6988-3464-8474</p>
<p>Then copy your data back here and Save.</p>
<p>After Save, you can test connection whether everything works fine.</p>

<p>Note: You need Amazon Professional Seller Account to use MWS. </p>
</div><!-- user-_amazon_mws_settings -->

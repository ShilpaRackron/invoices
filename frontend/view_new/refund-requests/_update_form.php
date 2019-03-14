<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\RefundRequests */
/* @var $form yii\widgets\ActiveForm */
$status = ["open"=>"Open","closed"=>"Closed"];

$amazon_staus = ["requested_by_amazon"=>"Requested by Amazon","asked_to_accountant"=>"Asked to accountant","sent_to_amazon"=>"Sent to Amazon","re_evaluation_successful"=>"Re-evaluation Successful","re_evaluation_refused"=>"Re-Evaluation Refused"];


?>
<div class="refund-requests-form">

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>
	<div class="refund_request">
		<?= $form->field($model, 'case_id')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'refund_amount')->textInput() ?>    
		<?= $form->field($model, 'status')->dropDownList($status)->label(); ?>
	</div>
	<div class="purchase_invoice">
		<?= $form->field($model, 'purchase_invoice_no')->textInput() ?>
		<?= $form->field($model, 'amazon_status')->dropDownList($amazon_staus,['prompt'=>'--Select--'])->label(); ?>
	</div>

	<?= $form->field($model, 'is_approved')->checkBox() ?>
	<input type='hidden' name='submiturl' id='updaterefundurl'>      
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

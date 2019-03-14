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
<?php $script = <<< JS
jQuery(document).ready(function(){
   jQuery("#requestbutton").on("click",function(){
   		var case_id				= jQuery("#refundrequests-case_id").val();		
		var refund_amount		= jQuery("#refundrequests-refund_amount").val();
		var is_refund_approved	= jQuery("#refundrequests-is_approved").val();
		var status				= jQuery("#refundrequests-status").val();
		var refundurl			= jQuery("#refundurl").val();
		if( case_id =="" || refund_amount =="" ) {
			alert("Case Id and refund amount can not be empty!");
			return false;
			}else{
			jQuery.ajax({
				url: refundurl,
				type: 'POST',
				data: {"case_id":case_id,"refund_amount":refund_amount,"is_refund_approved":is_refund_approved,'status':status},
				success: function (response) 
				{					
					location.reload(true);
				},
				error  : function () 
				{

				}
			});
			return false;
		}
   });
});	
JS;
$this->registerJs($script);
?>
<div class="refund-requests-form">

    <?php $form = ActiveForm::begin(['action' => ['#']]); ?>
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
	<input type='hidden' name='submiturl' id='refundurl'>      

    <div class="form-group">
        <?= Html::button(Yii::t('app', 'Save'), ['class' => 'btn btn-success','id'=>"requestbutton"]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

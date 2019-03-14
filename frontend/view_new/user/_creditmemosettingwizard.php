<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\InvoiceSettings */
/* @var $form ActiveForm */
?>
<div class="user-_invoice_settings">
<h3>Creditmemo numbering</h3>
	<div class="col-md-9">		
    <?php $form = ActiveForm::begin(['action' => ['user/savecreditmemonumber']]); ?>
			 <div class="row">
				<div class="col-md-3"><?= $form->field($model, 'creditmemo_prefix') ?></div>
				<div class="col-md-3"><?= $form->field($model, 'creditmemo_counter') ?></div> 
				<div class="col-md-3"><?= $form->field($model, 'creditmemo_sufix') ?></div>
				<div class="col-md-3"><?= $form->field($model, 'creditmemo_length') ?></div>
			  </div>
        <div class=" row form-group">
			<div class="col-md-9 right">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>	       
			</div>
		</div>
    <?php ActiveForm::end(); ?>	
	
	 </div>
	 <p>E.g. if prefix=2018, sufix=xyz counter is 101 and length is 5, then next invoice number is: 2018-00102-xyz</p>
</div><!-- user-_invoice_settings -->


<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\jui\DatePicker;
use yii\jui\AutoComplete;
use yii\web\JsExpression;


/* @var $this yii\web\View */
/* @var $model frontend\models\CreditMemo */
/* @var $form yii\widgets\ActiveForm */
$user_id = 	Yii::$app->user->id;
//$amazonOrderId = ArrayHelper::map($invoiceModel->find()->select(['id','amazon_order_id'])->distinct()->where(['and', "user_id='$user_id'"])->all(), 'amazon_order_id','amazon_order_id');

?>

<div class="credit-memo-form">

    <?php $form = ActiveForm::begin(); ?>
	<?= $form->field($model, 'amazon_order_id')->hiddenInput()->label(); ?>
	<?php
	$data =$invoiceModel->find()
        ->select(['amazon_order_id as value','amazon_order_id as label','id as id'])->distinct()->where(['and', "user_id='".$user_id."'"])
        ->asArray()
        ->all();
	echo AutoComplete::widget([    
	'clientOptions' => [
	'source' => $data,
	'minLength'=>'3', 
	'autoFill'=>true,
	'select' => new JsExpression("function( event, ui ) {
			        $('#creditmemo-amazon_order_id').val(ui.item.id);
			     }")],
	'options'=>['style'=>'width:25%;']
	 ]);
	?>	
	<div class="form-group required">
	<label class="control-label" for="creditmemo-return_date">Return Date</label>
	<?php
	  echo DatePicker::widget([
		'model' => $model,
		'attribute' => 'return_date',
		//'language' => 'ru',
		'dateFormat' => 'yyyy-MM-dd',
		'options'=>['class'=>'form-control', 'size'=>'25','style'=>'width:25%;']
	]);
	?>
	</div>
	 <?= $form->field($model, 'qty_return')->textInput(['style'=>'width:25%;'])->label(); ?>
    <?= $form->field($model, 'total_amount_refund')->textInput(['style'=>'width:25%;'])->label(); ?>
	<!-- 
    <?= $form->field($model, 'asin')->textInput()->label(); ?>

    <?= $form->field($model, 'fnsku')->textInput()->label(); ?>

    <?= $form->field($model, 'fulfillment_center_id')->textInput()->label() ?>
	<?= $form->field($model, 'license_plate_number')->textInput()->label() ?>
    <?= $form->field($model, 'detailed_disposition')->textInput()->label() ?>

    <?= $form->field($model, 'reason')->textarea(['rows' => 6])->label() ?>   
    
    <?= $form->field($model, 'customer_comments')->textarea(['rows' => 6])->label() ?>    
     -->
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

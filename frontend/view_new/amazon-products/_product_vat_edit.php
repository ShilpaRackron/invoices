<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
//use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonOrders */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="amazon-orders-form">
<h6>Set individual VAT rate for product and VAT Number</h6>
<?php $form = ActiveForm::begin([ 'enableClientValidation' => true,'method' => 'post', 'options'=> ['id'=>'dynamic-form']]); ?>
<table>
		<?php if(isset($vatRnModel[0]) && !empty($vatRnModel[0])) { ?>
		<tr><td>VAT RN: </td><td>default <b><?php echo $vatRnModel[0]->vat_no;?></b> Default rate: <?php echo $vatRnModel[0]->rate_percentage;?></td></tr>
		<?php } ?>
		<tr><td>SKU: </td><td><?php echo $model->sku;?></td></tr>
		<tr><td valign="top">Name: </td><td><?php echo $model->product_name;?></td></tr> 		
	 <tr><td><?= $form->field($model, 'sku')->hiddenInput()->label(false); ?> </td></tr>
    <tr><td><?= $form->field($model, 'vat_rate')->textInput() ?> </td></tr>   
    <tr><td><?= $form->field($model, 'vat_value')->textInput() ?> </td></tr>
	<tr><td><?= $form->field($model, 'comm_code')->textInput() ?></td></tr>
   <tr><td> <?= $form->field($model, 'weight')->textInput() ?><span>KG</span></td></tr>

   	<tr><td>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
		<?= Html::resetButton(Yii::t('app', 'Set Default'), ['class' => 'btn btn-primary', "id"=>"product_vat_edit_set_default"]) ?>
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
	</td>
	</tr>    
	</table>
	<?php ActiveForm::end(); ?>
</div>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonReimbursements */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="amazon-reimbursements-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'approval_date')->textInput() ?>

    <?= $form->field($model, 'reimbursement_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'case_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amazon_order_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reason')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sku')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fnsku')->textInput() ?>

    <?= $form->field($model, 'asin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'item_condition')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'currency_unit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount_per_unit')->textInput() ?>

    <?= $form->field($model, 'amount_total')->textInput() ?>

    <?= $form->field($model, 'quantity_reimbursed_cash')->textInput() ?>

    <?= $form->field($model, 'quantity_reimbursed_inventory')->textInput() ?>

    <?= $form->field($model, 'quantity_reimbursed_total')->textInput() ?>

    <?= $form->field($model, 'original_reimbursement_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'original_reimbursement_type')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonInventoryAdjustment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="amazon-inventory-adjustment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'adjusted_date')->textInput() ?>

    <?= $form->field($model, 'transaction_item_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fnsku')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sku')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fulfillment_center_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'reason')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'disposition')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

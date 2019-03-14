<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonInventory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="amazon-inventory-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'marketplace')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sku')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fnsku')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'asin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'product_condition')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'mfn_listing_exists')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mfn_fulfillable_quantity')->textInput() ?>

    <?= $form->field($model, 'afn_listing_exists')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'afn_warehouse_quantity')->textInput() ?>

    <?= $form->field($model, 'afn_fulfillable_quantity')->textInput() ?>

    <?= $form->field($model, 'afn_unsellable_quantity')->textInput() ?>

    <?= $form->field($model, 'afn_reserved_quantity')->textInput() ?>

    <?= $form->field($model, 'afn_total_quantity')->textInput() ?>

    <?= $form->field($model, 'per_unit_volume')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'afn_inbound_working_quantity')->textInput() ?>

    <?= $form->field($model, 'afn_inbound_shipped_quantity')->textInput() ?>

    <?= $form->field($model, 'afn_inbound_receiving_quantity')->textInput() ?>

    <?= $form->field($model, 'import_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

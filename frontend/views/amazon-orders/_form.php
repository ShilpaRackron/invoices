<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonOrders */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="amazon-orders-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'latest_ship_date')->textInput() ?>

    <?= $form->field($model, 'order_type')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'purchase_date')->textInput() ?>

    <?= $form->field($model, 'amazon_order_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_update_date')->textInput() ?>

    <?= $form->field($model, 'number_of_items_shipped')->textInput() ?>

    <?= $form->field($model, 'sales_channel')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'latest_delivery_date')->textInput() ?>

    <?= $form->field($model, 'number_of_items_unshipped')->textInput() ?>

    <?= $form->field($model, 'payment_method_detail')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'buyer_name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'earliest_delivery_date')->textInput() ?>

    <?= $form->field($model, 'earliest_ship_date')->textInput() ?>

    <?= $form->field($model, 'marketplace_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fulfillment_channel')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'payment_method')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'customer_name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'shipment_category')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

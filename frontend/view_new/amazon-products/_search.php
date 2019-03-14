<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonProductsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="amazon-products-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'sku') ?>

    <?= $form->field($model, 'asin') ?>

    <?= $form->field($model, 'vat_id') ?>

    <?php // echo $form->field($model, 'product_name') ?>

    <?php // echo $form->field($model, 'vat_rate') ?>

    <?php // echo $form->field($model, 'value') ?>

    <?php // echo $form->field($model, 'comm.code') ?>

    <?php // echo $form->field($model, 'condition_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

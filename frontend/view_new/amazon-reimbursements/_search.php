<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonReimbursementsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="amazon-reimbursements-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'approval_date') ?>

    <?= $form->field($model, 'reimbursement_id') ?>

    <?= $form->field($model, 'case_id') ?>

    <?php // echo $form->field($model, 'amazon_order_id') ?>

    <?php // echo $form->field($model, 'reason') ?>

    <?php // echo $form->field($model, 'sku') ?>

    <?php // echo $form->field($model, 'fnsku') ?>

    <?php // echo $form->field($model, 'asin') ?>

    <?php // echo $form->field($model, 'product_name') ?>

    <?php // echo $form->field($model, 'item_condition') ?>

    <?php // echo $form->field($model, 'currency_unit') ?>

    <?php // echo $form->field($model, 'amount_per_unit') ?>

    <?php // echo $form->field($model, 'amount_total') ?>

    <?php // echo $form->field($model, 'quantity_reimbursed_cash') ?>

    <?php // echo $form->field($model, 'quantity_reimbursed_inventory') ?>

    <?php // echo $form->field($model, 'quantity_reimbursed_total') ?>

    <?php // echo $form->field($model, 'original_reimbursement_id') ?>

    <?php // echo $form->field($model, 'original_reimbursement_type') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

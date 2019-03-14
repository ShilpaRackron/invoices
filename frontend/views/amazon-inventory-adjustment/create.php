<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonInventoryAdjustment */

$this->title = Yii::t('app', 'Create Amazon Inventory Adjustment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Amazon Inventory Adjustments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amazon-inventory-adjustment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonInventory */

$this->title = Yii::t('app', 'Create Amazon Inventory');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Amazon Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amazon-inventory-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

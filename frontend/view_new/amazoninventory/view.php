<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonInventory */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Amazon Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amazon-inventory-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'marketplace',
            'sku',
            'fnsku',
            'asin',
            'product_name:ntext',
            'product_condition',
            'price',
            'mfn_listing_exists',
            'mfn_fulfillable_quantity',
            'afn_listing_exists',
            'afn_warehouse_quantity',
            'afn_fulfillable_quantity',
            'afn_unsellable_quantity',
            'afn_reserved_quantity',
            'afn_total_quantity',
            'per_unit_volume',
            'afn_inbound_working_quantity',
            'afn_inbound_shipped_quantity',
            'afn_inbound_receiving_quantity',
            'import_date',
        ],
    ]) ?>

</div>

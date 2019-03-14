<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonOrders */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Amazon Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amazon-orders-view">

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
            'latest_ship_date',
            'order_type:ntext',
            'purchase_date',
            'buyer_email:email',
            'amazon_order_id',
            'is_replacement_order',
            'last_update_date',
            'number_of_items_shipped',
            'ship_service_level',
            'order_status',
            'sales_channel:ntext',
            'shipped_by_amazon_tfm',
            'is_business_order',
            'latest_delivery_date',
            'number_of_items_unshipped',
            'payment_method_detail:ntext',
            'buyer_name:ntext',
            'buyer_vat:ntext',
            'earliest_delivery_date',
            'is_premium_order',
            'order_currency',
            'total_amount',
            'earliest_ship_date',
            'marketplace_id',
            'fulfillment_channel:ntext',
            'payment_method:ntext',
            'city',
            'address_type',
            'postal_code',
            'state_or_region',
            'phone',
            'country_code',
            'customer_name:ntext',
            'address_2',
            'is_prime',
            'shipment_category:ntext',
        ],
    ]) ?>

</div>

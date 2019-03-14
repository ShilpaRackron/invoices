<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AmazonOrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Amazon Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amazon-orders-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Amazon Orders'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'latest_ship_date',
            'order_type:ntext',
            'purchase_date',
            //'buyer_email:email',
            //'amazon_order_id',
            //'is_replacement_order',
            //'last_update_date',
            //'number_of_items_shipped',
            //'ship_service_level',
            //'order_status',
            //'sales_channel:ntext',
            //'shipped_by_amazon_tfm',
            //'is_business_order',
            //'latest_delivery_date',
            //'number_of_items_unshipped',
            //'payment_method_detail:ntext',
            //'buyer_name:ntext',
            //'buyer_vat:ntext',
            //'earliest_delivery_date',
            //'is_premium_order',
            //'order_currency',
            //'total_amount',
            //'earliest_ship_date',
            //'marketplace_id',
            //'fulfillment_channel:ntext',
            //'payment_method:ntext',
            //'city',
            //'address_type',
            //'postal_code',
            //'state_or_region',
            //'phone',
            //'country_code',
            //'customer_name:ntext',
            //'address_2',
            //'is_prime',
            //'shipment_category:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

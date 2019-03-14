<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\AmazonReimbursementsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Amazon Reimbursements');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amazon-reimbursements-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

     <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'reimbursement_id',
            //'case_id',
            'amazon_order_id',
            'reason:ntext',
            'sku:ntext',
            //'fnsku',
            'asin',
            'product_name:ntext',
           // 'item_condition',
            'currency_unit',
           // 'amount_per_unit',
            'amount_total',
           // 'quantity_reimbursed_cash',
            'quantity_reimbursed_inventory',
            'quantity_reimbursed_total',
			'approval_date',
            //'original_reimbursement_id',
            //'original_reimbursement_type',

            //['class' => 'yii\grid\ActionColumn','template'=>""],
        ],
    ]); ?>
</div>

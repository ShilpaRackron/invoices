<?php
use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\RefundRequests;
use frontend\models\RefundRequestsSearch;
$searchModel = new RefundRequestsSearch();
$dataProvider = $searchModel->searchData(Yii::$app->request->queryParams, $model->id);
?>
<div class="refund-requests-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'case_id',
            'refund_amount',
           	[
			  'attribute' => 'is_approved',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  ($model->is_approved==1)?"Approved":"Pending";
			   },
			],
			'purchase_invoice_no',
			[
			  'attribute' => 'amazon_status',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->getStatus($model->amazon_status);
			   },
			],
            'status',
            'updated_date',
            ['class' => 'yii\grid\ActionColumn','template'=>'{update}', 'buttons' => [

                    'update' => function ($url, $model) {
                        return  Html::a('<span class="glyphicon glyphicon-pencil"></span>Update', '#', [ 'title' => Yii::t('app', 'Update'), 'class'=>'btn btn-primary btn-xs updaterefundrequest','updateurl'=>$url]) ;
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
				if ($action === 'update') {
                        $url = \yii\helpers\Url::toRoute(['refund-requests/update', 'id' => $model->id], ['class' => 'update_case updaterefundrequest']);
                        return $url;
                }				
				}],
        ],
    ]); ?>
</div>

<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\AmazonProductsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Amazon Products');
$this->params['breadcrumbs'][] = $this->title;
?>

<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/bootstrap.min.js"></script>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/ajax-modal-popup.js"></script>
<div class="amazon-products-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  //echo $this->render('_search', ['model' => $searchModel]); ?>
 
 <?php
yii\bootstrap\Modal::begin([
    'headerOptions' => ['id' => 'modalHeader'],
    'id' => 'modal',
    'size' => 'modal-lg',
    //keeps from closing modal with esc key or by clicking out of the modal.
    // user must click cancel or X to close
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
]);
echo "<div id='modalContent'><div style='text-align:center'><img src='".Yii::getAlias("@web/themes/amazitonew/")."images/loader.gif'></div></div>";
yii\bootstrap\Modal::end();
?>
 
 <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'rowOptions'   => function ($model, $key, $index, $grid) {
			return ['data-id' => $model->sku,'class'=>'showModalButton', "value"=>Url::to(['amazon-products/productvatedit', 'sku'=>$model->sku]),"title"=>"Update Vat"];
		},
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],
			//'vat_id',
			[
			  'attribute' => 'vat_id',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->getVat();
			   },
			],
            'product_name:ntext',
            'sku',
            'asin',
			//'vat_rate',
			[
			  'attribute' => 'vat_rate',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->getVatRate();
			   },
			],
			'vat_value',
			//'comm_code:ntext',
			[
			  'attribute' => 'comm_code',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->getCommCode();
			   },
			],
           // ['class' => 'yii\grid\ActionColumn','template'=>' '],			
        ],
    ]); ?>
</div>
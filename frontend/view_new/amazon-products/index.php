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
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
	</div>
<div class="amazon-products-index">
<div class="container-fluid mt--7">
      <div class="row">
       <div class="col-xl-12 order-xl-1">
          <div class="card bg-secondary shadow">
            <div class="card-header bg-white border-0">
              <div class="row align-items-center">
                <div class="col-8">
                  <h2 class="mb-0"><?= Html::encode($this->title) ?></h2>
                </div>
               
              </div>
            </div>
    
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
		'showHeader'=>true,

    		'layout' => "{summary}\n{items}\n{pager}",

    		'options' => array('class' => 'table-responsive'),

    		'tableOptions' => array('class' => 'table align-items-center table-flush '),
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
<?php

use yii\helpers\Html;
#use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\editable\Editable;
use frontend\models\AmazonInventoryAdjustment;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use kartik\icons\FontAwesomeAsset;
FontAwesomeAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\AmazonInventoryAdjustmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Inventory Adjustments');
$this->params['breadcrumbs'][] = $this->title;
$user_id  = Yii::$app->user->id;
?>
<?php

    Modal::begin([
        'header'=>'<h4>Request Refund</h4>',
        'id'=>'set_request_refund',
        'size'=>'modal-lg'
    ]);

	$content = $this->render('/refund-requests/_form', ['model' => $requestModel]);
	echo "<div id='setModalContent'>$content</div>";
    Modal::end();
?>
<?php $script = <<< JS
	jQuery(function () {
    jQuery('.setrefundrequest').click(function () {
		jQuery("#refundurl").val(jQuery(this).attr('url'))
        jQuery('#set_request_refund')
            .modal('show')
            .find('#setModalContent')
            .load(jQuery(this).attr('value'));
    });
	});
JS;
$this->registerJs($script);
?>
<div class="amazon-inventory-adjustment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
	<div style="clear:both;">&nbsp;</div>
	<?php   

	$reasonFilter = ArrayHelper::map(AmazonInventoryAdjustment::find()->select(['id','reason'])->distinct()->where(['and', "user_id='$user_id'"])->asArray()->all(), 'reason','reason');
	
	$gridColumns = [
	['class' => 'kartik\grid\SerialColumn'],
	[
    'class' => 'kartik\grid\ExpandRowColumn',
    'width' => '50px',
    'value' => function ($model, $key, $index, $column) {
        return GridView::ROW_COLLAPSED;
    },
    'detail' => function ($model, $key, $index, $column) {
        return Yii::$app->controller->renderPartial('_expand-row-details', ['model' => $model]);
    },
    'headerOptions' => ['class' => 'kartik-sheet-style'], 
    'expandOneOnly' => true
	],
	[         
        'attribute'=>'fnsku', 
        'vAlign'=>'middle',
		'pageSummary' => 'Page Total',
    ],    
    
	
	[
        'attribute'=>'product_name', 
        'vAlign'=>'middle',
    ],
	[
        
        'attribute'=>'fulfillment_center_id', 
        'vAlign'=>'middle',
    ],

	[       
        'attribute'=>'quantity',
        'vAlign'=>'middle',
		'pageSummary' => true,
		'value' => function ($model, $key, $index, $column) {
			
			return $model->getTotalQty($model->fnsku);
		
		},
    ],

	[
        'attribute'=>'reason',
        'vAlign'=>'middle',        
		'filterType' => GridView::FILTER_SELECT2,
		'filter' => $reasonFilter, 
		'filterWidgetOptions' => [
			'pluginOptions' =>['allowClear' => true],
		],
		'filterInputOptions' => ['placeholder' => 'Any reason'],
		'format' => 'raw'
    ],
	
	[  'attribute' => 'adjusted_date',        
        'vAlign'=>'middle',       
    ],
];
echo GridView::widget([
	'id'=>"master-grid",
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
    'beforeHeader'=>[ ],
    'toolbar' =>  [ ],
    'pjax' => true,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'floatHeader' => true,
    'floatHeaderOptions' => ['scrollingTop' => true],
    'showPageSummary' => true,
	'bootstrap'=>true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);
	?>
</div>

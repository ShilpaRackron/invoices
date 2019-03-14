<?php

use yii\helpers\Html;
#use yii\grid\GridView;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use kartik\editable\Editable;
use frontend\models\AmazonInventoryAdjustment;
use yii\helpers\ArrayHelper;
use kartik\icons\FontAwesomeAsset;
FontAwesomeAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\AmazonInventoryAdjustmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Inventory Adjustments');
$this->params['breadcrumbs'][] = $this->title;
$user_id  = Yii::$app->user->id;
?>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/bootstrap.min.js"></script>
<?php

    Modal::begin([
        'header'=>'<h4>Request Refund</h4>',
        'id'=>'set_request_refund',
        'size'=>'modal-lg'
    ]);
	$content = $this->render('/refund-requests/_form', ['model' => $requestModel]);
	echo "<div id='setModalContent'>$content</div>"; 
    Modal::end();


	
    Modal::begin([
        'header'=>'<h4>Update Request</h4>',
        'id'=>'update_request_refund',
        'size'=>'modal-lg'
    ]);
	?>
	<?php
	echo "<div id='updateRequestModalContent'></div>"; 
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

	jQuery('.updaterefundrequest').click(function () {	
		jQuery("#updateRequestModalContent").html("");
		jQuery("#updaterefundurl").val(jQuery(this).attr('updateurl'));
		jQuery('#updateRequestModalContent').load(jQuery(this).attr('updateurl'));
        jQuery('#update_request_refund')
            .modal('show')
            .find('#updateRequestModalContent')
            .load(jQuery(this).attr('updateurl'));
    });

	});
JS;
$this->registerJs($script);

 $inventoryModel = new AmazonInventoryAdjustment();
 $inventoryReason = $inventoryModel->getInventoryStatus();
 
?>
<div class="amazon-inventory-adjustment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel,'inventoryReason'=>$inventoryReason]); ?>
	<div style="clear:both;">&nbsp;</div>
	<?php   

	//$reasonFilter = ArrayHelper::map(AmazonInventoryAdjustment::find()->select(['id','reason'])->distinct()->where(['and', "user_id='$user_id'"])->asArray()->all(), 'reason','reason');
    // $end_date = date("Y-m-d");
     //$start_date = 	date("Y-m-d", strtotime("-30 days"));
	 
	 if(empty($searchModel->adjusted_date)) {
			$end_date = date("Y-m-d");
			$start_date = 	date("Y-m-d", strtotime("-30 days"));
	}
	elseif(!empty($searchModel->adjusted_date) && strpos($searchModel->adjusted_date, '-') !== false) {
			list($start_date, $end_date) = explode(' - ', $searchModel->adjusted_date);

			 $dt				=  \DateTime::createFromFormat('d-m-Y', $start_date);	
			 $start_date		= $dt->format('Y-m-d');

			 $dt1				=  \DateTime::createFromFormat('d-m-Y', $end_date);	
			 $end_date		= $dt1->format('Y-m-d');
	}
	
	$gridColumns = [
	//['class' => 'kartik\grid\SerialColumn'],
	[
    'class' => 'kartik\grid\ExpandRowColumn',
    'width' => '50px',
    'value' => function ($model, $key, $index, $column) {
        return GridView::ROW_COLLAPSED;
    },
    'detail' => function ($model, $key, $index, $column) {
        return Yii::$app->controller->renderPartial('refund_request_grid', ['model' => $model]);
    },
    'headerOptions' => ['class' => 'kartik-sheet-style'], 
    'expandOneOnly' => false,
	'format'=>'raw',
	],
	[         
        'attribute'=>'fnsku', 
        'vAlign'=>'top',
		'width' => '135px',
		'value' => function ($model, $key, $index, $widget) { 
                return $model->fnsku;
         },
		 'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(AmazonInventoryAdjustment::find(['id','fnsku'])->where(['and', "user_id='$user_id'"])->andFilterWhere(['between', 'DATE(adjusted_date)', $start_date, $end_date])->orderBy('fnsku')->asArray()->all(), 'fnsku', 'fnsku'), 
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
        'filterInputOptions' => ['placeholder' => 'fnsku'],
		'group' => true,
		'pageSummary' => 'Page Total',
		/* 'groupFooter' => function ($model, $key, $index, $widget) { // Closure method
                return [
                    'mergeColumns' => [[1,3]], // columns to merge in summary
                    'content' => [              // content to show in each summary cell
                        1 => 'Summary (' . $model->fnsku . ')',
                        4 => GridView::F_SUM,
                       // 5 => GridView::F_SUM,
                        //6 => GridView::F_SUM,
                    ],
                    'contentFormats' => [      // content reformatting for each summary cell
                        4 => ['format' => 'number'],
                       // 5 => ['format' => 'number'],
                      //  6 => ['format' => 'number'],
                    ],
                    'contentOptions' => [      // content html attributes for each summary cell
                        4 => ['style' => 'text-align:left'],
                       // 5 => ['style' => 'text-align:left'],
                        //6 => ['style' => 'text-align:left'],
                    ],
                    // html attributes for group summary row
                    'options' => ['class' => 'success table-success','style' => 'font-weight:bold;']
                ];
            }, */
    ],    
    
	
	[
        'attribute'=>'product_name',
		'width' => '150px',
        'vAlign'=>'middle',		
		'value' => function ($model, $key, $index, $widget) { 
                return $model->product_name .' <br /> <br/ > <b>SKU</b> = '.$model->sku;
         },
		'format' => 'raw',
    ],
	/* [
        'attribute'=>'sku', 
		'width' => '20px',
        'vAlign'=>'middle',	
        

    ],*/
	[  
		'attribute' => 'transaction_item_id',        
        'vAlign'=>'middle',
		'group' => true,  // enable grouping
        'subGroupOf' => 1
    ],
	

	[       
        'attribute'=>'quantity',
        'vAlign'=>'middle',
		'pageSummary' => true,
		'value' => function ($model, $key, $index, $column) {
			//return $model->quantity;
			return (isset($model->totalqty))?$model->totalqty:$model->quantity;
		},
    ],
	
	/* [
        
        'attribute'=>'fulfillment_center_id', 
        'vAlign'=>'middle',
    ], */	

	[
        'attribute'=>'reason',
		"width"=>"80px",
        'vAlign'=>'middle',		
		'value' => function ($model, $key, $index, $widget) { 
                return $model->getReasonText($model->reason);
         },
		 /* 'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(AmazonInventoryAdjustment::find(['id','reason'])->where(['and', "user_id='$user_id'"])->orderBy('transaction_item_id')->asArray()->all(), 'reason', 'reason'), 
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
        'filterInputOptions' => ['placeholder' => 'Cause'],*/

		'format' => 'raw'		
    ],
	
	[  'attribute' => 'adjusted_date',
		'value' => function ($model, $key, $index, $widget) { 
                return $model->getDateFormat($model->adjusted_date);
         },
        'vAlign'=>'middle',       
    ],
	[  
		'attribute' => 'disposition',		
        'vAlign'=>'middle',       
    ],
	['class' => 'kartik\grid\ActionColumn','template'=>'{request_refund}', 'buttons' => [

                    //view button
                   	'request_refund' => function ($url, $model) {
						$setRequestRefundText ="Open Case";
                        return  Html::a('<span class="fa fa-pdf" title="'.$setRequestRefundText.'"></span>'.$setRequestRefundText, '#', [ 'title' => Yii::t('app', $setRequestRefundText), 'class'=>'btn btn-xs setrefundrequest',  "url"=>$url ]) ;
                    },
                ],

                'urlCreator' => function ($action, $model, $key, $index) {
				if ($action === 'purchase_invoice') {
                        $url = \yii\helpers\Url::toRoute(['#', 'transaction_item_id' => $model->id]);
                        return $url;
                }

				if ($action === 'request_refund') {
                        $url = Yii::$app->urlManager->createAbsoluteUrl(['refund-requests/insert-case', 'transaction_item_id' => $model->id]);
                        return $url;
                }
				}],
];


echo GridView::widget([
	'id'=>"master-grid",
    'dataProvider' => $dataProvider,
	'showPageSummary' => true,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
    'beforeHeader'=>[ ],
    'toolbar' =>  [ ],
    'pjax' => true,
    'bordered' => true,
    'striped' => true,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'floatHeader' => true,
	'toggleDataContainer' => ['class' => 'btn-group mr-2'],
    'floatHeaderOptions' => ['scrollingTop' => true],
    'showPageSummary' => true,
	'bootstrap'=>true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);
	?>
</div>

<?php

use yii\helpers\Html;
#use yii\grid\GridView;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use kartik\editable\Editable;
use frontend\models\AmazonInventoryAdjustment;
use yii\helpers\ArrayHelper;
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
	/* $content = "<p><input type='text' name='case_id' id='case_id' placeholder ='Enter Case Id' value=''></p>
	<p><input type='text' name='refund_amount' id='refund_amount' placeholder ='Enter Refund Amount' value=''></p>
	<p><input type='checkbox' name='is_approved' id='is_refund_approved' value=''><lable>Refund Approved</label></p>
	<input type='hidden' name='submiturl' id='refundurl'><br><br>". Html::button('Submit', [
					'class' => 'btn btn-sm btn-primary', 
					'onclick' => 'submitRequestRefund()'
					]) ; */
	
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
   <!--  <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            'adjusted_date',
            'transaction_item_id',
            'fnsku',
            'sku',
            'product_name:ntext',
            'fulfillment_center_id',
            'quantity',
            'reason:ntext',
            'disposition',

           ['class' => 'yii\grid\ActionColumn','template'=>'{purchase_invoice}<br/><br/>{request_refund}', 'buttons' => [

                    //view button
                    'view' => function ($url, $model) {
                        return  Html::a('<span class="fa fa-search"></span>View', $url, 
[ 'title' => Yii::t('app', 'View'), 'class'=>'btn btn-primary btn-xs', ]) ;
                    },
					'purchase_invoice' => function ($url, $model) {
					return 	Html::a('<span class="fa fa-file-pdf-o" title="Purchase Invoice"></span>Purchase Invoice','#', ['class'=>'btn btn-primary', 'title' => Yii::t('yii', 'Purchase Invoice'), 'onclick'=>" $.ajax({
					type     :'POST',
					cache    : false,
					data:{is_ajax: true },
					url  : '".$url."',
					success  : function(response) {						
					location. reload(true);
					},
					error  : function () 
						{					
					}
					});return false;",
					]) ;                       
                    },

					'request_refund' => function ($url, $model) {
						//$request_refund = $model->request_refund;
						//$setRequestRefundText = (trim($checkVatExist) !="")?"EditVAT":"SetVAT";
						$setRequestRefundText ="Request Refund";
                        return  Html::a('<span class="fa fa-pdf" title="'.$setRequestRefundText.'"></span>'.$setRequestRefundText, '#', [ 'title' => Yii::t('app', $setRequestRefundText), 'class'=>'btn btn-xs setrefundrequest',  "url"=>$url ]) ;
                    },
                ],

                'urlCreator' => function ($action, $model, $key, $index) {
				if ($action === 'purchase_invoice') {
                        $url = \yii\helpers\Url::toRoute(['user/sendpdf', 'transaction_item_id' => $model->id]);
                        return $url;
                }

				if ($action === 'request_refund') {
                        $url = Yii::$app->urlManager->createAbsoluteUrl(['refund-requests/insert-case', 'transaction_item_id' => $model->id]);
                        return $url;
                }
				}],
        ],
    ]); ?> -->

	<?php   

	$reasonFilter = ArrayHelper::map(AmazonInventoryAdjustment::find()->select(['id','reason'])->distinct()->where(['and', "user_id='$user_id'"])->asArray()->all(), 'reason','reason');
	
	$gridColumns = [    
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
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);
	?>
</div>

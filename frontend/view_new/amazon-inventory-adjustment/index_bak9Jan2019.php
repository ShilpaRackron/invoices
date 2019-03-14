<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
#use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\AmazonInventoryAdjustmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Inventory Adjustments');
$this->params['breadcrumbs'][] = $this->title;
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
    <?= GridView::widget([
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
    ]); ?> 

	<?php

	 

	 /*  $gridColumns = [
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
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'adjusted_date',
        'pageSummary' => 'Page Total',
        'vAlign'=>'middle',
        'headerOptions'=>['class'=>'kv-sticky-column'],
        'contentOptions'=>['class'=>'kv-sticky-column'],
        'editableOptions'=>['header'=>'adjusted_date', 'size'=>'md']
    ],
    [
        'attribute'=>'transaction_item_id',
        'value'=>function ($model, $key, $index, $widget) {
            return "<span class='badge' style='background-color: {$model->color}'> </span>  <code>" . 
                $model->transaction_item_id . '</code>';
        },
        'filterType'=>GridView::FILTER_COLOR,
        'vAlign'=>'middle',
        'format'=>'raw',
        'width'=>'150px',
        'noWrap'=>true
    ],
    [
        'class'=>'kartik\grid\BooleanColumn',
        'attribute'=>'fnsku', 
        'vAlign'=>'middle',
    ],
	[
        'class'=>'kartik\grid\BooleanColumn',
        'attribute'=>'sku', 
        'vAlign'=>'middle',
    ],
	[
        'class'=>'kartik\grid\BooleanColumn',
        'attribute'=>'product_name', 
        'vAlign'=>'middle',
    ],
	[
        'class'=>'kartik\grid\BooleanColumn',
        'attribute'=>'fulfillment_center_id', 
        'vAlign'=>'middle',
    ],

	[
        'class'=>'kartik\grid\BooleanColumn',
        'attribute'=>'quantity', 
        'vAlign'=>'middle',
    ],

	[
        'attribute'=>'reason',
        'vAlign'=>'middle',      
        'noWrap'=>true
    ],
	
	[
        'class'=>'kartik\grid\BooleanColumn',
        'attribute'=>'disposition', 
        'vAlign'=>'middle',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => true,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { return '#'; },
        'viewOptions'=>['title'=>$viewMsg, 'data-toggle'=>'tooltip'],
        'updateOptions'=>['title'=>$updateMsg, 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['title'=>$deleteMsg, 'data-toggle'=>'tooltip'], 
    ],
    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
    'beforeHeader'=>[
        [
            'columns'=>[
                ['content'=>'Header Before 1', 'options'=>['colspan'=>4, 'class'=>'text-center warning']], 
                ['content'=>'Header Before 2', 'options'=>['colspan'=>4, 'class'=>'text-center warning']], 
                ['content'=>'Header Before 3', 'options'=>['colspan'=>3, 'class'=>'text-center warning']], 
            ],
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar' =>  [
        ['content'=>
            Html::button('&lt;i class="glyphicon glyphicon-plus">&lt;/i>', ['type'=>'button', 'title'=>Yii::t('kvgrid', 'Add Book'), 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
            Html::a('&lt;i class="glyphicon glyphicon-repeat">&lt;/i>', ['grid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
        ],
        '{export}',
        '{toggleData}'
    ],
    'pjax' => true,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'floatHeader' => true,
    'floatHeaderOptions' => ['scrollingTop' => $scrollingTop],
    'showPageSummary' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);	*/
	?>
</div>

<?php
use yii\helpers\Html;
use frontend\models\AmazonInventoryAdjustment;
use frontend\models\AmazonInventoryAdjustmentSearch;
use kartik\grid\GridView;
//use kartik\editable\Editable;
$searchModel = new AmazonInventoryAdjustmentSearch();
$dataProviderChild = $searchModel->getChildSearch([], $model->fnsku);

?>

<div class="amazon-inventory-adjustment-index">
  
  <?= GridView::widget([		
        'dataProvider' => $dataProviderChild,       
        'columns' => [           
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
			'expandOneOnly' => true
			],
            'adjusted_date',
            'transaction_item_id',           
            'sku',
            'product_name:ntext',
            'fulfillment_center_id',
            'quantity',
            'reason:ntext',
            'disposition',

           ['class' => 'kartik\grid\ActionColumn','template'=>'{purchase_invoice}<br/><br/>{request_refund}', 'buttons' => [

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


</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\CreditMemoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Credit Memos');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="credit-memo-index">
   <h1><?= Html::encode($this->title) ?></h1>
    <?php  
	echo $this->render('_search', ['model' => $searchModel,'invoiceModel'=>$invoiceModel,'userModel'=>$userModel]); ?>
	<div class="form-group col-sm-9" id="excel_acc_xlsx">
		<button type="button" class="btn btn-success btn-md button-excel m-r-5" id="export_excel_analytics">
			Analytics <i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i>
		</button>
		<button type="button" class="btn btn-success btn-md button-excel m-r-5" id="export_excel" datatype="normal"> 
			Accountant <i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i>
		</button>

		<?php 
		$accountantEmailUrl = \yii\helpers\Url::toRoute(['user/send-credit-note-account-report']);
		echo Html::a('<span class="fa fa-file-email-o" title="Send Email to Accountant"></span>Email Accountant','javascript: void(0);', ['class'=>'btn btn-primary', "id"=>"accountemail",'title' => Yii::t('yii', 'Send Email to Accountant')]);
		
		$analyticsEmailUrl = \yii\helpers\Url::toRoute(['user/send-credit-note-analytics-report']);
		$importOrderUrl = \yii\helpers\Url::toRoute(['user/import-credit-note-request']);
		
		echo Html::a('<span class="fa fa-file-email-o" title="Send Email to Analytics "></span>Email Analytics','javascript: void(0);', ['class'=>'btn btn-primary', "id"=>"analyticsemail",'title' => Yii::t('yii', 'Send Email to Analytics')]);
		?>
	</div>
	   <?php
	   $anlyticsAction =Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/exportanalyticscreditmemo'], true );
	   $accountAction =Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/exportcreditmemoaccount'], true );
$script = <<< JS
	jQuery("#export_excel_analytics").click(function(){
		location.href="{$anlyticsAction}";
	});
	jQuery("#export_excel").click(function(){
		location.href="{$accountAction}";
	});

	jQuery("#column_selection").click(function(){
		jQuery(".row1").toggle();
	})
	jQuery("#accountemail").click(function(){
		    var data = { 'attributes' : []};
			var emailIds	= jQuery("#emails").val();
			var is_checked	= false;
			var export_report_range	= jQuery("#export_report_range").val();			
			$(".attributes:checked").each(function() {				
			  data['attributes'].push($(this).val());
			  is_checked = true;
			});
			if(is_checked) {			
			jQuery.ajax({
				url: "{$accountantEmailUrl}",
				type: 'POST',
				data: {"attribute_data":data,"email":emailIds, "export_report_range":export_report_range},
				beforeSend: function() {
				   $('#accountemail').text('Sending Email...');
				},
				success: function (response) 
				{
					location.reload(true);
				},
				error  : function () 
				{
					//alert("Error In saving vat");
				}
			});

			}else{
				alert("Please select fields to exports");
			}

		});

		jQuery("#analyticsemail").click(function(){
		    var data = { 'attributes' : []};
			var emailIds	= jQuery("#emails").val();
			var is_checked	= false;
			var export_report_range	= jQuery("#export_report_range").val();
			$(".attributes:checked").each(function() {				
			  data['attributes'].push($(this).val());
			  is_checked = true;
			});
			if(is_checked) {
			jQuery.ajax({
				url: "{$analyticsEmailUrl}",
				type: 'POST',
				data: {"attribute_data":data,"email":emailIds, "export_report_range":export_report_range},
				beforeSend: function() {
				   $('#analyticsemail').text('Sending Email...');
				},
				success: function (response) 
				{
					location.reload(true);
				},
				error  : function () 
				{
					//alert("Error In saving vat");
				}
			});

			}else{
				alert("Please select fields to exports");
			}

		});
		jQuery("#selectall").change(function(){
	  	   if(jQuery(this).attr("checked")){ jQuery('input:checkbox').removeAttr('checked'); }
		   else{ jQuery('input:checkbox').attr('checked','checked'); }
		});

		jQuery("#import_order_request").click(function(){
			
			var dateSelection = jQuery("#import_report_range").val();
			if(dateSelection !=""){
				jQuery.ajax({
				url: "{$importOrderUrl}",
				type: 'POST',
				data: {"import_order_date":dateSelection},
				beforeSend: function() {
				   $('#import_order_request').text('Sending Request...');
				},
				success: function (response) 
				{
					$('#import_order_request').text('Request Completed..');
					location.reload(true);
				},
				error  : function () 
				{
					alert("Error in sending import request");
				}
			});
			} else{
				alert("Please select Date range to import orders");
			}		
		});
JS;
$this->registerJs($script);
?>
<p>
        <?= Html::a(Yii::t('app', 'Create Credit Note'), ['creditmemo/create'], ['class' => 'btn btn-success']) ?>
  </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            //'user_id',
			'order_import_date',
			'return_date',
			'credit_memo_no',
			'invoice_number',			
             'amazon_order_id:ntext',
			/* [
			  'attribute' => 'qty_return',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  "-".$model->qty_return;
			   },
			],*/

			 [
			  'attribute' => 'total_amount_refund',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->getOrderRefundAmount($model->amazon_order_id,$model->qty_return);
			   },
			],
			[
			  'attribute' => 'markeplace',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->getMarketPlace($model->amazon_order_id);
			   },
			],
			/* [
			  'attribute' => 'currency_code',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->getCurrencyCode($model->amazon_order_id);
			   },
			],*/
			/* [
			  'attribute' => 'order_type',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->getOrderType($model->amazon_order_id);
			   },
			], */
			'fulfillment_center_id',
			 [
			  'attribute' => 'creditmemo_email_sent',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  ($model->creditmemo_email_sent==1)?"YES":"NO";
			   },
			],
            

            ['class' => 'yii\grid\ActionColumn','template'=>'{view}<br/><br/>{sendpdf}', 'buttons' => [

                    //view button
                    'view' => function ($url, $model) {
                        return  Html::a('<span class="fa fa-search"></span>View', $url, 
[ 'title' => Yii::t('app', 'View'), 'class'=>'btn btn-primary btn-xs', ]) ;
                    },

					'sendpdf' => function ($url, $model) {
					return 	Html::a('<span class="fa fa-file-pdf-o" title="Send PDF"></span>PDF','#', ['class'=>'btn btn-primary', 'title' => Yii::t('yii', 'Close'), 'onclick'=>" $.ajax({
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
                        /*return  Html::a('<span class="fa fa-file-pdf-o" title="Send PDF"></span>PDF', $url, 
[ 'title' => Yii::t('app', 'SEND'), 'class'=>'btn btn-primary btn-xs', ]) ; */
                    },
                ],

                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = \yii\helpers\Url::toRoute(['user/viewcreditmemodetail', 'amazon_order_id' => $model->amazon_order_id]);
                        return $url;
                }
				if ($action === 'sendpdf') {
                        $url = \yii\helpers\Url::toRoute(['user/sendcreditmemopdf', 'amazon_order_id' => $model->amazon_order_id]);
                        return $url;
                }
				}],			
        ],
    ]); ?>
</div>
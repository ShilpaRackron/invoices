<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $searchModel app\models\AmazonOrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Amazon Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
	</div>
<div class="amazon-orders-index">
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/bootstrap.min.js"></script>
</div>
    <?php  echo $this->render('/amazon-orders/_search', ['model' => $searchModel,'invoiceModel'=>$invoiceModel,'userModel'=>$userModel]); ?>
	<div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
          <div class="card shadow">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col">
				<a href="#" class="btn btn-sm btn-outline-primary" id="export_excel" >Accountant <i class="fa fa-file-excel"></i></a> 
				  <a href="#" class="btn btn-sm btn-outline-default" id="export_excel_analytics">Analytics <i class="fa fa-file-excel"></i></a>
				  <!--<span class="small">Showing 1-20 of 9,138 items.</span>-->
                </div>
				<div class="col text-right">
		<?php 
		$accountantEmailUrl = \yii\helpers\Url::toRoute(['user/send-sales-report']);
		echo Html::a('<span class="fa fa-file-email-o" title="Send Email to Accountant"></span>Email Accountant','javascript: void(0);', ['class'=>'btn btn-sm btn-outline-primary', "id"=>"accountemail",'title' => Yii::t('yii', 'Send Email to Accountant')]);
		
		$analyticsEmailUrl = \yii\helpers\Url::toRoute(['user/send-sales-analytics-report']);
		echo Html::a('<span class="fa fa-file-email-o" title="Send Email to Analytics "></span>Email Analytics','javascript: void(0);', ['class'=>'btn btn-sm btn-outline-default', "id"=>"analyticsemail",'title' => Yii::t('yii', 'Send Email to Analytics') ]);
		?>
	</div>
	
              </div>
            </div>
	   <?php
	   $anlyticsAction =Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/exportanalytics'], true );
	   $accountAction =Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/exportaccount'], true );
	   $importOrderUrl = \yii\helpers\Url::toRoute(['user/import-order-request']);
$script = <<< JS
	jQuery("#export_excel_analytics").click(function(){
		location.href="{$anlyticsAction}";
	});
	jQuery("#export_excel").click(function(){
		location.href="{$accountAction}";
	});
	jQuery(function () {
    jQuery('.setvat').click(function () {
		jQuery("#vaturl").val(jQuery(this).attr('url'))
        jQuery('#set-buyer-vat')
            .modal('show')
            .find('#setvatModalContent')
            .load(jQuery(this).attr('value'));
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
		})
	});

JS;
$this->registerJs($script);
?>
<script>
function submitVat() {			       
		var vatId = jQuery("#buyer_vat").val();
		var vaturl = jQuery("#vaturl").val();
		if(vatId ==""){
			alert("Please enter Buyer Vat");
			return false;
			}else{
			jQuery.ajax({
				url: vaturl,
				type: 'POST',
				data: {"vatnumber":vatId},
				success: function (response) 
				{
					//alert("Vat saved successfully")
					location.reload(true);
				},
				error  : function () 
				{
					//alert("Error In saving vat");
				}
			});
			return false;
		}
	}
</script>
   <?php
    Modal::begin([
        'header'=>'<h4>Set Buyer Vat</h4>',
        'id'=>'set-buyer-vat',
        'size'=>'modal-lg'
    ]);
	$content = "<input type='text' name='buyer_vat' id='buyer_vat' placeholder ='Please enter Buyer Vat'> <input type='hidden' name='submiturl' id='vaturl'><br><br>". Html::button('Submit', [
					'class' => 'btn btn-sm btn-primary', 
					'onclick' => 'submitVat()'
					]) ;
	
    echo "<div id='setvatModalContent'>$content</div>";

    Modal::end();
?>
<p>
	 <?php 				 
			/* $ajaxUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/importinvoices'], true );
			echo Html::a('Import Invoices',['user/importinvoices'], ["id"=>"importinvoices",'class'=>'btn btn-primary', 'title' => Yii::t('yii', 'Close'), 'onclick'=>" $.ajax({
				type     :'POST',
				cache    : false,
				data:{is_ajax: true },
				url  : '".$ajaxUrl."',
				success  : function(response) {
					location. reload(true);
				},
				beforeSend: function(data){
					jQuery('#importinvoices').html('Importing in process... ');
					jQuery('#importinvoices').attr('disabled', true);
				}
				});return false;",
                ]); */
		?>
	 </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'showHeader'=>true,

    		'layout' => "{summary}\n{items}\n{pager}",

    		'options' => array('class' => 'table-responsive'),

    		'tableOptions' => array('class' => 'table align-items-center table-flush '),
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			 [
			  'attribute' => 'order_import_date',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  date("F d, Y", strtotime($model->order_import_date));
			   },
			],
            'invoice_number',
			'amazon_order_id',

			'customer_name:ntext',
			//'purchase_date',
			[
			  'attribute' => 'purchase_date',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  date("F d, Y", strtotime($model->purchase_date));
			   },
			],
			 /* [
			  'attribute' => 'latest_ship_date',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  date("F d, Y", strtotime($model->latest_ship_date));
			   },
			],*/

			'item_price',
			//'country_code',
			'buyer_vat',
			'sales_channel',
			'fulfillment_channel',
			'fulfillment_center_id',
			 [
			  'attribute' => 'invoice_email_sent',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  ($model->invoice_email_sent==1)?"YES":"NO";
			   },
			],
			
            ['class' => 'yii\grid\ActionColumn','template'=>'{view}&nbsp;{sendpdf}&nbsp;{setvat}', 'buttons' => [

                    //view button
                    'view' => function ($url, $model) {
                        return  Html::a('<span class="fas fa-eye"></span>', $url, 
[ 'title' => Yii::t('app', 'View'), 'class'=>'', ]) ;
                    },
					'sendpdf' => function ($url, $model) {
					return 	Html::a('<span class="fas fa-file-pdf" title="Send PDF"></span>','#', ['class'=>'', 'title' => Yii::t('yii', 'Send PDF'), 'onclick'=>" $.ajax({
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

					'setvat' => function ($url, $model) {
						$checkVatExist = $model->buyer_vat;
						$setVatText = (trim($checkVatExist) !="")?"EditVAT":"";
                        return  Html::a('<span class="far fa-file-alt" title="SetVAT"'.$setVatText.'"></span>'.$setVatText, '#', 
[ 'title' => Yii::t('app', $setVatText), 'class'=>'',  "url"=>$url ]) ;
                    },
                ],

                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = \yii\helpers\Url::toRoute(['user/viewinvoicedetail', 'amazon_order_id' => $model->amazon_order_id]);
                        return $url;
                }
				if ($action === 'sendpdf') {
                        $url = \yii\helpers\Url::toRoute(['user/sendpdf', 'amazon_order_id' => $model->amazon_order_id]);
                        return $url;
                }

				if ($action === 'setvat') {
                        $url = Yii::$app->urlManager->createAbsoluteUrl(['user/setbuyervat', 'amazon_order_id' => $model->amazon_order_id]);
                        return $url;
                }
				}],			
        ],
    ]); ?>
</div>

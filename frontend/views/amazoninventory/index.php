<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\AmazonInventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Amazon Inventories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amazon-inventory-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); 
	  if(count($dataProvider) > 0):
	?>

  <div class="form-group col-sm-9" id="excel_acc_xlsx"> 
   <button type="button" class="btn btn-success btn-md button-excel m-r-5" id="report_selection"><?php echo Yii::t('app', 'Export Report');?> <i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i></button>
		<!-- <button type="button" class="btn btn-success btn-md button-excel m-r-5" id="export_excel_analytics"> 
			Analytics <i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i>
		</button>
		<button type="button" class="btn btn-success btn-md button-excel m-r-5" id="export_excel" datatype="normal"> 
			Accountant <i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i>
		</button>  -->
		<?php
		 $inventoryEmailUrl = \yii\helpers\Url::toRoute(['user/send-inventory-report']);
		  echo Html::a('<span class="fa fa-file-email-o" title="Send Inventory Email"></span>Email Inventory','javascript: void(0);', ['class'=>'btn btn-primary', "id"=>"inventoryemail",'title' => Yii::t('yii', 'Send Inventory Email') ]);
		?>
	</div>
	   <?php
	   $anlyticsAction =Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/exportanalyticsinventory'], true );
	   $accountAction =Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/exportinventoryaccount'], true );
	   $exportReport = Yii::$app->getUrlManager ()->createAbsoluteUrl (['user/export-inventory-report',"report_type"=>'--REPORT--',"import_type"=>'--IMPORT--'], true);


$script = <<< JS
	jQuery("#export_excel_analytics").click(function(){
		location.href="{$anlyticsAction}";
	});
	jQuery("#export_excel").click(function(){
		location.href="{$accountAction}";
	});
	jQuery("#amazoninventorysearch-import_date").on("change",function(){		
		var currentSelection= jQuery(this).val();
		if(currentSelection=='C'){
			jQuery("#date_range").show();
		}else{
			jQuery("#date_range").hide();
		}
	});

	jQuery("#report_selection").on("click", function(){
		 var importTime = jQuery("#amazoninventorysearch-import_date").val();
		 var reportTypeValue = importTime;
		 if(importTime=='C'){
		   reportTypeValue =  jQuery("#export_date_range").val();
		 }
		 if(reportTypeValue !="") {
			 var url ="{$exportReport}";
			   url = url.replace('--REPORT--', reportTypeValue);
			   url = url.replace('--IMPORT--', importTime);
			   location.href= url;
		 } else{
			alert("Please select report type");
		 }
	});

	jQuery("#inventoryemail").on("click", function(){
		 var importTime = jQuery("#amazoninventorysearch-import_date").val();
		 var reportTypeValue = importTime;
		 if(importTime=='C'){
		   reportTypeValue =  jQuery("#export_date_range").val();
		 }
		 var inventory_email = jQuery("#inventory_email").val();
		 if(reportTypeValue !="" && inventory_email !="") {
			  jQuery.ajax({
				url: "{$inventoryEmailUrl}",
				type: 'POST',
				data: {"report_type": reportTypeValue,'import_type':importTime,'email':inventory_email},
				beforeSend: function() {
				   $('#report_selection').text('Sending Email...');
				},
				success: function (response) 
				{	 //alert(response);
					//location.reload(true);
				},
				error  : function () 
				{
					alert("Error in sending email");
				}
			});
		 } else{
			alert("Please select report type and enter email id to send report");
		 }
	});
JS;
$this->registerJs($script);
endif;
?> 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
		    'product_name:ntext',
           // 'marketplace',
            'sku',
            'fnsku',
            'asin',
            'product_condition',
            'price',
            //'mfn_listing_exists',
            'mfn_fulfillable_quantity',
            //'afn_warehouse_quantity',
            'afn_fulfillable_quantity',
            //'afn_unsellable_quantity',
            //'afn_reserved_quantity',
            'afn_total_quantity',
            'per_unit_volume',
            //'afn_inbound_working_quantity',
            //'afn_inbound_shipped_quantity',
            //'afn_inbound_receiving_quantity',
            //'import_date',
            //['class' => 'yii\grid\ActionColumn'],
			['class' => 'yii\grid\ActionColumn','template'=>'{delete}', 'buttons' => [

                    //view button
                    'view' => function ($url, $model) {
                        return  Html::a('<span class="fa fa-search"></span>Delete', $url, 
[ 'title' => Yii::t('app', 'Delete'), 'class'=>'btn btn-primary btn-xs', ]) ;
                    },
                ],

                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'delete') {
                        $url = \yii\helpers\Url::toRoute(['user/delete-inventory', 'id' => $model->id]);
                        return $url;
                } }],	
        ],
    ]); ?>
</div>

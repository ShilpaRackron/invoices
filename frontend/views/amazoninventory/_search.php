<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;


/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonInventorySearch */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
#wrapper{max-width:100%;}
.wrap{max-width:1000px; margin:50px auto;}
.form-section{ float:left; width:100%; font-size:14px; color:#5f5f5f; /*background:#fff;*/ padding:20px 10px;}
.form-section .colm-1{ float:left; width:60%;}
.form-section .colm-1 span{ float:left; font-weight:bold; font-size:14px; width:20%; margin-top:3px;}
.form-section .colm-1 p{ margin-left:20%; margin-top:5px;}
.form-section .colm-1 input{ display:inline-block; width:68%; border:1px solid #eaeaea; background:#fcfcfc; box-shadow: 0px 1px 2px 0 rgba(0, 0, 0, 0.1); border-radius: 2px; padding:5px 10px;}
.form-section .colm-1 p input{ width:85%;}
#p_scents{ width:80%; float:left;}
.remScnt13{ text-decoration:none; color:#c0bfbf; font-weight:100; font-size:14px;}
.form-section .colm-1 .plusicon{ display:inline-block; vertical-align:top; margin-top:3px; width:23px; margin-left:-14px;}
.form-section .colm-1 .plusicon img:hover{ opacity:0.7;}

.form-section .colm-2{ float:left; width:40%;}
.form-section .colm-2 .formfld2{ display:inline-block; border:1px solid #eaeaea; background:#fcfcfc; box-shadow: 0px 1px 2px 0 rgba(0, 0, 0, 0.1); border-radius: 2px; padding:5px 10px;}
.form-section .colm-2 label{ float:left; font-weight:bold; font-size:14px; margin:3px 10px;}
.form-section .colm-2 span{ margin:2px 10px;}
.form-section .row1{ float:left; width:100%; margin-top:20px;}
.form-section .row1 ul{ margin:0px; padding:0px;}
.form-section .row1 ul li{ list-style:none; float:left; width:18%; margin-right:1%; margin-top:8px;}
.form-section .row1 label{font-weight:bold; font-size:14px;}
.form-section .button-aset{ float:left; width:100%; text-align:center; margin-top:50px;}
.form-section .button-aset .export-btn{ display:inline-block; cursor:pointer; background-color:#00AAFF; border:1px solid #00a0f0; border-radius: 2px; box-shadow: 0px 1px 2px 0 rgba(0, 0, 0, 0.2); padding: 6px 22px; font-size: 14px; font-weight: 400; line-height: 1.42857143; text-align: center; white-space: nowrap; vertical-align: middle; color:#fff; text-decoration:none;}

.form-section .button-aset .export-btn:hover{ background:#00a0f0;}
</style>

<div class="amazon-inventory-search">
	<div class="form-row">
    <?php $form = ActiveForm::begin([
        'action' => ['manage-inventory'],
        'method' => 'get',
    ]); ?>   
		<div class="form-group col-md-12">
			<div class="form-group col-md-3">
				<?= $form->field($model, 'sku')->textInput(['placeholder' => "SKU"])->label(false); ?>
			</div>
			 <div class="form-group col-md-2">
				<?= $form->field($model, 'fnsku')->textInput(['placeholder' => "FNSKU"])->label(false); ?>
			</div>
			<div class="form-group col-md-2">
				<?php  echo $form->field($model, 'asin')->textInput(['placeholder' => "ASIN"])->label(false); ?>
			</div>
   
	
    <div class="form-group col-md-3">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div> 	
    <?php ActiveForm::end(); ?>
	</div>
</div>
<?php
  $reportType = [""=>'Select Reports','3'=>'Last 3 Days','7'=>'Last Week','30'=>'Last month','90'=>'Last 3 months',"180"=>'Last 6 months','C'=>'Custom Date Range']
?>

<div class="form-section col-md-12">
	<div class="col-md-4" >
	<label for="p_scnts"><span>Email to :</span></label>
	<br>
	<input type="text" id="inventory_email" name="inventory_email" value="" placeholder="Email Id" class="form-control"/>
	<p><small><?php echo Yii::t('app',"Enter email id if you want to send report");?></small></p>
	</div>
   <div class="col-md-4" >
	<label for="p_scnts"><span>Select Report</span></label>
	<br>
	<?php  echo Html::activeDropDownList($model, 'import_date',  $reportType,['class'=>'form-control formfld2']); ?></div>
   <div class="col-md-4" id='date_range' style='display: none;'>
     <label>Date Range:</label>
	  <?php		 
		 echo DateRangePicker::widget([				
				"name"=>"export_date_range",
				'class'=>"formfld2",
				'id'=>"export_date_range",
				'attribute'=>'export_date_range',
				'convertFormat'=>true,				
				'pluginOptions'=>[
					'timePicker'=>false,
					'autoclose' => true,
					'locale'=>[
						'format'=>'Y-m-d'
					]
				]
			]);
		?>
   </div>
   
   </div>
<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonInventoryAdjustmentSearch */
/* @var $form yii\widgets\ActiveForm */
$user_id = 	Yii::$app->user->id;
//$warehoueData= $model->getSellerWarehouse($user_id);

//$disposition = ArrayHelper::map($model->find()->select(['id','reason'])->distinct()->where(['and', "user_id='$user_id'"])->all(), 'reason','reason');
$disposition = $inventoryReason;
// M:  Missing Items  F: Found Items

$missingItems = ["1"=>"M > F","2"=>"M == F","3"=>"M < F"];
if($model->reason !=""){	
	$model->reason = explode(",", $model->reason);
}
?>
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
			<div class="card-body">
<div class="amazon-inventory-adjustment-search"> 
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
	
             		  
			  <div class="row">
	<div class="col-lg-4">
                      <div class="form-group " style="margin-top:25px;">
		<label>Transactions Dates </label>
		<?php		 
		 echo DateRangePicker::widget([
				'model'=>$model,
				'attribute'=>'adjusted_date',
				'convertFormat'=>true,
				'pluginOptions'=>[
					'timePicker'=>false,					
					'locale'=>[
						'format'=>'d-m-Y'
					]
				],
				'options'=>["autocomplete"=>"off"],
			]);			 
			?>
			</div>
		</div>
	<!-- <div class="form-group col-md-3"> <?= $form->field($model, 'transaction_item_id') ?></div> -->
	
	<div class="col-lg-4">
                      <div class="form-group">
                        <label class="form-control-label"></label>	 <?= $form->field($model, 'reason')->dropDownList($disposition, ['prompt'=>'','id'=>"top_search", 'multiple'=>'multiple','size'=>4])->label("Cause:"); ?></div>	</div>

	<div class="col-lg-4">
                      <div class="form-group">
                        <label class="form-control-label"></label>	 <?= $form->field($model, 'missing_check')->dropDownList($missingItems, ['prompt'=>'*','id'=>"missing_check"])->label("Missing Items:"); ?>	</div></div>
	</div>
    
			
			<div class="col-lg-12 text-center">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn btn-danger']) ?>
        <!-- <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?> -->
		</div>
		
  

	 </div>
    <?php ActiveForm::end(); ?>
</div>
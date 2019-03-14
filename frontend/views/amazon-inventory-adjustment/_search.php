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

<div class="amazon-inventory-adjustment-search"> 
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
	<div class="form-row">
		<div class="form-group col-md-6">
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
	<!-- <div class="form-group col-md-3"> <?= $form->field($model, 'transaction_item_id') ?></div> -->
	
	<div class="form-group col-md-3"> <?= $form->field($model, 'reason')->dropDownList($disposition, ['prompt'=>'','id'=>"top_search", 'multiple'=>'multiple','size'=>4])->label("Cause:"); ?>	</div>

	<div class="form-group col-md-3"> <?= $form->field($model, 'missing_check')->dropDownList($missingItems, ['prompt'=>'*','id'=>"missing_check"])->label("Missing Items:"); ?>	</div>
	
    <div class="row" >
			<div class="col-md-3 text-center" style="margin-top: 20px;">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <!-- <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?> -->
		</div>
    </div>

	 </div>
    <?php ActiveForm::end(); ?>
</div>
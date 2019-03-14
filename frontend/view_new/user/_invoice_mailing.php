<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\InvoiceMailing */
/* @var $form ActiveForm */

$monthsRange = range(1,12);
$monthsRange = array_combine($monthsRange, $monthsRange);
$days   = range(1,30);
$days = array_combine($days, $days);

//$report_start_date= date('Y-m-d', strtotime('+2 days'));
$script = <<< JS
	jQuery(function () {    
	jQuery("#invoicemailing-automatic_inventory_email").on("change",function(){		
			var is_checked = jQuery(this).is(":checked");			
			if(is_checked) {
				jQuery("#inventory_reports").show();
			}else{
				jQuery("#inventory_reports").hide();
			}

		});

	jQuery("#invoicemailing-automatic_reports_email").on("change",function(){		
			var is_checked = jQuery(this).is(":checked");			
			if(is_checked) {
				jQuery("#invoice_reports").show();
			}else{
				jQuery("#invoice_reports").hide();
			}

		});

	});

JS;
$this->registerJs($script);
?>

<div class="user-_invoice_mailing">
		<h2>Invoice mailing</h2>
    <?php $form = ActiveForm::begin(['action' => ['user/saveinvoicemailing']]); ?>

    <div class="row m-t-20">
		<div class="col-md-12 col-md-9 col-lg-4"><?= $form->field($model, 'automatic_mailing')->checkBox(); ?></div>	
		<div class="col-md-12 col-md-9 col-lg-4"><?= $form->field($model, 'automatic_reports_email')->checkBox(); ?></div>
		<div class="col-md-12 col-md-9 col-lg-4"><?= $form->field($model, 'automatic_inventory_email')->checkBox(); ?></div>
	</div>		
		
	<div id="invoice_reports" <?php if($model->automatic_reports_email !=1){ echo "style='display:none;'" ;}?>>
		<div><?= $form->field($model, 'report_months')->dropDownList($monthsRange, ['prompt'=>'*'])->label(); ?></div>
		<div><?= $form->field($model, 'report_send_day')->dropDownList($days, ['prompt'=>'*'])->label(); ?></div>

		<div><?php //$form->field($model, 'next_send_date')->label(); ?>
		
		<?= $form->field($model, 'next_send_date')->widget(\yii\jui\DatePicker::class, [
    //'language' => 'ru',
    'dateFormat' => 'yyyy-MM-dd',
]) ?>
</div>
	</div>

	<div id="inventory_reports" <?php if($model->automatic_inventory_email !=1){ echo "style='display:none;'" ;}?>>
		<div><?= $form->field($model, 'inventory_month')->dropDownList($monthsRange, ['prompt'=>'*'])->label(); ?></div>
		<div><?= $form->field($model, 'inventory_report_day')->dropDownList($days, ['prompt'=>'*'])->label(); ?></div>
		<div><?= $form->field($model, 'inventory_report_send_date')->widget(\yii\jui\DatePicker::class, [
    //'language' => 'ru',
    'dateFormat' => 'yyyy-MM-dd',
]) ?>
		</div>
	</div>

		<p>When On, invoice is automatically sent via e-mail for each imported order with purchase-date of last 7 days (and not sent ever).</p>
		<p>Timing: Every day at 09:00 AM Central Europe Time (CET). 08:00 London.</p>

		<p><h3>Text in e-mail:</h3></p>
		<?php echo Tabs::widget([
        'items' => [
			[
                'label' => 'amazon.co.uk',
                'content' => $form->field($model, 'amazon_uk')->textarea(['rows' => '6'])->label(false),'active' => true
            ],
            [
                'label' => 'amazon.de',
                'content' => $form->field($model, 'amazon_de')->textarea(['rows' => '6'])->label(false),
                
            ],            
			[
                'label' => 'amazon.es',
                'content' => $form->field($model, 'amazon_es')->textarea(['rows' => '6'])->label(false),
            ],
			[
                'label' => 'amazon.fr',
                'content' => $form->field($model, 'amazon_fr')->textarea(['rows' => '6'])->label(false),
            ],
			[
                'label' => 'amazon.it',
                'content' => $form->field($model, 'amazon_it')->textarea(['rows' => '6'])->label(false),
            ],
			
        ]]);
 ?>  

        
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- user-_invoice_mailing -->

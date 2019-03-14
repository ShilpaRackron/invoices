<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\InvoiceMailing */
/* @var $form ActiveForm */
?>
<div class="user-_invoice_mailing">
		<h2>Invoice mailing</h2>
    

        <div class="row m-t-20">
		<div class="col-md-12 col-md-9 col-lg-6">
        <?= $form->field($model, 'automatic_mailing')->checkBox(); ?></div>	
		<div class="col-md-12 col-md-9 col-lg-6">
		<?= $form->field($model, 'automatic_reports_email')->checkBox(); ?></div>
		</div>

		<p>When On, invoice is automatically sent via e-mail for each imported order with purchase-date of last 7 days (and not sent ever).</p>
		<p>Timing: Every day at 09:00 AM Central Europe Time (CET). 08:00 London.</p>

		<p><h3>Text in e-mail:</h3></p>
		<?php echo Tabs::widget([
        'items' => [
			[
                'label' => 'Amazon.co.uk',
                'content' => $form->field($model, 'amazon_uk')->textarea(['rows' => '6'])->label(false),'active' => true,
				'headerOptions'=>['style'=>"width:15%; height: 50px;"],
            ],
            [
                'label' => 'Amazon.de',
                'content' => $form->field($model, 'amazon_de')->textarea(['rows' => '6'])->label(false),
				'headerOptions'=>['style'=>"width:15%; height: 50px;"],
                
            ],            
			[
                'label' => 'Amazon.es',
                'content' => $form->field($model, 'amazon_es')->textarea(['rows' => '6'])->label(false),
				'headerOptions'=>['style'=>"width:15%; height: 50px;"],
            ],
			[
                'label' => 'Amazon.fr',
                'content' => $form->field($model, 'amazon_fr')->textarea(['rows' => '6'])->label(false),
				'headerOptions'=>['style'=>"width:15%; height: 50px;"],
            ],
			[
                'label' => 'Amazon.it',
                'content' => $form->field($model, 'amazon_it')->textarea(['rows' => '6'])->label(false),
				'headerOptions'=>['style'=>"width:15%; height: 50px;"],
            ],
			
        ]]);
 ?>      
    
</div><!-- user-_invoice_mailing -->

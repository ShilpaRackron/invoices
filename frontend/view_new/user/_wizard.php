<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\InvoiceSettings */
/* @var $form ActiveForm */
?>
<?php $form = ActiveForm::begin(['action' => ['user/savewizardinfo'],'options' => ['enctype' => 'multipart/form-data']]); ?>
<?php
$wizard_config = [
	'id' => 'stepwizard',
	'steps' => [
		1 => [
			'title' => 'My Company',
			'icon' => 'glyphicon glyphicon-cog',
			'content' => $this->render('_company_info_wizard', ['model' => $companyInfoModel,"form"=>$form]),
			'buttons' => [
				'next' => [
					'title' => 'Next', 
					'options' => [
						'class' => 'btn btn-default next-step'
					],
				 ],
			 ],
		],
		2 => [
			'title' => 'Amazon MWS',
			'icon' => 'glyphicon glyphicon-signal',
			'content' => $this->render('_amazon_mws_settings_wizard', ['model' => $amazonMwsSettingModel,"form"=>$form]),
			
		],
		 3 => [
			'title' => 'Invoice Mailing',
			'icon' => 'glyphicon glyphicon-file',
			'content' => $this->render('_invoice_mailing_wizard', ['model' => $invoiceMailingModel,"form"=>$form]),
			
		],
		4 => [
			'title' => 'Numbering',
			'icon' => 'glyphicon glyphicon-list-alt',
			'content' => $this->render('_invoice_settings_wizard', ['model' => $invoiceSettingsModel,"form"=>$form]),
			
		],
		5 => [
			'title' => 'Creditmemo Numbering',
			'icon' => 'glyphicon glyphicon-list-alt',
			'content' => $this->render('_creditmemosettingwizard', ['model' => $creditmemoSettingsModel,"form"=>$form]),
			
		],
		6 => [
			'title' => 'My Vat RNs',
			'icon' => 'glyphicon glyphicon-check',
			'content' => $this->render('_vat_rn_wizard', ['model' => $vatRnModel,"form"=>$form]),
			'buttons' => [
				'prev' => ['title' => 'Back'],
				 'save' => [
					'title' => 'Save', 
					'options' => [
						'class' => 'disabled',
						'type'=>'submit'
					],
				 ],
			 ],
		],
	/* 6 => [
			'title' => 'Import Data',
			'icon' => 'glyphicon glyphicon-check',
			'content' => $this->render('_import_amazon_data', ['model' => $vatRnModel,"form"=>$form]),
			'buttons' => [
				'prev' => ['title' => 'Back'],
				 'save' => [
					'title' => 'Save', 
					'options' => [
						'class' => 'disabled',
						'type'=>'submit'
					],
				 ],
			 ],
		],*/
	],
	'complete_content' => "", // Optional final screen
	'start_step' => 1, // Optional, start with a specific step
];
?>

<?= \drsdre\wizardwidget\WizardWidget::widget($wizard_config); ?>
 <?php ActiveForm::end(); ?>
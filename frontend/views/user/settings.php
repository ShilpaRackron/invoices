<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;

$this->title = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/bootstrap.min.js"></script>

<p><?= Html::a(Yii::t('app', 'Wizard'), ['wizard-setup'], ['class' => 'btn btn-primary', 'style'=>"float:right;"]) ?></p>
<?php echo Tabs::widget([
		'id' => 'tabs',	
        'items' => [
			[
                'label' => 'My Company',
                'content' => $this->render('_company_info', ['model' => $companyInfoModel,'invoiceModel'=>$invoiceModel,'creditMemoModel'=>$creditMemoModel]),'active' => true ,
				'linkOptions' => array('id'=>'company')
            ],
            [
                'label' => 'Amazon MWS',
                'content' => $this->render('_amazon_mws_settings', ['model' => $amazonMwsSettingModel]),
				'linkOptions' => array('id'=>'mwssetting')
            ],            
			[
                'label' => 'Invoice Mailing',
                'content' => $this->render('_invoice_mailing', ['model' => $invoiceMailingModel]),
				'linkOptions' => array('id'=>'invoicemailing')
            ],
			[
                'label' => 'Numbering',
                'content' => $this->render('_invoice_settings', ['model' => $invoiceSettingsModel]),
				'linkOptions' => array('id'=>'numbering')
            ],
			[
                'label' => 'CreditMemo Numbering',
                'content' => $this->render('_creditmemosetting', ['model' => $creditmemoSettingsModel]),
				'linkOptions' => array('id'=>'creditmemonumber')
            ],
			[
                'label' => 'My Vat RNs',
                'content' => $this->render('_vat_rn', ['model' => $vatRnModel]),
				'linkOptions' => array('id'=>'vatrn')
            ],
			
        ],				
        ]);

	$tabUrl =  Yii::$app->getUrlManager()->createAbsoluteUrl ( ['user/savetab'], true );
	$currentTab = (isset(Yii::$app->session["currentTab"]))?Yii::$app->session["currentTab"]:'company';
 ?>
 
 <script>
 $(document).ready(function(){
	
	 $("#tabs li a").click(function(){
	   	var tabid = $(this).attr("id");		
		savetab(tabid);
	  });
	  gototab('<?php echo $currentTab;?>');	 
 });

 function savetab(curTabId){
 	 if(curTabId){
	 	  $.ajax({
				type: 'POST',
				url: '<?php echo $tabUrl;?>',
				data: { curTab: curTabId }
			})
			.done(function( msg ) {
				activeTab = msg;
			});	 
	 } 
 }

 function gototab(activeTab) {	
	$('#'+activeTab).tab('show');
 }
 </script>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\VatRn;

/* @var $this yii\web\View */
/* @var $model frontend\models\VatRn */
/* @var $form ActiveForm */
$listData = Yii::$app->params['central_bank'];
?>
<div class="user-_vat_rn">
  	
	<h3>My VAT Reg. Numbers</h3>
    <?php $form = ActiveForm::begin(['action' => ['user/savevatrn']]); ?>
	<div class="row m-t-20">
	<div class="col-md-12 col-md-9 col-lg-6">
	<div class="table-responsive-md">
	 <table class="table table-sm table-borderless" id="vats">
<thead class="thead-default">
<tr>
<th style="width: 90px;">Country</th>
<th style="width: 80px;">Rate (%)</th>
<th style="width: 150px;">Your VAT No.</th>
<th style="width: 120px;">Central Bank</th>
<th style="width: 10px;"></th>
</tr>
</thead>
<tbody>
		<?php 
		 $totalRecordsData = VatRn::find()->orderBy('id DESC')->one();
		 $totalRecords=(int)$totalRecordsData->id;
		$countryData ="";
		
		foreach($listData as $key=>$name){ 
			$countryData .="<option value=\'".$key."\'>".$name."</option>";
		}
		if(isset($model[0]) && !empty($model[0])){
			$i=0;
		   foreach($model as $key=>$data){
			    $county =($i==0)?"default":$data->country;
				$rowId =($i==0)?"vtrndatarow":"rowid_".$i;
		   		?>				
				<tr id='<?php echo $rowId;?>'>
				<td><input type="text" name="VatRn[<?php echo $data->id;?>][country]" value="<?php echo $county;?>" <?php if($i ==0){ echo "disabled='true'"; }?> style="width: 90px;" maxlength='2'><span><small>(i.e IT,UK)</small></span></td>
				<td><input type="text" style="width: 80px;" name="VatRn[<?php echo $data->id;?>][rate_percentage]" value="<?php echo $data->rate_percentage;?>"> </td>
				<td ><input type="text" style="width: 150px;" name="VatRn[<?php echo $data->id;?>][vat_no]" value="<?php echo $data->vat_no;?>"></td>
				<td>
				<select name="VatRn[<?php echo $data->id;?>][central_bank]" style="width: 120px;">
					<?php 
					foreach($listData as $key=>$name){
						$selected ="";
						if($key==$data->central_bank){
						$selected ="selected='selected'";
						}
					?>
					<option value='<?php echo $key;?>' <?php echo $selected;?>><?php echo $name;?></option>
					<?php } ?>
					</select>
					</td>
					<?php if($i >0){ ?>
							<td class="remove" style="width: 10px;" id="remove_<?php echo $i;?>" onclick="javascript: removeblockdata('<?php echo $data->id;?>','<?php echo $rowId;?>')"><span class="glyphicon glyphicon-trash"></span></td>
					<?php } else{?>
							<td style="width: 10px;"></td>
					<?php }?>
				</tr>
				
				<?php
						$i++;
					}
				}else{  ?>
				<tr id="vtrndatarow">
				<td ><input type="text" name="VatRn[0][country]" value="default" disabled='true' style="width: 90px;"></td>
				<td><input type="text" name="VatRn[0][rate_percentage]" value="" style="width: 80px;"> </td>
				<td><input type="text" name="VatRn[0][vat_no]" value="" style="width: 150px;"></td>
				<td> <select name="VatRn[0][central_bank]" style="width: 120px;">
				 <?php 
					foreach($listData as $key=>$name){
						
					?>
					<option value='<?php echo $key;?>'><?php echo $name;?></option>
					<?php } ?>
					</select>
					</td>
					<td style="width: 10px;"></td>
				</tr>
				<?php } ?>
				<tr id="appendmore"><td colspan='5'></td></tr>
			 </tbody>
		 </table>
		 </div></div>
		 </div>
		<input type="hidden" value="<?php echo $totalRecords;?>" id="totalrecords">    		
		 
			<div class="row m-t-20" id="addmore">
				<div class="col-12">
					<div class="col-md-6"><a id="vat_add_more" class="btn btn-primary">Add new</a></div>			
			     </div>
		   </div>
		   <div class="row m-t-20" >
			<div class="col-12">
				 <p>If you are not VAT registered, set rate to 0 for default country.</p>
				<p>If you are VAT registered in multiple EU countries, add rows and insert country code, VAT rate and your VAT reg. number. 
				Example: IT, 22%, IT00395600398. If you have some products with reduced VAT, please set up them in Menu > SKUs.
				Invoices are calculated in real-time, so you can change these settings anytime.</p>
			</div>
			</div>

		 <div class="row m-t-20" >
			<div class="col-12">
				<div class="form-group col-md-6">
				<?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
			  </div>
			</div>
		 </div>	

		
    <?php ActiveForm::end(); ?>
    	</div>
	

<p>Why Central Bank?</p>
	<p>For example, your company is registered in UK and you pay VAT in GBP. But you sell in Euros as well. Therefore this machine connects to Bank of England (HMRC rates monthly) to check for foreign currency exchange rates and writes currency rate to every invoice you made in foreign currency (EUR). You can use it for your bookkeeping and you will see in a dashboard how much sterling is your payable VAT to UK government - summarized invoices in GBP + invoices in EUR or other currencies.<p>
	<p>Example 2: You are U.S. company and you have registered for EU VAT in Ireland. Thus you pay VAT in Euros, but you sell also in GBP - in United Kingdom. You need to know how much VAT in Euros is from your GBP invoices. Machine helps you to find out, it connects to European Central Bank Frankfurt every day to get foreign currencies rates.</p>
	<p>Is your central bank missing? Please contact us to add it. (Sweden, Denmark, Czech, Poland, Hungary, Croatia etc.)</p>
	</div>
</div>
<?php
$script = <<< JS
	jQuery("#vat_add_more").click(function(){
		
		// var formdata = jQuery('#vtrndatarow').clone();
		 var totatRecords = jQuery("#totalrecords").val();
		 totatRecords = parseInt(totatRecords);
		 totatRecords = totatRecords+1;
		 addnewentry(totatRecords);
		 jQuery("#totalrecords").val(totatRecords);
	});		
	function addnewentry(counter) {	

		var divData = '<tr id="rowid_'+counter+'"><td><input type="text" style="width: 90px;" name="VatRn['+counter+'][country]" value="" maxlength="2"></td><td><input type="text" style="width: 80px;" name="VatRn['+counter+'][rate_percentage]" value=""> </td><td ><input type="text" style="width: 150px;" name="VatRn['+counter+'][vat_no]" value=""></td><td><select name="VatRn['+counter+'][central_bank]" style="width: 120px;">{$countryData}</select></td><td class="remove" id="remove_'+counter+'" onclick="javascript: removeblock('+counter+');" style="width: 10px;"><span class="glyphicon glyphicon-trash"></span></td></tr>';
		jQuery('#appendmore').before(divData);
	}
JS;
$this->registerJs($script);

$ajaxUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/removevtrn'], true );
?>
<script type="text/javascript">
	function removeblock(blockId){
		 jQuery("#rowid_"+blockId).remove();
		 var totatRecords = jQuery("#totalrecords").val();
		 totatRecords = parseInt(totatRecords);
		 totatRecords = totatRecords-1;
		 jQuery("#totalrecords").val(totatRecords);
	}
	function removeblockdata(rowid, rowno){
		var confim  = window.confirm("Are your sure to delete it?");
		if(confim) {
		 $.ajax({
				url: '<?php echo $ajaxUrl;?>',
				type: 'POST',
				data: {"row_id": rowid,'is_ajax':1},
				success: function (response) 
				{  
					jQuery("#"+rowno).remove();
				},
				error  : function () 
				{
					alert("Error in deleting data");
				}
            });
		}
	}
</script>

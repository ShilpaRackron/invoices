<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use frontend\models\AmazonOrders;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $model app\models\AmazonOrdersSearch */
/* @var $form yii\widgets\ActiveForm */


$countries = array("AF" => "Afghanistan",
"AX" => "Aland Islands",
"AL" => "Albania",
"DZ" => "Algeria",
"AS" => "American Samoa",
"AD" => "Andorra",
"AO" => "Angola",
"AI" => "Anguilla",
"AQ" => "Antarctica",
"AG" => "Antigua and Barbuda",
"AR" => "Argentina",
"AM" => "Armenia",
"AW" => "Aruba",
"AU" => "Australia",
"AT" => "Austria",
"AZ" => "Azerbaijan",
"BS" => "Bahamas",
"BH" => "Bahrain",
"BD" => "Bangladesh",
"BB" => "Barbados",
"BY" => "Belarus",
"BE" => "Belgium",
"BZ" => "Belize",
"BJ" => "Benin",
"BM" => "Bermuda",
"BT" => "Bhutan",
"BO" => "Bolivia",
"BA" => "Bosnia and Herzegovina",
"BW" => "Botswana",
"BV" => "Bouvet Island",
"BR" => "Brazil",
"IO" => "British Indian Ocean Territory",
"BN" => "Brunei Darussalam",
"BG" => "Bulgaria",
"BF" => "Burkina Faso",
"BI" => "Burundi",
"KH" => "Cambodia",
"CM" => "Cameroon",
"CA" => "Canada",
"CV" => "Cape Verde",
"KY" => "Cayman Islands",
"CF" => "Central African Republic",
"TD" => "Chad",
"CL" => "Chile",
"CN" => "China",
"CX" => "Christmas Island",
"CC" => "Cocos (Keeling) Islands",
"CO" => "Colombia",
"KM" => "Comoros",
"CG" => "Congo",
"CD" => "Congo, The Democratic Republic of The",
"CK" => "Cook Islands",
"CR" => "Costa Rica",
"CI" => "Cote D'ivoire",
"HR" => "Croatia",
"CU" => "Cuba",
"CY" => "Cyprus",
"CZ" => "Czech Republic",
"DK" => "Denmark",
"DJ" => "Djibouti",
"DM" => "Dominica",
"DO" => "Dominican Republic",
"EC" => "Ecuador",
"EG" => "Egypt",
"SV" => "El Salvador",
"GQ" => "Equatorial Guinea",
"ER" => "Eritrea",
"EE" => "Estonia",
"ET" => "Ethiopia",
"FK" => "Falkland Islands (Malvinas)",
"FO" => "Faroe Islands",
"FJ" => "Fiji",
"FI" => "Finland",
"FR" => "France",
"GF" => "French Guiana",
"PF" => "French Polynesia",
"TF" => "French Southern Territories",
"GA" => "Gabon",
"GM" => "Gambia",
"GE" => "Georgia",
"DE" => "Germany",
"GH" => "Ghana",
"GI" => "Gibraltar",
"GR" => "Greece",
"GL" => "Greenland",
"GD" => "Grenada",
"GP" => "Guadeloupe",
"GU" => "Guam",
"GT" => "Guatemala",
"GG" => "Guernsey",
"GN" => "Guinea",
"GW" => "Guinea-bissau",
"GY" => "Guyana",
"HT" => "Haiti",
"HM" => "Heard Island and Mcdonald Islands",
"VA" => "Holy See (Vatican City State)",
"HN" => "Honduras",
"HK" => "Hong Kong",
"HU" => "Hungary",
"IS" => "Iceland",
"IN" => "India",
"ID" => "Indonesia",
"IR" => "Iran, Islamic Republic of",
"IQ" => "Iraq",
"IE" => "Ireland",
"IM" => "Isle of Man",
"IL" => "Israel",
"IT" => "Italy",
"JM" => "Jamaica",
"JP" => "Japan",
"JE" => "Jersey",
"JO" => "Jordan",
"KZ" => "Kazakhstan",
"KE" => "Kenya",
"KI" => "Kiribati",
"KP" => "Korea, Democratic People's Republic of",
"KR" => "Korea, Republic of",
"KW" => "Kuwait",
"KG" => "Kyrgyzstan",
"LA" => "Lao People's Democratic Republic",
"LV" => "Latvia",
"LB" => "Lebanon",
"LS" => "Lesotho",
"LR" => "Liberia",
"LY" => "Libyan Arab Jamahiriya",
"LI" => "Liechtenstein",
"LT" => "Lithuania",
"LU" => "Luxembourg",
"MO" => "Macao",
"MK" => "Macedonia, The Former Yugoslav Republic of",
"MG" => "Madagascar",
"MW" => "Malawi",
"MY" => "Malaysia",
"MV" => "Maldives",
"ML" => "Mali",
"MT" => "Malta",
"MH" => "Marshall Islands",
"MQ" => "Martinique",
"MR" => "Mauritania",
"MU" => "Mauritius",
"YT" => "Mayotte",
"MX" => "Mexico",
"FM" => "Micronesia, Federated States of",
"MD" => "Moldova, Republic of",
"MC" => "Monaco",
"MN" => "Mongolia",
"ME" => "Montenegro",
"MS" => "Montserrat",
"MA" => "Morocco",
"MZ" => "Mozambique",
"MM" => "Myanmar",
"NA" => "Namibia",
"NR" => "Nauru",
"NP" => "Nepal",
"NL" => "Netherlands",
"AN" => "Netherlands Antilles",
"NC" => "New Caledonia",
"NZ" => "New Zealand",
"NI" => "Nicaragua",
"NE" => "Niger",
"NG" => "Nigeria",
"NU" => "Niue",
"NF" => "Norfolk Island",
"MP" => "Northern Mariana Islands",
"NO" => "Norway",
"OM" => "Oman",
"PK" => "Pakistan",
"PW" => "Palau",
"PS" => "Palestinian Territory, Occupied",
"PA" => "Panama",
"PG" => "Papua New Guinea",
"PY" => "Paraguay",
"PE" => "Peru",
"PH" => "Philippines",
"PN" => "Pitcairn",
"PL" => "Poland",
"PT" => "Portugal",
"PR" => "Puerto Rico",
"QA" => "Qatar",
"RE" => "Reunion",
"RO" => "Romania",
"RU" => "Russian Federation",
"RW" => "Rwanda",
"SH" => "Saint Helena",
"KN" => "Saint Kitts and Nevis",
"LC" => "Saint Lucia",
"PM" => "Saint Pierre and Miquelon",
"VC" => "Saint Vincent and The Grenadines",
"WS" => "Samoa",
"SM" => "San Marino",
"ST" => "Sao Tome and Principe",
"SA" => "Saudi Arabia",
"SN" => "Senegal",
"RS" => "Serbia",
"SC" => "Seychelles",
"SL" => "Sierra Leone",
"SG" => "Singapore",
"SK" => "Slovakia",
"SI" => "Slovenia",
"SB" => "Solomon Islands",
"SO" => "Somalia",
"ZA" => "South Africa",
"GS" => "South Georgia and The South Sandwich Islands",
"ES" => "Spain",
"LK" => "Sri Lanka",
"SD" => "Sudan",
"SR" => "Suriname",
"SJ" => "Svalbard and Jan Mayen",
"SZ" => "Swaziland",
"SE" => "Sweden",
"CH" => "Switzerland",
"SY" => "Syrian Arab Republic",
"TW" => "Taiwan, Province of China",
"TJ" => "Tajikistan",
"TZ" => "Tanzania, United Republic of",
"TH" => "Thailand",
"TL" => "Timor-leste",
"TG" => "Togo",
"TK" => "Tokelau",
"TO" => "Tonga",
"TT" => "Trinidad and Tobago",
"TN" => "Tunisia",
"TR" => "Turkey",
"TM" => "Turkmenistan",
"TC" => "Turks and Caicos Islands",
"TV" => "Tuvalu",
"UG" => "Uganda",
"UA" => "Ukraine",
"AE" => "United Arab Emirates",
"GB" => "United Kingdom",
"US" => "United States",
"UM" => "United States Minor Outlying Islands",
"UY" => "Uruguay",
"UZ" => "Uzbekistan",
"VU" => "Vanuatu",
"VE" => "Venezuela",
"VN" => "Viet Nam",
"VG" => "Virgin Islands, British",
"VI" => "Virgin Islands, U.S.",
"WF" => "Wallis and Futuna",
"EH" => "Western Sahara",
"YE" => "Yemen",
"ZM" => "Zambia",
"ZW" => "Zimbabwe");
$month =[];
for ($i = 0; $i < 12; $i++) {
	$time = strtotime(sprintf('%d months', $i));   
	$label = date('F', $time);   
	$value = date('n', $time);
	$month[$value] =$label;       
}
ksort($month);
$user_id = 	Yii::$app->user->id;

$warehoueData = ArrayHelper::map($model->find()->select(['id','fulfillment_center_id'])->distinct()->where(['and', "user_id='$user_id'"])->all(), 'fulfillment_center_id','fulfillment_center_id');

$yearsArray = $model->getYearsList($user_id);
$amazonOrderModel = new AmazonOrders();
$sales_channel = ArrayHelper::map($amazonOrderModel->find()->select(['id','sales_channel'])->distinct()->where(['and', "user_id='$user_id'"])->all(), 'sales_channel','sales_channel');
$saveFieldsUrl = \yii\helpers\Url::toRoute(['user/save-creditmemo-fields']);
?>
<?php 
  $script = <<< JS
	jQuery(".reset").click(function() { 		
		jQuery("#creditmemosearch-return_date, #creditmemosearch-year,#creditmemosearch-month, #creditmemosearch-fulfillment_center_id, #creditmemosearch-sales_channel, #creditmemosearch-invoice_number,#creditmemosearch-credit_memo_no, #creditmemosearch-amazon_order_id").val("");		
	});
	jQuery(document).ready(function(){
		jQuery("#saveinvoicefields").click(function(){
		    var data = { 'attributes' : []};
			var emailIds	= jQuery("#emails").val();
			var is_checked	= false;
				
			$(".attributes:checked").each(function() {				
			  data['attributes'].push($(this).val());
			  is_checked = true;
			});
			if(is_checked) {			
			jQuery.ajax({
				url: "{$saveFieldsUrl}",
				type: 'POST',
				data: {"attribute_data":data},
				beforeSend: function() {
				   $('#saveinvoicefields').text('Saving...');
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
				alert("Please select fields to save");
			}

		});
	});
JS;
$this->registerJs($script);
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


<div class="credit-memo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['credit-memo'],
		'id'=>'creditmemosearch',
        'method' => 'get',
    ]); ?>

	<div class="form-row">
	<div class="form-group col-md-12">
		<label>Return date Range</label>
		<?php		 
		 echo DateRangePicker::widget([
				'model'=>$model,
				'attribute'=>'return_date',
				'convertFormat'=>true,
				'pluginOptions'=>[
					'timePicker'=>false,
					//'timePickerIncrement'=>30,
					'locale'=>[
						'format'=>'Y-m-d'
					]
				]
			]);			 
			?></div>
		<div class="form-group col-md-3"> <?= $form->field($model, 'year')->dropDownList($yearsArray, ['prompt'=>'*'])->label(); ?>	</div>
		<div class="form-group col-md-3"> <?= $form->field($model, 'month')->dropDownList($month, ['prompt'=>'*'])->label(); ?>	</div>
		<div class="form-group col-md-4"><?= $form->field($model, 'fulfillment_center_id')->dropDownList($warehoueData, ['prompt'=>'*'])->label(); ?></div>
		<!--<div class="form-group col-md-3"><?= $form->field($model, 'markeplace')->dropDownList($sales_channel, ['prompt'=>'*'])->label(); ?></div> -->
		<div class="form-group col-md-3"><?= $form->field($model, 'invoice_number')->textInput(['placeholder' => "Invoice No"])->label(false); ?></div>
		<div class="form-group col-md-3"><?= $form->field($model, 'credit_memo_no')->textInput(['placeholder' => "Credit memo No"])->label(false); ?></div>
		<div class="form-group col-md-3"><?= $form->field($model, 'amazon_order_id')->textInput(['placeholder' => "Amazon Order Id"])->label(false); ?></div>		
		<div class="row" style="margin-top: 20px;">
			<div class="col-md-3 text-center">
				<?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
				<?= Html::Button(Yii::t('app', 'Reset'), ['class' => 'btn btn-default reset']) ?>
			</div>
		</div>
	</div>
    <?php ActiveForm::end(); ?>

</div>


<?php
  $attributes = ['credit_memo_no','invoice_number','order_import_date','return_date','seller_sku','product_sku','product_name','seller_order_id','qty_return','amazon_order_id','license_plate_number','order_adjustment_item_id','fulfillment_center_id','reason','detailed_disposition','status'];

  //$checkedAttributes = Yii::$app->session->get('creditnote_columns');
  $checkedAttributes = (!empty($userModel->selected_creditnote_fields) && $userModel->selected_creditnote_fields !=NULL)?explode(",",$userModel->selected_creditnote_fields):[];
?>

<div class="form-section col-md-12">
   <div class="col-md-4" >
	<label for="p_scnts"><span>Email Send to :</span></label>
	<br>
	<input type="text" id="emails" name="emails" value="" placeholder="Email Id" class="form-control"/></div>
   <div class="col-md-4">
     <label>Date:</label>
	  <?php		 
		 echo DateRangePicker::widget([
				//'model'=>$model,
				"name"=>"export_report_range",
				'class'=>"formfld2",
				'id'=>"export_report_range",
				'attribute'=>'export_report_range',
				'convertFormat'=>true,
				//'startAttribute'=>'datetime_min',
				//'endAttribute'=>'datetime_max',
				'pluginOptions'=>[
					'timePicker'=>false,
					'autoclose' => true,
					//'timePickerIncrement'=>30,
					'locale'=>[
						'format'=>'Y-m-d'
					]
				]
			]);
		?>		
   </div> 
   <div class="column_selection col-md-4">
   <label>&nbsp;</label>
   <button type="button" class="btn btn-success btn-md button-plus form-control" id="column_selection">Select Columns</button></div>
   <div class="row1 col-md-12" style="display:none;">   
   <p><input type="checkbox" class="check" id="selectall" checked="checked"/>Select All</p>
    <ul>
	<?php
	$isSelectedFields = (is_array($checkedAttributes) && !empty($checkedAttributes) )?true:false;

	foreach($attributes as $key=>$value){  
		$selected =(is_array($checkedAttributes)&& in_array($value, $checkedAttributes))?"checked='checked'":"";
		?>
		<li><input name="attributes[<?php echo $value;?>]" type="checkbox" value="<?php echo $value;?>" <?php if(!$isSelectedFields){ echo "checked='checked'"; } else{ echo $selected; }?> class="attributes"> <?php echo $invoiceModel->getAttributeLabel($value);?></li>
	   <?php
	}?>
	</ul>
	<div style="clear:both;"></div>
	<div class="savebutton"><?php
		echo Html::a('<span class="fa fa-file-email-o" title="Save Fields"></span>Save Fields','javascript: void(0);', ['class'=>'btn btn-primary', "id"=>"saveinvoicefields",'title' => Yii::t('yii', 'Save Fields')]);
	?></div>
   </div>
   </div>


   <div class="form-section col-md-12">
    <div class="col-md-8">
     <label>Select Import Date:</label>
	  <?php	
	  $startDate= date('Y-m-d h:i:s');
	  $endDate = '+1m';
		 echo DateRangePicker::widget([
				"name"=>"import_report_range",
				'class'=>"formfld2",
				'id'=>"import_report_range",
				'attribute'=>'import_report_range',
				'convertFormat'=>true,				
				'pluginOptions'=>[
					'timePicker'=>false,
					'autoclose' => true,
					'locale'=>[
						'format'=>'Y-m-d',
					]
				]
			]);
		?>
		<p>Select Maximum one month range.</p>
   </div>
   <div class="importorder_selection col-md-4">
   <label>&nbsp;</label>
   <button type="button" class="btn btn-success btn-md button-plus form-control" id="import_order_request">Request Creditnotes </button></div>  
   </div>

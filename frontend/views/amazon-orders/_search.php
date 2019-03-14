<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
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
$month =["1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December"];

 
$user_id = 	Yii::$app->user->id;
//$warehoueData= $model->getSellerWarehouse($user_id);

$warehoueData = ArrayHelper::map($model->find()->select(['id','fulfillment_center_id'])->distinct()->where(['and', "user_id='$user_id'"])->all(), 'fulfillment_center_id','fulfillment_center_id');

$sales_channel = ArrayHelper::map($model->find()->select(['id','sales_channel'])->distinct()->where(['and', "user_id='$user_id'"])->all(), 'sales_channel','sales_channel');

$yearsArray = $model->getYearsList($user_id);
$saveFieldsUrl = \yii\helpers\Url::toRoute(['user/save-invoice-fields']);
?>

<?php 
  $script = <<< JS
	jQuery(".reset").click(function() { 
		
		jQuery("#amazonorderssearch-purchase_date, #amazonorderssearch-year,#amazonorderssearch-month,#amazonorderssearch-country_code, #amazonorderssearch-fulfillment_center_id, #amazonorderssearch-sales_channel, #amazonorderssearch-invoice_number,#amazonorderssearch-buyer_name, #amazonorderssearch-amazon_order_id").val("");		
	});
	jQuery(document).ready(function(){
		 //jQuery('#import_report_range').daterangepicker();
		jQuery('#import_report_range').on('apply.daterangepicker', function(ev, picker) {
				//var startDate = picker.startDate.format('YYYY-MM-DD');
				//var endDate   = picker.endDate.format('YYYY-MM-DD');
				//jQuery('#import_report_range').data('daterangepicker').setStartDate('03/01/2014');
				//jQuery('#import_report_range').data('daterangepicker').setEndDate('03/31/2014');
		});

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

<div class="amazon-orders-search">
    <?php $form = ActiveForm::begin([
        'action' => ['invoices'],
		'id'=>'invoicesearch',
        'method' => 'get',
    ]); ?>
	<div class="form-row">
		<div class="form-group col-md-12">
		<label>Purchase Date Range</label>
		<?php		 
		 echo DateRangePicker::widget([
				'model'=>$model,
				'attribute'=>'purchase_date',
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
		<div class="form-group col-md-2"> <?= $form->field($model, 'year')->dropDownList($yearsArray, ['prompt'=>'*'])->label(); ?>	</div>
		<div class="form-group col-md-2"> <?= $form->field($model, 'month')->dropDownList($month, ['prompt'=>'*'])->label(); ?>	</div>
		<div class="form-group col-md-2"> <?= $form->field($model, 'country_code')->dropDownList($countries, ['prompt'=>'*'])->label(); ?>	</div> 		
		<div class="form-group col-md-2"><?= $form->field($model, 'fulfillment_center_id')->dropDownList($warehoueData, ['prompt'=>'*'])->label(); ?></div>
		<div class="form-group col-md-2"><?= $form->field($model, 'sales_channel')->dropDownList($sales_channel, ['prompt'=>'*'])->label(); ?></div>
		<div class="form-group col-md-3"><?= $form->field($model, 'invoice_number')->textInput(['placeholder' => "Invoice No"])->label(false); ?></div>
		<div class="form-group col-md-3"><?= $form->field($model, 'buyer_name')->textInput(['placeholder' => "Customer"])->label(false); ?></div>
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
  $attributes = ['order_import_date','invoice_number','protocol_invoice_number','purchase_date','buyer_name','buyer_email','buyer_vat','number_of_items_shipped','item_price','latest_ship_date','amazon_order_id','product_sku','product_name','last_update_date','ship_service_level','order_status','sales_channel','latest_delivery_date','marketplace_id','fulfillment_channel','payment_method','customer_name','address_2','address_type','city','state_or_region','country_code','postal_code','phone','fulfillment_center_id','shipment_id','ship_address_1','ship_address_2','ship_address_3','ship_city','ship_state','ship_postal_code','ship_country','ship_phone_number','ship_promotion_discount','tracking_number','carrier','shipping_price','shipping_tax','gift_wrap_price','gift_wrap_tax'];
  $checkedAttributes = (!empty($userModel->selected_invoice_fields) && $userModel->selected_invoice_fields !=NULL)?explode(",",$userModel->selected_invoice_fields):[]; //Yii::$app->session->get('selected_columns');
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
   <button type="button" class="btn btn-success btn-md button-plus form-control" id="import_order_request">Import Invoice Request</button></div>  
   </div>


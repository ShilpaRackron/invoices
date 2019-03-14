<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model frontend\models\CompanyInfo */
/* @var $form ActiveForm */

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

?>
<?php
$script = <<< JS

	jQuery("#selectall").change(function(){
	  	   if(jQuery(this).attr("checked")){
			   jQuery('#companyinfo-invoice_select_fields input:checkbox').removeAttr('checked'); 
			   jQuery(this).removeAttr("checked");
			}
		   else{
			   jQuery('#companyinfo-invoice_select_fields input:checkbox').attr('checked','checked'); 
			   jQuery(this).attr("checked",'checked');
			   }

	});

	jQuery("#selectallcreditnote").change(function(){
	  	   if(jQuery(this).attr("checked")){
			   jQuery('#companyinfo-creditmemo_selected_field input:checkbox').removeAttr('checked'); 
			   jQuery(this).removeAttr("checked");
			}
		   else{
			   jQuery('#companyinfo-creditmemo_selected_field input:checkbox').attr('checked','checked'); 
			   jQuery(this).attr("checked",'checked');
			   }

	});

JS;
$this->registerJs($script);
?>
<?php
  $invoiceAttributes = ['order_import_date','invoice_number','protocol_invoice_number','purchase_date','buyer_name','buyer_email','buyer_vat','number_of_items_shipped','item_price','latest_ship_date','amazon_order_id','product_sku','product_name','last_update_date','ship_service_level','order_status','sales_channel','latest_delivery_date','marketplace_id','fulfillment_channel','payment_method','customer_name','address_2','address_type','city','state_or_region','country_code','postal_code','phone','fulfillment_center_id','shipment_id','ship_address_1','ship_address_2','ship_address_3','ship_city','ship_state','ship_postal_code','ship_country','ship_phone_number','ship_promotion_discount','tracking_number','carrier','shipping_price','shipping_tax','gift_wrap_price','gift_wrap_tax'];
  //$invoiceCheckedAttributes = Yii::$app->session->get('selected_columns');
  $invoiceFieldArray =[];
  
  foreach($invoiceAttributes as $key=>$value){
  	$invoiceFieldArray[$value] =$invoiceModel->getAttributeLabel($value); 
  }
  

   $creditnoteAttributes = ['credit_memo_no','invoice_number','order_import_date','return_date','seller_sku','product_sku','product_name','seller_order_id','qty_return','amazon_order_id','license_plate_number','order_adjustment_item_id','fulfillment_center_id','reason','detailed_disposition','status'];
   $creditnoteArray =[];

   foreach($creditnoteAttributes as $key=>$value){
   	   $creditnoteArray[$value] =$creditMemoModel->getAttributeLabel($value);   
   }

  //$creditnoteCheckedAttributes = Yii::$app->session->get('creditnote_columns');
$model->invoice_select_fields =($model->invoice_select_fields !="" && $model->invoice_select_fields !=NULL)?explode(",",$model->invoice_select_fields):$invoiceAttributes;

$model->creditmemo_selected_field =($model->creditmemo_selected_field !="")?explode(",",$model->creditmemo_selected_field):$creditnoteAttributes;
?>

<div class="user-_company_info">
	
	<h1>My Company</h1>
<h3>Your company data on invoices, except VAT No.</h3>
<hr class="my-4" />
    <?php $form = ActiveForm::begin(['action' => ['user/savecompanyinfo'],'options' => ['enctype' => 'multipart/form-data']]); ?>
		
		<?= $form->field($model, 'company_name')->textInput()->label(); ?>
		<?= $form->field($model, 'company_name_in_bul')->textInput()->label(); ?>		
		<?= $form->field($model, 'accountant_email')->textarea()->label(); ?>
		<?= $form->field($model, 'analytics_email')->textarea()->label(); ?>
		<?= $form->field($model, 'inventory_report_email')->textarea()->label(); ?>

		<div style="clear:both;">
			<p style="clear:both;"><input type="checkbox" class="check" id="selectall" checked="checked"/>Select/Unselect All Invoice Fields</p>		
		<?= $form->field($model, "invoice_select_fields")->checkboxList($invoiceFieldArray, [
            'item' =>function ($index, $label, $name, $checked, $value) {
                    return Html::checkbox($name, $checked, [
						'value' => $value,
                        'label' => '<label for="' . $label . '">' . $label . '</label>',
                        'labelOptions' => [
                            'class' => 'ckbox ckbox-primary col-md-4',
                        ],
                    ]);
                }, 'separator'=>false,'template'=>'<div class="item">{input}{label}</div>'])->label(); ?>

			 
			  
			</div>

		<div style="clear:both;">
		<p style="clear:both;"><input type="checkbox" class="check" id="selectallcreditnote" checked="checked"/>Select/Unselect All Credit Note Fields</p>
		<?= $form->field($model, "creditmemo_selected_field")->checkboxList($creditnoteArray, [
            'item' =>function ($index, $label, $name, $checked, $value) {
                    return Html::checkbox($name, $checked, [
						'value' => $value,
                        'label' => '<label for="' . $label . '">' . $label . '</label>',
                        'labelOptions' => [
                            'class' => 'ckbox ckbox-primary col-md-4',
                        ],
                    ]);
                }, 'separator'=>false,'template'=>'<div class="item">{input}{label}</div>'])->label(); ?>
		</div>

		<?= $form->field($model, 'bank_name')->textInput()->label(); ?>
		<?= $form->field($model, 'bank_ibn')->textInput()->label(); ?>
		<?= $form->field($model, 'bank_bic_swift')->textInput()->label(); ?>
		<?= $form->field($model, 'vat_article')->textarea()->label(); ?>
		<?= $form->field($model, 'vat_article_bul')->textarea()->label(); ?>
		<?= $form->field($model, 'vat_not_apply')->textarea()->label(); ?>
		<?= $form->field($model, 'vat_not_apply_bul')->textarea()->label(); ?>
        <?= $form->field($model, 'country')->dropDownList($countries, ['prompt'=>'Select Country'])->label(); ?>	
        <?= $form->field($model, 'company_header')->textarea(['rows' => '6'])->label(false); ?>
        <?= $form->field($model, 'company_logo')->fileInput() ?>
		<?php if($model->company_logo !=""){
		 		  echo Html::img(    Url::to('@web/uploads/thumb/'.$model->company_logo), ['alt' => 'Company Logo', "height"=>'100px', 'width'=>'200px']);
		}?>
         <p><h3>Note in invoice-footer</h3></p>
		<?php echo Tabs::widget([
        'items' => [
			[
                'label' => 'amazon.co.uk',
                'content' => $form->field($model, 'amazon_uk_footer')->textarea(['rows' => '6'])->label(false),'active' => true
            ],
            [
                'label' => 'amazon.de',
                'content' => $form->field($model, 'amazon_de_footer')->textarea(['rows' => '6'])->label(false),
                
            ],            
			[
                'label' => 'amazon.es',
                'content' => $form->field($model, 'amazon_es_footer')->textarea(['rows' => '6'])->label(false),
            ],
			[
                'label' => 'amazon.fr',
                'content' => $form->field($model, 'amazon_fr_footer')->textarea(['rows' => '6'])->label(false),
            ],
			[
                'label' => 'amazon.it',
                'content' => $form->field($model, 'amazon_it_footer')->textarea(['rows' => '6'])->label(false),
            ],
			
        ]]);
 ?>  
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- user-_company_setting -->

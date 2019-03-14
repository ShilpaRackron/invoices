<?php
	
	use yii\helpers\Html;
	//use yii\bootstrap\ActiveForm;
	use miloschuman\highcharts\Highcharts;
	use miloschuman\highcharts\Highmaps;
	use yii\web\JsExpression;
	$this->title = 'Dashboard';
	$this->params['breadcrumbs'][] = $this->title;
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

<!-- OVERVIEW -->
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row">
				<div class="col-xl-4 col-lg-6">
					<div class="card card-stats mb-4 mb-xl-0">
						<div class="card-body">
							<div class="row">
								<div class="col">
									<h5 class="card-title text-uppercase text-muted mb-0">Orders today</h5>
									<span class="h2 font-weight-bold mb-0"><?php echo count($todayOrder);?></span>
								</div>
								<div class="col-auto">
									<div class="icon icon-shape bg-danger text-white rounded-circle shadow">
										<i class="ni ni-bag-17"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 col-lg-6">
					<div class="card card-stats mb-4 mb-xl-0">
						<div class="card-body">
							<div class="row">
								<div class="col">
									<h5 class="card-title text-uppercase text-muted mb-0">Orders this month</h5>
									<span class="h2 font-weight-bold mb-0"><?php echo count($monthlyOrder);?></span>
								</div>
								<div class="col-auto">
									<div class="icon icon-shape bg-warning text-white rounded-circle shadow">
										<i class="ni ni-bag-17"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 col-lg-6">
					<div class="card card-stats mb-4 mb-xl-0">
						<div class="card-body">
							<div class="row">
								<div class="col">
									<h5 class="card-title text-uppercase text-muted mb-0">Sales</h5>
									<span class="h2 font-weight-bold mb-0">512,924</span>
								</div>
								<div class="col-auto">
									<div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
										<i class="ni ni-bag-17"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid mt--7">
	<div class="row">
		<div class="col-xl-8 mb-5 mb-xl-0">
			<div class="card bg-gradient-default shadow">
				<div class="card-header bg-transparent">
					<div class="row align-items-center">
						<div class="col">
							<h6 class="text-uppercase text-light ls-1 mb-1">Overview</h6>
							<h2 class="text-white mb-0">Sales value</h2>
						</div>
						<!--<div class="col">
							<ul class="nav nav-pills justify-content-end">
								<li class="nav-item mr-2 mr-md-0" data-toggle="chart" data-target="#chart-sales" data-update='{"data":{"datasets":[{"data":[0, 20, 10, 30, 15, 40, 20, 60, 60]}]}}' data-prefix="$" data-suffix="k">
									<a href="#" class="nav-link py-2 px-3 active" data-toggle="tab">
										<span class="d-none d-md-block">Month</span>
										<span class="d-md-none">M</span>
									</a>
								</li>
								<li class="nav-item" data-toggle="chart" data-target="#chart-sales" data-update='{"data":{"datasets":[{"data":[0, 20, 5, 25, 10, 30, 15, 40, 40]}]}}' data-prefix="$" data-suffix="k">
									<a href="#" class="nav-link py-2 px-3" data-toggle="tab">
										<span class="d-none d-md-block">Week</span>
										<span class="d-md-none">W</span>
									</a>
								</li>
							</ul>
						</div>--->
					</div>
				</div>
				<?php
	if(!empty($saleArrayStr)) {
		//echo $saleArrayStr;
		$saleArrayStr = array_map( create_function('$value', 'return (int)$value;'),
		$saleArrayStr);
		$countArrayStr = array_map( create_function('$value', 'return (int)$value;'),
		$countArrayStr);
		echo Highcharts::widget([
		'options' => [
		'title' => ['text' => 'AMAZON ORDERS'],
		'xAxis' => [
		'title' =>[	'text' => 'Years'],
		'categories' => $monthData
		],
		'yAxis' => [
		'title' => ['text' => 'Last 12 Months Data']
		],
		'series' => [
		['name' => 'total', 'data' =>$saleArrayStr],
		['name' => 'count', 'data' =>$countArrayStr]
		]
		]
		]);
	}
?>
</div>
		</div>
			<div class="col-xl-4">
			<div class="card shadow">
				<div class="card-header bg-transparent">
					<div class="row align-items-center">
						<div class="col">
							<h6 class="text-uppercase text-muted ls-1 mb-1">Performance</h6>
							<h2 class="mb-0">Total orders</h2>
						</div>
					</div>
				</div>
				<div class="card-body">
					<!-- Chart -->
					<div class="chart">
						<canvas id="chart-orders" class="chart-canvas"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
<div class="row mt-5">
		<div class="col-xl-12 mb-5 mb-xl-0">
			<div class="card shadow">
				<div class="card-header border-0">
					<div class="row align-items-center">
						<div class="col">
							<h3 class="mb-0">Annual Sales</h3>
						</div>
						<!--<div class="col text-right">
							<a href="#!" class="btn btn-sm btn-primary">See all</a>
						</div>-->
					</div>
				</div>
				<div class="table-responsive">
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col">Sales per country (annual)</th>
								<th scope="col"></th>
								<th scope="col">Curr</th>
								<th scope="col">Subtotal</th>
								<th scope="col">VAT</th>
								<th scope="col">Total</th>
							</tr>
						</thead>
						
						<tbody>
						<?php 
							$date =date('Y');
							$getAllCountriesSale = $orderModel->getOrderCountries(Yii::$app->user->id,$date);
							if(!empty($getAllCountriesSale)){
								
								foreach($getAllCountriesSale as $country=>$totalsale){
									$vatData				= $vatNewModel::getUserVat(Yii::$app->user->id,$country);
									$vatPercentage		= (is_object($vatData) && !empty($vatData))?$vatData->rate_percentage:0;
									$netPrice				=(float)(($totalsale*100)/(100+$vatPercentage));
									$productnetIVA		= (float)($totalsale -$netPrice);
									$currencyCode = ($country=='UK')?'GBP':'EURO';
								?>
								<tr>
									<td><span class="flag flag-<?php echo strtolower($country);?>"></span> <?php echo isset($countries[$country])?$countries[$country]:$country;?> </td>
									<td>
										<!--<div class="progress" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Threshold: <?php number_format($netPrice,2);?> <?php echo $currencyCode;?>&nbsp;&nbsp;&nbsp; Sales: <?php echo number_format($totalsale,2);?> <?php echo $currencyCode;?>">
											<div class="progress-bar bg-danger" role="progressbar" aria-valuenow="107" aria-valuemin="0" aria-valuemax="100" style="width:200px">107%</div>
										</div> -->
									</td>
									<td><?php echo $currencyCode;?></td>
									<td><?php echo number_format($netPrice,2);?></td>
									<td><?php echo number_format($productnetIVA,2);?></td>
									<td><?php echo number_format($totalsale,2);?></td>
								</tr>
								<?php }
							}
						?>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<!-- END OVERVIEW -->
<div class="row">
		<div class="col-md-12">
			
			<div class="panel country-vat">
				<div class="panel-body no-padding">
					<div class="table-responsive">			
						<table class="table card-table table-striped table-vcenter">
							<thead>
								<tr>
									<th>VAT per country</th>
									<th></th>
									<th>Rate</th>
									<th>Curr</th>
									<th>Subtotal</th>
									<th>VAT</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
							<?php if(!empty($vatModel)){
								$year = date('Y');
								foreach($vatModel as $key=>$vatData){
									if(is_object($vatData) && !empty($vatData)) {
										$country = $vatData->country;
										$user_id  = $vatData->user_id;
										$vatPercentage = $vatData->rate_percentage;
										$totalSale = $orderModel->getYearTotalSale($user_id,$country,$year);
										$netPrice				=(float)(($totalSale*100)/(100+$vatPercentage));
										$productnetIVA		= (float)($totalSale -$netPrice);
									?>
									<tr>
										<td><span class="flag flag-<?php echo ($country=='default')?"it":strtolower($country);?>"></span> <span class="mobile-hide"><?php echo $vatData->vat_no;?> </span></td>
										<td>
											<i class="fa fa-check-circle" style="color: green;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Valid VAT number: --- ---"></i>
										</td>
										<td><?php echo $vatData->rate_percentage;?>%</td>
										<td><?php echo ($country=='UK')?'GBP':'EURO';?></td>
										<td><?php echo number_format($netPrice,2);?></td>
										<td><?php echo number_format($productnetIVA,2);?></td>
										<td><?php echo number_format($totalSale,2);?></td>
									</tr>
									<?php 
									}
								}
							}?>
						</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-md-12">
						<p class="panel-note">You should have been VAT registered also in ES, FR, GB as long as goods were shipped from it.</p></div>
						<div class="col-md-12">
						<a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/setting'], true );?>" class="btn btn-primary">Add new VAT number</a> <!--<a href="#" class="btn btn-primary">Get VAT number</a>--> </div>
					</div>
				</div>
			</div>
			
		</div>
	<!-- <div class="col-md-6">
		
		<div class="panel">
		<div class="panel-heading">
		<h3 class="panel-title">ANNUAL SALES</h3>
		</div>
		<div class="panel-body">
		<div id="visits-trends-chart" class="ct-chart">									
		
		</div>
		</div>
		</div>
	</div> -->
</div>



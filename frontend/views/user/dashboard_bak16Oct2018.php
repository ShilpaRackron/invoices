<?php
 
use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
use miloschuman\highcharts\Highcharts;
$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

?>

					<!-- OVERVIEW -->
					<h3 class="page-title">Dashboard</h3>
						<div class="row">
								<div class="col-md-6">
									<div class="metric bg-flat-color-1">
										<span class="icon"><i class="fa fa-shopping-bag"></i></span>
										<p>
											<span class="number"><?php echo count($todayOrder);?></span>
											<span class="title">Orders today</span></p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="metric bg-flat-color-2">
										<span class="icon"><i class="fa fa-shopping-bag"></i></span>
										<p>
											<span class="number"><?php echo count($monthlyOrder);?></span>
											<span class="title">Orders this month</span></p>
									</div>
								</div>
							</div>
					<div class="panel panel-headline">
				   <div class="panel-body">
				   
				
				  <!-- <div class="row">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th class="w-1">Sales per country (annual)</th>
                          <th></th>
                          <th></th>
                          <th>Curr</th>
                          <th>Subtotal</th>
                          <th>VAT</th>
                          <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
						<tr>
						<td><span class="flag flag-gb"></span> United Kingdom</td>
						<td><div class="col-2 d-none d-md-block">
						<button type="button" class="btn btn-success btn-sm get-vat-number" style="float: right;">Get VAT </button>
						</div>
						
						</td>
						<td>
						<div class="progress" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Threshold: 70,000.00 GBP&nbsp;&nbsp;&nbsp; Sales: 75,191.90 GBP">
						<div class="progress-bar bg-danger" role="progressbar" aria-valuenow="107" aria-valuemin="0" aria-valuemax="100" style="width:200px">107%</div>
						</div>
						</td>
						<td>EUR</td>
						<td>84,276.96</td>
						<td>19,382.93</td>
						<td>103,659.89</td>
						</tr>
						<tr>
						<td><span class="flag flag-it"></span> Italy</td>
						<td><div class="col-2 d-none d-md-block">
						<button type="button" class="btn btn-success btn-sm get-vat-number" style="float: right;">Get VAT </button>
						</div>
						
						</td>
						<td>
					<div class="progress" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Threshold: 35,000.00 EUR&nbsp;&nbsp;&nbsp; Sales: 32,625.98 EUR">
					<div class="progress-bar bg-primary" role="progressbar" aria-valuenow="93" aria-valuemin="0" aria-valuemax="100" style="width: 93%">
					93%	</div>
					</div>
						</td>
						<td>EUR</td>
						<td>84,276.96</td>
						<td>19,382.93</td>
						<td>103,659.89</td>
						</tr>
						<tr>
						<td><span class="flag flag-es"></span> Spain</td>
						<td><div class="col-2 d-none d-md-block">
						<button type="button" class="btn btn-success btn-sm get-vat-number" style="float: right;">Get VAT </button>
						</div>
						
						</td>
						<td>
						<div class="progress" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Threshold: 35,000.00 EUR&nbsp;&nbsp;&nbsp; Sales: 6,879.34 EUR">
						<div class="progress-bar bg-success" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
						20%</div>
						</div>
						</td>
						<td>EUR</td>
						<td>84,276.96</td>
						<td>19,382.93</td>
						<td>103,659.89</td>
						</tr>
                      </tbody>
                    </table>
                  </div>
							</div>
						</div>
					</div> -->
					<!-- END OVERVIEW -->
					<!--<div class="row">
						<div class="col-md-6">
					
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
                        <tr>
                          <td><span class="flag flag-gb"></span> <span class="mobile-hide">DE114103379</span></td>
						  <td>
					<i class="fa fa-check-circle" style="color: green;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Valid VAT number: --- ---"></i>
						  </td>
						  <td>19%</td>
						  <td>EUR</td>
						  <td>788.83</td>
						  <td>138.83</td>
						  <td>927.66</td>
                        </tr>
						 <tr>
                          <td><span class="flag flag-it"></span> <span class="mobile-hide">DE114103379</span></td>
						  <td>
					<i class="fa fa-check-circle" style="color: green;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Valid VAT number: --- ---"></i>
						  </td>
						  <td>19%</td>
						  <td>EUR</td>
						  <td>788.83</td>
						  <td>138.83</td>
						  <td>927.66</td>
                        </tr>
						<tr>
                          <td><span class="flag flag-es"></span> <span class="mobile-hide">DE114103379</span></td>
						  <td>
					<i class="fa fa-times-circle" style="color: tomato;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Invalid VAT number"></i>
						  </td>
						  <td>19%</td>
						  <td>EUR</td>
						  <td>788.83</td>
						  <td>138.83</td>
						  <td>927.66</td>
                        </tr>
						<tr>
                          <td><span class="flag flag-gb"></span> <span class="mobile-hide">DE114103379</span></td>
						  <td>
					<i class="fa fa-times-circle" style="color: tomato;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Invalid VAT number"></i>
						  </td>
						  <td>19%</td>
						  <td>EUR</td>
						  <td>788.83</td>
						  <td>138.83</td>
						  <td>927.66</td>
                        </tr>
						<tr>
                          <td><span class="flag flag-es"></span> <span class="mobile-hide">DE114103379</span></td>
						  <td>
					<i class="fa fa-times-circle" style="color: tomato;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Invalid VAT number"></i>
						  </td>
						  <td>19%</td>
						  <td>EUR</td>
						  <td>788.83</td>
						  <td>138.83</td>
						  <td>927.66</td>
                        </tr>
                      </tbody>
                    </table>
					</div>
								</div>
								<div class="panel-footer">
									<div class="row">
										<div class="col-md-12">
										<p class="panel-note">You should have been VAT registered also in ES, FR, GB as long as goods were shipped from it.</p></div>
										<div class="col-md-12">
										<a href="#" class="btn btn-primary">Add new VAT number</a> <a href="#" class="btn btn-primary">Get VAT number</a> </div>
									</div>
								</div>
							</div>
							
						</div>
						<div class="col-md-6">
						
							<div class="panel">
								<div class="panel-heading">
									<h3 class="panel-title">ANNUAL SALES</h3>
								</div>
								<div class="panel-body">
									<div id="visits-trends-chart" class="ct-chart"></div>
								</div>
							</div>
							
						</div>
					</div>	-->

				<!-- Javascript -->

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
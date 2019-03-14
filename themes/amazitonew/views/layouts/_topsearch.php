<?php
use yii\widgets\ActiveForm;
?>
<!--TOP SEARCH SECTION-->
	<section id="myID" class="bottomMenu hom-top-menu">
		<div class="container top-search-main">
			<div class="row">
				<div class="ts-menu">
					<!--SECTION: LOGO-->
					<div class="ts-menu-1">
						<a href="<?php echo Yii::$app->homeUrl;?>"><img src="<?php echo Yii::getAlias("@web/themes/stralinglead/");?>images/logo_top.png" alt=""> </a>
					</div>
					<!--SECTION: BROWSE CATEGORY(NOTE:IT'S HIDE ON MOBILE & TABLET VIEW)-->
					<div class="ts-menu-2"><a href="#" class="t-bb">Menu <i class="fa fa-angle-down" aria-hidden="true"></i></a>
						<!--SECTION: BROWSE CATEGORY-->
						<div class="cat-menu cat-menu-1">
							<div class="dz-menu">
								<div class="dz-menu-inn">
									<ul>
									<li><a class='dropdown-button' href='<?php echo Yii::$app->homeUrl;?>'>Home</a>
									</li>
									<li><a class='dropdown-button' href='<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/about");?>'>Our Solution</a>
									</li>
									<li><a class='dropdown-button' href='<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/pricing");?>'>Pricing</a>
									</li>
									<li><a class='dropdown-button' href='<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/contact");?>'>Support</a>
									</li>
									</ul>
								</div>															
							</div>
							
						</div>
					</div>
					<!--SECTION: SEARCH BOX-->
					<div class="ts-menu-3" style="width:50%;">
						<div class="">
							<?php 
								$form2 = ActiveForm::begin([
											'id' => 'download-contact-top',
											'enableClientValidation' => true,
											'enableAjaxValidation' => false,
											'action'=> ["site/search"],
									         'method'=> 'get',
											 'options' => [
											 'class' => 'form-horizontal',
											//'onsubmit'=>'javascript: return checkvalidation()',
											]
									]);	 ?>	
								<div class="input-field">
									<!--<input type="text" id="top-select-city" class="autocomplete">
									<label for="top-select-city">Enter city</label> -->
								</div>
								<div class="input-field">
									<input type="text" name="searchtext" id="top-select-search" value="<?php echo (isset($_GET['searchtext']))?$_GET['searchtext']:"";?>">
									<label for="top-select-search" class="search-hotel-type">Search by name, title, company</label>
								</div>
								<div class="input-field">
								<input type="submit" value="" class="waves-effect waves-light tourz-top-sear-btn"> </div>
								<?php
					ActiveForm::end();
					?>
						</div>
					</div>
					<!--SECTION: REGISTER,SIGNIN AND ADD YOUR BUSINESS-->
					<div class="ts-menu-4" style="width: 30%;">
						<div class="v3-top-ri">
							<ul>

							<?php if (Yii::$app->user->isGuest) {  ?>
									<li><a class="v3-menu-sign" href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/signup");?>" >Register</a> </li>
									<li><a class="v3-menu-sign" href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/login");?>" >Sign In</a> </li>
						<?php } else { ?>

							<li><a class="v3-menu-sign" href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("user/dashboard");?>" >My Dashboard</a> </li>
							<li><a class="v3-menu-sign" href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/logout");?>" >Logout</a> </li>					

							<?php } ?>	
							
							<?php 
								$session_data = Yii::$app->getSession()->get('cart_data');
								 if(!empty($session_data) && count($session_data) >0){
									 $totalItemsInCart = count($session_data);
									 //$totalCost = (int)$totalItemsInCart * (float)Yii::$app->params['cost_per_contact'];

									 $cost		= Yii::$app->params['fix_cost'];
									 $extraCost =0;
									 if($totalItemsInCart > Yii::$app->params['fix_contacts'])
									 {
									   $extraItems = $totalItemsInCart-Yii::$app->params['fix_contacts'];
									   $extraCost  = (int)$extraItems * (float)Yii::$app->params['cost_per_contact'];
									 }
									 //$totalCost = (int)$totalItemsInCart * (float)Yii::$app->params['cost_per_contact'];
									 $totalCost = $cost+$extraCost;

									 ?>
									 <li><a href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/logout");?>" ><span class="glyphicon glyphicon-shopping-cart">(<?php echo $totalCost.' USD';?>)</span></a> </li>	
									 <?php
									 
								 }?>
							</ul>
						</div>
					</div>

					<!--MOBILE MENU ICON:IT'S ONLY SHOW ON MOBILE & TABLET VIEW-->
					<div class="ts-menu-5"><span><i class="fa fa-bars" aria-hidden="true"></i></span> </div>
					<!--MOBILE MENU CONTAINER:IT'S ONLY SHOW ON MOBILE & TABLET VIEW-->
					<div class="mob-right-nav" data-wow-duration="0.5s">
						<div class="mob-right-nav-close"><i class="fa fa-times" aria-hidden="true"></i> </div>
						<ul>
							<li><a href='<?php echo Yii::$app->homeUrl;?>'>Home</a></li>
							<li><a href='<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/about");?>'>Our Solution</a></li>
						    <li><a href='<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/pricing");?>'>Pricing</a></li>
							<li><a href='<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/contact");?>'>Support</a> </li>
						</ul>

						<ul class="mob-menu-icon">
							
							<?php if (Yii::$app->user->isGuest) {  ?>
									<li><a   href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/signup");?>" >Register</a> </li>
									<li><a  href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/login");?>" >Sign In</a> </li>
						<?php } else { ?>

							<li><a href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("user/dashboard");?>" >My Dashboard</a> </li>
							<li><a href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/logout");?>" >Logout</a> </li>

							

							<?php } ?>

							<?php 
								$session_data = Yii::$app->getSession()->get('cart_data');
								 if(!empty($session_data) && count($session_data) >0){
									 $totalItemsInCart = count($session_data);
									 //$totalCost = (int)$totalItemsInCart * (float)Yii::$app->params['cost_per_contact'];
									 $cost		= Yii::$app->params['fix_cost'];
									 $extraCost =0;
									 if($totalItemsInCart > Yii::$app->params['fix_contacts'])
									 {
									   $extraItems = $totalItemsInCart-Yii::$app->params['fix_contacts'];
									   $extraCost  = (int)$extraItems * (float)Yii::$app->params['cost_per_contact'];
									 }
									 //$totalCost = (int)$totalItemsInCart * (float)Yii::$app->params['cost_per_contact'];
									 $totalCost = $cost+$extraCost;

									 ?>
									 <li><a href="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl("site/logout");?>" ><span class="glyphicon glyphicon-shopping-cart">(<?php echo $totalCost.' USD';?>)</span></a> </li>	
									 <?php
									 
								 }?>
							
						</ul>
						
						
					</div>

				</div>
			</div>
		</div>
	</section>
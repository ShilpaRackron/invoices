<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>

<!doctype html>
<html lang="en">

<head>
	<title><?= Html::encode($this->title) ?></title>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<?= Html::csrfMetaTags() ?>
	<?php $this->head() ?>
	<!-- VENDOR CSS -->
	<link rel="stylesheet" href="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>linearicons/style.css">
	<link rel="stylesheet" href="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>chartist/css/chartist-custom.css">
	<!-- MAIN CSS -->
	<link rel="stylesheet" href="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>css/main.css">

	<!-- GOOGLE FONTS -->
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
</head>

<body>
<?php $this->beginBody() ?>
<?php $amazonClientId = Yii::$app->params['amazon_clientId']; ?>
<?php $logoutUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['site/logout'], true );?>
 <div id="amazon-root"></div>
<script type="text/javascript">

  window.onAmazonLoginReady = function() {
    amazon.Login.setClientId('<?php echo $amazonClientId;?>');
  };
  (function(d) {
    var a = d.createElement('script'); a.type = 'text/javascript';
    a.async = true; a.id = 'amazon-login-sdk';
    a.src = 'https://api-cdn.amazon.com/sdk/login1.js';
    d.getElementById('amazon-root').appendChild(a);
  })(document);

</script>


<!-- WRAPPER -->
	<div id="wrapper">
	<!-- NAVBAR -->
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="brand">
				<a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['site'], true );?>"><img src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>images/logo-dark.png" alt="Logo" class="img-responsive logo"></a></div>
			<div class="container-fluid">
				<div class="navbar-btn">
					<button type="button" class="btn-toggle-fullwidth"><i class="lnr lnr-arrow-left-circle"></i></button>
				</div>
				<!--<form class="navbar-form navbar-left">
					<div class="input-group">
						<input type="text" value="" class="form-control" placeholder="Search dashboard...">
						<span class="input-group-btn"><button type="button" class="btn btn-primary">Go</button></span></div>
				</form>-->

				<div id="navbar-menu">				
					<ul class="nav navbar-nav navbar-right">
					<?php  if (!Yii::$app->user->isGuest) { ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>images/user.png" class="img-circle" alt="Avatar"> <span><?php echo Yii::$app->user->identity->name;?></span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/setting'], true );?>"><i class="lnr lnr-cog"></i> <span>Settings</span></a></li>
								<li><a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/updateprofile'], true );?>"><i class="lnr lnr-cog"></i> <span>Update Profile</span></a></li>
								<li><a class = 'btn btn-link logout' id="Logout" ><i class="lnr lnr-exit"></i> <span>Logout</span></a></li>
							</ul>
						</li>
						<?php } else{?>
							  <li> <a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['site/login'], true );?>">Login</a></li>
						<?php }  ?>
					</ul>
				</div>
			</div>
		</nav>
		<!-- END NAVBAR -->

		<!-- LEFT SIDEBAR -->
		<?php  if (!Yii::$app->user->isGuest) { ?>
		<div id="sidebar-nav" class="sidebar">
			<div class="sidebar-scroll">			
			 
			<nav>
					<ul class="nav">
						<li><a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/dashboard'], true );?>" class="active"><i class="fa fa-columns"></i> <span>Dashboard</span></a></li>
						<li><a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/invoices'], true );?>" class=""><i class="fa fa-file"></i> <span>Invoices</span></a></li>
						<li><a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['amazon-products/index'], true );?>" class=""><i class="fa fa-barcode"></i> <span>SKU</span></a></li>
						<li><a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/credit-memo'], true );?>" class=""><i class="fa fa-barcode"></i> <span>Credit memo</span></a></li>
						<li><a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['amazon-log-info'], true );?>" class=""><i class="lnr lnr-chart-bars"></i> <span>Log</span></a></li>
						<!-- <li>
						<a href="#subPages" data-toggle="collapse" class="collapsed"><i class="fa fa-home"></i> <span>Info</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
							<div id="subPages" class="collapse ">
								<ul class="nav">
									<li><a href="#" class="">Home</a></li>
									<li><a href="#" class="">How To</a></li>
									<li><a href="#" class="">FAQ</a></li>
								</ul>
							</div>
						</li> -->
					</ul>
				</nav>
					
			</div>
		</div>
		<?php } ?>
		<!-- END LEFT SIDEBAR -->

   
   <!-- MAIN -->
		<div class="main">
			<!-- MAIN CONTENT -->			
			<div class="main-content">
			<div class="container-fluid">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
		</div>
			<!-- END MAIN CONTENT -->
    </div>
	<!-- END MAIN CONTENT -->	
</div>
 <div class="clearfix"></div>
	<footer>
			<div class="container-fluid">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>        
    </div>
		</footer>
</div>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/bootstrap.min.js"></script>
<script type="text/javascript">
 jQuery(document).ready(function(){
	  jQuery("#Logout").click(function() {		  
		  amazon.Login.logout();
		 window.location.href='<?php echo $logoutUrl;?>';
	  });
	});
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

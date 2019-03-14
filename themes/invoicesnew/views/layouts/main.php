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
	<?php //$this->head() ?>
	<link href="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>images/favicon.png" rel="icon" type="image/png">
	 <!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
	<!-- Icons -->
	<link href="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>css/nucleo.css" rel="stylesheet">
	<link href="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>css/all.min.css" rel="stylesheet">
	 <!-- main CSS -->
	<link type="text/css" href="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>css/style.css" rel="stylesheet">
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
   <nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
      <!-- Toggler -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- Brand -->
      <a class="navbar-brand pt-0" href="#">
        <img src="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>images/logo.png" class="navbar-brand-img" alt="...">
      </a>
      <!-- User -->
      <ul class="nav align-items-center d-md-none">
        <li class="nav-item dropdown">
          <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="media align-items-center">
              <span class="avatar avatar-sm rounded-circle">
                <img alt="" src="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>images/user.png">
              </span>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
            <div class=" dropdown-header noti-title">
              <h6 class="text-overflow m-0">Welcome!</h6>
            </div>
			<?php  if (!Yii::$app->user->isGuest) { ?>
            <a href="#" class="dropdown-item">
              <i class="ni ni-single-02"></i>
              <span>My profile</span>
            </a>
            <a href="#" class="dropdown-item">
              <i class="ni ni-settings-gear-65"></i>
              <span>Settings</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#!" class="dropdown-item">
              <i class="ni ni-user-run"></i>
              <span>Logout</span>
            </a>
			<?php } else{?>
				 <a class="dropdown-item" href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['site/login'], true );?>">Login</a>
			<?php }  ?>
          </div>
        </li>
      </ul>
      <!-- Collapse -->
      <div class="collapse navbar-collapse" id="sidenav-collapse-main">
        <!-- Collapse header -->
        <div class="navbar-collapse-header d-md-none">
          <div class="row">
            <div class="col-6 collapse-brand">
              <a href="index.html">
                <img src="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>images/logo.png">
              </a>
            </div>
            <div class="col-6 collapse-close">
              <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle sidenav">
                <span></span>
                <span></span>
              </button>
            </div>
          </div>
        </div>
        <!-- Form -->
        <form class="mt-4 mb-3 d-md-none">
          <div class="input-group input-group-rounded input-group-merge">
            <input type="search" class="form-control form-control-rounded form-control-prepended" placeholder="Search" aria-label="Search">
            <div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fa fa-search"></span>
              </div>
            </div>
          </div>
        </form>
        <!-- Navigation -->
        <ul class="navbar-nav">
		 <?php  if (!Yii::$app->user->isGuest) { ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/dashboard'], true );?>">
              <i class="ni ni-tv-2 text-primary"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/invoices'], true );?>">
              <i class="ni ni-single-copy-04 text-blue"></i> Invoices
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['amazon-products/index'], true );?>">
              <i class="fa fa-barcode text-orange"></i> SKU
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/credit-memo'], true );?>">
              <i class="fa fa-money-bill text-yellow"></i> Credit Memo
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/manage-inventory'], true );?>">
              <i class="fa fa-file text-red"></i> Inventory Data
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['amazon-inventory-adjustment/index'], true );?>">
              <i class="fa fa-sliders-h text-info"></i> Inventory Adjustment
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['amazon-log-info'], true );?>">
              <i class="ni ni-circle-08 text-pink"></i> Log
            </a>
          </li>
        </ul>
       <?php } ?>
      </div>
    </div>
  </nav>
 <!-- MAIN -->
		<div class="main-content">
		<!-- Top navbar -->
		
    <nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
      <div class="container-fluid">
        <!-- Brand -->
        <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="#"><?php  if (!Yii::$app->user->isGuest) { ?> Dashboard<?php } else { echo "Login"; } ?></a>
        <!-- Form -->
        <!-- <form class="navbar-search navbar-search-dark form-inline mr-3 d-none d-md-flex ml-lg-auto">
          <div class="form-group mb-0">
            <div class="input-group input-group-alternative">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
              </div>
              <input class="form-control" placeholder="Search" type="text">
            </div>
          </div>
        </form> -->
        <!-- User -->
        <ul class="navbar-nav align-items-center d-none d-md-flex">
          <li class="nav-item dropdown">
            <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <div class="media align-items-center">
                <span class="avatar avatar-sm rounded-circle">
                  <img alt="Image placeholder" src="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>images/user.png">
                </span>
                <div class="media-body ml-2 d-none d-lg-block">
                  <span class="mb-0 text-sm  font-weight-bold"><?php  if (!Yii::$app->user->isGuest) { ?> <?php echo Yii::$app->user->identity->name;?> <?php }?></span>
                </div>
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
              <div class=" dropdown-header noti-title">
                <h6 class="text-overflow m-0">Welcome!</h6>
              </div>

			  <?php  if (!Yii::$app->user->isGuest) { ?>
			 <a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/updateprofile'], true );?>" class="dropdown-item">
              <i class="ni ni-single-02"></i>
              <span>My profile</span>
            </a>
            <a href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['user/setting'], true );?>" class="dropdown-item">
              <i class="ni ni-settings-gear-65"></i>
              <span>Settings</span>
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item logout" id="Logout">
              <i class="ni ni-user-run"></i>
              <span>Logout</span>
            </a>		
			<?php } else{?>
				 <a class="dropdown-item" href="<?php echo Yii::$app->getUrlManager ()->createAbsoluteUrl ( ['site/login'], true );?>">Login</a>
			<?php }  ?>
            </div>
          </li>
        </ul>
      </div>
    </nav>


	 <!-- Header -->
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
      <div class="container-fluid">
        <div class="header-body">
          <!-- Card stats -->
          
        </div>
      </div>
    </div>
	<!-- Page content -->
    <div class="container-fluid mt--7">
      
     
	 
		<!--<?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>-->
        <?= Alert::widget() ?>
        <?= $content ?>		
    
	<!-- END MAIN CONTENT -->	

 <div class="clearfix"></div>
	  <!-- Footer -->
      <footer class="footer">
        <div class="row align-items-center justify-content-xl-between">
          <div class="col-xl-6">
            <div class="copyright text-center text-xl-left text-muted">
            Copyright &copy; <?= date('Y') ?> Pro Invoice System. All rights reserved.
            </div>
          </div>
          <div class="col-xl-6">
            <ul class="nav nav-footer justify-content-center justify-content-xl-end">
              <li class="nav-item">
                <a href="#" class="nav-link" target="_blank">Terms of Service</a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link" target="_blank">Privacy Policy</a>
              </li>
            </ul>
          </div>
        </div>
      </footer>
    </div>
  </div>

</div>
<script src="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>js/bootstrap.bundle.min.js"></script>
<!-- Argon JS -->
 <script src="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>js/Chart.min.js"></script>
  <script src="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>js/Chart.extension.js"></script>

<script src="<?php echo Yii::getAlias("@web/themes/invoicesnew/");?>js/common.js"></script>

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

<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/bootstrap.min.js"></script>
<div class="site-login">
    <div class="row">
        <div class="col-lg-5">
<?php $amazonReturnUrl  = Yii::$app->params['amazon_login_url']; ?>
<?php if(isset($_GET['response']) && !empty($_GET['response'])):?>
<a id="Logout" style="cursor:pointer;">Logout</a>
<br/><br/ >
<?php else: ?>
 <?php if(Yii::$app->user->isGuest) { ?>
   <?php $amazonClientId = Yii::$app->params['amazon_clientId']; ?>
	<a href="#" id="LoginWithAmazon">
  <img border="0" alt="Login with Amazon"
    src="https://images-na.ssl-images-amazon.com/images/G/01/lwa/btnLWA_gold_156x32.png"
    width="156" height="32" />
</a>
<div id="amazon-root"></div>

<script type="text/javascript">

  document.getElementById('LoginWithAmazon').onclick = function() {
    options = { scope : 'profile' };
    amazon.Login.authorize(options, '<?php echo $amazonReturnUrl;?>');
    return false;
  };

</script>
<script type="text/javascript">

  window.onAmazonLoginReady = function() {
    amazon.Login.setClientId('<?php echo $amazonClientId;?>');
  };
  (function(d) {
    var a = d.createElement('script'); a.type = 'text/javascript';
    a.async = true; a.id = 'amazon-login-sdk';
    a.src = 'https://api-cdn.amazon.com/sdk/login1.js';
    d.getElementById('amazon-root').appendChild(a);
	//jQuery("#Logout").show();
  })(document);

</script>
<script type="text/javascript">
 jQuery(document).ready(function(){
	  jQuery("#Logout").click(function() {
		   amazon.Login.logout();
	  });
	});
</script>
 <?php  }
endif;
?>
 </div>
    </div> 
   
</div>
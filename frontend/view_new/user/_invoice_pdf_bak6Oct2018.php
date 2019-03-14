<?php
	use yii\helpers\Html;
	use yii\helpers\Url;
	$this->title = Yii::t('app', 'Invoice Details');
	$this->params['breadcrumbs'][] = $this->title;
?>
<style>

@charset "utf-8";

/* Reset */
a, abbr, acronym, address, area, b, bdo, big, blockquote, body, button, caption, cite,
code, col, colgroup, dd, del, dfn, div, dl, dt, em, fieldset, form, h1, h2, h3, h4,
h5, h6, hr, html, i, images, ins, kbd, label, legend, li, map, object, ol, p, param, pre,
q, samp, small, span, strong, sub, sup, table, tbody, td, textarea, tfoot, th, thead,
tr, tt, ul, var {margin:0;padding:0;vertical-align:baseline}

/* Defaults */

abbr, acronym, dfn {border-bottom:1px dotted;cursor:help}
blockquote { width:365px; margin-left:99px; padding:0px 20px; background-image:url(../images/blockquote_img.jpg); background-repeat:no-repeat; background-position:left top; margin-bottom:35px; }
blockquote p{font-family:Arial, Helvetica, sans-serif; font-size:12px;color:#333333; font-weight:500; line-height:24px; font-style:italic; margin-bottom:0px}

/*body {background:#000;color:#000;font:75%/125% "HelveticaNeue", Arial, Sans-Serif}*/
code, pre {font-size:1em}
del {text-decoration:line-through}
dfn {font-style:italic;font-weight:bold}
dt {font-weight:bold}
dd {margin:0 0 1em 10px}
fieldset {border:0}
fieldset p {margin:0 0 5px}
img {height:auto; border:0px;}
ins {text-decoration:none}
hr {margin:0 0 0.5em}


textarea {font:1em Arial;overflow:auto; outline:medium;}
tt {display:block;margin:0.5em 0;padding: 0.5em 1em}
strong{margin-right:0px;}
input{outline:medium;}
select{outline:medium;}

h1,h2{}

p{ margin:0px; padding:0px;}

/****** Common Classes ******/
.clear{ display:block; clear:both; line-height:0;}
.space { display:block; clear:both; height:30px;}
.dspace { display:block; clear:both; height:22px;}

body { margin: 0px; padding:0px; font-family: "Open Sans",Tahoma,sans-serif; font-size:16px;}

/***** WRAPPER ******/

#wrapper{max-width:100%;}
.wrap{max-width:900px; margin:0 auto;}
.main-section{ float:left; width:100%; font-size:14px; color:#5f5f5f; background:#fff; padding:5px;}
.header-sec{ float:left; width:100%;}
.header-sec .logo{ float:left; width:300px;}
.header-sec .logo h1{ font-size:25px; color:#0072c1;}
.header-sec .logo span{ font-size:11px;}
.header-sec .invoice-title{ float:right; width:300px;}
.header-sec .invoice-title h1{ font-size:25px; font-weight:300; color:#0072c1;}
.header-sec .invoice-title span{ font-size:11px;}

.header-bottom{ float:left; width:100%;}
.header-sec .left{ float:left; width:300px; font-size:10px; color:#0072c1; margin-top:10px;}
.header-sec .right{ float:right; width:300px; font-size:8px; margin-top:10px;}
.header-sec .right td{ border-bottom:1px solid #ddd; padding:5px; font-size:10px;}

.section1{ float:left; width:100%; margin-top:30px;}
.section1 .left-colm{ float:left; width:300px;}
.section1 .right-colm{ float:right; width:300px;}
.section1 .head{background:#0072c1; padding:5px;}
.section1 .head h1{ font-size:10px; color:#fff;}
.section1 .head span{ font-size:11px;}
.section1 .info{font-size:10px; padding:20px 40px;}

.section2{ float:left; width:100%; font-size:10px; margin-top:10px;}
.section2 table{ border-collapse:collapse;}
.section2 table th{ background:#0072c1; color:#fff; padding:5px; font-size:10px;}
.section2 table td{ border:1px solid #ccc; padding:5px; font-size:8px;}
.section2 table .style1{ font-style:italic;}

.section3{ float:left; width:100%; font-size:12px; margin-top:20px;}
.section3 .left-colm{ float:left; width:300px;}
.section3 .right-colm{ float:right; width:300px;}
.section3 .box1{ border:1px solid #ccc; padding:10px; text-align:right;}
.section3 .box2{ border:1px solid #ccc; padding:10px; margin-top:10px;}
.style2{ border-bottom:1px solid #ccc; margin-bottom:10px; padding-bottom:5px; text-align:right;}

.section3 .right-colm table{ border-collapse:collapse;}
.section3 .right-colm table th{ padding:5px; border-bottom:1PX solid #ccc;}
.section3 .right-colm table td{ padding:5px;}
.section3 .right-colm table .total{ background:#0072c1; color:#fff; text-align:right; font-weight:bold; margin-top:10px;}
.section3 .right-colm table td:last-child{ border-left:1px solid #ccc;}
.style3{ background:#ddd;}
/***** media queries ******/

@media screen and (min-width:768px) and (max-width:1024px) 
{
body{ font-size:14px;}
.header{ width:100%;}
.header .logo{ width:18%;}
}


@media screen and (min-width: 320px) and (max-width: 767px)
{
body{ font-size:14px;}
.header{ padding-bottom:10px;}
.header .header-top{ margin-bottom:10px;}
}

@media screen and (min-width:980px) and (max-width:1279px) 
{
.header{ width:100%;}
.nav a{ margin:0px 15px;}
}
</style>
<?php 
  $countryCode = $companyModel->country;
  $showDoulCurrency = (isset($countryCode)&& $countryCode=="BG")?true:false;
?>
<div id="wrapper">
 <div class="wrap">
  <div class="main-section">
   <div class="header-sec">
     <div class="logo"><h1><?php echo $companyModel->company_name;?><?php if($showDoulCurrency):?>/<span><?php echo $companyModel->company_name_in_bul;?></span><?php endif;?></h1></div>
	 <div class="invoice-title"><h1><?php echo Yii::t("invoice","Invoice");?><?php if($showDoulCurrency):?>/<span>Фактура</span><?php endif;?></h1></div>
	 <div class="header-bottom">
	 <div class="left">
		<?php echo nl2br($companyModel->company_header);?>
		<?php if( $companyModel->company_logo !=""){ ?><?php  echo Html::img(Url::to('@web/uploads/thumb/'.$companyModel->company_logo), ['alt' => 'Company Logo', "height"=>'100px', 'width'=>'200px']); ?> <?php } ?>
	 </div>
	 <div class="right">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php echo Yii::t("invoice","Date");?><?php if($showDoulCurrency):?> / Дата<?php endif;?>:</td>
    <td class="style3"><?php echo date("F d, Y");?></td>
  </tr>
    <tr>
    <td><?php echo Yii::t("invoice","Invoice #");?><?php if($showDoulCurrency):?> / Фактура №<?php endif;?>:</td>
    <td><?php echo $amazonOrderModel->invoice_number;?></td>
  </tr>
    <tr>
    <td><?php echo Yii::t("invoice","Order N.");?><?php if($showDoulCurrency):?> / Номер на поръчката<?php endif;?>: </td>
    <td class="style3"><?php echo $amazonOrderModel->amazon_order_id;?></td>
  </tr>
    <tr>
    <td><?php echo Yii::t("invoice","Delivery");?> <?php if($showDoulCurrency):?> / Изпратено от<?php endif;?>:</td>
    <td>
	<?php echo $amazonOrderModel->fulfillment_channel=="AFN"?"Amazon FBA Warehouse":"Seller Warehouse Fulfilment";?></td>
  </tr>
</table>

	 </div>
	 </div>
	 
   </div>
 <!--section1-->  
   <div class="section1">
    <div class="left-colm">
	 <div class="head"><h1><?php echo Yii::t("invoice","Customer Billing Address");?><?php if($showDoulCurrency):?> / <span>Адрес за фактуриране</span><?php endif;?></h1></div>
	 <div class="info">
	  <p><?php echo $amazonOrderModel->buyer_name;?></p>
	   <p><?php echo $amazonOrderModel->address_2;?></p>
	    <p><?php echo $amazonOrderModel->postal_code ;?> <?php echo $amazonOrderModel->city;?></p>
		<!--<p><?php //echo $amazonOrderModel->state_or_region ;?></p>-->
		<p><?php echo $amazonOrderModel->country_code ;?></p>
		<p><?php if($amazonOrderModel->buyer_vat !=""){ echo "Vat Number: ".$amazonOrderModel->buyer_vat; }?></p>
	 </div>
	</div>
	<div class="right-colm">
	 <div class="head"><h1><?php echo Yii::t("invoice","Ship to");?><?php if($showDoulCurrency):?> / <span>Адрес за доставка:</span><?php endif;?></h1></div>
	 <div class="info">
	  <p><?php echo $amazonOrderModel->buyer_name;?></p>
	   <p><?php echo $amazonOrderModel->address_2;?></p>
	    <p><?php echo $amazonOrderModel->postal_code ;?> <?php echo $amazonOrderModel->city;?></p>
		<!--<p><?php //echo $amazonOrderModel->state_or_region ;?></p> -->
		<p><?php echo $amazonOrderModel->country_code ;?></p>
		<p><?php if($amazonOrderModel->buyer_vat !=""){ echo "Vat Number: ".$amazonOrderModel->buyer_vat; }?></p>
	 </div>	
	</div>
   </div>
 <!--section1 end--> 
 
 <!--section2-->   
 <div class="section2">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th><?php echo Yii::t("invoice","Sku");?><?php if($showDoulCurrency):?> / код<?php endif;?></th>
    <th><?php echo Yii::t("invoice","Description");?><?php if($showDoulCurrency):?> / Наименование на стоките и услугите<?php endif;?></th>
    <th><?php echo Yii::t("invoice","Qty");?><?php if($showDoulCurrency):?> / бр<?php endif;?></th>
    <th><?php echo Yii::t("invoice","Price");?><?php if($showDoulCurrency):?> / цена<?php endif;?></th>
    <th><?php echo Yii::t("invoice","Subtotal");?><?php if($showDoulCurrency):?> / междинна сума<?php endif;?></th>
  </tr>

  <?php
		
		$vatPercentage =$vat->rate_percentage;
		$totalVat = 0;
		$vatPer =$vat->rate_percentage;
		$discountAmount =0;
		$lvlConverPrice ='1.95583'; // 1EUR = 1.95583 LVL 
		$currencySymbol = ($amazonOrderModel->order_currency=="EUR")?"&#x20AC;":"&pound;";
		foreach($orderItems as $key=>$itemData ) { 
			$sellerSKU				= $itemData['SellerSKU'];
			$qty					= $itemData['QuantityOrdered'];
			$productVatPercentage	= $productModel->getProductVat($sellerSKU, Yii::$app->user->id);
			$shippingTax			= (isset($itemData['ShippingTax']))?$itemData['ShippingTax']['Amount']:0;
			$promotionDiscount		= $itemData['PromotionDiscount']['Amount'];
			$discountAmount			= $discountAmount +	$promotionDiscount;
			$itemPrice				=  $itemData['ItemPrice']['Amount'];
			$itemGrossPrice			= (float)$itemPrice*$qty;
			$vatPer					= ($productVatPercentage >0)?$productVatPercentage:$vatPercentage;
			$netPrice				= (float)(($itemGrossPrice*100)/(100+$vatPer));
			$productnetIVA			= (float)($itemGrossPrice -$netPrice);
			$totalVat				= (float)($totalVat+$productnetIVA);				  
		?>
		<tr>
			<td><?php echo $sellerSKU;?></td>
			<td><?php echo $itemData['Title'];?></td>
			<td><?php echo $qty;?></td>
			<td><?php echo $currencySymbol;?><?php echo number_format($itemPrice,2);?><?php if($showDoulCurrency):?><p class="style1">lev1<?php echo number_format($itemPrice*$lvlConverPrice,2);?></p><?php endif;?></td>
			<td><?php echo $currencySymbol;?><?php echo number_format($itemGrossPrice,2);?><?php if($showDoulCurrency):?><p class="style1">lev1<?php echo number_format($itemGrossPrice*$lvlConverPrice,2);?></p><?php endif;?></td>			
		</tr>
	<?php } ?>
	
    <tr>
    <td height="200">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>

 </div>
 <!--section2 end--> 

<!--section3--> 
<div class="section3">
 <div class="left-colm">
   <div class="box1">
     <p><?php echo Yii::t("invoice","VAT Exemption Article");?><?php if($showDoulCurrency):?> / Неначисляване ДДС<?php endif;?></p>
	 <br>
	 <p><?php echo Yii::t("invoice","reverse charge art. 21.c.2");?></p>
   </div>
      <div class="box2">
     <p class="style2"> <?php echo Yii::t("invoice","Modo di pagamento");?><?php if($showDoulCurrency):?> / Начин на плащане<?php endif;?></p>
	 <p><?php echo Yii::t("invoice","Banca");?><?php if($showDoulCurrency):?> / Банка <?php endif;?> <?php echo $companyModel->bank_info;?></p>
   </div>
 </div>
 
 <div class="right-colm">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th>&nbsp;</th>
    <th><?php echo $amazonOrderModel->order_currency;?></th>
	
	<?php if($showDoulCurrency):?><th>LEV</th><?php endif;?>
  </tr>

    <tr>
    <td><?php echo Yii::t("invoice","Subtotal");?><?php if($showDoulCurrency):?> / Дан. Осн <?php endif;?></td>
    <td><?php 
	$orderTotal = $amazonOrderModel->total_amount;
	$netOrderAmount = $orderTotal -$totalVat; 
	echo number_format($netOrderAmount,2);?></td>
	<?php if($showDoulCurrency):?><td><?php echo number_format($netOrderAmount*$lvlConverPrice,2);?></td><?php endif;?>
    
  </tr>
   <tr>
    <td><?php echo Yii::t("invoice","VAT Amount");?> <?php if($showDoulCurrency):?> / ДДС <?php endif;?> <?php echo $vatPer;?>%</td>
    <td><?php echo number_format($totalVat,2);?></td>
	<?php if($showDoulCurrency):?><td><?php echo number_format($totalVat*$lvlConverPrice,2);?></td><?php endif;?>

  </tr>
      <tr>
    <td><?php echo Yii::t("invoice","Discount");?> <?php if($showDoulCurrency):?> / отстъпка <?php endif;?></td>
    <td>-<?php echo number_format($discountAmount,2);?></td>
	<?php if($showDoulCurrency):?><td>-<?php echo number_format($discountAmount*$lvlConverPrice,2);?></td><?php endif;?>
  </tr>
      <tr>
    <td><?php echo Yii::t("invoice","Net Amount");?><?php if($showDoulCurrency):?> / Сума нето <?php endif;?></td>
    <td><?php	$orderTotal = $orderTotal -$discountAmount;
	echo number_format($orderTotal,2);?></td>
	<?php if($showDoulCurrency):?><td><?php echo number_format($orderTotal*$lvlConverPrice,2);?></td><?php endif;?>   
  </tr>
    <tr>
    <td colspan="3" class="total"><?php echo Yii::t("invoice","Net Amount");?><?php if($showDoulCurrency):?> / Сума нето <?php endif;?></td>
  </tr>
   <tr style="">
    <td colspan="2" align="right"><strong><?php echo $amazonOrderModel->order_currency;?></strong></td>
    <td><?php echo number_format($orderTotal,2);?></td>	
  </tr> 
   <?php if($showDoulCurrency):?>
   <tr style="border-bottom:1px solid #ccc;">
    <td colspan="2" align="right"><strong>LEV</strong></td>
    <td><?php echo number_format($orderTotal*$lvlConverPrice,2);?></td>
  </tr>
  <?php endif;?>
</table>
 </div>	 
</div>
<!--section3 end-->
 </div>
 </div>
</div>
<?php
// run after 6 hours
if(php_sapi_name() =='cli') {
echo shell_exec("php yii cronjob/genrate-invoice-number;php yii cronjob/genrate-credit-memo-number");
}else{
	echo "No Access to run this file";
}

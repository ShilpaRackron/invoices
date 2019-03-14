<?php
// run  oncea year on 1-1
if(php_sapi_name() =='cli') {
echo shell_exec("php yii cronjob/reset-invoices-counter;");
} else{
	echo "No Access to run this file";
}

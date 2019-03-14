<?php
// run once a day
if(php_sapi_name() =='cli') {
echo shell_exec("php yii cronjob/send-credit-memo-email; php yii cronjob/send-invoice-email; php yii cronjob/create-inventory-reports; php yii cronjob/create-inventory-adjustment-report;php yii cronjob/create-report-inventory-adjustment");
} else{
	echo "No access to run this file";
}
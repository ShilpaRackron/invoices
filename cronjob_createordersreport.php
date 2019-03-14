<?php
// run twice a day
if(php_sapi_name() =='cli') {
echo shell_exec("php yii cronjob/createordersreport;php yii cronjob/createordersreturnreport;php yii cronjob/sendreports;php yii cronjob/send-inventory-reports");
} else{
	echo "No access to run this file";
}
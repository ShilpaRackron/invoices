<?php
// run after 6 hours
if(php_sapi_name() =='cli') {
echo shell_exec("php yii cronjob/createdailyreport; php yii cronjob/generate-orders-return-report;php yii cronjob/getinventorydata;php yii cronjob/generate-reimbursements-report;");
}else{
	echo "No Access to run this file";
}

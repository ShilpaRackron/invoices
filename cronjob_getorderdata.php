<?php
// run after 4 hours
if(php_sapi_name() =='cli') {
	echo system("php yii cronjob/getorderdata; php yii cronjob/getorderreturndata;php yii cronjob/get-inventory-adjustment-data;php yii cronjob/get-reimbursements-data", $returndata);
} else{
		echo "No access to run this file";
}

<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "amazon_log_info".
 *
 * @property int $id
 * @property int $user_id
 * @property int $log_text
 * @property int $ip_address
 * @property string $log_date
 */
class AmazonLogInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amazon_log_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'log_text'], 'required'],
            [['id', 'user_id'], 'integer'],
            [['log_date', 'log_text','log_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'log_text' => 'Log Text',
            'ip_address' => 'Ip Address',
            'log_date' => 'Log Date',
        ];
    }
	public function insertLogData($log_text){
	  try{
			$logObj = new AmazonLogInfo();
			$logObj->user_id  = Yii::$app->user->id;
			$logObj->log_text  = $log_text;
			$logObj->ip_address  = $_SERVER['SERVER_ADDR'];
			$logObj->log_date  = date("Y-m-d h:i:s");
			if($logObj->save()){
			  //echo "Information saved";
			}
		}catch (Exception $e) {
		}
		return true;	
	}
}

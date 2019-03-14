<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "amazon_report_info".
 *
 * @property string $id
 * @property int $user_id
 * @property string $report_id
 * @property string $report_type
 * @property string $report_status
 * @property string $date_created
 */
class AmazonReportInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amazon_report_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'report_id', 'report_type', 'report_status', 'date_created'], 'required'],
            [['user_id','state'], 'integer'],
            [['report_type'], 'string'],
            [['date_created','start_date','end_date','marketplace'], 'safe'],
            [['report_id', 'report_status'], 'string', 'max' => 255],
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
            'report_id' => 'Report ID',
            'report_type' => 'Report Type',
            'report_status' => 'Report Status',
            'date_created' => 'Date Created',
			'state'=>'State',
			'start_date'=>'From',
			'end_date'=>'To',
			'marketplace'=>'Marketplace'
        ];
    }
	public function getReportInfo($user_id, $reporttype){
		$time = date("Y-m-d H:i:s", strtotime("-10 minutes"));
		$orderData = AmazonReportInfo::find()
				->where(["report_type"=>$reporttype])
				->andWhere(['and', "state=0", "user_id=".$user_id,"date_created <='".$time."'"])->orderBy(['start_date'=>SORT_ASC, 'user_id'=>SORT_ASC])->all();
		//echo $orderData->createCommand()->getRawSql();
		return $orderData;
	}
}

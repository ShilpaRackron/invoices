<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "invoice_cronjob".
 *
 * @property string $id
 * @property int $user_id
 * @property string $cronjob_name
 * @property string $start_time
 * @property string $end_time
 * @property string $status
 */
class InvoiceCronjob extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_cronjob';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'cronjob_name', 'start_time', 'status'], 'required'],
            [['user_id'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['cronjob_name', 'status'], 'string', 'max' => 255],
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
            'cronjob_name' => 'Cronjob Name',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'status' => 'Status',
        ];
    }
}

<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "refund_requests".
 *
 * @property string $id
 * @property int $user_id
 * @property string $transaction_item_id
 * @property string $case_id
 * @property double $refund_amount
 * @property int $is_approved
 * @property string $status
 * @property string $updated_date
 */
class RefundRequests extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'refund_requests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'transaction_item_id'], 'required'],
            [['user_id', 'is_approved'], 'integer'],
            [['refund_amount'], 'number'],
            [['updated_date','purchase_invoice_no','amazon_status'], 'safe'],
            [['transaction_item_id', 'case_id', 'status','purchase_invoice_no','amazon_status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'transaction_item_id' => Yii::t('app', 'Transaction Item ID'),
            'case_id' => Yii::t('app', 'Case ID'),
            'refund_amount' => Yii::t('app', 'Refund Amount'),
            'is_approved' => Yii::t('app', 'Is Approved'),
            'status' => Yii::t('app', 'Status'),
            'updated_date' => Yii::t('app', 'Updated Date'),
			'purchase_invoice_no'=> Yii::t('app', 'Purchase Invoice No'),
			'amazon_status'=> Yii::t('app', 'Amazon Status'),
        ];
    }
	public function getStatus($status) 	{
		$amazon_staus = ["requested_by_amazon"=>"Requested by Amazon","asked_to_accountant"=>"Asked to accountant","sent_to_amazon"=>"Sent to Amazon","re_evaluation_successful"=>"Re-evaluation Successful","re_evaluation_refused"=>"Re-Evaluation Refused"];
		
		return $statusText = ($status != NULL && $status !="")? $amazon_staus[$status]:"";

	}
}

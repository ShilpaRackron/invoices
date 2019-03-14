<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "amazon_reimbursements".
 *
 * @property string $id
 * @property int $user_id
 * @property string $approval_date
 * @property string $reimbursement_id
 * @property string $case_id
 * @property string $amazon_order_id
 * @property string $reason
 * @property string $sku
 * @property int $fnsku
 * @property string $asin
 * @property string $product_name
 * @property string $item_condition
 * @property string $currency_unit
 * @property double $amount_per_unit
 * @property double $amount_total
 * @property int $quantity_reimbursed_cash
 * @property int $quantity_reimbursed_inventory
 * @property int $quantity_reimbursed_total
 * @property string $original_reimbursement_id
 * @property string $original_reimbursement_type
 */
class AmazonReimbursements extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amazon_reimbursements';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'quantity_reimbursed_cash', 'quantity_reimbursed_inventory', 'quantity_reimbursed_total'], 'integer'],
            [['approval_date'], 'safe'],
            [['reason', 'sku', 'fnsku', 'product_name'], 'string'],
            [['amount_per_unit', 'amount_total'], 'number'],
            [['reimbursement_id', 'case_id', 'amazon_order_id', 'asin', 'item_condition', 'original_reimbursement_id', 'original_reimbursement_type'], 'string'],
            [['currency_unit'], 'string', 'max' => 10],
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
            'approval_date' => Yii::t('app', 'Approval Date'),
            'reimbursement_id' => Yii::t('app', 'Reimbursement ID'),
            'case_id' => Yii::t('app', 'Case ID'),
            'amazon_order_id' => Yii::t('app', 'Amazon Order ID'),
            'reason' => Yii::t('app', 'Reason'),
            'sku' => Yii::t('app', 'Sku'),
            'fnsku' => Yii::t('app', 'Fnsku'),
            'asin' => Yii::t('app', 'Asin'),
            'product_name' => Yii::t('app', 'Product Name'),
            'item_condition' => Yii::t('app', 'Item Condition'),
            'currency_unit' => Yii::t('app', 'Currency Unit'),
            'amount_per_unit' => Yii::t('app', 'Amount Per Unit'),
            'amount_total' => Yii::t('app', 'Amount Total'),
            'quantity_reimbursed_cash' => Yii::t('app', 'Quantity Reimbursed Cash'),
            'quantity_reimbursed_inventory' => Yii::t('app', 'Quantity Reimbursed Inventory'),
            'quantity_reimbursed_total' => Yii::t('app', 'Quantity Reimbursed Total'),
            'original_reimbursement_id' => Yii::t('app', 'Original Reimbursement ID'),
            'original_reimbursement_type' => Yii::t('app', 'Original Reimbursement Type'),
        ];
    }

	function checkExistingProduct($sku, $user_id, $reimbursement_id)	{
		$checkData = AmazonReimbursements::findOne(['sku' => $sku, "user_id"=>$user_id,'reimbursement_id'=>$reimbursement_id]);
		if(!empty($checkData)){
			return $checkData;
		}
		return false;	
	}

}

<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\AmazonReimbursements;

/**
 * AmazonReimbursementsSearch represents the model behind the search form of `frontend\models\AmazonReimbursements`.
 */
class AmazonReimbursementsSearch extends AmazonReimbursements
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'fnsku', 'quantity_reimbursed_cash', 'quantity_reimbursed_inventory', 'quantity_reimbursed_total'], 'integer'],
            [['approval_date', 'reimbursement_id', 'case_id', 'amazon_order_id', 'reason', 'sku', 'asin', 'product_name', 'item_condition', 'currency_unit', 'original_reimbursement_id', 'original_reimbursement_type'], 'safe'],
            [['amount_per_unit', 'amount_total'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AmazonReimbursements::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => Yii::$app->user->id,
            'approval_date' => $this->approval_date,
            'fnsku' => $this->fnsku,
            'amount_per_unit' => $this->amount_per_unit,
            'amount_total' => $this->amount_total,
            'quantity_reimbursed_cash' => $this->quantity_reimbursed_cash,
            'quantity_reimbursed_inventory' => $this->quantity_reimbursed_inventory,
            'quantity_reimbursed_total' => $this->quantity_reimbursed_total,
        ]);

        $query->andFilterWhere(['like', 'reimbursement_id', $this->reimbursement_id])
            ->andFilterWhere(['like', 'case_id', $this->case_id])
            ->andFilterWhere(['like', 'amazon_order_id', $this->amazon_order_id])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'asin', $this->asin])
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'item_condition', $this->item_condition])
            ->andFilterWhere(['like', 'currency_unit', $this->currency_unit])
            ->andFilterWhere(['like', 'original_reimbursement_id', $this->original_reimbursement_id])
            ->andFilterWhere(['like', 'original_reimbursement_type', $this->original_reimbursement_type]);

        return $dataProvider;
    }
}

<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\RefundRequests;

/**
 * RefundRequestsSearch represents the model behind the search form of `frontend\models\RefundRequests`.
 */
class RefundRequestsSearch extends RefundRequests
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'is_approved'], 'integer'],
            [['transaction_item_id', 'case_id', 'status', 'updated_date','purchase_invoice_no','amazon_status'], 'safe'],
            [['refund_amount'], 'number'],
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
        $query = RefundRequests::find();

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
            'refund_amount' => $this->refund_amount,
            'is_approved' => $this->is_approved,
            'updated_date' => $this->updated_date,
			'transaction_item_id' => $this->transaction_item_id,
			'purchase_invoice_no'=>$this->purchase_invoice_no,
			'amazon_status'=>$this->amazon_status
        ]);

        $query->andFilterWhere(['like', 'transaction_item_id', $this->transaction_item_id])
            ->andFilterWhere(['like', 'case_id', $this->case_id])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }


	 public function searchData($params, $transaction_id)
    {
        $query = RefundRequests::find();

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
            'refund_amount' => $this->refund_amount,
            'is_approved' => $this->is_approved,
            'updated_date' => $this->updated_date,
			'transaction_item_id' => $transaction_id,
			'purchase_invoice_no'=>$this->purchase_invoice_no,
			'amazon_status'=>$this->amazon_status
        ]);

        $query->andFilterWhere(['like', 'case_id', $this->case_id])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}

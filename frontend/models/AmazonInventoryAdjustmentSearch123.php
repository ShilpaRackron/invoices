<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\AmazonInventoryAdjustment;

/**
 * AmazonInventoryAdjustmentSearch represents the model behind the search form of `frontend\models\AmazonInventoryAdjustment`.
 */
class AmazonInventoryAdjustmentSearch extends AmazonInventoryAdjustment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'quantity', 'user_id'], 'integer'],
            [['adjusted_date', 'transaction_item_id', 'fnsku', 'sku', 'product_name', 'fulfillment_center_id', 'reason', 'disposition'], 'safe'],
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
        $query = AmazonInventoryAdjustment::find();

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
           // 'adjusted_date' => $this->adjusted_date,
            'quantity' => $this->quantity,
            'user_id' => Yii::$app->user->id,
        ]);

        $query->andFilterWhere(['like', 'transaction_item_id', $this->transaction_item_id])
            ->andFilterWhere(['like', 'fnsku', $this->fnsku])
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'fulfillment_center_id', $this->fulfillment_center_id])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'disposition', $this->disposition]);

		if(!empty($this->adjusted_date) && strpos($this->adjusted_date, '-') !== false) {
			list($start_date, $end_date) = explode(' - ', $this->adjusted_date);
			 $query->andFilterWhere(['between', 'DATE(adjusted_date)', $start_date, $end_date]);
		   } 
        return $dataProvider;
    }
}

<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\AmazonProducts;

/**
 * AmazonProductsSearch represents the model behind the search form of `frontend\models\AmazonProducts`.
 */
class AmazonProductsSearch extends AmazonProducts
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['sku', 'asin', 'vat_id', 'product_name', 'comm_code', 'condition_id','price','weight'], 'safe'],
            [['vat_rate', 'vat_value'], 'number'],
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
        $query = AmazonProducts::find();

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
            'vat_rate' => $this->vat_rate,
            'vat_value' => $this->vat_value,
        ]);

        $query->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'asin', $this->asin])
            ->andFilterWhere(['like', 'vat_id', $this->vat_id])
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'comm_code', $this->comm_code])
            ->andFilterWhere(['like', 'condition_id', $this->condition_id]);

        return $dataProvider;
    }
}

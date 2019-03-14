<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\AmazonInventory;

/**
 * AmazonInventorySearch represents the model behind the search form of `frontend\models\AmazonInventory`.
 */
class AmazonInventorySearch extends AmazonInventory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'mfn_fulfillable_quantity', 'afn_warehouse_quantity', 'afn_fulfillable_quantity', 'afn_unsellable_quantity', 'afn_reserved_quantity', 'afn_total_quantity', 'afn_inbound_working_quantity', 'afn_inbound_shipped_quantity', 'afn_inbound_receiving_quantity'], 'integer'],
            [['marketplace', 'sku', 'fnsku', 'asin', 'product_name', 'product_condition', 'mfn_listing_exists', 'afn_listing_exists', 'per_unit_volume', 'import_date'], 'safe'],
            [['price'], 'number'],
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
        $query = AmazonInventory::find()->groupBy(['sku']);

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
            //'id' => $this->id,
            'user_id' => Yii::$app->user->id,
            'price' => $this->price,
            'mfn_fulfillable_quantity' => $this->mfn_fulfillable_quantity,
            'afn_warehouse_quantity' => $this->afn_warehouse_quantity,
            'afn_fulfillable_quantity' => $this->afn_fulfillable_quantity,
            'afn_unsellable_quantity' => $this->afn_unsellable_quantity,
            'afn_reserved_quantity' => $this->afn_reserved_quantity,
            'afn_total_quantity' => $this->afn_total_quantity,
            'afn_inbound_working_quantity' => $this->afn_inbound_working_quantity,
            'afn_inbound_shipped_quantity' => $this->afn_inbound_shipped_quantity,
            'afn_inbound_receiving_quantity' => $this->afn_inbound_receiving_quantity,
            'import_date' => $this->import_date,
        ]);

        $query->andFilterWhere(['=', 'marketplace', "IT"])
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'fnsku', $this->fnsku])
            ->andFilterWhere(['like', 'asin', $this->asin])
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'product_condition', $this->product_condition])
            ->andFilterWhere(['like', 'mfn_listing_exists', $this->mfn_listing_exists])
            ->andFilterWhere(['like', 'afn_listing_exists', $this->afn_listing_exists])
            ->andFilterWhere(['like', 'per_unit_volume', $this->per_unit_volume]);

        return $dataProvider;
    }

	public function searchData($params, $user_id)
    {
        $query = AmazonInventory::find()->groupBy(['sku']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
        $this->load($params);	
		
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }
		
		// grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $user_id          
        ])->andFilterWhere(['=', 'marketplace', $this->marketplace]);
		
		 if(!empty($this->import_date) && strpos($this->import_date, '-') !== false) {
			 list($start_date, $end_date) = explode(' - ', $this->import_date);
			 $query->andFilterWhere(['between', 'DATE(import_date)', $start_date, $end_date]);
		  }		 
          return $dataProvider;
    }

}

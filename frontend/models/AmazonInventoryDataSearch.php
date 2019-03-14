<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\AmazonInventoryData;

/**
 * AmazonInventoryDataSearch represents the model behind the search form of `frontend\models\AmazonInventoryData`.
 */
class AmazonInventoryDataSearch extends AmazonInventoryData
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'total_quantity', 'sellable_quantity', 'unsellable_quantity'], 'integer'],
            [['snapshot_date', 'asin', 'fnsku', 'sku', 'product_name', 'currency', 'import_date'], 'safe'],
            [['your_price', 'sales_price', 'lowest_afn_new_price', 'lowest_mfn_new_price'], 'number'],
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
        $query = AmazonInventoryData::find();

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
            'user_id' => Yii::$app->user->id,           
        ]);
	   

        /* $query->andFilterWhere(['like', 'asin', $this->asin])
            ->andFilterWhere(['like', 'fnsku', $this->fnsku])
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'currency', $this->currency]);
		  */
		 if(!empty($this->snapshot_date) && strpos($this->snapshot_date, '-') !== false) {			
			 list($start_date, $end_date) = explode(' - ', $this->snapshot_date);
			 $query->andFilterWhere(['between', 'DATE(snapshot_date)', $start_date, $end_date]);
		  }
          return $dataProvider;
    }


public function searchData($params, $user_id)
    {
        $query = AmazonInventoryData::find();

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
            'user_id' => $user_id          
        ]);
		
		 if(!empty($this->snapshot_date) && strpos($this->snapshot_date, '-') !== false) {
			 list($start_date, $end_date) = explode(' - ', $this->snapshot_date);
			 $query->andFilterWhere(['between', 'DATE(snapshot_date)', $start_date, $end_date]);
		  }		 
          return $dataProvider;
    }
}

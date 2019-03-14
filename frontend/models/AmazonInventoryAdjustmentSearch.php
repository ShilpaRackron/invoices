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
	// public $reason;

    public function rules()
    {
       return [
            [['quantity', 'user_id'], 'integer'],
            [['adjusted_date', 'transaction_item_id', 'fnsku', 'sku', 'product_name', 'fulfillment_center_id', 'disposition','missing_check'], 'safe'],
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
		

		 if($this->missing_check >0) {
			$query->select(["*, SUM(quantity) as totalqty"]);
		 }
		 //echo $this->fnsku; die();
		 
        // grid filtering conditions
       $query->andFilterWhere([
            'id' => $this->id,
            //'adjusted_date' => $this->adjusted_date,
			'fnsku' => $this->fnsku,
			//'reason' => $this->reason,
            'quantity' => $this->quantity,
            'user_id' => Yii::$app->user->id,
        ]);
		  
        $query->andFilterWhere(['=', 'transaction_item_id', $this->transaction_item_id])
           // ->andFilterWhere(['like', 'fnsku', $this->fnsku])
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'fulfillment_center_id', $this->fulfillment_center_id])
            //->andFilterWhere(['like', 'disposition', $this->disposition])
			->orderBy('fnsku');
			//->groupBy(['fnsku']);
		
		 if(isset($params['AmazonInventoryAdjustmentSearch']) && (is_array($params['AmazonInventoryAdjustmentSearch']['reason']) && $params['AmazonInventoryAdjustmentSearch']['reason'][0] !="") && $this->missing_check=="") {

			$reason =$params['AmazonInventoryAdjustmentSearch']['reason'];			
			$query->andFilterWhere(['in', 'reason', $reason]);
			$this->reason = implode(",", $reason);
		}  
		  
		if(empty($this->adjusted_date)) {
			$end_date = date("Y-m-d");
			$start_date = 	date("Y-m-d", strtotime("-30 days"));
			$query->andFilterWhere(['between', 'DATE(adjusted_date)', $start_date, $end_date]);
		}	
		elseif(!empty($this->adjusted_date) && strpos($this->adjusted_date, '-') !== false) {
			list($start_date, $end_date) = explode(' - ', $this->adjusted_date);

			 $dt				=  \DateTime::createFromFormat('d-m-Y', $start_date);	
			 $start_date		= $dt->format('Y-m-d');

			 $dt1				=  \DateTime::createFromFormat('d-m-Y', $end_date);	
			 $end_date		= $dt1->format('Y-m-d');

			$query->andFilterWhere(['between', 'DATE(adjusted_date)', $start_date, $end_date]);
		}
		
		if($this->missing_check==1){
			$query->andFilterWhere(['in', 'reason', ['M','F']]);
			$this->reason = 'M,F';
		    $query->having("SUM(quantity) < 0");
		    $query->groupBy(['fnsku']);
			
		 }elseif($this->missing_check==2){
			 $query->andFilterWhere(['in', 'reason', ['M','F']]);
			 //$this->reason = ['M','F'];
			 $this->reason = 'M,F';
		    $query->having("SUM(quantity)=0");
		    $query->groupBy(['fnsku']);
		 }
		 elseif($this->missing_check==3){
			$query->andFilterWhere(['in', 'reason', ['M','F']]);
			//$this->reason = ['M','F'];
			$this->reason = 'M,F';
		    $query->having("SUM(quantity) > 0");
		    $query->groupBy(['fnsku']);
		 }
		
        return $dataProvider;
    }


	public function getChildSearch($params, $fnsku)
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
		//$query->select(['SUM(quantity) AS totalQty, *']);
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'reason' => $this->reason,
            'quantity' => $this->quantity,
			'fnsku' => $fnsku,
            'user_id' => Yii::$app->user->id,
        ]);

        $query->andFilterWhere(['like', 'transaction_item_id', $this->transaction_item_id])
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'fulfillment_center_id', $this->fulfillment_center_id])
           // ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'disposition', $this->disposition]);

		if(!empty($this->adjusted_date) && strpos($this->adjusted_date, '-') !== false) {
				
			list($start_date, $end_date) = explode(' - ', $this->adjusted_date);

			 $query->andFilterWhere(['between', 'DATE(adjusted_date)', $start_date, $end_date]);
		} 
        return $dataProvider;
    }
	
}

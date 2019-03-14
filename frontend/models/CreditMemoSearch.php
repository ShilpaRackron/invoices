<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\CreditMemo;
use frontend\models\AmazonOrders;


/**
 * CreditMemoSearch represents the model behind the search form of `frontend\models\CreditMemo`.
 */
class CreditMemoSearch extends CreditMemo
{
    /**
     * {@inheritdoc}
     */
	 public $year;
	 public $month;
	 public $country_code;
	 public $buyer_name;

    public function rules()
    {
        return [
            [['user_id', 'qty_return'], 'integer'],
            [['buyer_vat', 'seller_sku', 'markeplace', 'seller_order_id', 'order_adjustment_item_id', 'amazon_order_id', 'reason', 'status', 'license_plate_number', 'customer_comments', 'product_sku', 'product_name'], 'string'],
            [['total_amount_refund'], 'number'],
            [['invoice_number','return_date','month','year','credit_memo_no'], 'safe'],
            [['currency_code', 'asin', 'fnsku', 'fulfillment_center_id', 'detailed_disposition'], 'string', 'max' => 255],
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
        $query = CreditMemo::find()->Where(['not', ['credit_memo_no' => null]])->orderBy(['credit_memo_no' => SORT_DESC]);

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
            'total_amount_refund' => $this->total_amount_refund,
            //'return_date' => $this->return_date,
        ]);		
        $query->andFilterWhere(['like', 'amazon_order_id', trim($this->amazon_order_id)]);
		$query->andFilterWhere(['=', 'credit_memo_no', trim($this->credit_memo_no)]);
		$query->andFilterWhere(['=', 'invoice_number', trim($this->invoice_number)]);
		//$query->andFilterWhere(['=', 'markeplace', $this->markeplace]);
		if (isset($this->year) && $this->year >0){
				$query->andWhere('year(return_date) ="'.$this->year.'"');
		}
		if (isset($this->month) && $this->month >0){
				$query->andWhere('MONTH(return_date) = "'.$this->month.'"');
		}
		if(!empty($this->return_date) && strpos($this->return_date, '-') !== false) {
			list($start_date, $end_date) = explode(' - ', $this->return_date);
			 $query->andFilterWhere(['between', 'return_date', $start_date, $end_date]);
		}
        return $dataProvider;
    }

	 public function searchExport($params)
    {
        $query = CreditMemo::find()->Where(['not', ['credit_memo_no' => null]])->orderBy(['credit_memo_no' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id
        ]);
		$month = date("m", strtotime("-1 month"));
		$query->andWhere('year(return_date) ="'.date("Y").'"');
		$query->andWhere('MONTH(return_date) = "'.$month.'"');
        return $dataProvider;
    }

	 public function searchExportReports($params, $user_id)
    {
        $query = CreditMemo::find()->Where(['not', ['credit_memo_no' => null]])->orderBy(['credit_memo_no' => SORT_ASC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $user_id
        ]);
		if(!empty($this->return_date) && strpos($this->return_date, '-') !== false) {
				
			list($start_date, $end_date) = explode(' - ', $this->return_date);

			 $query->andFilterWhere(['between', 'DATE(return_date)', $start_date, $end_date]);
		} 

        return $dataProvider;
    }
	
}

<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\AmazonOrders;

/**
 * AmazonOrdersSearch represents the model behind the search form of `frontend\models\AmazonOrders`.
 */
class AmazonOrdersSearch extends AmazonOrders
{
    /**
     * {@inheritdoc}
     */
	 public $year;
	 public $month;
    public function rules()
    {
        return [          
            [['latest_ship_date', 'purchase_date', 'last_update_date', 'latest_delivery_date', 'earliest_delivery_date', 'earliest_ship_date', 'marketplace_id', 'protocol_invoice_number', 'buyer_email','is_replacement_order', 'is_business_order', 'is_premium_order','year','month','order_import_date'], 'safe'],
            [['order_type', 'sales_channel', 'payment_method_detail', 'buyer_name', 'buyer_vat', 'fulfillment_channel', 'payment_method', 'customer_name', 'shipment_category', 'ship_address_1', 'ship_address_2', 'ship_address_3', 'tracking_number', 'carrier', 'fulfillment_center_id'], 'string'],
            [['total_amount', 'item_promotion_discount', 'ship_promotion_discount', 'item_price', 'item_tax', 'shipping_price', 'shipping_tax', 'gift_wrap_price', 'gift_wrap_tax'], 'number'],
            [['invoice_number', 'protocol_invoice_number', 'buyer_email', 'amazon_order_id', 'ship_service_level', 'order_status', 'shipped_by_amazon_tfm', 'marketplace_id', 'city', 'state_or_region', 'address_2', 'merchant_order_id', 'shipment_id', 'shipment_item_id', 'amazon_order_item_id', 'merchant_order_item_id', 'ship_city', 'ship_state', 'ship_postal_code', 'ship_country'], 'string', 'max' => 255],
            [['address_type'], 'string', 'max' => 100],
            [['order_currency', 'country_code'], 'string', 'max' => 10],
            [['postal_code', 'phone', 'ship_phone_number'], 'string', 'max' => 20],			
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
       // $query = AmazonOrders::find()->Where(['not', ['invoice_number' => null]])->orderBy(['invoice_number' => SORT_DESC]);
	    $query = AmazonOrders::find()->Where(['not', ['invoice_number' => null]]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->setSort([
        'attributes' => [
            'order_import_date' => [
                'asc' => ['order_import_date' => SORT_ASC],
                'desc' => ['order_import_date' => SORT_DESC],
                'default' => SORT_ASC
            ],
            'amazon_order_id' => [
                'asc' => ['amazon_order_id' => SORT_ASC],
                'desc' => ['amazon_order_id' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'invoice_number' => [
                'asc' => ['invoice_number' => SORT_ASC],
                'desc' => ['invoice_number' => SORT_DESC],
                'default' => SORT_DESC,
            ],
			'purchase_date' => [
                'asc' => ['purchase_date' => SORT_ASC],
                'desc' => ['purchase_date' => SORT_DESC],
                'default' => SORT_DESC,
            ],
			'customer_name' => [
                'asc' => ['customer_name' => SORT_ASC],
                'desc' => ['customer_name' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'latest_ship_date' => [
                'asc' => ['latest_ship_date' => SORT_ASC],
                'desc' => ['latest_ship_date' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'item_price' => [
                'asc' => ['item_price' => SORT_ASC],
                'desc' => ['item_price' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'country_code' => [
                'asc' => ['country_code' => SORT_ASC],
                'desc' => ['country_code' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'sales_channel' => [
                'asc' => ['sales_channel' => SORT_ASC],
                'desc' => ['sales_channel' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'fulfillment_channel' => [
                'asc' => ['fulfillment_channel' => SORT_ASC],
                'desc' => ['fulfillment_channel' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'fulfillment_center_id' => [
                'asc' => ['fulfillment_center_id' => SORT_ASC],
                'desc' => ['fulfillment_center_id' => SORT_DESC],
                'default' => SORT_ASC,
            ],
        ],
        'defaultOrder' => [
            'invoice_number' => SORT_DESC
        ]
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
			'invoice_number' => trim($this->invoice_number),
			//'order_import_date'=>$this->order_import_date,
            'latest_ship_date' => $this->latest_ship_date,
            //'purchase_date' => $this->purchase_date,
            'last_update_date' => $this->last_update_date,
            'number_of_items_shipped' => $this->number_of_items_shipped,
            'latest_delivery_date' => $this->latest_delivery_date,
            'number_of_items_unshipped' => $this->number_of_items_unshipped,
            'earliest_delivery_date' => $this->earliest_delivery_date,
            'total_amount' => $this->total_amount,
            'earliest_ship_date' => $this->earliest_ship_date,
            'fulfillment_center_id' => $this->fulfillment_center_id,
			'amazon_order_id' => trim($this->amazon_order_id),
        ]);

        $query->andFilterWhere(['=', 'order_type', $this->order_type])
            ->andFilterWhere(['=', 'buyer_email', trim($this->buyer_email)])
           // ->andFilterWhere(['=', 'amazon_order_id', $this->amazon_order_id])
            ->andFilterWhere(['like', 'is_replacement_order', $this->is_replacement_order])
            ->andFilterWhere(['like', 'ship_service_level', $this->ship_service_level])
            ->andFilterWhere(['like', 'order_status', $this->order_status])
            ->andFilterWhere(['like', 'sales_channel', $this->sales_channel])           
            ->andFilterWhere(['like', 'payment_method_detail', $this->payment_method_detail])
            ->andFilterWhere(['like', 'buyer_name', trim($this->buyer_name)])
            ->andFilterWhere(['like', 'buyer_vat', $this->buyer_vat])          
            ->andFilterWhere(['like', 'order_currency', $this->order_currency])
            ->andFilterWhere(['like', 'marketplace_id', $this->marketplace_id])
            ->andFilterWhere(['=', 'fulfillment_channel', $this->fulfillment_channel])            
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'country_code', $this->country_code])
            ->andFilterWhere(['like', 'customer_name', trim($this->customer_name)])
            ->andFilterWhere(['like', 'address_2', $this->address_2])
            ->andFilterWhere(['like', 'shipment_category', $this->shipment_category]);

		if (isset($this->year) && $this->year >0){
				$query->andWhere('year(order_import_date) ="'.$this->year.'"');
				//$query->andWhere('year(purchase_date) ="'.$this->year.'"');
			}else{
				//$query->andWhere('year(order_import_date) ="'.date('Y').'"');
				//$query->andWhere('year(purchase_date) ="'.date('Y').'"');
			}
			if (isset($this->month) && $this->month >0){

				$query->andWhere('MONTH(order_import_date) = "'.$this->month.'"');
				//$query->andWhere('MONTH(purchase_date) = "'.$this->month.'"');
			}
			if(!empty($this->purchase_date) && strpos($this->purchase_date, '-') !== false) {
				
			list($start_date, $end_date) = explode(' - ', $this->purchase_date);
			 $query->andFilterWhere(['between', 'DATE(order_import_date)', $start_date, $end_date]);
			 //$query->andFilterWhere(['between', 'DATE(purchase_date)', $start_date, $end_date]);
		   }
		  if(!empty($this->order_import_date) && strpos($this->order_import_date, '-') !== false) {
				
			list($start_date, $end_date) = explode(' - ', $this->order_import_date);

			 $query->andFilterWhere(['between', 'DATE(order_import_date)', $start_date, $end_date]);
		   } 
		   return $dataProvider;
    }

	/* public function searchexport($params)
    {
       // $query = AmazonOrders::find()->Where(['not', ['invoice_number' => null]])->orderBy(['invoice_number' => SORT_DESC]);
	    $query = AmazonOrders::find()->Where(['not', ['invoice_number' => null]]);

		


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->setSort([
        'attributes' => [
            'order_import_date' => [
                'asc' => ['order_import_date' => SORT_ASC],
                'desc' => ['order_import_date' => SORT_DESC],
                'default' => SORT_ASC
            ],
            'amazon_order_id' => [
                'asc' => ['amazon_order_id' => SORT_ASC],
                'desc' => ['amazon_order_id' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'invoice_number' => [
                'asc' => ['invoice_number' => SORT_ASC],
                'desc' => ['invoice_number' => SORT_DESC],
                'default' => SORT_DESC,
            ],
			'purchase_date' => [
                'asc' => ['purchase_date' => SORT_ASC],
                'desc' => ['purchase_date' => SORT_DESC],
                'default' => SORT_DESC,
            ],
			'customer_name' => [
                'asc' => ['customer_name' => SORT_ASC],
                'desc' => ['customer_name' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'latest_ship_date' => [
                'asc' => ['latest_ship_date' => SORT_ASC],
                'desc' => ['latest_ship_date' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'item_price' => [
                'asc' => ['item_price' => SORT_ASC],
                'desc' => ['item_price' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'country_code' => [
                'asc' => ['country_code' => SORT_ASC],
                'desc' => ['country_code' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'sales_channel' => [
                'asc' => ['sales_channel' => SORT_ASC],
                'desc' => ['sales_channel' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'fulfillment_channel' => [
                'asc' => ['fulfillment_channel' => SORT_ASC],
                'desc' => ['fulfillment_channel' => SORT_DESC],
                'default' => SORT_ASC,
            ],
			'fulfillment_center_id' => [
                'asc' => ['fulfillment_center_id' => SORT_ASC],
                'desc' => ['fulfillment_center_id' => SORT_DESC],
                'default' => SORT_ASC,
            ],
        ],
        'defaultOrder' => [
            'invoice_number' => SORT_DESC
        ]
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
			'invoice_number' => $this->invoice_number,
			//'order_import_date'=>$this->order_import_date,
            'latest_ship_date' => $this->latest_ship_date,
            //'purchase_date' => $this->purchase_date,
            'last_update_date' => $this->last_update_date,
            'number_of_items_shipped' => $this->number_of_items_shipped,
            'latest_delivery_date' => $this->latest_delivery_date,
            'number_of_items_unshipped' => $this->number_of_items_unshipped,
            'earliest_delivery_date' => $this->earliest_delivery_date,
            'total_amount' => $this->total_amount,
            'earliest_ship_date' => $this->earliest_ship_date,
            'fulfillment_center_id' => $this->fulfillment_center_id,
			'amazon_order_id' => $this->amazon_order_id,
        ]);

        $query->andFilterWhere(['=', 'order_type', $this->order_type])
            ->andFilterWhere(['=', 'buyer_email', $this->buyer_email])
           // ->andFilterWhere(['=', 'amazon_order_id', $this->amazon_order_id])
            ->andFilterWhere(['like', 'is_replacement_order', $this->is_replacement_order])
            ->andFilterWhere(['like', 'ship_service_level', $this->ship_service_level])
            ->andFilterWhere(['like', 'order_status', $this->order_status])
            ->andFilterWhere(['like', 'sales_channel', $this->sales_channel])           
            ->andFilterWhere(['like', 'payment_method_detail', $this->payment_method_detail])
            ->andFilterWhere(['like', 'buyer_name', $this->buyer_name])
            ->andFilterWhere(['like', 'buyer_vat', $this->buyer_vat])          
            ->andFilterWhere(['like', 'order_currency', $this->order_currency])
            ->andFilterWhere(['like', 'marketplace_id', $this->marketplace_id])
            ->andFilterWhere(['=', 'fulfillment_channel', $this->fulfillment_channel])            
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'country_code', $this->country_code])
            ->andFilterWhere(['like', 'customer_name', $this->customer_name])
            ->andFilterWhere(['like', 'address_2', $this->address_2])
            ->andFilterWhere(['like', 'shipment_category', $this->shipment_category]);

		if (isset($this->year) && $this->year >0){
				//$query->andWhere('year(order_import_date) ="'.$this->year.'"');
				$query->andWhere('year(order_import_date) ="'.$this->year.'"');
			}else{
				//$query->andWhere('year(order_import_date) ="'.date('Y').'"');
				$query->andWhere('year(order_import_date) ="'.date('Y').'"');
			}
			if (isset($this->month) && $this->month >0){

				//$query->andWhere('MONTH(order_import_date) = "'.$this->month.'"');
				$query->andWhere('MONTH(order_import_date) = "'.$this->month.'"');
			}
			if(!empty($this->purchase_date) && strpos($this->purchase_date, '-') !== false) {
				
			list($start_date, $end_date) = explode(' - ', $this->purchase_date);

			 $query->andFilterWhere(['between', 'DATE(order_import_date)', $start_date, $end_date]);
		   }
		   return $dataProvider;
    }  */

	public function searchExport($params)
    {
      
	    $query = AmazonOrders::find()->Where(['not', ['invoice_number' => null]])->orderBy(['invoice_number' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
        $this->load($params);
        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' =>$this->user_id
		]);
		$month = date("m", strtotime("-1 month"));
		$query->andWhere('year(order_import_date) ="'.date('Y').'"');
		$query->andWhere('MONTH(order_import_date) = "'.$month.'"');
		return $dataProvider;
    }

	public function searchExportReports($params, $user_id)
    {
      
	    $query = AmazonOrders::find()->Where(['not', ['invoice_number' => null]])->orderBy(['invoice_number' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
        $this->load($params);
        // grid filtering conditions
		
        $query->andFilterWhere([
            'user_id' =>$user_id
		]);

		if(!empty($this->order_import_date) && strpos($this->order_import_date, '-') !== false) {
				
			list($start_date, $end_date) = explode(' - ', $this->order_import_date);

			 $query->andFilterWhere(['between', 'DATE(order_import_date)', $start_date, $end_date]);
		} 
		return $dataProvider;
    }
}

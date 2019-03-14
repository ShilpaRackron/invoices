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
            [['id', 'user_id', 'quantity', 'shop_category1', 'pending_quantity'], 'integer'],
            [['marketplace_code', 'item_name', 'item_description', 'listing_id', 'seller_sku', 'open_date', 'image_url', 'item_is_marketplace', 'item_note', 'item_condition', 'shop_browse_path', 'shop_storefront_feature', 'asin1', 'asin2', 'asin3', 'will_ship_internationally', 'expedited_shipping', 'product_id', 'bid_for_featured_placement', 'add_delete', 'fulfillment_channel', 'merchant_shipping_group', 'created_date'], 'safe'],
            [['price', 'shop_shipping_fee', 'business_price'], 'number'],
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
        $query = AmazonInventory::find();

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
            'price' => $this->price,
            'quantity' => $this->quantity,
            'open_date' => $this->open_date,
            'shop_shipping_fee' => $this->shop_shipping_fee,
            'shop_category1' => $this->shop_category1,
            'pending_quantity' => $this->pending_quantity,
            'business_price' => $this->business_price,
            'created_date' => $this->created_date,
        ]);

        $query->andFilterWhere(['like', 'marketplace_code', $this->marketplace_code])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'item_description', $this->item_description])
            ->andFilterWhere(['like', 'listing_id', $this->listing_id])
            ->andFilterWhere(['like', 'seller_sku', $this->seller_sku])
            ->andFilterWhere(['like', 'image_url', $this->image_url])
            ->andFilterWhere(['like', 'item_is_marketplace', $this->item_is_marketplace])
            ->andFilterWhere(['like', 'item_note', $this->item_note])
            ->andFilterWhere(['like', 'item_condition', $this->item_condition])
            ->andFilterWhere(['like', 'shop_browse_path', $this->shop_browse_path])
            ->andFilterWhere(['like', 'shop_storefront_feature', $this->shop_storefront_feature])
            ->andFilterWhere(['like', 'asin1', $this->asin1])
            ->andFilterWhere(['like', 'asin2', $this->asin2])
            ->andFilterWhere(['like', 'asin3', $this->asin3])
            ->andFilterWhere(['like', 'will_ship_internationally', $this->will_ship_internationally])
            ->andFilterWhere(['like', 'expedited_shipping', $this->expedited_shipping])
            ->andFilterWhere(['like', 'product_id', $this->product_id])
            ->andFilterWhere(['like', 'bid_for_featured_placement', $this->bid_for_featured_placement])
            ->andFilterWhere(['like', 'add_delete', $this->add_delete])
            ->andFilterWhere(['like', 'fulfillment_channel', $this->fulfillment_channel])
            ->andFilterWhere(['like', 'merchant_shipping_group', $this->merchant_shipping_group]);

        return $dataProvider;
    }
}

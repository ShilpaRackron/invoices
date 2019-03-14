<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "amazon_inventory".
 *
 * @property string $id
 * @property int $user_id
 * @property string $marketplace_code
 * @property string $item_name
 * @property string $item_description
 * @property string $listing_id
 * @property string $seller_sku
 * @property double $price
 * @property int $quantity
 * @property string $open_date
 * @property string $image_url
 * @property string $item_is_marketplace
 * @property double $shop_shipping_fee
 * @property string $item_note
 * @property string $item_condition
 * @property int $shop_category1
 * @property string $shop_browse_path
 * @property string $shop_storefront_feature
 * @property string $asin1
 * @property string $asin2
 * @property string $asin3
 * @property string $will_ship_internationally
 * @property string $expedited_shipping
 * @property string $product_id
 * @property string $bid_for_featured_placement
 * @property string $add_delete
 * @property int $pending_quantity
 * @property string $fulfillment_channel
 * @property string $merchant_shipping_group
 * @property double $business_price
 * @property string $created_date
 */
class AmazonInventory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amazon_inventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'quantity', 'shop_category1', 'pending_quantity'], 'integer'],
            [['item_name', 'item_description', 'listing_id', 'seller_sku', 'image_url', 'item_is_marketplace', 'item_note', 'shop_browse_path', 'shop_storefront_feature', 'will_ship_internationally', 'expedited_shipping', 'bid_for_featured_placement', 'add_delete'], 'string'],
            [['price', 'shop_shipping_fee', 'business_price'], 'number'],
            [['marketplace_code', 'item_name', 'item_description', 'listing_id', 'seller_sku', 'price', 'quantity', 'open_date', 'image_url', 'item_is_marketplace', 'shop_shipping_fee', 'item_note', 'item_condition', 'shop_category1', 'shop_browse_path', 'shop_storefront_feature', 'asin1', 'asin2', 'asin3', 'will_ship_internationally', 'expedited_shipping', 'product_id', 'bid_for_featured_placement', 'add_delete', 'pending_quantity', 'fulfillment_channel', 'merchant_shipping_group', 'business_price', 'created_date'], 'safe'],
            [['marketplace_code', 'item_condition', 'asin1', 'asin2', 'asin3', 'product_id', 'fulfillment_channel', 'merchant_shipping_group'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'marketplace_code' => 'Marketplace',
            'item_name' => 'Name',
            'item_description' => 'Description',
            'listing_id' => 'Listing ID',
            'seller_sku' => 'Sku',
            'price' => 'Price',
            'quantity' => 'Qty',
            'open_date' => 'Open Date',
            'image_url' => 'Image Url',
            'item_is_marketplace' => 'Item Is Marketplace',
            'shop_shipping_fee' => 'Shop Shipping Fee',
            'item_note' => 'Note',
            'item_condition' => 'Item Condition',
            'shop_category1' => 'Shop Category1',
            'shop_browse_path' => 'Shop Browse Path',
            'shop_storefront_feature' => 'Shop Storefront Feature',
            'asin1' => 'Asin1',
            'asin2' => 'Asin2',
            'asin3' => 'Asin3',
            'will_ship_internationally' => 'Will Ship Internationally',
            'expedited_shipping' => 'Expedited Shipping',
            'product_id' => 'Product ID',
            'bid_for_featured_placement' => 'Bid For Featured Placement',
            'add_delete' => 'Add Delete',
            'pending_quantity' => 'Pending Quantity',
            'fulfillment_channel' => 'Fulfillment Channel',
            'merchant_shipping_group' => 'Merchant Shipping Group',
            'business_price' => 'Business Price',
            'created_date' => 'Created Date',
        ];
    }

	public function checkExistingProduct($productSku, $user_id, $fulfillment_channel){
		$checkData = AmazonInventory::findOne(['seller_sku' => $productSku, "user_id"=>$user_id,'fulfillment_channel'=>$fulfillment_channel]);
		return $checkData;
	}
}

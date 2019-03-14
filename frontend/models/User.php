<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $amazon_token
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $superadmin
 * @property string $registration_ip
 * @property string $bind_to_ip
 * @property int $email_confirmed
 * @property string $confirmation_token
 * @property string $avatar
 * @property string $name
 *
 * @property Auth[] $auths
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property Media[] $media
 * @property Media[] $media0
 * @property MediaAlbum[] $mediaAlbums
 * @property MediaAlbum[] $mediaAlbums0
 * @property MediaCategory[] $mediaCategories
 * @property MediaCategory[] $mediaCategories0
 * @property Menu[] $menus
 * @property Menu[] $menus0
 * @property MenuLink[] $menuLinks
 * @property MenuLink[] $menuLinks0
 * @property Post[] $posts
 * @property Post[] $posts0
 * @property PostCategory[] $postCategories
 * @property PostCategory[] $postCategories0
 * @property PostTag[] $postTags
 * @property PostTag[] $postTags0
 * @property Seo[] $seos
 * @property Seo[] $seos0
 * @property UserSetting[] $userSettings
 * @property UserVisitLog[] $userVisitLogs
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['amazon_token', 'avatar', 'name'], 'string'],
            [['status', 'created_at', 'updated_at', 'superadmin', 'email_confirmed'], 'integer'],
            [['auth_key'], 'string', 'max' => 32],
            [['registration_ip'], 'string', 'max' => 15],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
			[['import_initial_orders', 'import_initial_creditmemo','selected_invoice_fields','selected_creditnote_fields','import_initial_inventory_adjustment'],'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'amazon_token' => 'Amazon Token',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'superadmin' => 'Superadmin',
            'registration_ip' => 'Registration Ip',
            'bind_to_ip' => 'Bind To Ip',
            'email_confirmed' => 'Email Confirmed',
            'confirmation_token' => 'Confirmation Token',
            'avatar' => 'Avatar',
            'name' => 'Name',
			'selected_invoice_fields'=>'Invoice Selected Fields',
			'selected_creditnote_fields'=>'Creditnote Selected Fields',
			'import_initial_inventory_adjustment'=>'Import Initial Inventory Adjustment'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuths()
    {
        return $this->hasMany(Auth::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('auth_assignment', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasMany(Media::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia0()
    {
        return $this->hasMany(Media::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaAlbums()
    {
        return $this->hasMany(MediaAlbum::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaAlbums0()
    {
        return $this->hasMany(MediaAlbum::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaCategories()
    {
        return $this->hasMany(MediaCategory::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaCategories0()
    {
        return $this->hasMany(MediaCategory::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(Menu::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenus0()
    {
        return $this->hasMany(Menu::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuLinks()
    {
        return $this->hasMany(MenuLink::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuLinks0()
    {
        return $this->hasMany(MenuLink::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosts0()
    {
        return $this->hasMany(Post::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostCategories()
    {
        return $this->hasMany(PostCategory::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostCategories0()
    {
        return $this->hasMany(PostCategory::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostTags()
    {
        return $this->hasMany(PostTag::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostTags0()
    {
        return $this->hasMany(PostTag::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeos()
    {
        return $this->hasMany(Seo::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeos0()
    {
        return $this->hasMany(Seo::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserSettings()
    {
        return $this->hasMany(UserSetting::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserVisitLogs()
    {
        return $this->hasMany(UserVisitLog::className(), ['user_id' => 'id']);
    }
}

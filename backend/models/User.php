<?php

namespace backend\models;

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
            [['username', 'auth_key', 'amazon_token', 'password_hash', 'email', 'created_at', 'updated_at', 'name'], 'required'],
            [['amazon_token', 'avatar', 'name'], 'string'],
            [['status', 'created_at', 'updated_at', 'superadmin', 'email_confirmed'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'bind_to_ip', 'confirmation_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['registration_ip'], 'string', 'max' => 15],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
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
            'superadmin' => 'Is Superadmin',
            'registration_ip' => 'Registration Ip',
            'bind_to_ip' => 'Bind To Ip',
            'email_confirmed' => 'Email Confirmed',
            'confirmation_token' => 'Confirmation Token',
            'avatar' => 'Avatar',
            'name' => 'Name',
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
	public function getDateData($date){
		return date("Y-m-d",$date);	
	}
	public function getUserStatus($status){
		
	   return ($status==1)?"Active":"Deactive";	
	}
	public function isEmailConfirmed($email){
		
	   return ($email==1)?"Confirmed":"Not Confirmed";	
	}
}

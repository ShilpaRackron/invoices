<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "creditmemo_settings".
 *
 * @property string $id
 * @property int $user_id
 * @property string $creditmemo_prefix
 * @property string $creditmemo_sufix
 * @property int $creditmemo_length
 */
class CreditmemoSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'creditmemo_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'creditmemo_length','creditmemo_counter'], 'integer'],
            [['creditmemo_prefix', 'creditmemo_sufix'], 'string', 'max' => 255],
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
            'creditmemo_prefix' => 'Creditmemo Prefix',
            'creditmemo_sufix' => 'Creditmemo Sufix',
            'creditmemo_length' => 'Creditmemo Length',
			'creditmemo_counter'=>'Counter'
        ];
    }

	public static function getModel($user_id){
	   $creditmemoSettingsObj = CreditmemoSettings::findOne(['user_id' => $user_id]);
		if(!empty($creditmemoSettingsObj))
			return $creditmemoSettingsObj;
		else
			return new CreditmemoSettings(); 
	}
	public function getCreditmemoNumber($user_id){

		$creditmemoSettingsObj = CreditmemoSettings::findOne(['user_id' => $user_id]);
		$preFix = $creditmemoSettingsObj->creditmemo_prefix;
		$sufix = $creditmemoSettingsObj->creditmemo_sufix;
		$creditmemo_counter = $creditmemoSettingsObj->creditmemo_counter;		
		$length = $creditmemoSettingsObj->creditmemo_length;

		$preFixText = ($preFix !="")?$preFix.'-':"";
		$sufixText = ($sufix !="")?'-'.$sufix:"";

		$creditmemoNumber =$preFixText.str_pad($creditmemo_counter, $length, "0", STR_PAD_LEFT).$sufixText;
		return $creditmemoNumber;
	}
	public function updateCreditmemoNumber($user_id) {
		try{
		$creditmemoSettingsObj					= CreditmemoSettings::findOne(['user_id' => $user_id]);
		$creditmemo_counter						= (int)$creditmemoSettingsObj->creditmemo_counter;
		$creditmemoSettingsObj->creditmemo_counter	= $creditmemo_counter+1;
			if($creditmemoSettingsObj->save()){
			} 		
		}catch(Exception $e){		
		}
		return true;
	}
	public function resetCounter($user_id){
		// Reset counter for credit memo on 1 days of each year
		$model = CreditmemoSettings::find()->where(['user_id' => $user_id])->one();
		if($model && !empty($model)){
			$model->creditmemo_counter=001;
			$model->save();
		}
		return true;	
	}

}

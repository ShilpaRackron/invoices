<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>  

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            //'auth_key',
            //'amazon_token:ntext',
            //'password_hash',
            //'password_reset_token',
			'name:ntext',
            'email:email',
            
            //'created_at',
			[
			  'attribute' => 'created_at',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->getDateData($model->created_at);
			   },
			],
            //'updated_at',
			[
			  'attribute' => 'updated_at',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->getDateData($model->updated_at);
			   },
			],
            //'superadmin',
            //'registration_ip',
            //'bind_to_ip',
           // 'email_confirmed:email',
			[
			  'attribute' => 'email_confirmed',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->isEmailConfirmed($model->email_confirmed);
			   },
			],
			[
			  'attribute' => 'status',
			  'filter' => false,
			  'format' => 'raw',
			   'value' => function ($model) {
				   return  $model->getUserStatus($model->status);
			   },
			],
					
            //'confirmation_token',
            //'avatar:ntext',
            

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

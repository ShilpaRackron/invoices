<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          //  'id',
			
            'username',
			'name:ntext',
            //'auth_key',
            //'amazon_token:ntext',
           // 'password_hash',
            //'password_reset_token',
			'superadmin',
            'email:email',           
            'created_at',
            'updated_at',            
            'registration_ip',
            'bind_to_ip',
            'email_confirmed:email',
			 'status',

           // 'confirmation_token',
           // 'avatar:ntext',
            
        ],
    ]) ?>

</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\AmazonLogInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Amazon Log Infos');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amazon-log-info-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'log_text:ntext',
            'ip_address:ntext',
            'log_date',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

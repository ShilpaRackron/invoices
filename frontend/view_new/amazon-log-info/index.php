<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\AmazonLogInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Amazon Log Infos');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
	</div>
<div class="amazon-log-info-index">
<div class="container-fluid mt--7">
      <div class="row">
       <div class="col-xl-12 order-xl-1">
          <div class="card bg-secondary shadow">
            <div class="card-header bg-white border-0">
              <div class="row align-items-center">
                <div class="col-8">
                  <h2 class="mb-0"><?= Html::encode($this->title) ?></h2>
                </div>
               
              </div>
            </div>
    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'showHeader'=>true,

    		'layout' => "{summary}\n{items}\n{pager}",

    		'options' => array('class' => 'table-responsive'),

    		'tableOptions' => array('class' => 'table align-items-center table-flush '),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'log_text:ntext',
            'ip_address:ntext',
            'log_date',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

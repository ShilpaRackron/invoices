<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonOrders */

$this->title = Yii::t('app', 'Create Amazon Orders');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Amazon Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amazon-orders-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

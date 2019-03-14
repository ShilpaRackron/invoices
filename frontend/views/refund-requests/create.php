<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\RefundRequests */

$this->title = Yii::t('app', 'Create Refund Requests');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Refund Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="refund-requests-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

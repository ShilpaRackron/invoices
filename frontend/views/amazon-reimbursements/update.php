<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonReimbursements */

$this->title = Yii::t('app', 'Update Amazon Reimbursements: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Amazon Reimbursements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="amazon-reimbursements-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

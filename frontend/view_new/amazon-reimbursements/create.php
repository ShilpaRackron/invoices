<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\AmazonReimbursements */

$this->title = Yii::t('app', 'Create Amazon Reimbursements');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Amazon Reimbursements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amazon-reimbursements-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

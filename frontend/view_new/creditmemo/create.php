<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\CreditMemo */

$this->title = Yii::t('app', 'Create Credit Memo');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Credit Memos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="credit-memo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'invoiceModel'=>$invoiceModel
    ]) ?>

</div>

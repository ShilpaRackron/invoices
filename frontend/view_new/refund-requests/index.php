<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\RefundRequestsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Refund Requests');
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias("@web/themes/amazitonew/");?>js/bootstrap.min.js"></script>
<?php
    Modal::begin([
        'header'=>'<h4>Update Request</h4>',
        'id'=>'update_request_refund',
        'size'=>'modal-lg'
    ]);
	?>
	<?php
	echo "<div id='updateRequestModalContent'></div>"; 
    Modal::end();

?>

<?php $script = <<< JS
	jQuery(function () {
	jQuery('.updaterefundrequest').click(function () {
		jQuery("#updateRequestModalContent").html("");
		jQuery("#updaterefundurl").val(jQuery(this).attr('updateurl'));
		jQuery('#updateRequestModalContent').load(jQuery(this).attr('updateurl'));
        jQuery('#update_request_refund')
            .modal('show')
            .find('#updateRequestModalContent')
            .load(jQuery(this).attr('updateurl'));
    });

	});
JS;
$this->registerJs($script);
?>

<div class="refund-requests-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            //'user_id',
            'transaction_item_id',
            'case_id',
            'refund_amount',
			'purchase_invoice_no',
			'amazon_status',
            'status',
            'updated_date',
			 'is_approved',

           ['class' => 'yii\grid\ActionColumn','template'=>'{update}', 'buttons' => [

                    'update' => function ($url, $model) {
                        return  Html::a('<span class="glyphicon glyphicon-pencil"></span>Update', '#', [ 'title' => Yii::t('app', 'Update'), 'class'=>'btn btn-primary btn-xs updaterefundrequest','updateurl'=>$url]) ;
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
				if ($action === 'update') {
                        $url = \yii\helpers\Url::toRoute(['refund-requests/update', 'id' => $model->id], ['class' => 'update_case updaterefundrequest']);
                        return $url;
                }				
				}],
        ],
    ]); ?>
</div>

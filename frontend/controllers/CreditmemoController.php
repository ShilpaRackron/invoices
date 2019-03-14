<?php

namespace frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\AmazonOrders;
use frontend\models\AmazonOrdersSearch;
use frontend\models\InvoiceSettings;
use frontend\models\VatRn;
use frontend\models\AmazonLogInfo;
use frontend\models\CreditMemo;
use frontend\models\CreditMemoSearch;
use frontend\models\CreditmemoSettings;

/**
 * CreditmemoController implements the CRUD actions for CreditMemo model.
 */
class CreditmemoController extends Controller
{
    /**
     * {@inheritdoc}
     */
    /* public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }*/

	public function behaviors() {
			return [
			'access' => [
			'class' => AccessControl::className(),
			'only' => ['create','index'],
			'rules' => [
			[                        
			'allow' => true,
			'roles' => ['@'],
			],
			
			],
            ],            
			];
		}

    /**
     * Lists all CreditMemo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CreditMemoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

   
    /**
     * Creates a new CreditMemo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model			= new CreditMemo();
		$user_info		= Yii::$app->user;
		$user_id		= $user_info->id;
		$invoiceModel	=  new AmazonOrders;
        if ($model->load(Yii::$app->request->post())) {
			 
				$creditmemoSettings	= new CreditmemoSettings();
				$amazonOrders		= new AmazonOrders();
				//$creditMemoModel	= new CreditMemo();
				$amazon_order_id	= $model->amazon_order_id;
				$checkOrder			= $model->checkExistingOrder($amazon_order_id, $user_id);
				$updateInvoiceCounter = true;
				if(!empty($checkOrder)) {
					$model		= $checkOrder;				
				}
				
				$checkIfOrderExist			= $amazonOrders->checkExistingOrder($amazon_order_id, $user_id);
				if($checkIfOrderExist){
					$model->invoice_number	= $checkIfOrderExist->invoice_number;
				}
				else{
					$model->invoice_number	= null;
				}
				$model->user_id							= $user_id;
				$model->amazon_order_id					= $amazon_order_id ;
				$model->qty_return						= $checkIfOrderExist->number_of_items_shipped ;
				$model->seller_sku						= $checkIfOrderExist->product_sku;			
				$model->fulfillment_center_id			= $checkIfOrderExist->fulfillment_center_id;				
				$model->product_sku						= $checkIfOrderExist->product_sku;
				$model->product_name					= htmlspecialchars($checkIfOrderExist->product_name);
				$model->total_amount_refund				=($model->total_amount_refund !="")?$model->total_amount_refund:0.00;
				$model->order_import_date				= date("Y-m-d H:i:s");
				$date1									= $model->return_date;
				$date2									= date('Y-m-d');
				$date1									= date_create($date1);
				$date2									= date_create($date2 );			
				$diff									= date_diff($date1,$date2);
				$dayDiff								= $diff->days;
				$model->order_import_date	= ($dayDiff>2)?$model->return_date:date("Y-m-d H:i:s");
				echo"\n\n";
				echo "importing for order id =".$amazon_order_id;
				echo"\n\n";
				if($model->save()){
					Yii::$app->getSession()->setFlash('success', "Credit not saved for amazon order id $amazon_order_id");
					$this->redirect(['user/credit-memo']);
				} else{
					Yii::$app->getSession()->setFlash('error', "Error in saving credit note");
				}
        }

        return $this->render('create', [
            'model' => $model,'invoiceModel'=>$invoiceModel
        ]);
    }

    /**
     * Updates an existing CreditMemo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

   /**
     * Finds the CreditMemo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CreditMemo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CreditMemo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}

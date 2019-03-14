<?php

namespace frontend\controllers;

use Yii;
use frontend\models\RefundRequests;
use frontend\models\RefundRequestsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\AmazonInventoryAdjustment;

/**
 * RefundRequestsController implements the CRUD actions for RefundRequests model.
 */
class RefundRequestsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
       return [
			'access' => [
			'class' => AccessControl::className(),
			'only' => ['insert-case','index'],
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
	* Lists all RefundRequests models.
	* @return mixed
	*/
	   public function actionIndex()
	   {
		   $searchModel = new RefundRequestsSearch();
		   $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		   return $this->render('index', [
			   'searchModel' => $searchModel,
			   'dataProvider' => $dataProvider,
		   ]);
	   }
  

  public function actionInsertCase() {
	  try{
        $model = new RefundRequests();
		$transaction_item_id = Yii::$app->request->get('transaction_item_id');
		if($transaction_item_id !=""){
			$data						= Yii::$app->request->post();
			$model->user_id				= Yii::$app->user->id;
			$model->refund_amount		= $data['refund_amount'];
			$model->transaction_item_id	= $transaction_item_id;
			$model->updated_date		= date('Y-m-d');
			$model->case_id				= $data['case_id'];
			$model->is_approved			= isset($data['is_approved'])?1:0;
			$model->status				= $data['status'];

			if ($model->save()) {
			   Yii::$app->session->setFlash('success', "Refund request saved successfully.");
			   echo "Refund request saved successfully.";
			   $transactionModel = AmazonInventoryAdjustment::findOne($transaction_item_id);
			   $transactionModel->requested_refund =1;
			   $transactionModel->save();
			} else{
				Yii::$app->session->setFlash('error', "Request not saved. Please try again");
				echo "Request not saved. Please try again";
			}
		}else{
			echo "You are not authorized to perform this operation";
		}
	  }
	  catch(Exception $e){
	  	  echo $e->getMessage();
	  }
	  exit;

    }

    
    /**
     * Updates an existing RefundRequests model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', "Data updated successfully");
            //return $this->redirect(['view', 'id' => $model->id]);
			$this->redirect(Yii::$app->request->referrer);
			//return $this->redirect(['/amazon-inventory-adjustment/index/']);
        }
        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }   

    /**
     * Finds the RefundRequests model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return RefundRequests the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RefundRequests::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}

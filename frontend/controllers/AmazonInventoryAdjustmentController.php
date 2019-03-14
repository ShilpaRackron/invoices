<?php

namespace frontend\controllers;

use Yii;
use frontend\models\AmazonInventoryAdjustment;
use frontend\models\AmazonInventoryAdjustmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\RefundRequests;
use yii\filters\AccessControl;

/**
 * AmazonInventoryAdjustmentController implements the CRUD actions for AmazonInventoryAdjustment model.
 */
class AmazonInventoryAdjustmentController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
         return [
			'access' => [
			'class' => AccessControl::className(),
			'only' => ['index'],
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
     * Lists all AmazonInventoryAdjustment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AmazonInventoryAdjustmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$requestModel  = new RefundRequests();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'requestModel'=>$requestModel
        ]);
    }
   
    /**
     * Finds the AmazonInventoryAdjustment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AmazonInventoryAdjustment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AmazonInventoryAdjustment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}

<?php

namespace frontend\controllers;

use Yii;
use frontend\models\AmazonProducts;
use frontend\models\AmazonProductsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\VatRn;

/**
 * AmazonController implements the CRUD actions for AmazonProducts model.
 */
class AmazonProductsController extends Controller
{
    /**
     * {@inheritdoc}
     */
     public function behaviors()
    {
        return [

			'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','productvatedit'],
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
     * Lists all AmazonProducts models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new AmazonProductsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

   
    /**
     * Finds the AmazonProducts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AmazonProducts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AmazonProducts::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

	public function actionProductvatedit($sku){
		
		try{
				$vatRnModel				= VatRn::getModel(Yii::$app->user->id);			
				$model		= AmazonProducts::findOne(['sku' => $sku,'user_id'=>Yii::$app->user->id]);
				if ($model->load(Yii::$app->request->post()) && $model->save()) {
					return $this->redirect(['index']);					
				}elseif(Yii::$app->request->isAjax) {
					return $this->renderAjax('_product_vat_edit', ['model' => $model,'vatRnModel'=>$vatRnModel]);
				}
			}
			catch(Exception $e){
				echo $e->getMessage();			
			}
			exit;
		}
}

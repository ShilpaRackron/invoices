<?php

namespace frontend\controllers;

use Yii;
use frontend\models\AmazonLogInfo;
use frontend\models\AmazonLogInfoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AmazonLogInfoController implements the CRUD actions for AmazonLogInfo model.
 */
class AmazonLogInfoController extends Controller
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
     * Lists all AmazonLogInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AmazonLogInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }    
   
    protected function findModel($id)
    {
        if (($model = AmazonLogInfo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}

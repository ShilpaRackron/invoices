<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
           /*  'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],*/
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
		if (!Yii::$app->user->isGuest) {
			return $this->redirect(['site/login']);
		}else{
			return $this->redirect(['user/dashboard']);
		}
        //return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            //return $this->goHome();
			return $this->redirect(['user/dashboard']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
	public function actionAmazonlogin(){
	 
	
	if(isset($_REQUEST['access_token']) && !empty($_REQUEST['access_token'])) {
		try{
			 
			$amazonClientId = Yii::$app->params['amazon_clientId'];
			
			$c = curl_init('https://api.amazon.com/auth/o2/tokeninfo?access_token=' . urlencode($_REQUEST['access_token']));
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			 
			$r = curl_exec($c);
			curl_close($c);
			$d = json_decode($r);
			 
			if ($d->aud != $amazonClientId) {
			  // the access token does not belong to us
			  header('HTTP/1.1 404 Not Found');
			  echo 'Page not found';
			  exit;
			}
			 
			// exchange the access token for user profile
			$c = curl_init('https://api.amazon.com/user/profile');
			curl_setopt($c, CURLOPT_HTTPHEADER, array('Authorization: bearer ' . $_REQUEST['access_token']));
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			 
			$r = curl_exec($c);
			curl_close($c);
			$d = json_decode($r);
			$model = new SignupForm();
			
			$checkExistingUser		= $model->checkUser($d->user_id);
			if(!empty($checkExistingUser) && $checkExistingUser->id !=""){
				if ($checkExistingUser->status==1 && Yii::$app->getUser()->login($checkExistingUser)) {
					//return $this->goHome();
					return $this->redirect(['user/dashboard']);
				}else{
					//$logoutUrl = Yii::$app->getUrlManager ()->createAbsoluteUrl( ['site/logout'], true );
					Yii::$app->session->setFlash('error', 'Your profile not actived yet.');
					return $this->redirect(['site/login','response'=>$_REQUEST['access_token']]);
				}
			}else{
				
				$model->username		= $d->user_id;
				$model->email			= $d->email;
				$model->password		= uniqid(rand(), true);;
				$model->amazon_token	= $_REQUEST['access_token'];
				$model->name= $d->name;
				
				if ($model->attributes) {
					if ($user = $model->signup()) {
						if (Yii::$app->getUser()->login($user)) {
							return $this->redirect(['user/dashboard']);
							//return $this->goHome();
						}else{
							Yii::$app->session->setFlash('error', 'Your profile not actived yet.');
							return $this->redirect(['site/login','response'=>$_REQUEST['access_token']]);
						}
					} else{
						Yii::$app->session->setFlash('error', 'Error in User registration.');
						return $this->redirect(['site/login']);					 
					}
				}
			}
		}catch(Exception $e){
			
				echo "<pre>";
				print_r($e->getMessage());
				die();
		}
	  }
	}
}
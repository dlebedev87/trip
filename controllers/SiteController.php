<?php

namespace app\controllers;

use app\models\AirportName;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SearchForm;
use app\models\Trip;
use yii\data\ActiveDataProvider;


class SiteController extends Controller
{
    const CACHE_TIME = 3600;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
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
     * @return string
     */
    public function actionIndex()
    {
        $listdata=AirportName::find()
            ->select(['value as value'])
            ->where(['language_id'=>AirportName::LANGUAGE_ID_RUS])
            ->asArray()
            ->cache(self::CACHE_TIME)
            ->all();

        $model = new SearchForm();
        if($model->load(Yii::$app->request->get())) {
            if($model->validate()){
                $authorsPosts = Trip::find()
                    ->joinWith(['tripServices' => function($q) {
                        $q->joinWith(['flightSegments' => function ($q2){
                            $q2->joinWith(['airportNames' => function ($q3){
                                $q3 -> from('guide_etalon.' . AirportName::tableName())
                                    ->cache(self::CACHE_TIME)
                                    ->andWhere(['like', 'airport_name.value', Yii::$app->request->get('airport_name')]);
                            }]);
                        }]);
                    }])
                    ->cache(self::CACHE_TIME)
                    ->where(['trip.corporate_id' => Trip::COPRORATE_ID])
                    ->andWhere(['trip_service.service_id' => Trip::SERVICE_ID]);
                $dataProvider = new ActiveDataProvider([
                    'query' => $authorsPosts,
                ]);
            }
            else{
                Yii::$app->session->setFlash('error','Ошибка');
            }
        }
        return $this->render('index',compact('model','authorsPosts','dataProvider','listdata'));
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

}

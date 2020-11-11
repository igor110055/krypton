<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Api\Bittrex;
use app\models\Api\Binance;
use app\models\BotEngine;
use app\models\Predictor\DataHandler;
use app\models\Predictor\Indicator;
use app\models\Predictor\Predictor;

class SiteController extends Controller
{
    /**
     * @inheritdoc
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
     * @inheritdoc
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
        $result = '';
//        $bot = new BotEngine();
//        $result = $bot->getExchangeClient('Binance')->getPrices();
//        $bot->prepareActualPrices();
//        $result = $bot->getMarketLastBids();
        $client = new Binance();
//        $result = $client->getAllOrders('BTCUSDT');
//        $result = $client->getMyTrades('BTCUSDT');
        $result = $client->checkOrder('ETHUSDT', '2009924003');
//        $result = $client->getAccountInfo();
//        $result = $client->buyOrder('ETHUSDT', 0.04, 450);
//        $result = $client->getOpenOrders();
        return $this->render('index', [
            'result' => $result
        ]);
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
        $api = new Bittrex;
        $result = '';
//        $result = $api->getOpenOrders('BTC-REP');
//        $result = $api->getTicker('BTC-REP');

        return $this->render('about', [
            'result' => $result
        ]);
    }

    public function actionOscylators()
    {
        return $this->render('oscylators');
    }
}

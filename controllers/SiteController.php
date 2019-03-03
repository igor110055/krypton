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
use app\models\BotEngine;

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
        $api = new Bittrex;
        $result = '';
//        $result = $api->getMarkets();
//        $result = $api->getOpenOrders();
//        $result = $api->getCurrencies();
//        $result = $api->getTicker('BTC-TRX');
//        $result = $api->getMarketSummaries();
//        $result = $api->getMarketSummary('BTC-TRX');
//        $result = $api->getOrderBook('BTC-TRX');
//        $result = $api->getMarketHistory('BTC-TRX');
//        $result = $api->getBalances();
//        $result = $api->getBalance('TRX');
//        $result = $api->getDepositAddress('BTC');
//        $result = $api->getOrder('613626e2-ee4e-419f-9502-9246642340be');
//        $result = $api->getOrders();
//        $result = $api->getOrders('BTC-TRX');
//        $result = $api->getWithdrawalHistory();
//        $result = $api->getDepositHistory();
//        $result = $api->placeSellOrder('BTC-REP',4.49110895, 0.0042);
//        $result = $api->cancelOrder('056bb71e-40f5-49df-afcc-f37dce42e011');
        $bot = new BotEngine();
//        $result = $bot->checkAlerts();
//        $result = $bot->checkPendingOrders();
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

        $result = $api->getOpenOrders('BTC-REP');
//        $result = $api->getTicker('BTC-REP');

        return $this->render('about', [
            'result' => json_decode($result, true)
        ]);
    }
}

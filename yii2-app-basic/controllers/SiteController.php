<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Login;
use app\models\ContactForm;
use app\models\Users;

class Bearer extends HttpBearerAuth {
    public function handleFailure($response)
    {
        Yii::$app->response->setStatusCode(403);
        return Yii::$app->response->data = [
            'message' => 'Необходима авторизация'
        ];
    }
}

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'only' => ['logout'],
//                'rules' => [
//                    [
//                        'actions' => ['logout'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
//        ];
//    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => Bearer::className(),
            'except' => ['login', 'signup']
        ];
        return $behaviors;
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
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
//    public function actionLogin()
//    {
//        if (!Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
//
//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        }
//
//        $model->password = '';
//        return $this->render('login', [
//            'model' => $model,
//        ]);
//    }

    /**
     * Logout action.
     *
     * @return Response
     */
//    public function actionLogout()
//    {
//        Yii::$app->user->logout();
//
//        return $this->goHome();
//    }

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

    public function actionSignup()
    {
        $model = new Users();
        if (Yii::$app->request->isPost) {
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $model->email = $_POST['email'];
                $model->first_name = !empty($_POST['first_name']) ? $_POST['first_name'] : NULL;
                $model->surname = !empty($_POST['surname']) ? $_POST['surname'] : NULL;
                $model->phone = !empty($_POST['phone']) ? $_POST['phone'] : NULL;
                $model->password = $_POST['password'];
                $model->register_date = date('d-m-Y H:i:s');
            } else {
                Yii::$app->response->setStatusCode(422);
                return 'Заполните email и пароль!';
            }
            if ($model->validate()) {
                Yii::$app->response->setStatusCode(201);
                $model->save();
                $data = $model::findOne(
                    ['email' => $model->email]
                );
                return [
                    'id' => $data['id'],
                    'register_date' => $data['register_date'],
                    ];
            } else {
                Yii::$app->response->setStatusCode(422);
                return $model->getErrors();
            }
        } else {
            return 'Отправлен не POST запрос!';
        }
    }

    public function actionLogin()
    {
        $model = new Login();
        if (Yii::$app->request->isPost) {
            $model->email = $_POST['email'];
            $model->password = $_POST['password'];
            $user = Users::findOne([
                'email' => $model->email,
            ]);
            if (!$model->validate()){
                Yii::$app->response->setStatusCode(402);
                return $model->getErrors();
            };
            if (empty($user) || $model->password !== $user['password']) {
                Yii::$app->response->setStatusCode(404);
                return 'Логин или пароль неверный!';
            } else {
                $user = Users::findOne($user['id']);
                $user->token = Yii::$app->getRequest()->getCsrfToken();
                $user->save(false);
            }
        } else {
            return 'Отправлен не POST запрос!';
        }
        return 'Вы успешно авторизованы'.' '.'Токен:'.' '.$user->token;
    }

    public function actionLogout()
    {
        $token = substr(Yii::$app->request->headers->get('Authorization'),7);
        $user = Users::findOne([
            'token' => $token
        ]);
        $user->token = '';
        $user->save(false);
        return 'Вы разлогинены!';

    }
    public function actionEdit()
    {
        if (Yii::$app->request->isPost) {
            $token = substr(Yii::$app->request->headers->get('Authorization'),7);
            $user = Users::findOne([
                'token' => $token
            ]);
            if (!empty($_POST['email'])) {
            $user->email = $_POST['email'];
            }
            if (!empty($_POST['password'])) {
            $user->password = $_POST['password'];
            }
            $user->first_name = !empty($_POST['first_name']) ? $_POST['first_name'] : NULL;
            $user->surname = !empty($_POST['surname']) ? $_POST['surname'] : NULL;
            $user->phone = !empty($_POST['phone']) ? $_POST['phone'] : NULL;

            if ($user->validate()) {
                Yii::$app->response->setStatusCode(201);
                $user->update();
                $data = $user::findOne(
                    ['email' => $user->email]
                );
                return 'Данные успешно изменены!';
            } else {
                Yii::$app->response->setStatusCode(422);
                return $user->getErrors();
            }

        } else {
            return 'Отправлен не POST запрос!';
        }
    }
    public function beforeAction($action)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }
}

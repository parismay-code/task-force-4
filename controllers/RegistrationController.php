<?php

namespace app\controllers;

use app\models\VkAuth;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;
use Yii;
use app\models\City;
use app\models\RegistrationForm;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class RegistrationController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        parent::init();
        Yii::$app->user->loginUrl = ['auth/index'];
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?']
                    ],
                ]
            ]
        ];
    }

    /**
     * Возвращает страницу регистрации, обрабатывает POST запрос и создает аккаунт
     *
     * @param string $code
     *
     * @throws StaleObjectException
     * @throws \Throwable
     * @throws VKApiException
     * @throws VKClientException
     *
     * @return Response|string
     */
    public function actionIndex(string $code = ''): Response|string
    {
        $cities = City::find()->all();

        $model = new RegistrationForm();

        if ($code) {
            $oauth = new VkAuth();
            $token = $oauth->getToken($code, 'registration');

            if (!$token['user_id']) {
                return $this->redirect(Url::to(['registration/index']));
            }

            $userData = $oauth->getUserData($token);

            if ($model->vkRegister($userData)) {
                return $this->redirect(Url::to(['tasks/index']));
            }
        }

        if ($model->load($this->request->post()) && $model->register()) {
            return $this->redirect(Url::to(['tasks/index']));
        }


        $model->password = null;
        $model->password_repeat = null;

        return $this->render('index', [
            'model' => $model,
            'cities' => $cities
        ]);
    }
}
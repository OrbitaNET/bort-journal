<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\User;
use app\services\TelegramService;

/**
 * AuthController — registration and login via phone + Telegram code.
 *
 * Registration:
 *   1. User fills in username + phone number → account created (telegram not yet linked)
 *   2. User opens Telegram bot and sends: /start <phone>  → telegram_id gets saved
 *
 * Login:
 *   1. User enters phone number
 *   2. System finds telegram_id by phone, sends 4-digit code via bot
 *   3. User enters code → logged in
 */
class AuthController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'register' => ['get', 'post'],
                    'login'    => ['get', 'post'],
                    'verify'   => ['get', 'post'],
                    'logout'   => ['post'],
                ],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Registration
    // -------------------------------------------------------------------------

    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (Yii::$app->request->isPost) {
            $post     = Yii::$app->request->post();
            $username = trim($post['username'] ?? '');
            $phone    = User::normalizePhone($post['phone'] ?? '');

            $errors = [];

            if ($username === '') {
                $errors[] = 'Имя пользователя обязательно.';
            } elseif (!preg_match('/^[a-zA-Z0-9_]{3,64}$/', $username)) {
                $errors[] = 'Имя пользователя: 3–64 символа, только буквы, цифры и _.';
            }

            if (!preg_match('/^\+\d{7,15}$/', $phone)) {
                $errors[] = 'Введите корректный номер телефона (например: +79001234567).';
            }

            if (empty($errors)) {
                if (User::findByUsername($username)) {
                    $errors[] = 'Пользователь с таким именем уже существует.';
                }
                if (User::findByPhone($phone)) {
                    $errors[] = 'Аккаунт с таким номером телефона уже зарегистрирован.';
                }
            }

            if (!empty($errors)) {
                return $this->render('register', [
                    'errors' => $errors,
                    'data'   => $post,
                ]);
            }

            $user           = new User();
            $user->username = $username;
            $user->phone    = $phone;
            $user->status   = User::STATUS_ACTIVE;

            if (!$user->save()) {
                Yii::error($user->errors, __METHOD__);
                return $this->render('register', [
                    'errors' => ['Не удалось создать аккаунт. Попробуйте позже.'],
                    'data'   => $post,
                ]);
            }

            Yii::$app->session->setFlash('register_success', $phone);

            return $this->redirect(['auth/register-done']);
        }

        return $this->render('register', ['errors' => [], 'data' => []]);
    }

    /**
     * Page shown after successful registration — instructs user to start the bot.
     */
    public function actionRegisterDone()
    {
        $phone = Yii::$app->session->getFlash('register_success');
        if (!$phone) {
            return $this->redirect(['auth/register']);
        }

        $service      = new TelegramService();
        $botUsername  = $service->getBotUsername();

        return $this->render('register-done', [
            'phone'       => $phone,
            'botUsername' => $botUsername,
        ]);
    }

    // -------------------------------------------------------------------------
    // Login
    // -------------------------------------------------------------------------

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (Yii::$app->request->isPost) {
            $phone  = User::normalizePhone(Yii::$app->request->post('phone', ''));
            $errors = [];

            if (!preg_match('/^\+\d{7,15}$/', $phone)) {
                $errors[] = 'Введите корректный номер телефона.';
            }

            $user = null;
            if (empty($errors)) {
                $user = User::findByPhone($phone);
                if (!$user) {
                    $errors[] = 'Аккаунт с таким номером не найден.';
                } elseif (!$user->telegram_id) {
                    $errors[] = 'Telegram не привязан. Откройте бота и отправьте /start ' . $phone;
                }
            }

            if (!empty($errors)) {
                return $this->render('login', ['errors' => $errors]);
            }

            $code    = $user->generateAuthCode();
            $service = new TelegramService();
            $sent    = $service->sendAuthCode($user->telegram_id, $code);

            if (!$sent) {
                Yii::warning("Could not send auth code to telegram_id={$user->telegram_id}", __METHOD__);
                return $this->render('login', [
                    'errors' => ['Не удалось отправить код в Telegram. Попробуйте позже.'],
                ]);
            }

            Yii::$app->session->set('pending_auth_user_id', $user->id);

            return $this->redirect(['auth/verify']);
        }

        return $this->render('login', ['errors' => []]);
    }

    // -------------------------------------------------------------------------
    // Code verification
    // -------------------------------------------------------------------------

    public function actionVerify()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $userId = Yii::$app->session->get('pending_auth_user_id');
        if (!$userId) {
            return $this->redirect(['auth/login']);
        }

        $errors = [];

        if (Yii::$app->request->isPost) {
            $code = trim(Yii::$app->request->post('code', ''));

            if (!preg_match('/^\d{4}$/', $code)) {
                $errors[] = 'Код должен состоять из 4 цифр.';
            }

            if (empty($errors)) {
                $user = User::findIdentity($userId);
                if (!$user || !$user->validateAuthCode($code)) {
                    $errors[] = 'Неверный или просроченный код.';
                }
            }

            if (empty($errors)) {
                $user->clearAuthCode();
                Yii::$app->session->remove('pending_auth_user_id');
                Yii::$app->user->login($user, 3600 * 24 * 30);

                return $this->goHome();
            }
        }

        return $this->render('verify', ['errors' => $errors]);
    }

    // -------------------------------------------------------------------------
    // Logout
    // -------------------------------------------------------------------------

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}

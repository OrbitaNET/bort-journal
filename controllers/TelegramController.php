<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\User;
use app\services\TelegramService;

/**
 * TelegramController handles the bot webhook.
 *
 * Supported commands:
 *   /start <phone>  — links the user's Telegram account to their registered phone number.
 *                     User sends: /start +79001234567
 *
 * Set webhook via:
 *   https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://yourdomain/telegram/webhook
 */
class TelegramController extends Controller
{
    // Disable CSRF — Telegram sends POST without CSRF token
    public $enableCsrfValidation = false;

    public function actionWebhook()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $body   = Yii::$app->request->rawBody;
        $update = json_decode($body, true);

        if (empty($update['message'])) {
            return ['ok' => true];
        }

        $message    = $update['message'];
        $chatId     = $message['chat']['id'];
        $text       = trim($message['text'] ?? '');
        $fromId     = $message['from']['id'];
        $fromName   = $message['from']['first_name'] ?? '';
        $tgUsername = $message['from']['username'] ?? null;

        $service = new TelegramService();

        // /start or /start <phone>
        if (strpos($text, '/start') === 0) {
            $parts = explode(' ', $text, 2);
            $phone = isset($parts[1]) ? trim($parts[1]) : null;

            if ($phone === null || $phone === '') {
                $service->sendMessage($chatId,
                    "Привет, {$fromName}! 👋\n\n"
                    . "Для привязки аккаунта отправьте команду с вашим номером телефона:\n"
                    . "/start +79001234567"
                );
                return ['ok' => true];
            }

            $user = User::findByPhone($phone);

            if (!$user) {
                $service->sendMessage($chatId,
                    "Аккаунт с номером {$phone} не найден.\n"
                    . "Сначала зарегистрируйтесь на сайте."
                );
                return ['ok' => true];
            }

            if ($user->telegram_id && $user->telegram_id !== $fromId) {
                $service->sendMessage($chatId,
                    "К этому номеру уже привязан другой Telegram-аккаунт."
                );
                return ['ok' => true];
            }

            // Link telegram account
            $user->telegram_id       = $fromId;
            $user->telegram_username = $tgUsername;
            $user->save(false);

            $service->sendMessage($chatId,
                "✅ Telegram-аккаунт успешно привязан к номеру {$phone}!\n\n"
                . "Теперь вы можете войти на сайте, используя ваш номер телефона."
            );

            return ['ok' => true];
        }

        // Unknown command
        $service->sendMessage($chatId,
            "Используйте /start <номер_телефона> для привязки аккаунта."
        );

        return ['ok' => true];
    }

    /**
     * Register webhook with Telegram. Call once after deploy.
     * GET /telegram/set-webhook?url=https://yourdomain
     */
    public function actionSetWebhook()
    {
        $baseUrl    = Yii::$app->request->get('url', Yii::$app->request->hostInfo);
        $webhookUrl = rtrim($baseUrl, '/') . '/telegram/webhook';
        $token      = Yii::$app->params['telegramBotToken'];

        $apiUrl = "https://api.telegram.org/bot{$token}/setWebhook?url=" . urlencode($webhookUrl);

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10]);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}

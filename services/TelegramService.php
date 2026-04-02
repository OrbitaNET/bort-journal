<?php

namespace app\services;

use Yii;

class TelegramService
{
    private $botToken;
    private $apiBase = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $this->botToken = Yii::$app->params['telegramBotToken'];
    }

    /**
     * Send a text message to a Telegram chat.
     */
    public function sendMessage($chatId, $text, $parseMode = null)
    {
        $url  = $this->apiBase . $this->botToken . '/sendMessage';
        $data = ['chat_id' => $chatId, 'text' => $text];

        if ($parseMode) {
            $data['parse_mode'] = $parseMode;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Yii::error("Telegram sendMessage cURL error: {$error}", __METHOD__);
            return false;
        }

        return json_decode($response, true);
    }

    /**
     * Send 4-digit auth code to the user's Telegram.
     */
    public function sendAuthCode($telegramId, $code)
    {
        $text   = "Ваш код подтверждения: *{$code}*\n\nКод действителен 5 минут.";
        $result = $this->sendMessage($telegramId, $text, 'Markdown');

        return isset($result['ok']) && $result['ok'] === true;
    }

    /**
     * Get bot info — used to build the bot link for the user.
     */
    public function getBotUsername()
    {
        $url = $this->apiBase . $this->botToken . '/getMe';
        $ch  = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5]);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response['result']['username'] ?? null;
    }
}

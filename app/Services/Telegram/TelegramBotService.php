<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    /**
     * Token bot Telegram.
     */
    protected string $token;

    /**
     * Base URL endpoint API Telegram Bot.
     */
    protected string $baseUrl;

    public function __construct(?string $token = null)
    {
        $this->token = $token ?: (string) (config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN', ''));
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}/";
    }

    /**
     * Mengirim pesan teks dengan dukungan Parse Mode HTML dan Inline Keyboard.
     */
    public function sendMessage($chatId, string $text, $keyboard = null, string $parseMode = 'HTML')
    {
        if (empty($this->token) || empty($chatId)) {
            Log::warning('TelegramBotService: token atau chatId kosong.');
            return false;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ];

        if ($keyboard !== null) {
            $payload['reply_markup'] = is_array($keyboard) ? json_encode($keyboard) : $keyboard;
        }

        try {
            $response = Http::timeout(10)->post($this->baseUrl . 'sendMessage', $payload);
            return $response->json();
        } catch (\Throwable $e) {
            Log::error('TelegramBotService sendMessage error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Alias bahasa Indonesia untuk sendMessage.
     */
    public function kirimPesanTeks($chatId, string $text, $keyboard = null, string $parseMode = 'HTML')
    {
        return $this->sendMessage($chatId, $text, $keyboard, $parseMode);
    }

    /**
     * Mengirim berkas dokumen (seperti PDF) ke chat Telegram dengan lampiran biner.
     */
    public function sendDocument($chatId, $fileContent, string $filename, ?string $caption = null, $keyboard = null)
    {
        if (empty($this->token) || empty($chatId)) {
            Log::warning('TelegramBotService: token atau chatId kosong saat kirim dokumen.');
            return false;
        }

        try {
            $data = [
                'chat_id' => (string) $chatId,
            ];

            if ($caption !== null) {
                $data['caption'] = $caption;
                $data['parse_mode'] = 'HTML';
            }

            if ($keyboard !== null) {
                $data['reply_markup'] = is_array($keyboard) ? json_encode($keyboard) : $keyboard;
            }

            $response = Http::timeout(30)
                ->attach('document', $fileContent, $filename)
                ->post($this->baseUrl . 'sendDocument', $data);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('TelegramBotService sendDocument error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Membalas interaksi klik callback_query tombol inline keyboard.
     */
    public function answerCallbackQuery($callbackQueryId, ?string $text = null, bool $showAlert = false)
    {
        if (empty($this->token) || empty($callbackQueryId)) {
            return false;
        }

        $payload = [
            'callback_query_id' => $callbackQueryId,
            'show_alert' => $showAlert,
        ];

        if ($text !== null) {
            $payload['text'] = $text;
        }

        try {
            $response = Http::timeout(5)->post($this->baseUrl . 'answerCallbackQuery', $payload);
            return $response->json();
        } catch (\Throwable $e) {
            Log::error('TelegramBotService answerCallbackQuery error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mendaftarkan daftar command / perintah bot di menu Telegram client.
     */
    public function setMyCommands(array $commands)
    {
        if (empty($this->token)) {
            Log::warning('TelegramBotService: token kosong saat setMyCommands.');
            return false;
        }

        try {
            $response = Http::timeout(10)->post($this->baseUrl . 'setMyCommands', [
                'commands' => $commands,
            ]);
            return $response->json();
        } catch (\Throwable $e) {
            Log::error('TelegramBotService setMyCommands error: ' . $e->getMessage());
            return false;
        }
    }
}


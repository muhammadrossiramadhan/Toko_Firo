<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramWebhookSecurityTest extends TestCase
{
    protected string $secretToken = 'TokoFiroSecret123';
    protected string $allowedChatId = '-1001234567890';
    protected string $adminChatId = '8063111465';

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.telegram.webhook_secret', $this->secretToken);
        Config::set('services.telegram.chat_id', $this->allowedChatId);
        Config::set('services.telegram.admin_chat_id', $this->adminChatId);

        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true, 'result' => []], 200),
        ]);
    }

    /**
     * Uji penolakan request tanpa secret token (harus 403 Forbidden).
     */
    public function test_penolakan_request_tanpa_secret_token(): void
    {
        $payload = [
            'update_id' => 10001,
            'message' => [
                'message_id' => 1,
                'chat' => ['id' => $this->allowedChatId],
                'text' => '/start',
            ],
        ];

        // Request tanpa header X-Telegram-Bot-Api-Secret-Token
        $response = $this->postJson('/api/telegram/webhook', $payload);

        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Forbidden: Invalid secret token',
        ]);
    }

    /**
     * Uji penolakan request dengan secret token salah (harus 403 Forbidden).
     */
    public function test_penolakan_request_dengan_secret_token_salah(): void
    {
        $payload = [
            'update_id' => 10002,
            'message' => [
                'message_id' => 2,
                'chat' => ['id' => $this->allowedChatId],
                'text' => '/menu',
            ],
        ];

        $response = $this->withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => 'wrong-secret-token',
        ])->postJson('/api/telegram/webhook', $payload);

        $response->assertStatus(403);
    }

    /**
     * Uji penolakan request dari chat_id yang tidak terdaftar di whitelist (harus 403 Forbidden).
     */
    public function test_penolakan_request_dari_chat_id_liar_tidak_terdaftar(): void
    {
        $payload = [
            'update_id' => 10003,
            'message' => [
                'message_id' => 3,
                'chat' => ['id' => '999999999999'], // Chat ID tidak terdaftar
                'text' => '/start',
            ],
        ];

        $response = $this->withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => $this->secretToken,
        ])->postJson('/api/telegram/webhook', $payload);

        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Forbidden: Unauthorized chat ID',
        ]);
    }

    /**
     * Uji respon 200 OK saat secret token dan chat_id valid.
     */
    public function test_respon_200_ok_saat_secret_token_dan_chat_id_valid(): void
    {
        $payload = [
            'update_id' => 10004,
            'message' => [
                'message_id' => 4,
                'chat' => ['id' => $this->allowedChatId],
                'text' => '/start',
            ],
        ];

        $response = $this->withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => $this->secretToken,
        ])->postJson('/api/telegram/webhook', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'type' => 'message',
        ]);
    }

    /**
     * Uji respon 200 OK untuk callback query dari admin chat ID.
     */
    public function test_respon_200_ok_untuk_callback_query_admin_valid(): void
    {
        $payload = [
            'update_id' => 10005,
            'callback_query' => [
                'id' => 'cb-101',
                'from' => ['id' => $this->adminChatId],
                'message' => [
                    'message_id' => 10,
                    'chat' => ['id' => $this->adminChatId],
                ],
                'data' => 'menu_admin',
            ],
        ];

        $response = $this->withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => $this->secretToken,
        ])->postJson('/api/telegram/webhook', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'type' => 'callback_query',
        ]);
    }

    /**
     * Uji rate limiter webhook (throttle:30,1).
     */
    public function test_rate_limiter_webhook_membatasi_request_berlebih(): void
    {
        $payload = [
            'update_id' => 20000,
            'message' => [
                'message_id' => 20,
                'chat' => ['id' => $this->allowedChatId],
                'text' => '/menu',
            ],
        ];

        // Lakukan 30 request pertama yang diizinkan limit
        for ($i = 0; $i < 30; $i++) {
            $response = $this->withHeaders([
                'X-Telegram-Bot-Api-Secret-Token' => $this->secretToken,
            ])->postJson('/api/telegram/webhook', $payload);

            $this->assertContains($response->getStatusCode(), [200, 429]);
            if ($response->getStatusCode() === 429) {
                break;
            }
        }

        // Request ke-31 harus menerima 429 Too Many Requests
        $responseTerakhir = $this->withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => $this->secretToken,
        ])->postJson('/api/telegram/webhook', $payload);

        $responseTerakhir->assertStatus(429);
    }
}


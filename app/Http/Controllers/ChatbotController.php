<?php

namespace App\Http\Controllers;

use App\Services\Chatbot\ChatbotKatalogService;
use App\Services\Chatbot\ChatbotSanitizerService;
use App\Services\PengaturanTokoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ChatbotController extends Controller
{
    protected ChatbotSanitizerService $sanitizer;
    protected ChatbotKatalogService $katalog;

    public function __construct(
        ChatbotSanitizerService $sanitizer,
        ChatbotKatalogService $katalog
    ) {
        $this->sanitizer = $sanitizer;
        $this->katalog = $katalog;
    }

    /**
     * Menangani pertanyaan pelanggan ke AI Chatbot Layanan Publik Toko Firo.
     */
    public function tanya(Request $request): JsonResponse
    {
        $rawMessage = $request->input('message') ?? $request->input('pesan');

        // 1. Sanitasi, Normalisasi Unicode NFKC, Zero-width Strip & Multilingual Regex Scan
        [$isSafe, $cleanMessage, $rejectionReason] = $this->sanitizer->sanitize($rawMessage);

        if (!$isSafe) {
            return response()->json([
                'sukses' => true,
                'reply' => $rejectionReason,
                'jawaban' => $rejectionReason,
            ]);
        }

        try {
            // 2. Data Isolation: Ambil HANYA 9 atribut publik terenkapsulasi
            $konteksStok = $this->katalog->getPublicContext($cleanMessage);

            // 3. System Prompt terintegrasi dengan soul.md
            $systemPrompt = config('chatbot.system_prompt') . "\n\nDATA BARANG RESMI TOKO FIRO:\n" . $konteksStok;

            // 4. Request ke Provider OpenAI-Compatible (TrustIsGold)
            $baseUrl = rtrim((string) (config('services.ai.base_url') ?: 'https://api.trustisgold.web.id/v1'), '/');
            $apiKey  = (string) (config('services.ai.key') ?: env('AI_API_KEY'));
            $model   = (string) (config('services.ai.model') ?: 'tig/auto');

            // Fallback key jika belum diset di env
            if (empty($apiKey)) {
                $apiKey = 'mock_api_key';
            }

            $response = Http::timeout(35)
                ->withToken($apiKey)
                ->post("{$baseUrl}/chat/completions", [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $cleanMessage],
                    ],
                    'temperature' => 0.2,
                    'max_tokens'  => 600,
                ]);

            if ($response->successful()) {
                $rawContent = $response->json('choices.0.message.content');
                if (!empty($rawContent)) {
                    $cleanReply = trim((string) $rawContent);
                    return response()->json([
                        'sukses'  => true,
                        'reply'   => $cleanReply,
                        'jawaban' => $cleanReply,
                    ]);
                }
            }

            Log::warning('AI Chatbot API gagal, beralih ke fallback lokal', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Fallback programatik lokal jika API eksternal offline
            $fallback = $this->fallbackResponse($cleanMessage);
            return response()->json([
                'sukses' => true,
                'reply' => $fallback,
                'jawaban' => $fallback,
            ]);

        } catch (\Throwable $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            $fallback = $this->fallbackResponse($cleanMessage);
            return response()->json([
                'sukses' => true,
                'reply' => $fallback,
                'jawaban' => $fallback,
            ]);
        }
    }

    /**
     * Respon fallback programatik berbasis database lokal jika API offline.
     */
    protected function fallbackResponse(string $pesan): string
    {
        $lower = strtolower($pesan);

        $tokoInfo = null;
        if (Schema::hasTable('pengaturan_toko')) {
            $tokoInfo = DB::table('pengaturan_toko')->first();
        }
        $settings = app(PengaturanTokoService::class)->getSettings();
        $namaToko = $tokoInfo->nama_toko ?? $settings['nama_toko'] ?? 'Toko Firo';
        $alamatToko = $tokoInfo->alamat ?? 'Jl. Yos Sudarso No.BA-9, Krajan, Sidopekso, Kec. Kraksaan, Kabupaten Probolinggo, Jawa Timur 67282';
        $teleponToko = $tokoInfo->no_telepon ?? $tokoInfo->telepon ?? $settings['telepon'] ?? '081234567890';

        if (str_contains($lower, 'alamat') || str_contains($lower, 'lokasi') || str_contains($lower, 'dimana') || str_contains($lower, 'tempat')) {
            return "{$namaToko} beralamat di {$alamatToko}. Silakan kunjungi toko kami!";
        }

        if (str_contains($lower, 'jam') || str_contains($lower, 'buka') || str_contains($lower, 'tutup') || str_contains($lower, 'operasional')) {
            return "{$namaToko} buka setiap hari mulai pukul 08:00 hingga 21:00 WIB.";
        }

        if (str_contains($lower, 'telepon') || str_contains($lower, 'wa') || str_contains($lower, 'whatsapp') || str_contains($lower, 'kontak') || str_contains($lower, 'hubungi')) {
            return "Anda dapat menghubungi {$namaToko} via Telepon/WhatsApp di nomor {$teleponToko}.";
        }

        // Ambil 3 produk relevan dari tabel publik
        $kata = collect(preg_split('/\s+/', $lower))->filter(fn ($k) => mb_strlen($k) >= 3)->take(4);
        $items = DB::table('daftar_item')
            ->select('nama_item', 'harga_jual', 'stok', 'satuan')
            ->where(function ($w) use ($kata) {
                foreach ($kata as $k) {
                    $w->orWhere('nama_item', 'like', "%{$k}%");
                }
            })
            ->limit(3)
            ->get();

        if ($items->isNotEmpty()) {
            $lines = $items->map(function ($item) {
                $harga = number_format((float) $item->harga_jual, 0, ',', '.');
                $status = $item->stok > 0 ? "tersedia ({$item->stok} {$item->satuan})" : "stok habis";
                return "- {$item->nama_item}: Rp {$harga} ({$status})";
            })->implode("\n");

            return "Berikut informasi produk di {$namaToko}:\n{$lines}\nAda hal lain yang dapat kami bantu?";
        }

        return "Halo! Saya Asisten Virtual {$namaToko}. Saya dapat membantu mengecek ketersediaan stok barang ATK, harga produk, fotokopi, atau lokasi toko kami di Kraksaan, Probolinggo. Silakan ketik nama barang yang dicari.";
    }
}

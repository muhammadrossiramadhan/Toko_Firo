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
        $isPesanKey = $request->has('pesan') && !$request->has('message');
        if ($isPesanKey) {
            $request->validate(['pesan' => 'required|string|max:500']);
        } else {
            $request->validate(['message' => 'required|string|max:500']);
        }

        $rawMessage = $request->input('message') ?? $request->input('pesan');

        // Sanitasi awal: buang skrip dan pastikan ada teks substantif tersisa
        $stripScript = preg_replace('/<\s*script\b[^>]*>(.*?)<\s*\/\s*script\s*>/is', '', (string) $rawMessage);
        $cleanStrip = trim(strip_tags((string) $stripScript));

        if ($cleanStrip === '') {
            return response()->json([
                'sukses' => false,
                'message' => 'Pesan tidak boleh kosong setelah sanitasi.',
                'reply' => 'Pesan tidak boleh kosong.',
            ], 422);
        }

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
            // 2. Data Isolation: Ambil HANYA atribut publik terenkapsulasi
            $konteksStok = $this->katalog->getPublicContext($cleanMessage);

            // 3. System Prompt terintegrasi
            $systemPrompt = (string) (config('chatbot.system_prompt') ?: 'Kamu adalah asisten resmi Toko Firo.') . "\n\nDATA BARANG RESMI TOKO FIRO:\n" . $konteksStok;

            // 4. Request ke AI Provider (Gemini / OpenAI compatible)
            $apiKey  = (string) (config('services.gemini.key') ?: config('services.ai.key') ?: env('AI_API_KEY'));
            $modelId = (string) (config('services.gemini.model') ?: config('services.ai.model') ?: 'gemini-1.5-flash');

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelId}:generateContent?key=" . ($apiKey ?: 'mock_api_key');

            $response = Http::timeout(15)
                ->withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $apiKey ?: 'mock_api_key',
                ])
                ->post($url, [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => [['role' => 'user', 'parts' => [['text' => $cleanMessage]]]],
                    'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 300],
                ]);

            if ($response->successful()) {
                $rawContent = $response->json('candidates.0.content.parts.0.text')
                    ?? $response->json('choices.0.message.content');

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
        $alamatToko = $tokoInfo->alamat ?? 'Jl. Yos Sudarso No. BA-9, Sidopekso, Krajan, Kec. Kraksaan, Kabupaten Probolinggo, Jawa Timur 67282';
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
                $stok = (int) ($item->stok ?? 0);
                $status = $stok > 0 ? "stok tersedia ({$stok} {$item->satuan})" : "stok habis";
                return "- {$item->nama_item}: Rp {$harga} ({$status})";
            })->implode("\n");

            return "Berikut informasi produk di {$namaToko}:\n{$lines}\nAda hal lain yang dapat kami bantu?";
        }

        return "Halo! Saya Asisten Virtual {$namaToko}. Saya dapat membantu mengecek ketersediaan stok barang ATK, harga produk, fotokopi, atau lokasi toko kami di Kraksaan, Probolinggo. Silakan ketik nama barang yang dicari.";
    }
}

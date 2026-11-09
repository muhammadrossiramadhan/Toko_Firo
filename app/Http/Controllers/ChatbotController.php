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
use Illuminate\Support\Facades\RateLimiter;
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
        // 0. Rate Limiting & Anti-Spam: Cegah spam pengiriman pesan berturut-turut dalam waktu singkat
        $ip = $request->ip() ?: '127.0.0.1';
        $throttleKey = 'chatbot_spam:' . $ip;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $pesanTolak = 'Terlalu banyak pesan dalam waktu singkat. Harap tunggu beberapa detik sebelum mengirim pesan lagi.';

            return response()->json([
                'sukses'      => false,
                'message'     => $pesanTolak,
                'reply'       => $pesanTolak,
                'jawaban'     => $pesanTolak,
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($throttleKey, 20);

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
            // 2. Pemisah Niat (Intent Detection Sederhana)
            $lowerMessage = mb_strtolower($cleanMessage);

            // Deteksi pertanyaan layanan / jasa / informasi umum toko
            $isServiceOrInfo = (bool) preg_match(
                '/\b(fotokopi|fotocopy|foto\s*copy|foto\s*kopi|print|cetak|ngeprint|jilid|penjilidan|laminating|laminasi|scan|jam\s*(buka|tutup|operasional)?|buka\s*jam|tutup\s*jam|alamat|lokasi|dimana|tempat|telepon|wa|whatsapp|kontak|nomor|siapa\s*(kamu|anda)|profil|halo|hai|hey|selamat\s*(pagi|siang|sore|malam)|permisi|terima\s*kasih|makasih|thanks)\b/ui',
                $lowerMessage
            );

            // Deteksi pertanyaan barang fisik / stok spesifik
            $hasProductIntent = (bool) preg_match(
                '/\b(stok|ada\s*(barang|stok|nggak|kah)?|punya|harga\s*(barang|produk|item|buku|pulpen|pensil|pen|kertas|map|spidol|penghapus|tipe[- ]?x|amplop|lem|penggaris|krayon|binder|cat|lakban|gunting|cutter)|beli|jual|katalog|merk|merek|varian|warna|ukuran|lembar|pcs|pak|dus|rim|kotak)\b/ui',
                $lowerMessage
            );

            // Pencarian katalog produk ke database HANYA aktif jika:
            // - User benar-benar menanyakan stok/produk, ATAU
            // - Pesan bukan merupakan pertanyaan layanan/info murni
            $needsProductSearch = $hasProductIntent || !$isServiceOrInfo;

            $konteksStok = '';
            if ($needsProductSearch) {
                $konteksStok = $this->katalog->getPublicContext($cleanMessage);
            }

            // 3. System Prompt terintegrasi
            $basePrompt = (string) (config('chatbot.system_prompt') ?: 'Kamu adalah asisten resmi Toko Firo.');
            if (!empty($konteksStok)) {
                $systemPrompt = $basePrompt . "\n\nDATA BARANG RESMI TOKO FIRO:\n" . $konteksStok;
            } else {
                $systemPrompt = $basePrompt . "\n\n(Catatan Khusus: Pengguna sedang menanyakan informasi layanan jasa atau info umum toko. Jawab langsung secara ramah, luwes, dan natural berdasarkan data profil & layanan toko di atas tanpa memaksakan pencarian stok barang fisik.)";
            }

            // 4. Request ke AI Provider (Format OpenAI Compatible Secara Penuh)
            $apiKey = (string) (
                config('services.openai.key')
                ?: config('services.ai.key')
                ?: env('OPENAI_API_KEY')
                ?: env('AI_API_KEY')
                ?: 'mock_api_key'
            );

            $baseUrl = (string) (
                config('services.openai.base_url')
                ?: config('services.ai.base_url')
                ?: env('OPENAI_BASE_URL')
                ?: env('AI_BASE_URL')
                ?: 'https://api.openai.com/v1'
            );

            $modelId = (string) (
                config('services.openai.model')
                ?: config('services.ai.model')
                ?: env('OPENAI_MODEL')
                ?: env('AI_MODEL')
                ?: 'gpt-4o-mini'
            );

            $baseUrl = rtrim(trim($baseUrl), '/');
            if (str_ends_with($baseUrl, '/chat/completions')) {
                $url = $baseUrl;
            } else {
                $url = "{$baseUrl}/chat/completions";
            }

            $isGoogleGemini = str_contains($url, 'generativelanguage.googleapis.com');

            if ($isGoogleGemini) {
                $response = Http::connectTimeout(5)
                    ->timeout(12)
                    ->withoutVerifying()
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'x-goog-api-key' => $apiKey,
                    ])
                    ->post($url, [
                        'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                        'contents' => [['role' => 'user', 'parts' => [['text' => $cleanMessage]]]],
                        'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 300],
                    ]);
            } else {
                $payload = [
                    'model' => $modelId,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt,
                        ],
                        [
                            'role' => 'user',
                            'content' => $cleanMessage,
                        ],
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 300,
                ];

                $response = Http::connectTimeout(5)
                    ->timeout(12)
                    ->withoutVerifying()
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type'  => 'application/json',
                    ])
                    ->post($url, $payload);
            }

            if ($response->successful()) {
                $rawContent = $response->json('choices.0.message.content')
                    ?? $response->json('choices.0.text')
                    ?? $response->json('candidates.0.content.parts.0.text');

                if (!empty($rawContent)) {
                    $cleanReply = trim((string) $rawContent);
                    // Sanitasi emoji agar sesuai K12 zero-emoji standard
                    $cleanReply = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{FE00}-\x{FE0F}\x{1F900}-\x{1F9FF}]/u', '', $cleanReply);

                    return response()->json([
                        'sukses'  => true,
                        'reply'   => $cleanReply,
                        'jawaban' => $cleanReply,
                    ]);
                }
            }

            Log::warning('AI Chatbot API gagal, beralih ke fallback lokal', [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 500),
                'url'    => $url,
                'model'  => $modelId,
            ]);

            // Fallback programatik lokal jika API eksternal offline
            $fallback = $this->fallbackResponse($cleanMessage);
            return response()->json([
                'sukses' => true,
                'reply' => $fallback,
                'jawaban' => $fallback,
            ]);

        } catch (\Throwable $e) {
            Log::error('Chatbot error / timeout: ' . $e->getMessage(), [
                'clean_message' => $cleanMessage,
                'exception' => get_class($e),
            ]);
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

        // 1. Sapaan / Salam
        if (preg_match('/\b(halo|hai|hey|hei|assalamu|selamat\s+(pagi|siang|sore|malam)|permisi)\b/ui', $lower)) {
            return "Halo! Selamat datang di {$namaToko}. Saya siap membantu Anda mengecek stok alat tulis kantor/sekolah (ATK), informasi harga produk, atau layanan fotokopi dan percetakan. Ada yang bisa kami bantu?";
        }

        if (preg_match('/\b(terima\s*kasih|makasih|thanks|matur\s*nuwun)\b/ui', $lower)) {
            return "Sama-sama! Senang bisa membantu Anda di {$namaToko}. Silakan bertanya kembali jika membutuhkan perlengkapan ATK atau jasa fotokopi.";
        }

        // 2. Alamat & Lokasi
        if (str_contains($lower, 'alamat') || str_contains($lower, 'lokasi') || str_contains($lower, 'dimana') || str_contains($lower, 'tempat')) {
            return "{$namaToko} beralamat di {$alamatToko}. Silakan kunjungi toko kami!";
        }

        // 3. Jam Operasional
        if (str_contains($lower, 'jam') || str_contains($lower, 'buka') || str_contains($lower, 'tutup') || str_contains($lower, 'operasional')) {
            return "{$namaToko} buka setiap hari mulai pukul 08:00 hingga 21:00 WIB.";
        }

        // 4. Telepon & Kontak
        if (str_contains($lower, 'telepon') || str_contains($lower, 'wa') || str_contains($lower, 'whatsapp') || str_contains($lower, 'kontak') || str_contains($lower, 'hubungi')) {
            return "Anda dapat menghubungi {$namaToko} via Telepon/WhatsApp di nomor {$teleponToko}.";
        }

        // 5. Layanan Fotokopi
        if (preg_match('/\b(fotokopi|fotocopy|foto\s*copy|foto\s*kopi|copy)\b/ui', $lower)) {
            return "Toko Firo melayani jasa fotokopi setiap hari (08:00 - 21:00 WIB). Tarif fotokopi hitam putih (B/W) mulai Rp 250 - Rp 350 per lembar, dan fotokopi warna mulai Rp 1.500 - Rp 2.500 per lembar tergantung jenis kertas. Layanan kami selalu siap dan tersedia. Ada dokumen yang ingin difotokopi?";
        }

        // 6. Layanan Print / Cetak
        if (preg_match('/\b(print|cetak|ngeprint)\b/ui', $lower)) {
            return "Toko Firo menyediakan jasa cetak / print dokumen dari WhatsApp, Email, atau Flashdisk. Tarif print teks hitam putih mulai Rp 500 per lembar dan print warna mulai Rp 1.500 per lembar. Silakan bawa berkas Anda atau kirimkan via WhatsApp!";
        }

        // 7. Layanan Jilid, Laminating, Scan
        if (preg_match('/\b(jilid|penjilidan|laminating|laminasi|scan)\b/ui', $lower)) {
            return "Toko Firo melayani jasa penjilidan dokumen (lakban & spiral kawat/plastik), laminating dokumen/sertifikat, dan scan dokumen ke format PDF/JPG. Layanan siap setiap hari.";
        }

        // 8. Pencarian Produk di Database (Hanya jika ada kata kunci pencarian barang)
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

        return "Halo! Saya Asisten Virtual {$namaToko}. Saya dapat membantu mengecek ketersediaan stok barang ATK, harga produk, jasa fotokopi, atau lokasi toko kami di Kraksaan, Probolinggo. Silakan sebutkan kebutuhan Anda.";
    }
}

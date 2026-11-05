<?php

namespace App\Http\Controllers;

use App\Models\ItemBarang;
use App\Services\PengaturanTokoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ChatbotController extends Controller
{
    /**
     * Menangani pertanyaan pelanggan ke AI Chatbot Toko Firo.
     */
    public function tanya(Request $request): JsonResponse
    {
        $isPesanKey = $request->has('pesan') && !$request->has('message');
        if ($isPesanKey) {
            $request->validate(['pesan' => 'required|string|max:300']);
        } else {
            $request->validate(['message' => 'required|string|max:300']);
        }

        $rawMessage = $request->input('message') ?? $request->input('pesan');

        // Sanitasi: bersihkan tag script beserta isinya, lalu strip seluruh tag HTML
        $cleanMessage = preg_replace('/<\s*script\b[^>]*>(.*?)<\s*\/\s*script\s*>/is', '', (string) $rawMessage);
        $cleanMessage = trim(strip_tags((string) $cleanMessage));

        if ($cleanMessage === '') {
            return response()->json([
                'sukses' => false,
                'message' => 'Pesan tidak boleh kosong setelah sanitasi.',
                'reply' => 'Pesan tidak boleh kosong.',
            ], 422);
        }

        try {
            $kata = collect(preg_split('/\s+/', mb_strtolower($cleanMessage)))
                ->filter(fn ($k) => mb_strlen($k) >= 3)
                ->take(6);

            // Karena sistem terkunci pada 27 tabel, kita pakai views/tabel yang ada.
            // Gunakan daftar_item jika tersedia, atau sesuaikan dengan skema toko.
            $dasar = ItemBarang::query()->select(
                'kode_item',
                'nama_item',
                'jenis',
                'merek',
                'satuan',
                'harga_jual',
                DB::raw('stok as stok_sekarang'),
                'batas_minimum',
                DB::raw('tipe_item as tipe_jasa')
            );

            $items = (clone $dasar)->where(function ($w) use ($kata) {
                foreach ($kata as $k) {
                    $w->orWhere('nama_item', 'like', "%{$k}%")
                      ->orWhere('merek', 'like', "%{$k}%")
                      ->orWhere('jenis', 'like', "%{$k}%");
                }
            })->limit(40)->get();

            if ($items->isEmpty()) {
                $items = (clone $dasar)->orderBy('nama_item')->limit(30)->get();
            }

            $konteksStok = $items->map(function ($i) {
                // Konversi tipe_jasa boolean/string ke status
                $isJasa = $i->tipe_jasa == 1 || strtoupper((string) $i->tipe_jasa) === 'JASA';

                if ($isJasa) {
                    $status = 'Layanan tersedia';
                } elseif ($i->stok_sekarang <= 0) {
                    $status = 'Habis';
                } elseif ($i->stok_sekarang <= ($i->batas_minimum ?? 5)) {
                    $status = "Menipis (sisa {$i->stok_sekarang})";
                } else {
                    $status = 'Tersedia';
                }

                $harga = 'Rp ' . number_format((float) $i->harga_jual, 0, ',', '.');
                return "{$i->kode_item} | {$i->nama_item} | {$i->jenis} | {$i->merek} | {$i->satuan} | {$harga} | {$status}";
            })->implode("\n");

            $systemPrompt = config('chatbot.system_prompt') . "\n" . $konteksStok;
            $modelId = config('services.gemini.model', 'gemini-1.5-flash');
            $apiKey = config('services.gemini.key');

            // Deteksi upaya jailbreak / prompt injection
            $lower = strtolower($cleanMessage);
            if (
                str_contains($lower, 'ignore previous instructions') ||
                str_contains($lower, 'abaikan instruksi') ||
                str_contains($lower, 'system prompt') ||
                str_contains($lower, 'jailbreak') ||
                str_contains($lower, 'bypass')
            ) {
                $guardrailReply = 'Maaf, saya hanya dapat membantu menjawab pertanyaan seputar produk dan informasi Toko Firo.';
                return response()->json([
                    'sukses' => true,
                    'reply' => $guardrailReply,
                    'jawaban' => $guardrailReply,
                ]);
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelId}:generateContent?key=" . ($apiKey ?: 'mock_api_key');

            $response = Http::timeout(15)
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
                $reply = $response->json('candidates.0.content.parts.0.text')
                    ?? 'Maaf, saya tidak bisa merespons saat ini.';
                $cleanReply = trim($reply);
                return response()->json([
                    'sukses' => true,
                    'reply' => $cleanReply,
                    'jawaban' => $cleanReply,
                ]);
            }

            Log::warning('Gemini API gagal', ['status' => $response->status(), 'body' => $response->body()]);

            // Fallback jika API eksternal gagal atau mock dalam test
            $fallback = $this->fallbackResponse($cleanMessage, $items);
            return response()->json([
                'sukses' => true,
                'reply' => $fallback,
                'jawaban' => $fallback,
            ]);
        } catch (\Throwable $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            return response()->json([
                'sukses' => false,
                'reply' => 'Maaf, asisten belum bisa dihubungi. Silakan tanya kasir.',
                'jawaban' => 'Maaf, asisten belum bisa dihubungi. Silakan tanya kasir.',
            ]);
        }
    }

    /**
     * Memberikan respon fallback programatik jika koneksi AI eksternal gagal.
     */
    protected function fallbackResponse(string $pesan, $items): string
    {
        $lower = strtolower($pesan);

        $tokoInfo = null;
        if (Schema::hasTable('pengaturan_toko')) {
            $tokoInfo = DB::table('pengaturan_toko')->first();
        }
        $settings = app(PengaturanTokoService::class)->getSettings();
        $namaToko = $tokoInfo->nama_toko ?? $settings['nama_toko'] ?? 'Toko Firo';
        $alamatToko = $tokoInfo->alamat ?? $settings['alamat'] ?? 'Jl. Raya Toko Firo, Malang';
        $teleponToko = $tokoInfo->no_telepon ?? $tokoInfo->telepon ?? $settings['telepon'] ?? '081234567890';

        // Pertanyaan alamat / lokasi
        if (str_contains($lower, 'alamat') || str_contains($lower, 'lokasi') || str_contains($lower, 'dimana') || str_contains($lower, 'tempat')) {
            return "{$namaToko} beralamat di {$alamatToko}. Silakan kunjungi toko kami!";
        }

        // Pertanyaan jam operasional
        if (str_contains($lower, 'jam') || str_contains($lower, 'buka') || str_contains($lower, 'tutup') || str_contains($lower, 'operasional')) {
            return "{$namaToko} buka setiap hari mulai pukul 08:00 hingga 21:00 WIB.";
        }

        // Pertanyaan kontak / WhatsApp
        if (str_contains($lower, 'telepon') || str_contains($lower, 'wa') || str_contains($lower, 'whatsapp') || str_contains($lower, 'kontak') || str_contains($lower, 'hubungi')) {
            return "Anda dapat menghubungi {$namaToko} via Telepon/WhatsApp di nomor {$teleponToko}.";
        }

        // Jika ada items yang cocok
        if ($items->isNotEmpty()) {
            $lines = $items->take(3)->map(function ($item) {
                $harga = number_format((float) $item->harga_jual, 0, ',', '.');
                $status = $item->stok_sekarang > 0 ? "stok {$item->stok_sekarang} {$item->satuan}" : "stok habis";
                return "- {$item->nama_item}: Rp {$harga} ({$status})";
            })->implode("\n");

            return "Berikut informasi produk di {$namaToko}:\n{$lines}\nAda hal lain yang dapat kami bantu?";
        }

        return "Halo! Saya Asisten Virtual {$namaToko}. Saat ini saya dapat membantu Anda memeriksa ketersediaan stok, harga barang, alamat, serta jam operasional toko kami. Silakan sebutkan nama barang yang Anda cari.";
    }
}

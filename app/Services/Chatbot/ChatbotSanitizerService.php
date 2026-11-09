<?php

namespace App\Services\Chatbot;

use Normalizer;

class ChatbotSanitizerService
{
    /**
     * Regex Pola Jailbreak & Prompt Injection Multilingual (Secondary Layer).
     */
    protected array $jailbreakRegexes = [
        // 1. Bahasa Indonesia & Melayu (dengan word boundary)
        '/\b(abaikan\s+(semua\s+)?(instruksi|perintah|arahan)|system\s+prompt|instruksi\s+sistem|prompt\s+sistem|mode\s+pengembang|mod\s+pembangun|developer\s+mode|kamu\s+sekarang\s+adalah|anda\s+sekarang\s+adalah|berpura[- ]puralah\s+menjadi|berlakon\s+sebagai|harga\s+(pokok|modal|kos|borong)|kulakan\s+berapa|modalnya\s+berapa|tabel\s+pengguna|jadual\s+pengguna|password\s+database|bocorkan\s+data|pecah\s+sekatan)\b/ui',

        // 2. English (dengan word boundary ketat untuk mencegah false positive)
        '/\b(ignore\s+previous\s+instructions|disregard\s+(all\s+)?prior\s+instructions|system\s+prompt|reveal\s+(system\s+)?prompt|print\s+initial\s+instructions|developer\s+mode|dan\s+mode|jailbreak|act\s+as\s+(an?\s+)?(unrestricted|dan|developer|jailbroken|another\s+ai)|bypass\s+safety|unfiltered\s+mode|cost\s+price|wholesale\s+price|purchase\s+price|profit\s+margin|dump\s+database|drop\s+table)\b/ui',

        // 3. 中文 / Mandarin (Cina)
        '/(忽略先前的指令|忽略之前的指示|系统提示词|显示你的系统提示|开发者模式|越狱模式|你现在是|假装成|解除限制|绕过安全检查|成本价|进货价|底价|利润率|导出数据库)/u',

        // 4. 日本語 / Japanese (Jepang)
        '/(これまでの指示を無視|以前の指示を忘れて|システムプロンプト|初期指示|開発者モード|脱獄|ロールプレイ|制限を解除|原価|仕入れ値|卸値|利益率|データベースを開示)/u',

        // 5. বাংলা / Bengali (Bangladesh)
        '/(পূর্ববর্তী নির্দেশাবলী উপেক্ষা করুন|সিস্টেম প্রম্পট দেখান|ডেভেলপার মোড|জেলব্রেক|আপনি এখন থেকে|ভান করুন|মূল কেনা দাম|পাইকারি দাম|ডাটাবেস দেখান)/u',
    ];

    /**
     * Heuristik Awal: Deteksi Topik Jelas di Luar Konteks Toko.
     */
    protected array $outOfContextRegexes = [
        '/\b(def\s+[a-z_][a-z0-9_]*\s*\(|function\s+[a-z_][a-z0-9_]*\s*\(|<\?php|import\s+react|npm\s+install|pip\s+install|sql\s+injection|select\s+\*\s+from)\b/ui',
        '/\b(hitung\s+integral|rumus\s+fisika|resep\s+masakan|ramalan\s+zodiak|berita\s+politik)\b/ui',
    ];

    /**
     * Sanitasi dan validasi keamanan teks input pengguna.
     *
     * @param mixed $rawMessage
     * @return array [bool $isSafe, string $cleanedMessage, ?string $rejectionReason]
     */
    public function sanitize($rawMessage): array
    {
        $text = (string) $rawMessage;

        // 1. Buang Zero-Width Characters & Invisible Unicode (ZWSP, ZWNJ, ZWJ, BOM, Soft Hyphen)
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{00AD}\p{Cf}]/u', '', $text);

        // 2. Normalisasi Unicode NFKC
        if (class_exists(Normalizer::class)) {
            $normalized = Normalizer::normalize($text, Normalizer::FORM_KC);
            if ($normalized !== false) {
                $text = $normalized;
            }
        }

        // 3. Pembersihan XSS & Tag HTML Liar
        $text = preg_replace('/<\s*script\b[^>]*>(.*?)<\s*\/\s*script\s*>/is', '', $text);
        $text = trim(strip_tags($text));

        // 4. Batas Karakter
        if ($text === '') {
            return [false, '', 'Pesan tidak boleh kosong.'];
        }

        if (mb_strlen($text) > 300) {
            $text = mb_substr($text, 0, 300);
        }

        // 5. Pengecekan Multilingual Regex Jailbreak
        foreach ($this->jailbreakRegexes as $regex) {
            if (preg_match($regex, $text)) {
                return [
                    false,
                    $text,
                    'Maaf, saya hanya dapat membantu menjawab pertanyaan seputar produk dan informasi Toko Firo.'
                ];
            }
        }

        // 6. Heuristik Awal: Pertanyaan Non-Toko
        foreach ($this->outOfContextRegexes as $regex) {
            if (preg_match($regex, $text)) {
                return [
                    false,
                    $text,
                    'Mohon maaf, saya khusus melayani pertanyaan seputar perlengkapan alat tulis kantor, sekolah, dan jasa fotokopi Toko Firo. Ada perlengkapan ATK yang sedang Anda butuhkan?'
                ];
            }
        }

        return [true, $text, null];
    }
}

<?php

namespace App\Services;

class PengaturanTokoService
{
    protected string $filePath;

    protected array $defaults = [
        'nama_toko'       => 'Toko Firo',
        'alamat'          => 'Jl. Raya Toko Firo, Malang',
        'telepon'         => '081234567890',
        'header_struk'    => 'TOKO FIRO — ATK & Fotocopy',
        'footer_struk'    => 'Terima kasih telah berbelanja!',
        'whatsapp_status' => 'Nonaktif (Belum Dikonfigurasi)',
    ];

    public function __construct()
    {
        $this->filePath = storage_path('app/settings/shop_settings.json');
    }

    /**
     * Mengambil seluruh data pengaturan toko dengan fallback nilai default.
     *
     * @return array<string, mixed>
     */
    public function getSettings(): array
    {
        if (file_exists($this->filePath)) {
            try {
                $content = file_get_contents($this->filePath);
                $decoded = json_decode((string) $content, true);
                if (is_array($decoded)) {
                    return array_merge($this->defaults, $decoded);
                }
            } catch (\Throwable $e) {
                // Gunakan default jika terjadi kesalahan baca file
            }
        }

        return $this->defaults;
    }

    /**
     * Memperbarui dan menyimpan pengaturan toko ke file JSON.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateSettings(array $data): array
    {
        $dir = dirname($this->filePath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $current = $this->getSettings();
        $updated = array_merge($current, array_intersect_key($data, $this->defaults));

        file_put_contents($this->filePath, json_encode($updated, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $updated;
    }

    /**
     * Mengambil nilai pengaturan tertentu.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->getSettings();

        return $settings[$key] ?? $default;
    }

    public function getNamaToko(): string
    {
        return (string) $this->get('nama_toko', $this->defaults['nama_toko']);
    }

    public function getAlamat(): string
    {
        return (string) $this->get('alamat', $this->defaults['alamat']);
    }

    public function getTelepon(): string
    {
        return (string) $this->get('telepon', $this->defaults['telepon']);
    }

    public function getHeaderStruk(): string
    {
        return (string) $this->get('header_struk', $this->defaults['header_struk']);
    }

    public function getFooterStruk(): string
    {
        return (string) $this->get('footer_struk', $this->defaults['footer_struk']);
    }
}


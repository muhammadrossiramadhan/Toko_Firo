<?php

namespace Database\Factories;

use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Pengguna>
 */
class PenggunaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Pengguna>
     */
    protected $model = Pengguna::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'password_hash' => Hash::make('password'),
            'role' => 'ADMIN',
            'status' => 'AKTIF',
            'telegram_chat_id' => null,
            'login_terakhir' => null,
        ];
    }

    /**
     * State untuk pengguna dengan role KASIR.
     */
    public function kasir(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'KASIR',
        ]);
    }

    /**
     * State untuk pengguna dengan status NONAKTIF.
     */
    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'NONAKTIF',
        ]);
    }
}


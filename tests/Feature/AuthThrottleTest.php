<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthThrottleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // bersihkan cache limiter sebelum tiap test
        Artisan::call('cache:clear');
    }

    public function test_login_throttle_after_five_failed_attempts(): void
    {
        User::factory()->create([
            'nip'      => '123456',
            'phone'    => '08123456789',
            'password' => Hash::make('correct-password'),
            'status'   => 'aktif',
        ]);

        // 5x gagal
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.process'), [
                'nip'      => '123456',
                'password' => 'wrong-password',
            ]);
        }

        // request ke-6 harus kena throttle
        $response = $this->post(route('login.process'), [
            'nip'      => '123456',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('throttle');
    }

    public function test_login_throttle_clears_after_successful_login(): void
    {
        User::factory()->create([
            'nip'      => '123456',
            'phone'    => '08123456789',
            'password' => Hash::make('correct-password'),
            'status'   => 'aktif',
        ]);

        // 3x gagal
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('login.process'), [
                'nip'      => '123456',
                'password' => 'wrong-password',
            ]);
        }

        // login sukses -> clear limiter
        $this->post(route('login.process'), [
            'nip'      => '123456',
            'password' => 'correct-password',
        ]);

        auth()->logout();

        // harus belum kena throttle
        $response = $this->post(route('login.process'), [
            'nip'      => '123456',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionDoesntHaveErrors('throttle');
    }

    public function test_register_throttle_after_five_failed_attempts(): void
    {
        // 5x gagal
        for ($i = 0; $i < 5; $i++) {
            $this->from(route('register'))->post(route('register.process'), [
                'name'                  => '',
                'nip'                   => '123456',
                'phone'                 => '',
                'email'                 => 'invalid-email',
                'bidang_unit'           => '',
                'jabatan'               => '',
                'password'              => '123',
                'password_confirmation' => '321',
            ]);
        }

        // request ke-6 harus throttle
        $response = $this->from(route('register'))->post(route('register.process'), [
            'name'                  => '',
            'nip'                   => '123456',
            'phone'                 => '',
            'email'                 => 'invalid-email',
            'bidang_unit'           => '',
            'jabatan'               => '',
            'password'              => '123',
            'password_confirmation' => '321',
        ]);

        $response->assertSessionHasErrors('throttle');
    }
}
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * AuthServiceProvider
 *
 * Menyediakan definisi policy dan Gate (otorisasi).
 * Untuk sekarang kita tidak akan daftarkan policy apa pun.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Policy mapping.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Contoh: 'App\Models\Post' => 'App\Policies\PostPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Contoh Gate custom (jika nanti dibutuhkan)
        // Gate::define('admin-only', fn($user) => $user->isAdmin());
    }
}

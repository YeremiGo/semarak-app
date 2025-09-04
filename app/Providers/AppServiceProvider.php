<?php

namespace App\Providers;

use App\Models\Notifiaksi;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate untuk mengelola data aset (tambah, edit, hapus)
        // Hanya Admin yang boleh.
        Gate::define('manage-aset', function (User $user) {
            return $user->role->name === 'admin';
        });

        // Gate untuk menghapus laporan
        // Hanya Admin yang boleh.
        Gate::define('delete-laporan', function (User $user) {
            return $user->role->name === 'admin';
        });

        // Gate untuk mengubah status laporan
        // Admin dan Dinas boleh.
        Gate::define('update-status-laporan', function (User $user) {
            return $user->role->name === 'dinas';
        });

        View::composer('layouts.app', function ($view) {
        $view->with('notifikasis', Notifiaksi::latest()->get());
        });
    }
}

<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\AccountList;
use App\Livewire\TradeCopyManager;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/accounts', AccountList::class)->name('accounts');
    Route::get('/trade-copy', TradeCopyManager::class)->name('trade-copy');
    
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';

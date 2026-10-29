<?php

use App\Http\Controllers\FinShieldController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FinShieldController::class, 'beranda'])->name('beranda');
Route::get('/edukasi-judol', [FinShieldController::class, 'edukasiJudol'])->name('edukasi-judol');
Route::get('/kalkulator-pinjol', [FinShieldController::class, 'kalkulatorPinjol'])->name('kalkulator-pinjol');
Route::get('/kelola-uang', [FinShieldController::class, 'kelolaUang'])->name('kelola-uang');

// Endpoint kalkulasi sisi server (opsional, untuk verifikasi / integrasi).
// Frontend utama menghitung secara real-time dengan Alpine.js tanpa memanggil endpoint ini.
// Jika dipanggil via fetch(), sertakan header X-CSRF-TOKEN dari <meta name="csrf-token">.
Route::post('/api/judol/hitung', [FinShieldController::class, 'hitungJudol'])->name('api.judol.hitung');
Route::post('/api/pinjol/hitung', [FinShieldController::class, 'hitungPinjol'])->name('api.pinjol.hitung');

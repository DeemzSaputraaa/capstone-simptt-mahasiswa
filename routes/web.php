<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormLegalisasiController;
use App\Http\Controllers\MhSyaratController;
use App\Http\Controllers\AkValidasiIjazahController;
use App\Http\Controllers\AkValidasiIjazahCommentController;

// API Routes untuk mengakses data dari database
Route::prefix('api')->group(function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'processLogin']);

    // Protected routes
    Route::middleware(['auth.session'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
    // Form Legalisasi routes
    Route::resource('form-legalisasi', FormLegalisasiController::class);

    // Mh Syarat routes
    Route::resource('mh-syarat', MhSyaratController::class);

    // Ak Validasi Ijazah routes
    Route::resource('ak-validasi-ijazah', AkValidasiIjazahController::class);

    // Ak Validasi Ijazah Comment routes
    Route::resource('ak-validasi-ijazah-comments', AkValidasiIjazahCommentController::class);

    // Route khusus untuk testing
    Route::get('comments/{id}', [AkValidasiIjazahCommentController::class, 'index']);
    Route::post('comments', [AkValidasiIjazahCommentController::class, 'store']);

    // Test endpoint sederhana
    Route::get('test-comments', function () {
        return response()->json([
            'message' => 'API working',
            'timestamp' => now(),
            'table_exists' => Schema::hasTable('comment')
        ]);
    });

    // Routes tambahan untuk data master
    Route::get('form-legalisasi-status', function () {
        return response()->json(\App\Models\FormLegalisasiStatus::all());
    });

    Route::get('mh-syarat-jenis', function () {
        return response()->json(\App\Models\MhSyaratJenis::all());
    });

    Route::get('mh-syarat-paket', function () {
        return response()->json(\App\Models\MhSyaratPaket::all());
    });

    Route::get('ak-tahun-akademik', function () {
        return response()->json(\App\Models\AkTahunAkademik::all());
    });
});

// Vue.js routes (untuk SPA)
Route::get('{any?}', function () {
    return view('application');
})->where('any', '.*');
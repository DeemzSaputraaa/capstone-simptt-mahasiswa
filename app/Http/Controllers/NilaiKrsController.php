<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NilaiKrsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $payload = $request->attributes->get('jwt_payload', []);
            $nim = $payload['username'] ?? $payload['nim'] ?? null;

            if (!$nim) {
                return response()->json([
                    'success' => false,
                    'code' => 'UNAUTHORIZED',
                    'message' => 'User not authenticated',
                ], 401);
            }

            $records = DB::table('ak_v_nilaikrs_terbaik')
                ->where('nim', $nim)
                ->select(
                    'kdkrsnilai',
                    'kodematakuliah',
                    'matakuliah',
                    'nilai',
                    'nilaiangka',
                    'sks'
                )
                ->orderBy('kdkrsnilai')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $records,
            ]);
        } catch (\Throwable $e) {
            Log::error('NilaiKrs index error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Failed to fetch nilai KRS data',
            ], 500);
        }
    }
}

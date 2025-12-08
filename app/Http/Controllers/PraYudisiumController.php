<?php

namespace App\Http\Controllers;

use App\Models\AkPraYudisium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PraYudisiumController extends Controller
{
    /**
     * Simpan data Pra Yudisium.
     */
    public function index()
    {
        try {
            $records = AkPraYudisium::query()
                ->leftJoin('mh_v_nama as m', 'm.kdmahasiswa', '=', 'ak_pra_yudisium.kdmahasiswa')
                ->select(
                    'ak_pra_yudisium.kdprayudisium',
                    'ak_pra_yudisium.kdmahasiswa',
                    'ak_pra_yudisium.berkas_foto_ijazah',
                    'ak_pra_yudisium.berkas_ijazah_terakhir',
                    'ak_pra_yudisium.berkas_kk_ktp',
                    'ak_pra_yudisium.is_validate',
                    'ak_pra_yudisium.tgl_validate',
                    'ak_pra_yudisium.comment',
                    'ak_pra_yudisium.create_at',
                    'm.nim',
                    'm.namalengkap'
                )
                ->orderByDesc('ak_pra_yudisium.create_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $records,
            ]);
        } catch (\Throwable $e) {
            Log::error('PraYudisium index error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Failed to fetch Pra Yudisium data',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $payload = $request->attributes->get('jwt_payload', []);
            $nim = $payload['username'] ?? null;

            if (!$nim) {
                return response()->json([
                    'success' => false,
                    'code' => 'UNAUTHORIZED',
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Ambil kdmahasiswa dari mh_v_nama berdasarkan nim
            $kdmahasiswa = DB::table('mh_v_nama')
                ->where('nim', $nim)
                ->value('kdmahasiswa');

            // Validasi input
            $validator = Validator::make($request->all(), [
                'berkas_foto_ijazah' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'berkas_ijazah_terakhir' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
                'berkas_kk_ktp' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'comment' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'code' => 'BAD_REQUEST',
                    'errors' => $validator->errors()->toArray(),
                ], 400);
            }

            // Simpan berkas ke storage
            $paths = [];
            $folder = 'pra-yudisium';

            if ($request->hasFile('berkas_foto_ijazah')) {
                $paths['berkas_foto_ijazah'] = $request->file('berkas_foto_ijazah')->store($folder, 'public');
            }

            if ($request->hasFile('berkas_ijazah_terakhir')) {
                $paths['berkas_ijazah_terakhir'] = $request->file('berkas_ijazah_terakhir')->store($folder, 'public');
            }

            if ($request->hasFile('berkas_kk_ktp')) {
                $paths['berkas_kk_ktp'] = $request->file('berkas_kk_ktp')->store($folder, 'public');
            }

            $record = AkPraYudisium::create([
                'berkas_foto_ijazah' => $paths['berkas_foto_ijazah'] ?? null,
                'berkas_ijazah_terakhir' => $paths['berkas_ijazah_terakhir'] ?? null,
                'berkas_kk_ktp' => $paths['berkas_kk_ktp'] ?? null,
                'kdmahasiswa' => $kdmahasiswa,
                'is_validate' => false,
                'tgl_validate' => null,
                'comment' => $request->get('comment'),
            ]);

            return response()->json([
                'success' => true,
                'data' => $record,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('PraYudisium store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Failed to save Pra Yudisium data',
            ], 500);
        }
    }
}

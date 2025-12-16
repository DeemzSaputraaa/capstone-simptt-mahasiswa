<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\AkValidasiIjazahMahasiswa;
use App\Models\AkValidasiIjazahMahasiswaComment;

class AkValidasiIjazahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $payload = $request->attributes->get('jwt_payload', []);
            $nim = $payload['username'] ?? null;

            if (!$nim) {
                return response()->json([
                    'success' => false,
                    'code' => 'UNAUTHORIZED',
                    'message' => 'User tidak terautentikasi',
                ], 401);
            }

            $kdmahasiswa = DB::table('mh_v_nama')
                ->where('nim', $nim)
                ->value('kdmahasiswa');

            if (!$kdmahasiswa) {
                return response()->json([
                    'success' => false,
                    'code' => 'NOT_FOUND',
                    'message' => 'Data mahasiswa tidak ditemukan',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'coment_ijazah' => 'nullable|string',
                'coment_transkrip' => 'nullable|string',
                'diambil_sendiri' => 'nullable|boolean',
                'surat_kuasa' => 'nullable|string',
                'tgl_diambil_ijazah' => 'nullable|date',
                'comment' => 'nullable|string', // komentar umum dari modal
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'code' => 'BAD_REQUEST',
                    'errors' => $validator->errors()->toArray(),
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Simpan / perbarui data validasi ijazah mahasiswa
                $record = AkValidasiIjazahMahasiswa::updateOrCreate(
                    ['kdmahasiswa' => $kdmahasiswa],
                    [
                        'is_ijazah_validate' => false,
                        'is_transkrip_validate' => false,
                        'coment_ijazah' => $request->input('coment_ijazah', $request->input('comment')),
                        'coment_transkrip' => $request->input('coment_transkrip'),
                        'diambil_sendiri' => $request->boolean('diambil_sendiri'),
                        'surat_kuasa' => $request->input('surat_kuasa'),
                        'tgl_diambil_ijazah' => $request->input('tgl_diambil_ijazah'),
                    ]
                );

                // Simpan komentar ke tabel comment jika ada
                $commentText = $request->input('comment');
                if ($commentText) {
                    AkValidasiIjazahMahasiswaComment::create([
                        'kdvalidasiijazahmahasiswa' => $record->kdvalidasiijazahmahasiswa,
                        'parent_id' => null,
                        'comment' => $commentText,
                        'create_at' => now(),
                        'update_at' => now(),
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $record,
                    'message' => 'Laporan validasi ijazah tersimpan',
                ], 201);
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::error('AkValidasiIjazah store error', [
                    'error' => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'code' => 'INTERNAL_SERVER_ERROR',
                    'message' => 'Gagal menyimpan laporan',
                ], 500);
            }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

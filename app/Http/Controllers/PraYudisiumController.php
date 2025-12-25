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
                    'ak_pra_yudisium.status_foto',
                    'ak_pra_yudisium.status_ijazah',
                    'ak_pra_yudisium.status_ktp',
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
                'status_foto' => 'submitted',
                'status_ijazah' => 'submitted',
                'status_ktp' => 'submitted',
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

    /**
     * Update komentar Pra Yudisium.
     */
    public function updateComment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'code' => 'BAD_REQUEST',
                    'errors' => $validator->errors()->toArray(),
                ], 400);
            }

            $affected = AkPraYudisium::where('kdprayudisium', $id)->update([
                'comment' => $request->get('comment'),
                'update_at' => now(),
            ]);

            if (!$affected) {
                return response()->json([
                    'success' => false,
                    'code' => 'NOT_FOUND',
                    'message' => 'Record not found',
                ], 404);
            }

            $updated = AkPraYudisium::where('kdprayudisium', $id)->first();

            return response()->json([
                'success' => true,
                'data' => $updated,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('PraYudisium updateComment error', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Failed to update comment',
            ], 500);
        }
    }

    /**
     * Update berkas Pra Yudisium (resubmission).
     */
    public function update(Request $request, $id)
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

            $record = AkPraYudisium::where('kdprayudisium', $id)->first();
            if (!$record) {
                return response()->json([
                    'success' => false,
                    'code' => 'NOT_FOUND',
                    'message' => 'Record not found',
                ], 404);
            }

            $kdmahasiswa = DB::table('mh_v_nama')
                ->where('nim', $nim)
                ->value('kdmahasiswa');

            if (!$kdmahasiswa || (string) $record->kdmahasiswa !== (string) $kdmahasiswa) {
                return response()->json([
                    'success' => false,
                    'code' => 'FORBIDDEN',
                    'message' => 'Forbidden',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'berkas_foto_ijazah' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'berkas_ijazah_terakhir' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
                'berkas_kk_ktp' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'code' => 'BAD_REQUEST',
                    'errors' => $validator->errors()->toArray(),
                ], 400);
            }

            $hasNewFiles = $request->hasFile('berkas_foto_ijazah')
                || $request->hasFile('berkas_ijazah_terakhir')
                || $request->hasFile('berkas_kk_ktp');

            if (!$hasNewFiles) {
                return response()->json([
                    'success' => false,
                    'code' => 'BAD_REQUEST',
                    'message' => 'No new files uploaded',
                ], 400);
            }

            $folder = 'pra-yudisium';
            $updates = [
                'is_validate' => false,
                'tgl_validate' => null,
                'comment' => null,
                'update_at' => now(),
            ];

            if ($request->hasFile('berkas_foto_ijazah')) {
                if ($record->berkas_foto_ijazah) {
                    Storage::disk('public')->delete($record->berkas_foto_ijazah);
                }
                $updates['berkas_foto_ijazah'] = $request->file('berkas_foto_ijazah')->store($folder, 'public');
                $updates['status_foto'] = 'submitted';
            }

            if ($request->hasFile('berkas_ijazah_terakhir')) {
                if ($record->berkas_ijazah_terakhir) {
                    Storage::disk('public')->delete($record->berkas_ijazah_terakhir);
                }
                $updates['berkas_ijazah_terakhir'] = $request->file('berkas_ijazah_terakhir')->store($folder, 'public');
                $updates['status_ijazah'] = 'submitted';
            }

            if ($request->hasFile('berkas_kk_ktp')) {
                if ($record->berkas_kk_ktp) {
                    Storage::disk('public')->delete($record->berkas_kk_ktp);
                }
                $updates['berkas_kk_ktp'] = $request->file('berkas_kk_ktp')->store($folder, 'public');
                $updates['status_ktp'] = 'submitted';
            }

            $record->update($updates);

            return response()->json([
                'success' => true,
                'data' => $record->fresh(),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('PraYudisium update error', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Failed to update Pra Yudisium data',
            ], 500);
        }
    }

    /**
     * Update status dokumen Pra Yudisium.
     */
    public function updateDocumentStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status_foto' => 'nullable|in:submitted,revision,approved',
                'status_ijazah' => 'nullable|in:submitted,revision,approved',
                'status_ktp' => 'nullable|in:submitted,revision,approved',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'code' => 'BAD_REQUEST',
                    'errors' => $validator->errors()->toArray(),
                ], 400);
            }

            $record = AkPraYudisium::where('kdprayudisium', $id)->first();
            if (!$record) {
                return response()->json([
                    'success' => false,
                    'code' => 'NOT_FOUND',
                    'message' => 'Record not found',
                ], 404);
            }

            $updates = array_filter([
                'status_foto' => $request->get('status_foto'),
                'status_ijazah' => $request->get('status_ijazah'),
                'status_ktp' => $request->get('status_ktp'),
            ], fn ($value) => $value !== null);

            if (!$updates) {
                return response()->json([
                    'success' => false,
                    'code' => 'BAD_REQUEST',
                    'message' => 'No status provided',
                ], 400);
            }

            $updates['update_at'] = now();

            $hasRevision = in_array('revision', $updates, true);
            if ($hasRevision) {
                $updates['is_validate'] = false;
                $updates['tgl_validate'] = null;
            }

            $record->update($updates);

            return response()->json([
                'success' => true,
                'data' => $record->fresh(),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('PraYudisium updateDocumentStatus error', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Failed to update document status',
            ], 500);
        }
    }

    /**
     * Hapus data Pra Yudisium beserta berkasnya.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $record = AkPraYudisium::where('kdprayudisium', $id)->first();

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'code' => 'NOT_FOUND',
                    'message' => 'Record not found',
                ], 404);
            }

            // Hapus file yang tersimpan di storage (jika ada)
            $paths = [
                $record->berkas_foto_ijazah,
                $record->berkas_ijazah_terakhir,
                $record->berkas_kk_ktp,
            ];

            foreach ($paths as $path) {
                if ($path) {
                    Storage::disk('public')->delete($path);
                }
            }

            $record->forceDelete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pra Yudisium berhasil dihapus',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('PraYudisium destroy error', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Failed to delete record',
            ], 500);
        }
    }

    /**
     * Approve pengajuan Pra Yudisium.
     */
    public function approve($id)
    {
        try {
            $record = AkPraYudisium::where('kdprayudisium', $id)->first();

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'code' => 'NOT_FOUND',
                    'message' => 'Record not found',
                ], 404);
            }

            $record->is_validate = true;
            $record->tgl_validate = now();
            $record->status_foto = 'approved';
            $record->status_ijazah = 'approved';
            $record->status_ktp = 'approved';
            $record->update_at = now();
            $record->save();

            return response()->json([
                'success' => true,
                'data' => $record,
                'message' => 'Pra Yudisium approved',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('PraYudisium approve error', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Failed to approve Pra Yudisium',
            ], 500);
        }
    }
}

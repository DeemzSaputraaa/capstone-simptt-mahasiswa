<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AkValidasiIjazahMahasiswaComment;
use App\Models\AkValidasiIjazahMahasiswa;
use Illuminate\Support\Facades\DB;

class AkValidasiIjazahCommentController extends Controller
{
    /**
     * Cache sederhana agar lookup nama mahasiswa tidak berulang.
     */
    private array $commenterNameCache = [];

    /**
     * Menampilkan semua komentar untuk validasi ijazah tertentu
     */
    public function index(Request $request, $validasiIjazahId)
    {
        try {
            \Log::info('Loading comments for validasi_ijazah_id: ' . $validasiIjazahId);

            $resolvedValidasiId = $this->resolveValidasiId($request, $validasiIjazahId);
            if (!$resolvedValidasiId) {
                return response()->json([]);
            }

            // Ambil komentar utama (tanpa parent)
            $comments = AkValidasiIjazahMahasiswaComment::with(['replies.validasiIjazah', 'validasiIjazah'])
                ->where('kdvalidasiijazahmahasiswa', $resolvedValidasiId)
                ->whereNull('parent_id')
                ->orderBy('kdcomment', 'desc')
                ->get();

            \Log::info('Found comments count: ' . $comments->count());
            \Log::info('Comments data: ' . $comments->toJson());

            $transformedComments = $this->transformComments($comments);

            \Log::info('Transformed comments: ' . json_encode($transformedComments));

            return response()->json($transformedComments);
        } catch (\Exception $e) {
            \Log::error('Error loading comments: ' . $e->getMessage());
            // Return empty array jika ada error
            return response()->json([]);
        }
    }

    /**
     * Menyimpan komentar baru
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Storing comment with data: ' . json_encode($request->all()));

            $validated = $request->validate([
                'comment' => 'required|string',
                'parent_id' => 'nullable|integer',
                'kdvalidasiijazahmahasiswa' => 'nullable|integer',
                'validasi_ijazah_id' => 'nullable|integer', // fallback name
            ]);

            $resolvedValidasiId = $this->resolveValidasiId(
                $request,
                $validated['kdvalidasiijazahmahasiswa'] ?? $validated['validasi_ijazah_id'] ?? null,
                true // create jika belum ada untuk user ini
            );

            if (!$resolvedValidasiId) {
                return response()->json(['error' => 'Validasi ijazah tidak ditemukan untuk pengguna ini'], 404);
            }

            $dataToStore = [
                'kdvalidasiijazahmahasiswa' => $resolvedValidasiId,
                'parent_id' => $validated['parent_id'] ?? null,
                'comment' => $validated['comment'],
                'create_at' => now(),
                'update_at' => now(),
            ];

            \Log::info('Data to store: ' . json_encode($dataToStore));

            $comment = AkValidasiIjazahMahasiswaComment::create($dataToStore);
            // Pastikan relasi validasi ter-load untuk kebutuhan nama
            $comment->load('validasiIjazah');

            \Log::info('Comment created successfully: ' . $comment->toJson());

            return response()->json($this->normalizeComment($comment), 201);
        } catch (\Exception $e) {
            \Log::error('Error storing comment: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to store comment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan komentar tertentu
     */
    public function show($id)
    {
        $comment = AkValidasiIjazahMahasiswaComment::with('replies')->findOrFail($id);
        return response()->json($comment);
    }

    /**
     * Update komentar
     */
    public function update(Request $request, $id)
    {
        $comment = AkValidasiIjazahMahasiswaComment::findOrFail($id);

        $validated = $request->validate([
            'content' => 'sometimes|string',
        ]);

        $comment->update($validated);

        return response()->json($comment);
    }

    /**
     * Hapus komentar
     */
    public function destroy($id)
    {
        $comment = AkValidasiIjazahMahasiswaComment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Komentar berhasil dihapus']);
    }

    /**
     * Transform komentar untuk frontend (mengubah struktur data)
     */
    private function transformComments($comments)
    {
        return $comments->map(function ($comment) {
            return $this->normalizeComment($comment);
        });
    }

    /**
     * Transform replies untuk frontend
     */
    private function transformReplies($replies)
    {
        return $replies->map(function ($reply) {
            return [
                'id' => $reply->getKey(),
                'user' => $this->resolveCommenterName($reply),
                'text' => $reply->comment,
                'date' => optional($reply->create_at ?? $reply->created_at ?? now())->toDateTimeString(),
                'replies' => $this->transformReplies($reply->replies ?? collect()),
            ];
        });
    }

    /**
     * Normalisasi komentar ke bentuk yang dipakai frontend
     */
    private function normalizeComment(AkValidasiIjazahMahasiswaComment $comment): array
    {
        return [
            'id' => $comment->getKey(),
            'user' => $this->resolveCommenterName($comment),
            'text' => $comment->comment ?? '',
            'date' => optional($comment->create_at ?? $comment->created_at ?? now())->toDateTimeString(),
            'replies' => $this->transformReplies($comment->replies ?? collect()),
        ];
    }

    /**
     * Ambil nama mahasiswa dari relasi user atau dari kdmahasiswa pada validasi ijazah.
     */
    private function resolveCommenterName(AkValidasiIjazahMahasiswaComment $comment): string
    {
        // Jika ini balasan (parent_id ada) tanpa penanda, fallback Admin
        if (!is_null($comment->parent_id)) {
            return 'Admin';
        }

        // Jika relasi user tersedia, pakai itu dulu
        if ($comment->relationLoaded('user') && $comment->user) {
            return $comment->user->name ?? 'Mahasiswa';
        }

        // Cek cache per kdvalidasi
        $key = $comment->kdvalidasiijazahmahasiswa;
        if ($key && isset($this->commenterNameCache[$key])) {
            return $this->commenterNameCache[$key];
        }

        // Ambil kdmahasiswa dari relasi yang sudah di-load atau query sekali
        if ($comment->relationLoaded('validasiIjazah') && $comment->validasiIjazah) {
            $kdMahasiswa = $comment->validasiIjazah->kdmahasiswa;
        } else {
            $kdMahasiswa = AkValidasiIjazahMahasiswa::where('kdvalidasiijazahmahasiswa', $comment->kdvalidasiijazahmahasiswa)
                ->value('kdmahasiswa');
        }

        if (!$kdMahasiswa) {
            return 'Mahasiswa';
        }

        // Ambil nama dari view mh_v_nama
        // Ambil nama lengkap (kolom umum di mh_v_nama)
        $nama = DB::table('mh_v_nama')
            ->where('kdmahasiswa', $kdMahasiswa)
            ->value('namalengkap');

        $nama = $nama ?: 'Mahasiswa';
        if ($key) {
            $this->commenterNameCache[$key] = $nama;
        }

        return $nama;
    }

    /**
     * Cari kdvalidasiijazahmahasiswa berdasarkan request atau user login
     */
    private function resolveValidasiId(Request $request, $validasiIjazahId = null, bool $createIfMissing = false): ?int
    {
        // Jika diberikan ID numerik langsung, pakai itu
        if ($validasiIjazahId && $validasiIjazahId !== 'me') {
            return (int) $validasiIjazahId;
        }

        // Ambil nim dari payload JWT
        $payload = $request->attributes->get('jwt_payload', []);
        $nim = $payload['username'] ?? $payload['nim'] ?? null;

        if (!$nim) {
            return null;
        }

        // Ambil kdmahasiswa dari view nama
        $kdmahasiswa = DB::table('mh_v_nama')
            ->where('nim', $nim)
            ->value('kdmahasiswa');

        if (!$kdmahasiswa) {
            return null;
        }

        $existing = AkValidasiIjazahMahasiswa::where('kdmahasiswa', $kdmahasiswa)
            ->first();

        if ($existing) {
            return $existing->kdvalidasiijazahmahasiswa;
        }

        if (!$createIfMissing) {
            return null;
        }

        $created = AkValidasiIjazahMahasiswa::create([
            'kdmahasiswa' => $kdmahasiswa,
            'is_ijazah_validate' => false,
            'is_transkrip_validate' => false,
        ]);

        return $created->kdvalidasiijazahmahasiswa ?? null;
    }
}

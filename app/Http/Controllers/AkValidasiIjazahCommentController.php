<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AkValidasiIjazahMahasiswaComment;
use App\Models\AkValidasiIjazahMahasiswa;

class AkValidasiIjazahCommentController extends Controller
{
    /**
     * Menampilkan semua komentar untuk validasi ijazah tertentu
     */
    public function index($validasiIjazahId)
    {
        try {
            \Log::info('Loading comments for validasi_ijazah_id: ' . $validasiIjazahId);

            // Coba query sederhana dulu tanpa relasi
            $comments = AkValidasiIjazahMahasiswaComment::where('validasi_ijazah_id', $validasiIjazahId)
                ->whereNull('parent_id')
                ->orderBy('id', 'desc')
                ->get();

            \Log::info('Found comments count: ' . $comments->count());
            \Log::info('Comments data: ' . $comments->toJson());

            // Transform data sederhana untuk frontend
            $transformedComments = $comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'user' => 'User', // Default user name
                    'text' => $comment->content ?? $comment->text ?? '',
                    'date' => new \DateTime($comment->created_at ?? now()),
                    'replies' => [],
                ];
            });

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

            // Validasi yang lebih fleksibel
            $data = [
                'validasi_ijazah_id' => $request->input('validasi_ijazah_id', 1),
                'parent_id' => $request->input('parent_id'),
                'user_id' => $request->input('user_id', 1),
                'content' => $request->input('content'),
            ];

            \Log::info('Data to store: ' . json_encode($data));

            $comment = AkValidasiIjazahMahasiswaComment::create($data);

            \Log::info('Comment created successfully: ' . $comment->toJson());

            return response()->json($comment, 201);
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
            return [
                'id' => $comment->id,
                'user' => $comment->user ? $comment->user->name : 'Unknown User',
                'text' => $comment->content,
                'date' => new \DateTime($comment->created_at),
                'replies' => $this->transformReplies($comment->replies),
            ];
        });
    }

    /**
     * Transform replies untuk frontend
     */
    private function transformReplies($replies)
    {
        return $replies->map(function ($reply) {
            return [
                'id' => $reply->id,
                'user' => $reply->user ? $reply->user->name : 'Unknown User',
                'text' => $reply->content,
                'date' => new \DateTime($reply->created_at),
                'replies' => $this->transformReplies($reply->replies ?? collect()),
            ];
        });
    }
}

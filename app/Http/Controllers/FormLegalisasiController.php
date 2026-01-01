<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormLegalisasi;
use App\Models\FormLegalisasiStatus;

class FormLegalisasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Filter by logged-in mahasiswa (berdasarkan nim -> kdmahasiswa)
        $payload = request()->attributes->get('jwt_payload', []);
        $nim = $payload['username'] ?? null;
        $kdmahasiswa = null;

        if ($nim) {
            $kdmahasiswa = \DB::table('mh_v_nama')->where('nim', $nim)->value('kdmahasiswa');
        }

        $query = FormLegalisasi::query()
            ->leftJoin('mh_v_nama as m', 'm.kdmahasiswa', '=', 'form_legalisasi.kdmahasiswa')
            ->select('form_legalisasi.*', 'm.nim', 'm.namalengkap as nama_mahasiswa')
            ->orderByDesc('form_legalisasi.create_at');

        if ($kdmahasiswa) {
            $query->where('form_legalisasi.kdmahasiswa', $kdmahasiswa);
        }

        $formLegalisasi = $query->get();

        return response()->json([
            'success' => true,
            'data' => $formLegalisasi,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = FormLegalisasiStatus::all();
        return response()->json(['statuses' => $statuses]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kdmahasiswa' => 'nullable|integer',
            'jumlah_legalisasi' => 'required|integer|min:1',
            'biaya_legalisasi' => 'nullable|numeric',
            'alamat_kirim' => 'required|string',
            'nama_penerima_legalisasi' => 'required|string|max:255',
            'noresi' => 'nullable|string|max:100',
            'idtagihan' => 'nullable|string|max:100',
            'tgl_dikirim' => 'nullable|date',
            'kdlegalisasi_sebelum' => 'nullable|integer',
            'telp_penerima' => 'nullable|string|max:50',
            'comment' => 'nullable|string',
            'dokumen' => 'nullable|string|max:50',
        ]);

        // Jika kdmahasiswa tidak dikirim, ambil dari payload JWT (nim -> kdmahasiswa)
        if (empty($validated['kdmahasiswa'])) {
            $payload = $request->attributes->get('jwt_payload', []);
            $nim = $payload['username'] ?? null;
            if ($nim) {
                $kd = \DB::table('mh_v_nama')->where('nim', $nim)->value('kdmahasiswa');
                if ($kd) {
                    $validated['kdmahasiswa'] = $kd;
                }
            }
        }

        $formLegalisasi = FormLegalisasi::create($validated);
        return response()->json([
            'success' => true,
            'data' => $formLegalisasi,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $formLegalisasi = FormLegalisasi::with(['status', 'history'])->findOrFail($id);
        return response()->json($formLegalisasi);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $formLegalisasi = FormLegalisasi::findOrFail($id);
        $statuses = FormLegalisasiStatus::all();
        return response()->json([
            'formLegalisasi' => $formLegalisasi,
            'statuses' => $statuses
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kdmahasiswa' => 'sometimes|required|integer',
            'jumlah_legalisasi' => 'sometimes|required|integer|min:1',
            'biaya_legalisasi' => 'nullable|numeric',
            'alamat_kirim' => 'sometimes|required|string',
            'nama_penerima_legalisasi' => 'sometimes|required|string|max:255',
            'noresi' => 'nullable|string|max:100',
            'idtagihan' => 'nullable|string|max:100',
            'tgl_dikirim' => 'nullable|date',
            'kdlegalisasi_sebelum' => 'nullable|integer',
            'telp_penerima' => 'nullable|string|max:50',
            'comment' => 'nullable|string',
            'dokumen' => 'nullable|string|max:50',
            'status_penerimaan' => 'nullable|in:received,not_received',
        ]);

        $formLegalisasi = FormLegalisasi::findOrFail($id);
        $formLegalisasi->update($validated);

        $newRecord = null;
        $requestedStatus = $validated['status_penerimaan'] ?? null;
        if ($requestedStatus === 'not_received' && $formLegalisasi->status_penerimaan === 'not_received') {
            $newRecord = FormLegalisasi::create([
                'kdmahasiswa' => $formLegalisasi->kdmahasiswa,
                'jumlah_legalisasi' => $formLegalisasi->jumlah_legalisasi,
                'biaya_legalisasi' => null,
                'alamat_kirim' => $formLegalisasi->alamat_kirim,
                'nama_penerima_legalisasi' => $formLegalisasi->nama_penerima_legalisasi,
                'noresi' => null,
                'idtagihan' => null,
                'tgl_dikirim' => null,
                'kdlegalisasi_sebelum' => $formLegalisasi->getKey(),
                'telp_penerima' => $formLegalisasi->telp_penerima,
                'comment' => $formLegalisasi->comment,
                'dokumen' => $formLegalisasi->dokumen,
                'status_penerimaan' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $formLegalisasi,
            'new_record' => $newRecord,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $formLegalisasi = FormLegalisasi::findOrFail($id);
        $formLegalisasi->delete();
        return response()->json(['message' => 'Form legalisasi berhasil dihapus']);
    }
}

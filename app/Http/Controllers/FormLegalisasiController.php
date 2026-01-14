<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormLegalisasi;
use App\Models\FormLegalisasiStatus;
use Illuminate\Support\Facades\Schema;

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

        $mhNama = \DB::table('mh_v_nama')
            ->select(
                'kdmahasiswa',
                \DB::raw('MIN(nim) as nim'),
                \DB::raw('MIN(namalengkap) as namalengkap')
            )
            ->groupBy('kdmahasiswa');

        $query = FormLegalisasi::query()
            ->leftJoinSub($mhNama, 'm', 'm.kdmahasiswa', '=', 'form_legalisasi.kdmahasiswa')
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
            'alamat_kirim' => 'required|string|max:255|regex:/^[A-Za-z0-9\\s\\.,-]+$/',
            'nama_penerima_legalisasi' => 'required|string|max:255|regex:/^[A-Za-z\\s]+$/',
            'noresi' => 'nullable|string|max:100|regex:/^[A-Za-z0-9]+$/',
            'idtagihan' => 'nullable|string|max:100',
            'tgl_dikirim' => 'nullable|date',
            'kdlegalisasi_sebelum' => 'nullable|integer',
            'telp_penerima' => 'nullable|string|max:50|regex:/^[0-9]+$/',
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
        $this->recordHistory($formLegalisasi, $this->resolveLegalisasiStatusName($formLegalisasi));
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
            'alamat_kirim' => 'sometimes|required|string|max:255|regex:/^[A-Za-z0-9\\s\\.,-]+$/',
            'nama_penerima_legalisasi' => 'sometimes|required|string|max:255|regex:/^[A-Za-z\\s]+$/',
            'noresi' => 'nullable|string|max:100|regex:/^[A-Za-z0-9]+$/',
            'idtagihan' => 'nullable|string|max:100',
            'tgl_dikirim' => 'nullable|date',
            'kdlegalisasi_sebelum' => 'nullable|integer',
            'telp_penerima' => 'nullable|string|max:50|regex:/^[0-9]+$/',
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

        $this->recordHistory($formLegalisasi, $this->resolveLegalisasiStatusName($formLegalisasi));
        if ($newRecord) {
            $this->recordHistory($newRecord, $this->resolveLegalisasiStatusName($newRecord));
        }

        return response()->json([
            'success' => true,
            'data' => $formLegalisasi,
            'new_record' => $newRecord,
        ]);
    }

    private function resolveLegalisasiStatusName(FormLegalisasi $formLegalisasi): string
    {
        $hasBiaya = $formLegalisasi->biaya_legalisasi !== null
            && $formLegalisasi->biaya_legalisasi !== '';
        $hasResi = !empty($formLegalisasi->noresi);

        if ($formLegalisasi->status_penerimaan === 'received') {
            return 'Diterima';
        }
        if ($formLegalisasi->status_penerimaan === 'not_received') {
            return 'Gagal';
        }
        if ($hasBiaya && $hasResi && !empty($formLegalisasi->tgl_dikirim)) {
            return 'Dikirim';
        }
        if ($hasBiaya && $hasResi) {
            return 'Proses Pengiriman';
        }
        if ($hasBiaya && !$hasResi) {
            return 'Belum Dibayar';
        }

        return 'Pending';
    }

    private function recordHistory(FormLegalisasi $formLegalisasi, string $statusName): void
    {
        $statusTable = 'form_legalisasi_status';
        $historyTable = 'form_legalisasi_history';

        $statusColumn = Schema::hasColumn($statusTable, 'status')
            ? 'status'
            : (Schema::hasColumn($statusTable, 'nama') ? 'nama' : null);
        $statusIdColumn = Schema::hasColumn($statusTable, 'kdlegalisasistatus')
            ? 'kdlegalisasistatus'
            : (Schema::hasColumn($statusTable, 'id') ? 'id' : null);

        if (!$statusColumn || !$statusIdColumn) {
            return;
        }

        $statusRecord = \DB::table($statusTable)->where($statusColumn, $statusName)->first();
        if ($statusRecord) {
            $statusId = $statusRecord->{$statusIdColumn} ?? null;
        } else {
            $statusId = \DB::table($statusTable)->insertGetId([
                $statusColumn => $statusName,
            ], $statusIdColumn);
        }

        if (!$statusId) {
            return;
        }

        $historyStatusColumn = Schema::hasColumn($historyTable, 'kdlegalisasistatus')
            ? 'kdlegalisasistatus'
            : (Schema::hasColumn($historyTable, 'status') ? 'status' : null);
        $historyLegalisasiColumn = Schema::hasColumn($historyTable, 'kdlegalisasi')
            ? 'kdlegalisasi'
            : (Schema::hasColumn($historyTable, 'form_legalisasi_id') ? 'form_legalisasi_id' : null);
        $historyDateColumn = Schema::hasColumn($historyTable, 'tgl_history')
            ? 'tgl_history'
            : (Schema::hasColumn($historyTable, 'created_at') ? 'created_at' : null);

        if (!$historyStatusColumn || !$historyLegalisasiColumn || !$historyDateColumn) {
            return;
        }

        $orderColumn = Schema::hasColumn($historyTable, 'tgl_history')
            ? 'tgl_history'
            : (Schema::hasColumn($historyTable, 'created_at')
                ? 'created_at'
                : (Schema::hasColumn($historyTable, 'kdlegalisasihistory') ? 'kdlegalisasihistory' : null));

        $query = \DB::table($historyTable)
            ->where($historyLegalisasiColumn, $formLegalisasi->getKey());
        if ($orderColumn) {
            $query->orderByDesc($orderColumn);
        }
        $lastStatusId = $query->value($historyStatusColumn);

        if ((string) $lastStatusId === (string) $statusId) {
            return;
        }

        \DB::table($historyTable)->insert([
            $historyLegalisasiColumn => $formLegalisasi->getKey(),
            $historyStatusColumn => $statusId,
            $historyDateColumn => now(),
        ]);

        if (Schema::hasColumn('form_legalisasi', 'kdlegalisasilstatus')) {
            $formLegalisasi->kdlegalisasilstatus = $statusId;
            $formLegalisasi->save();
        }
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

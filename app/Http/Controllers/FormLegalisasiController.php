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
        $formLegalisasi = FormLegalisasi::with(['status', 'history'])->get();
        return response()->json($formLegalisasi);
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
            // Tambahkan validasi sesuai dengan struktur tabel
        ]);

        $formLegalisasi = FormLegalisasi::create($validated);
        return response()->json($formLegalisasi, 201);
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
            // Tambahkan validasi sesuai dengan struktur tabel
        ]);

        $formLegalisasi = FormLegalisasi::findOrFail($id);
        $formLegalisasi->update($validated);
        return response()->json($formLegalisasi);
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

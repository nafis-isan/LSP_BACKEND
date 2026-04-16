<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TukController extends Controller
{
    public function index()
    {
        $tuks = Tuk::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $tuks
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'foto' => 'nullable|image',
            'nama_tuk' => 'required',
            'jenis_tuk' => 'required|in:sewaktu,mandiri,tempat kerja',
            'deskripsi' => 'nullable',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('tuk', 'public');
        }

        $tuk = Tuk::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditambahkan',
            'data' => $tuk
        ], 201);
    }

    public function show(Tuk $tuk)
    {
        return response()->json([
            'success' => true,
            'data' => $tuk
        ]);
    }

    public function update(Request $request, Tuk $tuk)
    {
        $data = $request->validate([
            'foto' => 'nullable|image',
            'nama_tuk' => 'required',
            'jenis_tuk' => 'required|in:sewaktu,mandiri,tempat kerja',
            'deskripsi' => 'nullable',
        ]);

        if ($request->hasFile('foto')) {
            if ($tuk->foto) {
                Storage::disk('public')->delete($tuk->foto);
            }
            $data['foto'] = $request->file('foto')->store('tuk', 'public');
        }

        $tuk->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate',
            'data' => $tuk
        ]);
    }

    public function destroy(Tuk $tuk)
    {
        if ($tuk->foto) {
            Storage::disk('public')->delete($tuk->foto);
        }

        $tuk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
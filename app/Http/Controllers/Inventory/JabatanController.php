<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $data = Jabatan::orderBy('id_jabatan')->get();
        return view('jabatan.index', compact('data'));
    }

    public function create()
    {
        return view('jabatan.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nama_jabatan' => 'required|string|max:255']);
        Jabatan::create($request->only('nama_jabatan'));

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        return view('jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_jabatan' => 'required|string|max:255']);
        Jabatan::where('id_jabatan', $id)->update($request->only('nama_jabatan'));

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil diperbarui');
    }

    public function destroy($id)
    {
        Jabatan::where('id_jabatan', $id)->delete();
        return redirect()->route('jabatan.index')->with('success', 'Jabatan dihapus');
    }
}

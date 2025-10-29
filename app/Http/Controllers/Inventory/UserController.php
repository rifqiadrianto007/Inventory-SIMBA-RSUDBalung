<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserInventory;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = UserInventory::orderBy('username')->get();
        return view('akun.index', compact('users'));
    }

    public function create()
    {
        return view('akun.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|unique:user_inventory,username',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'status' => 'nullable|string',
        ]);

        $data['password'] = Hash::make($data['password']);
        UserInventory::create($data);

        return redirect()->route('akun.index')->with('success', 'Akun baru berhasil dibuat.');
    }

    public function edit($id)
    {
        $user = UserInventory::findOrFail($id);
        return view('akun.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = UserInventory::findOrFail($id);

        $data = $request->validate([
            'username' => 'required|string|unique:user_inventory,username,' . $user->user_id . ',user_id',
            'role' => 'required|string',
            'status' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return redirect()->route('akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = UserInventory::findOrFail($id);
        $user->delete();
        return redirect()->route('akun.index')->with('success', 'Akun berhasil dihapus.');
    }
}

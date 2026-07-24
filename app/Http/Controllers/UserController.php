<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('q')) {
            $query->where('nama_lengkap', 'like', '%' . $request->q . '%')
                  ->orWhere('username', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('user.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:100|unique:tb_users,username',
            'password'     => 'required|string|min:6',
            'role'         => ['required', Rule::in(['super_admin', 'admin_pdd', 'mahasiswa'])],
            'is_aktif'     => 'boolean',
        ]);

        $user = User::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'username'     => $validated['username'],
            'password'     => Hash::make($validated['password']),
            'role'         => $validated['role'],
            'is_aktif'     => $request->has('is_aktif'),
        ]);

        LogAktivitas::catat(Auth::id(), 'Membuat akun pengguna: ' . $user->username, $request->ip());

        return redirect()->back()->with('success', 'Akun anggota berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => ['required', 'string', 'max:100', Rule::unique('tb_users')->ignore($user->id_user, 'id_user')],
            'password'     => 'nullable|string|min:6',
            'role'         => ['required', Rule::in(['super_admin', 'admin_pdd', 'mahasiswa'])],
            'is_aktif'     => 'boolean',
        ]);

        $data = [
            'nama_lengkap' => $validated['nama_lengkap'],
            'username'     => $validated['username'],
            'role'         => $validated['role'],
            'is_aktif'     => $request->has('is_aktif'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        LogAktivitas::catat(Auth::id(), 'Memperbarui akun pengguna: ' . $user->username, $request->ip());

        return redirect()->back()->with('success', 'Akun anggota berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id_user === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $username = $user->username;
        $user->delete();

        LogAktivitas::catat(Auth::id(), 'Menghapus akun pengguna: ' . $username, $request->ip());

        return redirect()->back()->with('success', 'Akun anggota berhasil dihapus.');
    }
}

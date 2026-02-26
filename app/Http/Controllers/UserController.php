<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('role', 'like', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        return view('dashboard.pengguna', compact('users', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,kasir,owner',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function update(Request $request, User $pengguna)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($pengguna->id)],
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|in:admin,kasir,owner',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        return redirect()->route('pengguna.index')->with('success', 'Data Pengguna berhasil diperbarui!');
    }

    public function destroy(User $pengguna)
    {
        // Cegah admin menghapus dirinya sendiri (opsional tapi disarankan)
        if (auth()->id() === $pengguna->id) {
             return redirect()->route('pengguna.index')->withErrors(['error' => 'Anda tidak bisa menghapus akun ada sendiri.']);
        }

        $pengguna->delete();
        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}

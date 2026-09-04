<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar anggota / pengguna sistem.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Fitur Pencarian Cerdas
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telegram_id', 'like', "%{$search}%")
                  ->orWhere('divisi', 'like', "%{$search}%")
                  ->orWhere('witel', 'like', "%{$search}%");
            });
        }

        // Filter Role
        if ($request->filled('role') && $request->role !== 'all') {
            $roleFilter = strtolower($request->role);
            if ($roleFilter === 'pikol') {
                $query->where(function ($q) {
                    $q->whereIn('role', ['pikol', 'admin', 'supervisor'])
                      ->orWhereIn('email', ['admin@telkom.com', 'admin@telkom.co.id']);
                });
            } elseif ($roleFilter === 'sa' || $roleFilter === 'hotd') {
                $query->where(function ($q) {
                    $q->whereIn('role', ['sa', 'hotd', 'sales'])
                      ->orWhereNull('role');
                })->whereNotIn('email', ['admin@telkom.com', 'admin@telkom.co.id']);
            }
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'all') {
            $statusFilter = strtolower($request->status);
            if ($statusFilter === 'aktif') {
                $query->where(function ($q) {
                    $q->where('status', 'aktif')->orWhereNull('status');
                });
            } elseif ($statusFilter === 'tidak_aktif') {
                $query->where('status', 'tidak_aktif');
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Statistik Ringkas
        $totalUsers = User::count();
        $totalPikol = User::whereIn('role', ['pikol', 'admin', 'supervisor'])->orWhereIn('email', ['admin@telkom.com', 'admin@telkom.co.id'])->count();
        $totalSa = User::whereNotIn('email', ['admin@telkom.com', 'admin@telkom.co.id'])->where(function ($q) {
            $q->whereIn('role', ['sa', 'hotd', 'sales'])->orWhereNull('role');
        })->count();
        $totalHotd = $totalSa; // Backwards compatibility
        $totalAktif = User::where('status', 'aktif')->orWhereNull('status')->count();
        $totalNonaktif = User::where('status', 'tidak_aktif')->count();

        return view('users.index', compact(
            'users',
            'totalUsers',
            'totalPikol',
            'totalSa',
            'totalHotd',
            'totalAktif',
            'totalNonaktif'
        ));
    }

    /**
     * Daftarkan anggota / agensi baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:100',
            'kode' => 'nullable|string|max:50',
            'email' => 'required|string|email|max:255|unique:users,email',
            'telegram_id' => 'nullable|string|max:50',
            'password' => 'required|string|min:6',
            'role' => 'required|in:pikol,sa,hotd',
            'status' => 'required|in:aktif,tidak_aktif',
            'divisi' => 'nullable|string|max:100',
            'witel' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'status.required' => 'Status akun wajib dipilih.',
        ]);

        // Standardize role to 'sa' if 'hotd' is passed
        $role = ($request->role === 'hotd') ? 'sa' : $request->role;

        // Generate username otomatis jika tidak diisi
        $username = $request->username ?: strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $request->email)[0]));

        User::create([
            'name' => $request->name,
            'username' => $username,
            'kode' => $request->kode,
            'email' => $request->email,
            'telegram_id' => $request->telegram_id,
            'password' => Hash::make($request->password),
            'role' => $role,
            'status' => $request->status,
            'divisi' => $request->divisi ?? ($role === 'pikol' ? 'PIKOL Collection' : 'Sales Agency'),
            'witel' => $request->witel ?? 'Witel Priangan Timur',
        ]);

        return redirect()->route('users.index')->with('success', 'Anggota baru berhasil didaftarkan.');
    }

    /**
     * Update data anggota.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:100',
            'kode' => 'nullable|string|max:50',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telegram_id' => 'nullable|string|max:50',
            'role' => 'required|in:pikol,sa,hotd',
            'status' => 'required|in:aktif,tidak_aktif',
            'divisi' => 'nullable|string|max:100',
            'witel' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
        ]);

        $role = ($request->role === 'hotd') ? 'sa' : $request->role;

        $data = [
            'name' => $request->name,
            'username' => $request->username ?: $user->username,
            'kode' => $request->kode,
            'email' => $request->email,
            'telegram_id' => $request->telegram_id,
            'role' => $role,
            'status' => $request->status,
            'divisi' => $request->divisi,
            'witel' => $request->witel,
        ];

        $user->update($data);

        return redirect()->route('users.index')->with('success', "Data anggota {$user->name} berhasil diperbarui.");
    }

    /**
     * Ubah status anggota (Aktif <-> Tidak Aktif).
     */
    public function toggleStatus(User $user)
    {
        // Mencegah admin menonaktifkan akunnya sendiri saat ini
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif.');
        }

        $newStatus = ($user->status === 'aktif' || empty($user->status)) ? 'tidak_aktif' : 'aktif';
        $user->update(['status' => $newStatus]);

        $label = ($newStatus === 'aktif') ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Status anggota {$user->name} berhasil {$label}.");
    }

    /**
     * Hapus anggota dari sistem.
     */
    public function destroy(User $user)
    {
        // Mencegah admin menghapus akunnya sendiri
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('users.index')->with('success', "Anggota {$nama} berhasil dihapus dari sistem.");
    }
}

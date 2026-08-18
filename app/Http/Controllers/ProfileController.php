<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal harus 6 karakter.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini yang Anda masukkan salah.');
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'divisi' => ['nullable', 'string', 'max:255'],
            'witel' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'name.required' => 'Nama Lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'profile_photo.image' => 'File harus berupa gambar.',
            'profile_photo.max' => 'Ukuran gambar maksimal adalah 2MB.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'divisi' => $request->divisi,
            'witel' => $request->witel,
        ];

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('uploads/profile-photos');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            if ($user->profile_photo_path && file_exists(public_path($user->profile_photo_path))) {
                @unlink(public_path($user->profile_photo_path));
            }

            $file->move($destinationPath, $fileName);
            $data['profile_photo_path'] = 'uploads/profile-photos/' . $fileName;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}

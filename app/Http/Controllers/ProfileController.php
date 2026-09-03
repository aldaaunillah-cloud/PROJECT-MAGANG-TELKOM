<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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

        /** @var User $user */
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
        /** @var User $user */
        $user = Auth::user();

        // Jika user adalah member (HOTD), hanya boleh mengedit username dan foto profil
        if (!$user->isPikol()) {
            $request->validate([
                'username' => ['required', 'string', 'max:100'],
                'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            ], [
                'username.required' => 'Username wajib diisi.',
                'profile_photo.image' => 'File harus berupa gambar.',
                'profile_photo.max' => 'Ukuran gambar maksimal adalah 2MB.',
            ]);

            $data = [
                'username' => strtolower(preg_replace('/\s+/', '', $request->username)),
            ];
        } else {
            // Jika user adalah admin (PIKOL), bebas mengedit profil lengkapnya
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['nullable', 'string', 'max:100'],
                'kode' => ['nullable', 'string', 'max:50'],
                'telegram_id' => ['nullable', 'string', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'divisi' => ['nullable', 'string', 'max:255'],
                'witel' => ['nullable', 'string', 'max:255'],
                'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
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
                'username' => $request->username ? strtolower(preg_replace('/\s+/', '', $request->username)) : $user->username,
                'kode' => $request->kode,
                'telegram_id' => $request->telegram_id,
                'email' => $request->email,
                'divisi' => $request->divisi,
                'witel' => $request->witel,
            ];
        }

        // Upload & Kompresi Otomatis Foto Profil ke resolusi max 400x400 px
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('uploads/profile-photos');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Hapus foto lama jika ada
            if ($user->profile_photo_path && file_exists(public_path($user->profile_photo_path))) {
                @unlink(public_path($user->profile_photo_path));
            }

            // Proses resize & kompresi cerdas
            $this->processAndSaveProfilePhoto($file, $destinationPath, $fileName);
            
            $data['profile_photo_path'] = 'uploads/profile-photos/' . $fileName;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Resize dan kompres foto profil ke resolusi max 400x400px agar ringan (~30KB-60KB) dan hemat storage server.
     */
    private function processAndSaveProfilePhoto($uploadedFile, $destinationPath, $fileName)
    {
        $filePath = $uploadedFile->getRealPath();
        $imageInfo = @getimagesize($filePath);

        // Jika fungsi GD / info gambar tidak tersedia, simpan file biasa
        if (!$imageInfo || !function_exists('imagecreatetruecolor')) {
            $uploadedFile->move($destinationPath, $fileName);
            return;
        }

        $origWidth = $imageInfo[0];
        $origHeight = $imageInfo[1];
        $mime = $imageInfo['mime'];

        // Ukuran target foto profil avatar (maksimal 400x400 px)
        $targetMax = 400;

        // Jika gambar aslinya sudah kecil, langsung simpan
        if ($origWidth <= $targetMax && $origHeight <= $targetMax) {
            $uploadedFile->move($destinationPath, $fileName);
            return;
        }

        // Hitung rasio dimensi baru proporsional
        if ($origWidth >= $origHeight) {
            $newWidth = $targetMax;
            $newHeight = (int) round(($origHeight / $origWidth) * $targetMax);
        } else {
            $newHeight = $targetMax;
            $newWidth = (int) round(($origWidth / $origHeight) * $targetMax);
        }

        // Buat resource gambar sesuai tipe format
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $srcImage = @imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $srcImage = @imagecreatefrompng($filePath);
                break;
            case 'image/gif':
                $srcImage = @imagecreatefromgif($filePath);
                break;
            case 'image/webp':
                $srcImage = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($filePath) : null;
                break;
            default:
                $srcImage = null;
        }

        if (!$srcImage) {
            $uploadedFile->move($destinationPath, $fileName);
            return;
        }

        $destImage = imagecreatetruecolor($newWidth, $newHeight);

        // Pertahankan transparansi untuk PNG, GIF, dan WEBP
        if (in_array($mime, ['image/png', 'image/gif', 'image/webp'])) {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        $targetFullPath = $destinationPath . '/' . $fileName;

        // Simpan dengan kompresi berkualitas tinggi (ukuran file turun dari 2MB menjadi ~30-50KB)
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($destImage, $targetFullPath, 82);
                break;
            case 'image/png':
                imagepng($destImage, $targetFullPath, 7);
                break;
            case 'image/gif':
                imagegif($destImage, $targetFullPath);
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    imagewebp($destImage, $targetFullPath, 80);
                } else {
                    imagejpeg($destImage, $targetFullPath, 82);
                }
                break;
            default:
                imagejpeg($destImage, $targetFullPath, 82);
        }

        imagedestroy($srcImage);
        imagedestroy($destImage);
    }
}
